<?php namespace Hampel\KnownBots\Cache;

interface CacheInterface
{
    public function getValue($key);

    public function keyExists($key);

    public function setValue($key, $value);

    public function deleteValue($key);
}
