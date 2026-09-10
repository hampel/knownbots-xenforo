<?php namespace Tests\Unit;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hampel\KnownBots\Api\KnownBots;
use Hampel\KnownBots\Exception\CustomerException;
use Hampel\KnownBots\Exception\RequestException;
use Hampel\KnownBots\Exception\ServerException;
use Hampel\KnownBots\Exception\UnauthorizedException;
use Hampel\KnownBots\SubContainer\Log;
use Tests\TestCase;

/**
 * The API client against faked HTTP responses.
 *
 * These drive the trusted request path. XenForo's untrusted reader resolves the host and refuses
 * local and unresolvable addresses before the HTTP client runs, so a faked response can never
 * answer it. The status handling and decoding covered here are shared by both paths; only the
 * reader method differs.
 */
class ApiClientTest extends TestCase
{
    private const BASE = 'https://api.knownbots.test/api';

    protected function setUp() : void
    {
        parent::setUp();

        $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });
    }

    // fetch() ----------------------------------------------------------

    public function test_fetch_returns_the_decoded_payload_on_200()
    {
        $payload = ['version' => 3, 'built' => 1700000000, 'maps' => ['foobot' => 'foobot']];
        $this->fakesHttpByUrl([self::BASE . '/v3/bots' => new Response(200, [], json_encode($payload))]);

        $this->assertSame($payload, $this->client()->fetch(0, true));
    }

    public function test_fetch_returns_null_when_not_modified()
    {
        $this->fakesHttpByUrl(['*' => new Response(304)]);

        $this->assertNull($this->client()->fetch(1700000000));
    }

    public function test_fetch_sends_if_modified_since_unless_forced()
    {
        $this->fakesHttpByUrl(['*' => new Response(304)]);

        $this->client()->fetch(1700000000);
        $this->client()->fetch(1700000000, true);

        [$conditional, $forced] = $this->getHttpRequests();
        $this->assertSame(gmdate('D, d M Y H:i:s', 1700000000) . ' GMT', $conditional->getHeaderLine('If-Modified-Since'));
        $this->assertFalse($forced->hasHeader('If-Modified-Since'));
    }

    public function test_fetch_raises_a_server_exception_on_5xx()
    {
        $this->fakesHttpByUrl(['*' => new Response(503)]);

        $this->assertSame(ServerException::class, $this->thrownBy(fn() => $this->client()->fetch(0, true)));
    }

    public function test_fetch_raises_a_customer_exception_on_4xx()
    {
        $this->fakesHttpByUrl(['*' => new Response(404)]);

        $this->assertSame(CustomerException::class, $this->thrownBy(fn() => $this->client()->fetch(0, true)));
    }

    public function test_fetch_raises_a_request_exception_when_the_request_fails()
    {
        $this->fakesHttpByUrl([
            '*' => new ConnectException('connection refused', new Request('GET', self::BASE . '/v3/bots')),
        ]);

        $this->assertSame(RequestException::class, $this->thrownBy(fn() => $this->client()->fetch(0, true)));
    }

    // validate() -------------------------------------------------------

    public function test_validate_returns_the_issued_token_and_sends_the_licence_token_and_domain()
    {
        $this->fakesHttpByUrl([self::BASE . '/v3/validate-customer' => new Response(200, [], '{"token":"issued-token"}')]);

        $this->assertSame('issued-token', $this->client()->validate('licence-token', 'www.example.com'));

        [$request] = $this->getHttpRequests();
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame(
            ['validation_token' => 'licence-token', 'domain' => 'www.example.com'],
            json_decode((string) $request->getBody(), true)
        );
    }

    public function test_validate_returns_an_empty_token_when_none_is_issued()
    {
        $this->fakesHttpByUrl(['*' => new Response(200, [], '{}')]);

        $this->assertSame('', $this->client()->validate('licence-token', 'www.example.com'));
    }

    public function test_validate_raises_a_customer_exception_on_403()
    {
        $this->fakesHttpByUrl(['*' => new Response(403)]);

        $this->assertSame(CustomerException::class, $this->thrownBy(fn() => $this->client()->validate('t', 'd')));
    }

    public function test_validate_raises_a_server_exception_on_5xx()
    {
        $this->fakesHttpByUrl(['*' => new Response(500)]);

        $this->assertSame(ServerException::class, $this->thrownBy(fn() => $this->client()->validate('t', 'd')));
    }

    // checkApiToken() --------------------------------------------------

    public function test_check_api_token_returns_the_decoded_response_and_sends_a_bearer_token()
    {
        $this->fakesHttpByUrl([self::BASE . '/v3/check-token' => new Response(200, [], '{"domain":"www.example.com"}')]);

        $this->assertSame(['domain' => 'www.example.com'], $this->client()->checkApiToken('api-token'));

        [$request] = $this->getHttpRequests();
        $this->assertSame('Bearer api-token', $request->getHeaderLine('Authorization'));
    }

    public function test_check_api_token_raises_unauthorized_on_401()
    {
        $this->fakesHttpByUrl(['*' => new Response(401)]);

        $this->assertSame(UnauthorizedException::class, $this->thrownBy(fn() => $this->client()->checkApiToken('t')));
    }

    public function test_check_api_token_reports_403_as_a_customer_error_not_unauthorized()
    {
        // only a 401 makes the callers revalidate, so a 403 must not be reported as one
        $this->fakesHttpByUrl(['*' => new Response(403)]);

        $this->assertSame(CustomerException::class, $this->thrownBy(fn() => $this->client()->checkApiToken('t')));
    }

    // sendUserAgents() -------------------------------------------------

    public function test_send_user_agents_returns_the_decoded_response()
    {
        $this->fakesHttpByUrl([self::BASE . '/v3/user-agents' => new Response(200, [], '{"received":2}')]);

        $this->assertSame(['received' => 2], $this->client()->sendUserAgents('api-token', ['A/1.0', 'B/2.0']));

        [$request] = $this->getHttpRequests();
        $this->assertSame('Bearer api-token', $request->getHeaderLine('Authorization'));
        $this->assertSame(['agents' => ['A/1.0', 'B/2.0']], json_decode((string) $request->getBody(), true));
    }

    public function test_send_user_agents_drops_invalidly_encoded_agents()
    {
        $this->fakesHttpByUrl(['*' => new Response(200, [], '{}')]);

        $this->client()->sendUserAgents('api-token', ["Bad\xB1/1.0", 'Good/1.0']);

        [$request] = $this->getHttpRequests();
        $agents = json_decode((string) $request->getBody(), true)['agents'];

        // values only: removing an entry leaves a gap in the keys, so the agents currently
        // arrive as a JSON object rather than a list whenever the dropped entry is not last
        $this->assertSame(['Good/1.0'], array_values($agents));
    }

    public function test_send_user_agents_raises_unauthorized_on_401()
    {
        $this->fakesHttpByUrl(['*' => new Response(401)]);

        $this->assertSame(
            UnauthorizedException::class,
            $this->thrownBy(fn() => $this->client()->sendUserAgents('t', ['A/1.0']))
        );
    }

    // ------------------------------------------------------------------

    private function client() : KnownBots
    {
        return new KnownBots($this->app(), self::BASE, true);
    }

    /**
     * The exact class thrown. expectException() accepts subclasses, and UnauthorizedException
     * extends CustomerException, so it cannot tell a 401 from a 403.
     */
    private function thrownBy(callable $call) : string
    {
        try
        {
            $call();
        }
        catch (\Throwable $e)
        {
            return get_class($e);
        }

        $this->fail('Expected an exception, none was thrown');
    }
}
