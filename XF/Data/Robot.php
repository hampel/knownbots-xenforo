<?php namespace Hampel\KnownBots\XF\Data;

use Hampel\KnownBots\SubContainer\Cache;

class Robot extends XFCP_Robot
{
	public function getRobotUserAgents()
	{
	    $maps = $this->loadFromCodeCache('maps');

	    return $maps ?? parent::getRobotUserAgents();
	}

	public function getFalsePositives()
	{
		$falsePositives = $this->loadFromCodeCache('falsepos');

		return $falsePositives ?? [];
	}

    public function getGenericMaps()
    {
        $generic = $this->loadFromCodeCache('generic');

        return $generic ?? [];
    }

    public function userAgentMatchesGeneric($userAgent)
    {
        $bots = $this->getGenericMaps();

        if (!empty($bots) && preg_match(
            '#(' . implode('|', array_map('preg_quote', array_keys($bots))) . ')#i',
            strtolower($userAgent),
            $match
        ))
        {
            return $bots[$match[1]];
        }
        else
        {
            return '';
        }
    }

    protected function userAgentMatchesFalsePositives($userAgent)
    {
        $falsePositives = $this->getFalsePositives();

        if (empty($falsePositives)) return false;

        return preg_match(
            '#(' . implode('|', array_map('preg_quote', $falsePositives)) . ')#i',
            strtolower($userAgent)
        );
    }

    protected function getIgnored()
    {
        $ignored = $this->loadFromCodeCache('ignored');

        return $ignored ?? [];
    }

    public function userAgentMatchesRobot($userAgent)
	{
		$robotName = parent::userAgentMatchesRobot($userAgent);
		if (!empty($robotName))
		{
			// if we already found a robot, we're done
			return $robotName;
		}

		$robotName = $this->userAgentMatchesGeneric($userAgent);
		if (!empty($robotName))
		{
			// generic match ... better check for false positives
			if ($this->userAgentMatchesFalsePositives($userAgent))
			{
				// anything that matches our false positive list is considered not a bot
				return '';
			}
			else
			{
				// we found an actual generic bot/crawler/spider match

                if (!in_array($userAgent, $this->getIgnored()))
                {
                    // add it to the cache - but only if it's not in our ignored list
                    $this->getCache()->addUserAgent($userAgent);
                }

				// return generic bot string
				return $robotName;
			}
		}
		else
		{
			return '';
		}
	}

	public function getRobotList()
	{
        $bots = $this->loadFromCodeCache('bots');

        return $bots ?? parent::getRobotList();
	}

    public function loadFromCodeCache($type)
    {
        return $this->getCache()->getCodeCache($type);
    }

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return \XF::app()->container('knownbots.cache');
    }
}
