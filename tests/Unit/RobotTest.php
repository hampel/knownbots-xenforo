<?php namespace Tests\Unit;

use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use Tests\TestCase;
use XF\Data\Robot;

class RobotTest extends TestCase
{
	/**
	 * @var Robot
	 */
	private $robot;

	private $cache;

    private $repo;

	protected function setUp() : void
	{
		parent::setUp();

		$this->robot = $this->app()->data('XF:Robot');

		$this->cache = $this->mock('knownbots.cache', Cache::class);
        $this->repo = $this->mockRepository('Hampel\KnownBots:Agent');

        // detection logs as a side effect; the logger is an optional integration with
        // Hampel/Monolog, so mock it rather than let the suite depend on that add-on
        $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });
	}

	public function test_robotClass()
	{
		$this->assertInstanceOf(Robot::class, $this->robot);
        $this->assertEquals('Hampel\KnownBots\XF\Data\Robot', get_class($this->robot));
	}

    public function test_robot_user_agents_has_default_bots()
    {
        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(null);
        $this->assertArrayHasKey('magpie-crawler', $this->robot->getRobotUserAgents());
    }

	public function test_robot_user_agents_has_custom_bots()
	{
        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(['foo' => 'bar']);

        $agents = $this->robot->getRobotUserAgents();
		$this->assertArrayHasKey('foo', $agents);
		$this->assertEquals('bar', $agents['foo']);
	}

    public function test_robot_list_has_default_bots()
    {
        $this->cache->expects('loadBotData')->with('bots')->once()->andReturns(null);

        $robots = $this->robot->getRobotList();

        $this->assertArrayHasKey('brandwatch', $robots);
        $this->assertEquals('Brandwatch', $robots['brandwatch']['title']);

    }

    public function test_robot_list_has_custom_bots()
    {
        $this->cache->expects('loadBotData')->with('bots')->twice()->andReturns(['foo' => ['title' => 'Foo', 'link' => 'http://example.com']]);

        $this->assertArrayHasKey('foo', $this->robot->getRobotList());
        $info = $this->robot->getRobotInfo('foo');

        $this->assertIsArray($info);
        $this->assertArrayHasKey('title', $info);
        $this->assertEquals('Foo', $info['title']);
        $this->assertEquals('http://example.com', $info['link']);
    }

    public function test_userAgentMatchesRobot_returns_robotName_on_match()
    {
        // a match is only stored when storing is enabled - set it rather than inherit the install's value
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);

        $this->cache->expects('loadBotData')->with('maps')->times(3)->andReturns(null);
        $this->repo->expects('addUserAgent')->times(3)->andReturns(0);

        $this->assertEquals('baidu', $this->robot->userAgentMatchesRobot('baiduspider'));
        $this->assertEquals('baidu', $this->robot->userAgentMatchesRobot('abc baiduspider 123'));
        $this->assertEquals('bing', $this->robot->userAgentMatchesRobot('bingbot'));
    }

    public function test_userAgentMatchesRobot_returns_empty_on_no_matches_and_no_save()
    {
        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(null);
        $this->cache->expects('loadBotData')->with('complex')->once()->andReturns(null);

        $this->assertEmpty($this->robot->userAgentMatchesRobot('abc', false));
    }

    public function test_userAgentMatchesRobot_returns_empty_on_no_matches_and_store_not_enabled()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => false, 'days' => 90]);

        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(null);
        $this->cache->expects('loadBotData')->with('complex')->once()->andReturns(null);

        $this->assertEmpty($this->robot->userAgentMatchesRobot('abc', true));
    }

    public function test_userAgentMatchesRobot_returns_empty_on_no_matches()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);

        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(null);
        $this->cache->expects('loadBotData')->with('complex')->once()->andReturns(null);
        $this->cache->expects('loadBotData')->with('ignored')->once()->andReturns(null);
        $this->cache->expects('loadBotData')->with('browsers')->once()->andReturns(null);

        $this->repo->expects('addUserAgent')->andReturns(0);

        $this->assertEmpty($this->robot->userAgentMatchesRobot('abc', true));
    }

    // The tests below return a non-null 'maps' that matches nothing, so the simple match is
    // decided by these tests rather than by XenForo's own robot list. Every loadBotData() call
    // not expected here fails the test, so each also asserts how far detection got.

    public function test_userAgentMatchesRobot_returns_robotName_on_complex_match()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);

        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->expects('loadBotData')->with('complex')->once()->andReturns(['zzqx\w+crawler' => 'zzqx']);

        $this->repo->expects('addUserAgent')->with('ZzqxFooCrawler/2.1', 'zzqx')->once()->andReturns(1);

        $this->assertSame('zzqx', $this->robot->userAgentMatchesRobot('ZzqxFooCrawler/2.1'));
    }

    public function test_userAgentMatchesRobot_stops_for_a_logged_in_member_without_storing()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);
        $this->actingAsMember();

        // no 'ignored' or 'browsers' expectation: a member is assumed human after the bot checks
        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->expects('loadBotData')->with('complex')->once()->andReturns([]);

        $this->repo->expects('addUserAgent')->never();

        $this->assertSame('', $this->robot->userAgentMatchesRobot('SomeUnknownAgent/1.0'));
    }

    public function test_userAgentMatchesRobot_does_not_store_an_ignored_agent()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);

        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->expects('loadBotData')->with('complex')->once()->andReturns([]);
        $this->cache->expects('loadBotData')->with('ignored')->once()->andReturns(['^Java/\d']);

        $this->repo->expects('addUserAgent')->never();

        $this->assertSame('', $this->robot->userAgentMatchesRobot('Java/1.8.0_292'));
    }

    public function test_userAgentMatchesRobot_does_not_store_a_valid_browser()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);
        $this->expectBrowserChecks();

        $this->repo->expects('addUserAgent')->never();

        // every part of the string is accounted for by a browser pattern
        $this->assertSame('', $this->robot->userAgentMatchesRobot('Mozilla/5.0 (X11; Linux x86_64) Firefox/120.0'));
    }

    public function test_userAgentMatchesRobot_stores_a_browser_like_agent_with_anything_left_over()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);
        $this->expectBrowserChecks();

        // browser detection is by subtraction: strip every browser pattern and require nothing to
        // remain, so a real browser string with one unexplained token is NOT a browser
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) Firefox/120.0 SneakyScraper/1.0';
        $this->repo->expects('addUserAgent')->with($userAgent, null)->once()->andReturns(1);

        $this->assertSame('', $this->robot->userAgentMatchesRobot($userAgent));
    }

    // ------------------------------------------------------------------

    private function expectBrowserChecks() : void
    {
        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(['nomatch-xyz' => 'nomatch']);
        $this->cache->expects('loadBotData')->with('complex')->once()->andReturns([]);
        $this->cache->expects('loadBotData')->with('ignored')->once()->andReturns([]);
        $this->cache->expects('loadBotData')->with('browsers')->once()->andReturns([
            'Mozilla/\d\.\d',
            '\(X11; Linux x86_64\)',
            'Firefox/[\d.]+',
        ]);
    }
}
