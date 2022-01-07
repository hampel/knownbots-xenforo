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

    /**
     * @return CodeCache
     */
    public function codeCache()
    {
        return $this->container['code.cache'];
    }

    /**
     * @return SimpleCache
     */
    public function simpleCache()
    {
        return $this->container['simple.cache'];
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

    public function setUserAgents(array $agents)
    {
        $this->simpleCache()->setValue('user-agents', $agents);
    }

    public function getUserAgents()
    {
        $agents = $this->simpleCache()->getValue('user-agents');
        return $agents ?? [];
    }

    public function countUserAgents()
    {
        return count($this->getUserAgents());
    }

    public function addUserAgent($userAgent)
    {
        // only store the first 512 characters of the UserAgent string to prevent bad data causing issues
        $userAgent = substr($userAgent, 0, 512);
        $agents = $this->getUserAgents();

        if (!in_array($userAgent, $agents))
        {
            $agents[] = $userAgent;

            $this->setUserAgents($agents);
        }
    }

    public function clearUserAgents()
    {
        $this->setUserAgents([]);
    }

    public function setCodeCache($type, $data)
    {
        $this->codeCache()->setValue($type, $data);
    }

    public function getCodeCache($type)
    {
        return $this->codeCache()->getValue($type);
    }

    public function rebuildBotCache($bots)
    {
        $this->setCodeCache('maps', $bots['maps']);
        $this->setCodeCache('bots', $bots['bots']);
        $this->setCodeCache('generic', $bots['generic']);
        $this->setCodeCache('falsepos', $bots['falsepos']);
        $this->setCodeCache('ignored', $bots['ignored']);
    }
}
