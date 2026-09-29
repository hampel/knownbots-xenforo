<?php namespace Tests\Feature;

use Hampel\KnownBots\Cli\Command\CheckApiToken;
use Hampel\KnownBots\Cli\Command\TestBots;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use Symfony\Component\Console\Command\Command;
use Tests\TestCase;

/**
 * The CLI commands, run against the booted application the way cmd.php runs them. Each one
 * duplicates a cron method or an admin action, so this covers the half that has no other caller.
 */
class ConsoleCommandTest extends TestCase
{
    private $cache;

    protected function setUp() : void
    {
        parent::setUp();

        $this->cache = $this->mock('knownbots.cache', Cache::class);
        $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });
    }

    public function test_the_bot_test_command_identifies_a_known_bot()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['baiduspider' => 'baidu']);
        $this->cache->allows('loadBotData')->with('bots')->andReturns(['baidu' => ['title' => 'Baidu', 'link' => 'http://example.com']]);
        $this->cache->allows('loadBotData')->andReturns([]);

        $tester = $this->runConsoleCommand(TestBots::class, ['agent' => 'Mozilla/5.0 (compatible; Baiduspider/2.0)']);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertStringContainsString('Found robot: [baidu]', $tester->getDisplay());
        $this->assertStringContainsString('Baidu', $tester->getDisplay());
    }

    public function test_the_bot_test_command_reports_an_unknown_agent()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->allows('loadBotData')->andReturns([]);

        $tester = $this->runConsoleCommand(TestBots::class, ['agent' => 'definitely-not-a-browser/1.0']);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertStringContainsString('Unknown user agent', $tester->getDisplay());
    }

    public function test_the_bot_test_command_reports_a_valid_browser()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->allows('loadBotData')->with('browsers')->andReturns([
            'Mozilla/\d\.\d', '\(X11; Linux x86_64\)', 'Firefox/[\d.]+',
        ]);
        $this->cache->allows('loadBotData')->andReturns([]);

        $tester = $this->runConsoleCommand(TestBots::class, ['agent' => 'Mozilla/5.0 (X11; Linux x86_64) Firefox/120.0']);

        $this->assertStringContainsString('valid browser', $tester->getDisplay());
    }

    public function test_the_bot_test_command_reports_an_ignored_agent()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->allows('loadBotData')->with('ignored')->andReturns(['^Java/\d']);
        $this->cache->allows('loadBotData')->andReturns([]);

        $tester = $this->runConsoleCommand(TestBots::class, ['agent' => 'Java/1.8.0_292']);

        $this->assertStringContainsString('ignored list', $tester->getDisplay());
    }

    public function test_the_token_check_command_refuses_when_the_api_is_not_configured()
    {
        $this->setOption('knownbotsSendUserAgents', ['enabled' => false, 'validation_token' => '', 'api_token' => '']);

        $tester = $this->runConsoleCommand(CheckApiToken::class);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('API is not configured', $tester->getDisplay());
    }

    public function test_the_token_check_command_reports_the_domain_a_valid_token_belongs_to()
    {
        $this->setOption('knownbotsSendUserAgents', [
            'enabled' => true, 'validation_token' => 'licence-token', 'api_token' => 'api-token',
        ]);
        $this->mockService('Hampel\KnownBots:ApiTokenChecker', function ($mock)
        {
            $mock->allows('setApiToken');
            $mock->allows('setValidationToken');
            $mock->expects('checkToken')->once()->andReturns('www.example.com');
        });

        $tester = $this->runConsoleCommand(CheckApiToken::class);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertStringContainsString('valid for domain www.example.com', $tester->getDisplay());
    }
}
