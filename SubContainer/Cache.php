<?php namespace Hampel\KnownBots\SubContainer;

use Hampel\KnownBots\Cache\CodeCache;
use Hampel\KnownBots\Cache\SimpleCache;
use XF\SubContainer\AbstractSubContainer;

class Cache extends AbstractSubContainer
{
    public function initialize()
    {
        $container = $this->container;

        $container['code.cache'] = function($c)
        {
            return new CodeCache($this->app->fs());
        };

        $container['simple.cache'] = function($c)
        {
            return new SimpleCache($this->app->simpleCache()->getSet('Hampel/KnownBots'));
        };
    }

    public function setLastChecked($timestamp)
    {
        $this->simpleCache()->setValue('last-checked', $timestamp);
    }

    public function getLastChecked()
    {
        $last = $this->simpleCache()->getValue('last-checked');

        return $last ?? 0;
    }

    public function loadBotData($type)
    {
        return $this->codeCache()->getValue($type);
    }

    public function rebuildBotCache($bots)
    {
        $cache = $this->codeCache();

        $cache->setValue('maps', $bots['maps']);
        $cache->setValue('bots', $bots['bots']);
        $cache->setValue('complex', $bots['complex']);
        $cache->setValue('ignored', $bots['ignored']);
        $cache->setValue('browsers', $bots['browsers']);
    }

    /**
     * @return CodeCache
     */
    protected function codeCache()
    {
        return $this->container['code.cache'];
    }

    /**
     * @return SimpleCache
     */
    protected function simpleCache()
    {
        return $this->container['simple.cache'];
    }
}
