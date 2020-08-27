<?php namespace Hampel\KnownBots\Repository;

use XF\Mvc\Entity\Repository;
use XF\SimpleCacheSet;

class UserAgentCache extends Repository
{
	/** @return SimpleCacheSet */
	public function getCache()
	{
		return \XF::app()->simpleCache()->getSet('Hampel/KnownBots');
	}

	/** @return array */
	public function getUserAgents()
	{
		$agents = $this->getCache()->getValue('user-agents');
		return $agents ?? [];
	}

	public function countUserAgents()
	{
		return count($this->getUserAgents());
	}

	public function setUserAgents(array $agents)
	{
		$this->getCache()->setValue('user-agents', $agents);
	}

	public function addUserAgent($userAgent)
	{
		// only store the first 512 characters of the UserAgent string to prevent
		$userAgent = substr(strtolower($userAgent), 0, 512);
		$agents = $this->getUserAgents();

		if (!in_array($userAgent, $agents))
		{
			$agents[] = $userAgent;

			$this->setUserAgents(array_slice($agents, -5)); // store the last 5
		}
	}

	public function clearCache()
	{
		$this->setUserAgents([]);
	}
}
