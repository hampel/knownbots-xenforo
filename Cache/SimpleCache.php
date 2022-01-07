<?php namespace Hampel\KnownBots\Cache;

use XF\SimpleCacheSet;

class SimpleCache implements CacheInterface
{
    /** @var SimpleCacheSet */
    protected $cache;

    public function __construct(SimpleCacheSet $cache)
    {
        $this->cache = $cache;
    }

    public function getValue($key)
    {
        return $this->cache->getValue($key);
    }

    public function keyExists($key)
    {
        return $this->cache->keyExists($key);
    }

    public function setValue($key, $value)
    {
        return $this->cache->setValue($key, $value);
    }

    public function deleteValue($key)
    {
        return $this->cache->deleteValue($key);
    }
}
