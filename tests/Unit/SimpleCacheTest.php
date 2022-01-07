<?php namespace Tests\Unit;

use Hampel\KnownBots\Cache\SimpleCache;
use Tests\TestCase;

class SimpleCacheTest extends TestCase
{
	/**
	 * @var SimpleCache
	 */
	private $cache;

	protected function setUp() : void
	{
		parent::setUp();

        $this->fakesSimpleCache();

		$this->cache = new SimpleCache($this->app()->simpleCache()->getSet('foo'));
	}

	public function test_cache()
    {
        $this->cache->setValue('abc', 123);

        $this->assertSimpleCacheHas('foo', 'abc');
        $this->assertSimpleCacheEqual(123, 'foo', 'abc');

        $this->assertSimpleCacheHasNot('foo', 'def');
        $this->assertSimpleCacheHasNot('bar', 'abc');

        $this->assertTrue($this->cache->keyExists('abc'));
        $this->assertFalse($this->cache->keyExists('def'));
        $this->assertEquals(123, $this->cache->getValue('abc'));

        $this->cache->deleteValue('abc');
        $this->assertSimpleCacheHasNot('foo', 'abc');
    }
}
