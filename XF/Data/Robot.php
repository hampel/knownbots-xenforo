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
		if ($robotName = parent::userAgentMatchesRobot($userAgent))
		{
			// we found a robot
            $this->saveUserAgent($save, $userAgent, $robotName);

			return $robotName;
		}
        elseif ($robotName = $this->userAgentMatchesComplexBot($userAgent))
        {
            // we found a complex bot match
            $this->saveUserAgent($save, $userAgent, $robotName);

            return $robotName;
        }
        elseif (!StoreUserAgents::isEnabled())
        {
            // if we're not going to store the user agents for further analysis, there's no point continuing
            return '';
        }
        elseif (empty($this->userAgentMatchesValidBrowser($userAgent)))
        {
            // we found a valid user browser
            return '';
        }
        elseif (in_array(strtolower($userAgent), $this->getIgnored()))
        {
            // we found an ignored user agent, we don't know what these UA's belong to, so we won't explicitly
            // categorise them
            return '';
        }
        else
        {
            // we don't know what we have, so add it to the database for later analysis
            $this->saveUserAgent($save, $userAgent);

            return '';
        }
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

    protected function getGenericMaps()
    {
        $generic = $this->loadBotData('generic');

        return $generic ?? [];
    }

    public function getIgnored()
    {
        $ignored = $this->loadBotData('ignored');

        if (!$ignored)
        {
            return [];
        }

        return array_map('strtolower', $ignored);
    }

    protected function getBrowsers()
    {
        $browsers = $this->loadBotData('browsers');

        return $browsers ?? [];
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

        return trim(trim(preg_replace($searches, '', $userAgent)), '()[]{};-"\'');
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
        $ignored = $this->getIgnored();

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
                    $rowsAffected = $repo->addUserAgent($user_agent, $robot_key);

                    if ($rowsAffected == 2)
                    {
                        $log->info("Updated bot user agent", compact('user_agent', 'robot_key'));
                    }
                    elseif ($rowsAffected == 0)
                    {
                        $log->debug("Skipped existing bot user agent", compact('user_agent', 'robot_key'));
                    }
                }
                elseif (empty($this->userAgentMatchesValidBrowser($user_agent)))
                {
                    // we have a valid browser, delete the user agent
                    $repo->deleteUserAgent($user_agent);
                    $log->info("Deleted valid browser user agent", compact('user_agent'));
                }
                elseif (in_array(strtolower($user_agent), $ignored))
                {
                    // delete ignored user agents
                    $repo->deleteUserAgent($user_agent);
                    $log->info("Deleted ignored user agent", compact('user_agent'));
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
