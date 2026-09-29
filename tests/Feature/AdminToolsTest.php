<?php namespace Tests\Feature;

use Hampel\KnownBots\Repository\Agent;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use Hampel\KnownBots\Exception\ServerException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The add-on's admin tools, reached the way an administrator reaches them - through the router,
 * so each action's own permission check runs. GET pages go through dispatch(); the actions that
 * act on data are called directly, since XenForo requires a CSRF token for a POST.
 */
class AdminToolsTest extends TestCase
{
    // the BASE controller, not the add-on's own class: naming the extended class directly needs
    // XenForo to have built its XFCP proxy already, which only an earlier dispatch in the same
    // run does - and resolving this add-on's action through the base proves the extension applied
    private const CONTROLLER = 'XF:Tools';

    private $cache;

    protected function setUp() : void
    {
        parent::setUp();

        $this->cache = $this->mock('knownbots.cache', Cache::class);
        $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });

        // every action asserts this permission itself, rather than in preDispatch(), so a
        // directly called action refuses without it just as a dispatched route does
        $this->asKnownBotsAdmin();
    }

    public function test_the_known_bots_list_renders_for_an_administrator_with_the_permission()
    {
        $this->withBotData();
        $this->cache->expects('getLastChecked')->once()->andReturns(1234567890);

        $reply = $this->dispatch('tools/hampel-knownbots-list', 'admin');

        $this->assertReplyIsView($reply);
        $this->assertReplyTemplate($reply, 'hampel_knownbots_list');
        $this->assertArrayHasKey('foo', $this->replyParam($reply, 'knownBots'));
        $this->assertSame(1234567890, $this->replyParam($reply, 'updated'));
    }

    #[DataProvider('permissionGatedRoutes')]
    public function test_an_administrator_without_the_permission_is_refused(string $route)
    {
        $this->actingAsMember(['is_admin' => true]);   // an administrator, but not this permission

        $this->assertReplyIsError($this->dispatch($route, 'admin'), 403, null, "{$route} should refuse");
    }

    public static function permissionGatedRoutes() : array
    {
        return [
            'list' => ['tools/hampel-knownbots-list'],
            'detect' => ['tools/hampel-knownbots-detect'],
            'new' => ['tools/hampel-knownbots-new'],
            'markdown' => ['tools/hampel-knownbots-md'],
            'fetch' => ['tools/hampel-knownbots-fetch'],
        ];
    }

    public function test_the_bot_markdown_tool_renders()
    {
        $this->withBotData();
        $this->cache->expects('getLastChecked')->once()->andReturns(1234567890);

        $reply = $this->dispatch('tools/hampel-knownbots-md', 'admin');

        $this->assertReplyIsView($reply);
        $this->assertReplyTemplate($reply, 'hampel_knownbots_md');
        $this->assertArrayHasKey('foo', $this->replyParam($reply, 'bots'));
    }

    public function test_the_detect_tool_opens_with_nothing_detected()
    {

        $reply = $this->dispatch('tools/hampel-knownbots-detect', 'admin');

        $this->assertReplyIsView($reply);
        $this->assertReplyTemplate($reply, 'hampel_knownbots_detect');
        $this->assertSame('', $this->replyParam($reply, 'status'));
        $this->assertSame('', $this->replyParam($reply, 'useragent'));
    }

    public function test_the_detect_tool_reports_a_known_bot()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['baiduspider' => 'baidu']);
        $this->cache->allows('loadBotData')->with('bots')->andReturns(['baidu' => ['title' => 'Baidu', 'link' => 'http://www.baidu.com/search/spider.htm']]);
        $this->cache->allows('loadBotData')->andReturns([]);

        $reply = $this->detect('Mozilla/5.0 (compatible; Baiduspider/2.0)');

        $this->assertSame('bot', $this->replyParam($reply, 'status'));
        $this->assertSame('Baidu', $this->replyParam($reply, 'botInfo')['title']);
    }

    public function test_the_detect_tool_reports_a_real_browser()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->allows('loadBotData')->with('browsers')->andReturns([
            'Mozilla/\\d\\.\\d', '\\(X11; Linux x86_64\\)', 'Firefox/[\\d.]+',
        ]);
        $this->cache->allows('loadBotData')->andReturns([]);

        $reply = $this->detect('Mozilla/5.0 (X11; Linux x86_64) Firefox/120.0');

        $this->assertSame('browser', $this->replyParam($reply, 'status'));
    }

    public function test_the_detect_tool_reports_an_ignored_agent()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->allows('loadBotData')->with('ignored')->andReturns(['^Java/\\d']);
        $this->cache->allows('loadBotData')->andReturns([]);

        $this->assertSame('ignored', $this->replyParam($this->detect('Java/1.8.0_292'), 'status'));
    }

    public function test_the_detect_tool_reports_an_agent_it_cannot_place()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->allows('loadBotData')->andReturns([]);

        $this->assertSame('unknown', $this->replyParam($this->detect('definitely-not-a-browser/1.0'), 'status'));
    }

    public function test_the_detect_tool_names_a_bot_it_has_no_details_for()
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['mysterybot' => 'mystery']);
        $this->cache->allows('loadBotData')->andReturns([]);

        $reply = $this->detect('mysterybot/2.0');

        $this->assertSame('bot', $this->replyParam($reply, 'status'));
        $this->assertSame(['title' => 'mystery', 'link' => ''], $this->replyParam($reply, 'botInfo'));
    }

    public function test_fetching_bots_reports_what_the_api_returned()
    {
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('fetchBots')->once()->andReturns([
                'maps' => ['a' => 'a', 'b' => 'b'],
                'bots' => ['a' => []],
                'browsers' => ['c' => 'c'],
            ]);
        });

        $reply = $this->dispatch('tools/hampel-knownbots-fetch', 'admin');

        $this->assertReplyIsMessage($reply);
    }

    public function test_fetching_bots_reports_nothing_new_rather_than_failing()
    {
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('fetchBots')->once()->andReturns(null);
        });

        $this->assertReplyIsMessage($this->dispatch('tools/hampel-knownbots-fetch', 'admin'));
    }

    public function test_fetching_bots_reports_an_api_failure_as_a_message_rather_than_an_error()
    {
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('fetchBots')->once()->andThrow(new ServerException('fetching bots', 'the api is down', 503));
        });

        $this->assertReplyIsMessage($this->dispatch('tools/hampel-knownbots-fetch', 'admin'));
    }

    public function test_clearing_user_agents_reports_the_row_count()
    {
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('clearAllUserAgents')->once()->andReturns(7);
        });

        $this->assertReplyIsMessage($this->callAction(self::CONTROLLER, 'HampelKnownBotsClear', 'admin'));
    }

    public function test_purging_user_agents_reports_the_row_count()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('purgeUserAgents')->with(90)->once()->andReturns(3);
        });

        $this->assertReplyIsMessage($this->callAction(self::CONTROLLER, 'HampelKnownBotsPurge', 'admin'));
    }

    public function test_sending_reports_when_there_is_nothing_to_send()
    {
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('getUserAgentsForSending')->once()->andReturns([]);
            $mock->expects('markUserAgentsSent')->never();
        });

        $this->assertReplyIsMessage($this->callAction(self::CONTROLLER, 'HampelKnownBotsSend', 'admin'));
    }

    public function test_sending_by_email_marks_the_agents_sent()
    {
        $this->fakesMail();
        $this->setOption('knownbotsEmailUserAgents', ['enabled' => true, 'email' => 'admin@example.com']);
        $this->setOption('knownbotsSendUserAgents', ['enabled' => false, 'validation_token' => '', 'api_token' => '']);
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->expects('getUserAgentsForSending')->once()->andReturns(['agent-one']);
            $mock->expects('markUserAgentsSent')->once()->andReturns(1);
        });

        $this->assertReplyIsMessage($this->callAction(self::CONTROLLER, 'HampelKnownBotsSend', 'admin'));
        $this->assertMailSentTimes(1);
    }

    // ------------------------------------------------------------------

    private function detect(string $userAgent)
    {
        return $this->callAction(self::CONTROLLER, 'HampelKnownBotsDetect', 'admin', ['useragent' => $userAgent]);
    }

    private function asKnownBotsAdmin()
    {
        $user = $this->actingAsMember(['is_admin' => true]);
        $this->setVisitorAdminPermissions($user, ['knownbots']);

        return $user;
    }

    private function withBotData() : void
    {
        $this->cache->allows('loadBotData')->with('maps')->andReturns(['foo-agent' => 'foo']);
        $this->cache->allows('loadBotData')->with('bots')->andReturns(['foo' => ['title' => 'Foo', 'link' => 'http://example.com']]);
        $this->cache->allows('loadBotData')->andReturns(null);
    }
}
