<?php namespace Tests\Unit;

use Hampel\KnownBots\Repository\UserAgentCache;
use Tests\TestCase;
use XF\SimpleCacheSet;

class CacheTest extends TestCase
{
	/**
	 * @var UserAgentCache
	 */
	private $cache;

	protected function setUp() : void
	{
		parent::setUp();

		$this->cache = new UserAgentCache($this->app()->em(), 'foo');
	}

	public function test_getCache()
	{
		$this->fakesSimpleCache();

		$this->assertInstanceOf(SimpleCacheSet::class, $this->cache->getCache());
	}

	public function test_getUserAgents_returns_empty_array_when_no_data_set()
	{
		$this->fakesSimpleCache();

		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');

		$value = $this->cache->getUserAgents();
		$this->assertIsArray($value);
		$this->assertEmpty($value);
		$this->assertEquals(0, $this->cache->countUserAgents());
	}

	public function test_setUserAgents_sets_empty_array()
	{
		$this->fakesSimpleCache();

		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');

		$this->cache->setUserAgents([]);

		$this->assertSimpleCacheHas('Hampel/KnownBots', 'user-agents');
		$this->assertSimpleCacheEquals([], 'Hampel/KnownBots', 'user-agents');
	}

	public function test_addUserAgent_sets_array()
	{
		$this->fakesSimpleCache();

		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');

		$this->cache->addUserAgent('FOO');

		$this->assertSimpleCacheHas('Hampel/KnownBots', 'user-agents');
		$this->assertSimpleCacheEquals(['foo'], 'Hampel/KnownBots', 'user-agents');

		$value = $this->cache->getUserAgents();
		$this->assertIsArray($value);
		$this->assertNotEmpty($value);
		$this->assertEquals(1, $this->cache->countUserAgents());
		$this->assertEquals(['foo'], $value);

		$this->cache->addUserAgent('Bar');

		$this->assertSimpleCacheEquals(['foo', 'bar'], 'Hampel/KnownBots', 'user-agents');

		$value = $this->cache->getUserAgents();
		$this->assertEquals(2, $this->cache->countUserAgents());
		$this->assertEquals(['foo', 'bar'], $value);
	}

	public function test_addUserAgent_only_stores_last_5_strings()
	{
		$this->fakesSimpleCache();

		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');

		$this->cache->setUserAgents(['foo1', 'foo2', 'foo3', 'foo4', 'foo5']);

		$this->assertSimpleCacheHas('Hampel/KnownBots', 'user-agents');
		$this->assertEquals(5, $this->cache->countUserAgents());

		$this->cache->addUserAgent('foo6');

		$this->assertSimpleCacheEquals(['foo2', 'foo3', 'foo4', 'foo5', 'foo6'], 'Hampel/KnownBots', 'user-agents');

		$this->assertEquals(5, $this->cache->countUserAgents());
	}

	public function test_addUserAgent_doesnt_update_duplicate_string()
	{
		$this->fakesSimpleCache();

		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');

		$this->cache->setUserAgents(['aaa', 'bbb']);

		$this->assertSimpleCacheHas('Hampel/KnownBots', 'user-agents');
		$this->assertEquals(2, $this->cache->countUserAgents());

		$this->cache->addUserAgent('bbb');

		$this->assertSimpleCacheEquals(['aaa', 'bbb'], 'Hampel/KnownBots', 'user-agents');
	}

	public function test_clearCache_empties_cache()
	{
		$this->fakesSimpleCache();

		$this->assertSimpleCacheHasNot('Hampel/KnownBots', 'user-agents');

		$this->cache->addUserAgent('FOO');
		$this->assertSimpleCacheEquals(['foo'], 'Hampel/KnownBots', 'user-agents');

		$this->cache->clearCache();

		$this->assertSimpleCacheHas('Hampel/KnownBots', 'user-agents');
		$this->assertSimpleCacheEquals([], 'Hampel/KnownBots', 'user-agents');
	}
}
