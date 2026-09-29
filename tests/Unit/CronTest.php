<?php namespace Tests\Unit;

use Hampel\KnownBots\Cron\FetchBots;
use Hampel\KnownBots\Cron\SendAgents;
use Hampel\KnownBots\Exception\CustomerException;
use Hampel\KnownBots\Exception\ServerException;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Log;
use Tests\TestCase;

/**
 * The cron entry points - how everything here runs on a live forum. Each is a guard sequence over
 * the same services the CLI and the admin tools use, and the guards are what decide whether a
 * forum sends anything at all.
 */
class CronTest extends TestCase
{
    private $log;

    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesErrors();
        $this->log = $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });
    }

    public function test_the_fetch_cron_does_nothing_when_fetching_is_disabled()
    {
        $this->setOption('knownbotsFetchNewBots', false);
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('fetchBots')->never();
        });

        FetchBots::fetchBots();
    }

    public function test_the_fetch_cron_fetches_when_enabled()
    {
        $this->setOption('knownbotsFetchNewBots', true);
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('fetchBots')->once()->andReturns(null);
        });

        FetchBots::fetchBots();
        $this->assertNoExceptionsLogged();
    }

    public function test_a_server_error_is_a_warning_rather_than_an_error_log_entry()
    {
        $this->setOption('knownbotsFetchNewBots', true);
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('fetchBots')->once()->andThrow(new ServerException('fetching bots', 'service unavailable', 503));
        });

        FetchBots::fetchBots();

        // a 5xx is transient: logged to the add-on's own log, but not to XenForo's error log
        $this->assertNoExceptionsLogged();
    }

    public function test_any_other_api_failure_reaches_the_error_log()
    {
        $this->setOption('knownbotsFetchNewBots', true);
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('fetchBots')->once()->andThrow(new CustomerException('fetching bots', 'not authorised', 403));
        });

        FetchBots::fetchBots();

        $this->assertExceptionLogged(CustomerException::class, 1);
    }

    public function test_the_send_cron_stops_when_storing_is_disabled()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => false, 'days' => 90]);
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('getUserAgentsForSending')->never();
        });

        SendAgents::send();
    }

    public function test_the_send_cron_stops_when_neither_sending_nor_emailing_is_enabled()
    {
        $this->storingEnabled();
        $this->setOption('knownbotsSendUserAgents', ['enabled' => false, 'validation_token' => '', 'api_token' => '']);
        $this->setOption('knownbotsEmailUserAgents', ['enabled' => false, 'email' => '']);
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('getUserAgentsForSending')->never();
        });

        SendAgents::send();
    }

    public function test_the_send_cron_marks_nothing_when_there_is_nothing_to_send()
    {
        $this->storingEnabled();
        $this->emailOnly();
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('getUserAgentsForSending')->once()->andReturns([]);
            $mock->expects('markUserAgentsSent')->never();
        });

        SendAgents::send();
    }

    public function test_the_send_cron_emails_the_agents_and_marks_them_sent()
    {
        $this->fakesMail();
        $this->storingEnabled();
        $this->emailOnly();
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('getUserAgentsForSending')->once()->andReturns(['agent-one', 'agent-two']);
            $mock->expects('markUserAgentsSent')->once()->andReturns(2);
        });

        SendAgents::send();

        $this->assertMailSentTimes(1);
    }

    public function test_the_send_cron_does_not_mark_agents_sent_when_the_api_refuses_them()
    {
        $this->storingEnabled();
        $this->setOption('knownbotsSendUserAgents', ['enabled' => true, 'validation_token' => 'v', 'api_token' => 'a']);
        $this->setOption('knownbotsEmailUserAgents', ['enabled' => false, 'email' => '']);
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('getUserAgentsForSending')->once()->andReturns(['agent-one']);
            $mock->expects('markUserAgentsSent')->never();
        });
        $this->mockService('Hampel\KnownBots:UserAgentSender', function ($mock)
        {
            $mock->allows('setApiToken');
            $mock->allows('setValidationToken');
            $mock->allows('setUserAgents');
            $mock->expects('sendUserAgents')->once()->andReturns(false);
        });

        SendAgents::send();
    }

    public function test_the_purge_cron_does_nothing_when_retention_is_set_to_never()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 0]);
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('purgeUserAgents')->never();
        });

        SendAgents::purgeAgents();
    }

    public function test_the_purge_cron_purges_to_the_configured_retention()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 45]);
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('purgeUserAgents')->with(45)->once()->andReturns(12);
        });

        SendAgents::purgeAgents();
    }

    // ------------------------------------------------------------------

    private function storingEnabled() : void
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);
    }

    private function emailOnly() : void
    {
        $this->setOption('knownbotsSendUserAgents', ['enabled' => false, 'validation_token' => '', 'api_token' => '']);
        $this->setOption('knownbotsEmailUserAgents', ['enabled' => true, 'email' => 'admin@example.com']);
    }
}
