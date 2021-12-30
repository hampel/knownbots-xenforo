<?php namespace Hampel\KnownBots\Repository;

use XF\Mvc\Entity\Repository;
use XF\SimpleCacheSet;

class BotFetcherCache extends Repository
{
	/** @return SimpleCacheSet */
	public function getCache()
	{
		return \XF::app()->simpleCache()->getSet('Hampel/KnownBots');
	}

    public function getLastChecked()
    {
        return $this->getCache()->getValue('botFetchLastCheck');
    }

    public function setLastChecked($timestamp)
    {
        $this->getCache()->setValue('botFetchLastCheck', $timestamp);
    }

    public function resetLastChecked()
    {
        $this->setLastChecked(0);
    }

    public function getFalsePositives()
    {
        return $this->getCache()->getValue('falsePositives');
    }

    public function setFalsePositives(array $search_keys)
    {
        $this->getCache()->setValue('falsePositives', $search_keys);
    }

    public function resetFalsePositives()
    {
        $this->setFalsePositives([]);
    }
}
