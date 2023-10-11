<?php namespace Hampel\KnownBots\XF\Data;

use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\Repository\Agent;
use Hampel\KnownBots\SubContainer\Log;

class Robot extends XFCP_Robot
{
	public function getRobotUserAgents()
	{
	    $maps = $this->loadBotData('maps');

	    return $maps ?? parent::getRobotUserAgents();
	}

    public function userAgentMatchesRobot($userAgent, $save = true)
	{
        // 1. bot search key match
		if ($robotName = $this->userAgentMatchesSimpleBot($userAgent))
		{
			// we found a robot
            $this->saveUserAgent($save, $userAgent, $robotName);

			return $robotName;
		}

        // 2. bot regex "complex" match
        if ($robotName = $this->userAgentMatchesComplexBot($userAgent))
        {
            // we found a complex bot match
            $this->saveUserAgent($save, $userAgent, $robotName);

            return $robotName;
        }

        // 3. stop now if we aren't going to be storing user agents
        if (!$save || !StoreUserAgents::isEnabled())
        {
            // if we're not going to store the user agents for further analysis, there's no point continuing
            return '';
        }

        // 4. stop if the user agent matches any of our ignored regex searches
        if ($this->userAgentMatchesIgnored($userAgent))
        {
            // we found an ignored user agent, we don't know what these UA's belong to, so we won't explicitly
            // categorise them and won't save them either
            return '';
        }

        // 5. stop if we consider this to be a valid browser user agent
        if (empty($this->userAgentMatchesValidBrowser($userAgent)))
        {
            // we found a valid user browser
            return '';
        }

        // 6. if we got this far, we don't know what this user agent is, so add it to the database for later analysis
        $this->saveUserAgent($save, $userAgent);
        return '';
	}

	public function getRobotList()
	{
        $bots = $this->loadBotData('bots');

        return $bots ?? parent::getRobotList();
	}

    // -------------------------------------------------------------

    protected function saveUserAgent($save, $userAgent, $robot_key = null)
    {
        if ($save && StoreUserAgents::isEnabled())
        {
            $rowsAffected = $this->getAgentRepo()->addUserAgent($userAgent, $robot_key);

            $message = '';
            if ($rowsAffected == 0)
            {
                $message = 'Skipped existing';
            }
            elseif ($rowsAffected == 1)
            {
                $message = 'Added';
            }
            elseif ($rowsAffected == 2)
            {
                $message = 'Updated';
            }

            $this->getLog()->debug("{$message} user agent", compact('userAgent', 'robot_key'));
        }
    }

    protected function getComplexBots()
    {
        $complex = $this->loadBotData('complex');

        return $complex ?? [];
    }

    public function getIgnored()
    {
        $ignored = $this->loadBotData('ignored');

        return $ignored ?? [];
    }

    protected function getBrowsers()
    {
        $browsers = $this->loadBotData('browsers');

        return $browsers ?? [];
    }

    protected function userAgentMatchesSimpleBot($userAgent)
    {
        $userAgent = strtolower($userAgent);

        foreach ($this->getRobotUserAgents() as $search => $bot)
        {
            // assume all search strings are already lowercase
            if (strpos($userAgent, $search) !== false)
            {
                return $bot;
            }
        }

        return null;
    }

    protected function userAgentMatchesComplexBot($userAgent)
    {
        foreach ($this->getComplexBots() as $regex => $bot)
        {
            if (preg_match('#' . $regex . '#i', $userAgent, $match))
            {
                return $bot;
            }
        }

        return null;
    }

    public function userAgentMatchesIgnored($userAgent)
    {
        foreach ($this->getIgnored() as $regex)
        {
            if (preg_match('#' . $regex . '#i', $userAgent, $match))
            {
                return true;
            }
        }

        return false;
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

        empty(trim(trim(trim(preg_replace($searches, '', $userAgent)), '()[]{};-"\'')));
    }

    protected function loadBotData($type)
    {
        return $this->getCache()->loadBotData($type);
    }

    public function reprocessUserAgents($onlyNull = true)
    {
        $log = $this->getLog();
        $repo = $this->getAgentRepo();
        $agents = $repo->getUserAgentsForReprocessing($onlyNull);

        if ($agents->count() > 0)
        {
            $nullOrAll = $onlyNull ? "unknown" : "all";
            $log->info("Reprocessing {$nullOrAll} user agents");

            foreach ($agents as $agent)
            {
                $user_agent = $agent->user_agent;

                $robot_key = $this->userAgentMatchesRobot($user_agent, false);
                if (!empty($robot_key))
                {
                    // add the robot info to the database if it has changed, but don't update the last_updated time
                    $rowsAffected = $repo->addUserAgent($user_agent, $robot_key, false);

                    if ($rowsAffected == 2)
                    {
                        $log->info("Updated bot user agent", compact('user_agent', 'robot_key'));
                    }
                    elseif ($rowsAffected == 0)
                    {
                        $log->debug("Skipped existing bot user agent", compact('user_agent', 'robot_key'));
                    }
                }
                elseif ($this->userAgentMatchesIgnored($user_agent))
                {
                    // delete ignored user agents
                    $repo->deleteUserAgent($user_agent);
                    $log->info("Deleted ignored user agent", compact('user_agent'));
                }
                elseif (empty($this->userAgentMatchesValidBrowser($user_agent)))
                {
                    // we have a valid browser, delete the user agent
                    $repo->deleteUserAgent($user_agent);
                    $log->info("Deleted valid browser user agent", compact('user_agent'));
                }
                else
                {
                    $log->debug("Skipped unknown user agent", compact('user_agent'));
                }
            }
        }
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

    /**
     * @return Log
     */
    protected function getLog()
    {
        return \XF::app()->container('knownbots.log');
    }
}
