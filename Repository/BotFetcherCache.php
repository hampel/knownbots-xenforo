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
        return $this->getCache()->getValue('last-checked');
    }

    public function setLastChecked($timestamp)
    {
        $this->getCache()->setValue('last-checked', $timestamp);
    }

    public function resetLastChecked()
    {
        $this->setLastChecked(0);
    }

    public function getFalsePositives()
    {
        return $this->getCache()->getValue('false-positives');
    }

    public function setFalsePositives(array $search_keys)
    {
        $this->getCache()->setValue('false-positives', $search_keys);
    }

    public function resetFalsePositives()
    {
        $this->setFalsePositives([]);
    }
}
