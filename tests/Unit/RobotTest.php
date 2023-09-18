<?php namespace Tests\Unit;

use Hampel\KnownBots\SubContainer\Cache;
use Tests\TestCase;
use XF\Data\Robot;

class RobotTest extends TestCase
{
	/**
	 * @var Robot
	 */
	private $robot;

	private $cache;

	protected function setUp() : void
	{
		parent::setUp();

		$this->robot = $this->app()->data('XF:Robot');

		$this->cache = $this->mock('knownbots.cache', Cache::class);
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
        $this->cache->expects('loadBotData')->with('maps')->times(3)->andReturns(null);

        $this->assertEquals('baidu', $this->robot->userAgentMatchesRobot('baiduspider'));
        $this->assertEquals('baidu', $this->robot->userAgentMatchesRobot('abc baiduspider 123'));
        $this->assertEquals('bing', $this->robot->userAgentMatchesRobot('bingbot'));
    }

    public function test_userAgentMatchesRobot_returns_empty_on_no_matches()
    {
        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(null);
        $this->cache->expects('loadBotData')->with('complex')->once()->andReturns(null);
        $this->cache->expects('loadBotData')->with('browsers')->once()->andReturns(null);
        $this->cache->expects('loadBotData')->with('ignored')->once()->andReturns(null);

        $this->assertEmpty($this->robot->userAgentMatchesRobot('abc', false));
    }

//	public function test_userAgentMatchesRobot_returns_empty_string_on_fp_match()
//	{
//        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(null);
//        $this->cache->expects('loadBotData')->with('generic')->once()->andReturns(['bot' => 'generic-bot']);
//        $this->cache->expects('loadBotData')->with('falsepos')->once()->andReturns(['cubot']);
//
//		$this->assertEmpty($this->robot->userAgentMatchesRobot('xyx cubot_123'));
//	}
//
//	public function test_userAgentMatchesRobot_returns_generic_name_on_match_cache_ignored()
//	{
//        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(null);
//        $this->cache->expects('loadBotData')->with('generic')->once()->andReturns(['bot' => 'generic-bot']);
//        $this->cache->expects('loadBotData')->with('falsepos')->once()->andReturns(null);
//        $this->cache->expects('loadBotData')->with('ignored')->once()->andReturns(['bot']);
//
//        $this->cache->shouldNotReceive('addUserAgent');
//
//		$this->assertEquals('generic-bot', $this->robot->userAgentMatchesRobot('bot'));
//	}
//
//    public function test_userAgentMatchesRobot_returns_generic_name_on_match_added_to_cache()
//    {
//        $this->cache->expects('loadBotData')->with('maps')->once()->andReturns(null);
//        $this->cache->expects('loadBotData')->with('generic')->once()->andReturns(['spider' => 'generic-spider']);
//        $this->cache->expects('loadBotData')->with('falsepos')->once()->andReturns(null);
//        $this->cache->expects('loadBotData')->with('ignored')->once()->andReturns(['bot']);
//
//        $this->cache->expects('addUserAgent')->with('xspiderx');
//
//		$this->assertEquals('generic-spider', $this->robot->userAgentMatchesRobot('xspiderx'));
//    }
}
