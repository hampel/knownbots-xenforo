<?php namespace Tests\Unit;

use Hampel\KnownBots\Exception\CustomerException;
use Hampel\KnownBots\Exception\ServerException;
use Hampel\KnownBots\Exception\UnauthorizedException;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Log;
use Tests\TestCase;

/**
 * The retry-once-on-401 flow: send, and on an authorisation failure revalidate the licence, store
 * the new token and send once more. Any other failure, or a second 401, is final.
 */
class UserAgentSenderTest extends TestCase
{
    private const AGENTS = ['Agent/1.0'];

    private $api;

    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesErrors();

        $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });

        $this->api = $this->mock('knownbots.api', Api::class);
    }

    public function test_sends_once_when_the_token_is_accepted()
    {
        $this->api->expects('sendUserAgents')->with('old-token', self::AGENTS)->once()->andReturns(['received' => 1]);
        $this->api->expects('validate')->never();
        $this->api->expects('updateApiToken')->never();

        $this->assertSame(['received' => 1], $this->sender()->sendUserAgents());
        $this->assertNoExceptionsLogged();
    }

    public function test_revalidates_stores_the_new_token_and_retries_after_a_401()
    {
        $this->api->expects('sendUserAgents')->with('old-token', self::AGENTS)->once()->andThrow($this->unauthorized());
        $this->api->expects('validate')->with('licence-token')->once()->andReturns('new-token');
        $this->api->expects('updateApiToken')->with('new-token')->once();
        $this->api->expects('sendUserAgents')->with('new-token', self::AGENTS)->once()->andReturns(['received' => 1]);

        $this->assertSame(['received' => 1], $this->sender()->sendUserAgents());
        $this->assertNoExceptionsLogged();
    }

    public function test_a_second_401_is_final()
    {
        $this->api->expects('sendUserAgents')->with('old-token', self::AGENTS)->once()->andThrow($this->unauthorized());
        $this->api->expects('validate')->with('licence-token')->once()->andReturns('new-token');
        $this->api->expects('updateApiToken')->with('new-token')->once();
        $this->api->expects('sendUserAgents')->with('new-token', self::AGENTS)->once()->andThrow($this->unauthorized());

        $sender = $this->sender();

        $this->assertFalse($sender->sendUserAgents());
        $this->assertNotEmpty($sender->getError());
        // the first 401 is expected and only noted; the second is the failure
        $this->assertExceptionLogged(UnauthorizedException::class, 1);
    }

    public function test_a_failed_revalidation_stops_without_storing_or_retrying()
    {
        $this->api->expects('sendUserAgents')->once()->andThrow($this->unauthorized());
        $this->api->expects('validate')->once()->andThrow(new CustomerException('validating license token', 'Forbidden', 403));
        $this->api->expects('updateApiToken')->never();

        $this->assertFalse($this->sender()->sendUserAgents());
        $this->assertExceptionLogged(CustomerException::class, 1);
    }

    public function test_any_other_failure_stops_without_revalidating()
    {
        $this->api->expects('sendUserAgents')->once()->andThrow(new ServerException('sending user agents', 'Service Unavailable', 503));
        $this->api->expects('validate')->never();

        $this->assertFalse($this->sender()->sendUserAgents());
        $this->assertExceptionLogged(ServerException::class, 1);
    }

    // ------------------------------------------------------------------

    private function sender()
    {
        $sender = $this->app()->service('Hampel\KnownBots:UserAgentSender');
        $sender->setApiToken('old-token');
        $sender->setValidationToken('licence-token');
        $sender->setUserAgents(self::AGENTS);

        return $sender;
    }

    private function unauthorized() : UnauthorizedException
    {
        return new UnauthorizedException('sending user agents', 'Unauthorized', 401);
    }
}
