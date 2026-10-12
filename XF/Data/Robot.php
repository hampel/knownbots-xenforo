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

    public function userAgentMatchesRobot($userAgent, $save = true, $stopIfLoggedIn = true)
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

        // 3. stop now if we have a valid user
        if ($stopIfLoggedIn && \XF::visitor()->user_id)
        {
            // if they're logged in, they are almost certainly not a bot
            // we'll still check for bots first, just in case - but we won't bother continuing looking for a valid
            // browser or ignored user agent, etc. This will speed up processing for members, while visitors will still
            // undergo the additional checks
            return '';
        }

        // 4. stop now if we aren't going to be storing user agents
        if (!$save || !StoreUserAgents::isEnabled())
        {
            // if we're not going to store the user agents for further analysis, there's no point continuing
            return '';
        }

        // 5. stop if the user agent matches any of our ignored regex searches
        if ($this->userAgentMatchesIgnored($userAgent))
        {
            // we found an ignored user agent, we don't know what these UA's belong to, so we won't explicitly
            // categorise them and won't save them either
            return '';
        }

        // 6. stop if we consider this to be a valid browser user agent
        if ($this->userAgentMatchesValidBrowser($userAgent))
        {
            // we found a valid user browser
            return '';
        }

        // 7. if we got this far, we don't know what this user agent is, so add it to the database for later analysis
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

            $actions = [0 => 'skipped', 1 => 'added', 2 => 'updated'];

            // this runs on every unrecognised guest page view, so it stays at debug - and it
            // carries a user agent, which is why the documentation says what this level logs.
            // The message is a constant because that is what a log store groups and counts on
            $this->getLog()->debug('Stored user agent', [
                'action' => $actions[$rowsAffected] ?? 'unknown',
                'user_agent' => $userAgent,
                'robot_key' => $robot_key,
            ]);
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
            return false;
        }

        $searches = array_map(function ($item) {
            return "#{$item}#i";
        }, $browsers);

        return empty(trim(trim(trim(preg_replace($searches, '', $userAgent)), '()[]{};-"\'')));
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
            $scope = $onlyNull ? 'unknown' : 'all';
            $counts = ['updated' => 0, 'skipped' => 0, 'deleted_ignored' => 0, 'deleted_browser' => 0, 'unrecognised' => 0];

            foreach ($agents as $agent)
            {
                $user_agent = $agent->user_agent;

                $robot_key = $this->userAgentMatchesRobot($user_agent, false, false);
                if (!empty($robot_key))
                {
                    // add the robot info to the database if it has changed, but don't update the last_updated time
                    $rowsAffected = $repo->addUserAgent($user_agent, $robot_key, false);

                    if ($rowsAffected == 2)
                    {
                        $counts['updated']++;
                        $log->debug('Reprocessed user agent', ['action' => 'updated'] + compact('user_agent', 'robot_key'));
                    }
                    elseif ($rowsAffected == 0)
                    {
                        $counts['skipped']++;
                        $log->debug('Reprocessed user agent', ['action' => 'skipped'] + compact('user_agent', 'robot_key'));
                    }
                }
                elseif ($this->userAgentMatchesIgnored($user_agent))
                {
                    // delete ignored user agents
                    $repo->deleteUserAgent($user_agent);
                    $counts['deleted_ignored']++;
                    $log->debug('Reprocessed user agent', ['action' => 'deleted_ignored'] + compact('user_agent'));
                }
                elseif ($this->userAgentMatchesValidBrowser($user_agent))
                {
                    // we have a valid browser, delete the user agent
                    $repo->deleteUserAgent($user_agent);
                    $counts['deleted_browser']++;
                    $log->debug('Reprocessed user agent', ['action' => 'deleted_browser'] + compact('user_agent'));
                }
                else
                {
                    $counts['unrecognised']++;
                    $log->debug('Reprocessed user agent', ['action' => 'unrecognised'] + compact('user_agent'));
                }
            }

            // the loop reports its summary; the lines inside it are the item-level trace
            $log->info('Reprocessed user agents', ['scope' => $scope, 'total' => $agents->count()] + $counts);
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
