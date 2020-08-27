<?php namespace Tests\Unit;

use Tests\TestCase;
use XF\Data\Robot;

class RobotTest extends TestCase
{
	/**
	 * @var Robot
	 */
	private $robot;

	protected function setUp() : void
	{
		parent::setUp();

		$this->robot = $this->app()->data('XF:Robot');
	}

	public function test_robotClass()
	{
		$this->assertInstanceOf(Robot::class, $this->robot);
	}

	public function test_robot_list_has_custom_bots()
	{
		$this->assertArrayHasKey('curl', $this->robot->getRobotUserAgents());
	}

	public function test_userAgentMatchesRobot_returns_empty_string_on_no_match()
	{
		$this->fakesSimpleCache();

		$this->assertEmpty($this->robot->userAgentMatchesRobot('foo'));

		// no user agents should have been cached
		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');
	}

	public function test_userAgentMatchesRobot_returns_robotName_on_match()
	{
		$this->fakesSimpleCache();

		$this->assertEquals('curl', $this->robot->userAgentMatchesRobot('curl'));
		$this->assertEquals('curl', $this->robot->userAgentMatchesRobot('xcurlx'));
		$this->assertEquals('msnbot', $this->robot->userAgentMatchesRobot('msnbot'));
		$this->assertEquals('msnbot', $this->robot->userAgentMatchesRobot('xmsnbotx'));

		// no user agents should have been cached
		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');
	}

	public function test_userAgentMatchesRobot_returns_generic_name_on_match()
	{
		$this->fakesSimpleCache();

		$this->assertEquals('bot', $this->robot->userAgentMatchesRobot('bot'));
		$this->assertEquals('bot', $this->robot->userAgentMatchesRobot('xbotx'));
		$this->assertEquals('crawl', $this->robot->userAgentMatchesRobot('crawl'));
		$this->assertEquals('crawl', $this->robot->userAgentMatchesRobot('xcrawlx'));
		$this->assertEquals('spider', $this->robot->userAgentMatchesRobot('spider'));
		$this->assertEquals('spider', $this->robot->userAgentMatchesRobot('xspiderx'));

		// cache should contain last 5 bots
		$this->assertSimpleCacheEquals(['xbotx', 'crawl', 'xcrawlx', 'spider', 'xspiderx'], 'Hampel/KnownBots', 'user-agents');
	}

	public function test_userAgentMatchesRobot_returns_empty_on_false_positive()
	{
		$this->fakesSimpleCache();

		$userAgents = [
			'Mozilla/5.0 (Linux; Android 6.0; IDbot553PLUS Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/79.0.3945.136 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 6.0; B BOT 50 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.116 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 6.0; B BOT 550 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.124 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 6.0; IDbot553 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.85 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 6.0; IDbot553PLUS Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.111 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 6.0; M BOT 551 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.85 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 6.0; POWER BOT Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.85 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 7.0; ID bot 53 Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/63.0.3239.111 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 7.0; ID bot 53+ Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.91 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 7.0; M bot 51 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 7.0; M bot 54 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.83 Mobile Safari/537.36',
			'Mozilla/5.0 (Linux; Android 7.0; M bot 60 Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/56.0.2924.87 Mobile Safari/537.36',
		];

		foreach ($userAgents as $userAgent)
		{
			$bot = $this->robot->userAgentMatchesRobot($userAgent);
			$this->assertEmpty($bot, "incorrectly detected [{$bot}] for UserAgent string [{$userAgent}]");
		}

		// no user agents should have been cached
		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');
	}
}
