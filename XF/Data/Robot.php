<?php namespace Hampel\KnownBots\XF\Data;

use Hampel\KnownBots\Option\EmailNewBots;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\Repository\Agent;

class Robot extends XFCP_Robot
{
	public function getRobotUserAgents()
	{
	    $maps = $this->loadBotData('maps');

	    return $maps ?? parent::getRobotUserAgents();
	}

    public function userAgentMatchesRobot($userAgent)
	{
		$robotName = parent::userAgentMatchesRobot($userAgent);
		if (!empty($robotName))
		{
			// if we already found a robot, we're done
			return $robotName;
		}

        if (EmailNewBots::isEnabled())
        {
            $stripped = $this->userAgentMatchesValidBrowser($userAgent);
            if (empty($stripped))
            {
                // we found a valid user browser, we're done
                return '';
            }
        }

        // check for generic bots that are otherwise meaningless
		$robotName = $this->userAgentMatchesGeneric($userAgent);
		if (!empty($robotName))
		{
            if (!in_array($userAgent, $this->getIgnored()) && EmailNewBots::isEnabled())
            {
                // add it to the database - but only if it's not in our ignored list
                $this->getAgentRepo()->addUserAgent($userAgent);
            }

            // return generic bot string
            return $robotName;
		}
		else
		{
            // we don't know what this is
			return '';
		}
	}

	public function getRobotList()
	{
        $bots = $this->loadBotData('bots');

        return $bots ?? parent::getRobotList();
	}

    // -------------------------------------------------------------

    protected function getGenericMaps()
    {
        $generic = $this->loadBotData('generic');

        return $generic ?? [];
    }

    protected function getIgnored()
    {
        $ignored = $this->loadBotData('ignored');

        return $ignored ?? [];
    }

    protected function getBrowsers()
    {
        $browsers = $this->loadBotData('browsers');

        return $browsers ?? [];
    }

    protected function userAgentMatchesGeneric($userAgent)
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

    public function userAgentMatchesValidBrowser(string $userAgent)
    {
        $browsers = $this->getBrowsers();
        if (empty($browsers))
        {
            // something went wrong with our browser list - just continue
            return $userAgent;
        }

        $searches = array_map(function ($item) {
            return "#{$item}#i";
        }, $browsers);

        return trim(trim(preg_replace($searches, '', $userAgent)), '()[]{}"\'');
    }

    protected function loadBotData($type)
    {
        return $this->getCache()->loadBotData($type);
    }

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return \XF::app()->container('knownbots.cache');
    }

    /**
     * @return Agent
     */
    protected function getAgentRepo()
    {
        return \XF::repository('Hampel\KnownBots:Agent');
    }
}
