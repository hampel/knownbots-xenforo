<?php namespace Tests\Unit;

use Hampel\KnownBots\Api\KnownBots;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * fetchBots() given a response that is not a bot payload. The HTTP client is replaced inside the
 * API sub-container, and the filesystem and caches are mocked, so nothing real is fetched,
 * written or cached.
 */
class FetchBotsTest extends TestCase
{
    private $log;

    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesErrors();

        // strict mocks: anything past the validity check - storing the payload, rebuilding the
        // cache - calls a method nothing here expects, and fails the test instead of writing
        $this->mockFs('internal-data');
        $this->mock('knownbots.cache', Cache::class, function ($mock)
        {
            $mock->allows('getLastChecked')->andReturns(0);
        });

        $this->log = $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });
    }

    #[DataProvider('scalarBodies')]
    public function test_rejects_a_scalar_response_without_fatalling($decoded)
    {
        $this->clientReturns($decoded);
        // the payload itself is deliberately not logged - only enough of its shape to say why it
        // was refused, since a response body is where an unplanned value would end up
        $this->log->expects('error')->with('Invalid bot data returned', [
            'type' => gettype($decoded),
            'entries' => null,
            'keys' => null,
            'version' => null,
        ])->once();

        $this->assertFalse($this->app()['knownbots.api']->fetchBots(true, false));
        $this->assertErrorLogged('Known Bots: invalid bot data returned from api call');
    }

    public static function scalarBodies() : array
    {
        // each is non-empty, so it gets past fetchBots()' empty() check
        return [
            'string' => ['ok'],
            'true' => [true],
            'number' => [123],
        ];
    }

    public function test_still_rejects_an_array_that_is_not_a_valid_payload()
    {
        $payload = ['version' => 2];
        $this->clientReturns($payload);
        $this->log->expects('error')->with('Invalid bot data returned', [
            'type' => 'array',
            'entries' => 1,
            'keys' => 'version',
            'version' => 2,
        ])->once();

        $this->assertFalse($this->app()['knownbots.api']->fetchBots(true, false));
        $this->assertErrorLogged('Known Bots: invalid bot data returned from api call');
    }

    // ------------------------------------------------------------------

    private function clientReturns($decoded) : void
    {
        $client = \Mockery::mock(KnownBots::class);
        $client->allows('fetch')->andReturns($decoded);

        $this->swap([$this->app()['knownbots.api'], 'bots'], function () use ($client)
        {
            return $client;
        });
    }
}
