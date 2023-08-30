<?php namespace Hampel\KnownBots\XF\Admin\Controller;

use Hampel\KnownBots\Option\EmailNewBots;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\Service\BotMailer;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use Hampel\KnownBots\Repository\Agent;
use Hampel\KnownBots\XF\Data\Robot;

class Tools extends XFCP_Tools
{
	public function actionHampelKnownBotsList()
	{
		$this->setSectionContext('hampelKnownBotsList');

		$knownBots = $this->getRobot()->getRobotList();

		ksort($knownBots);

        $updated = $this->getCache()->getLastChecked();

		$viewParams = compact('knownBots', 'updated');

		return $this->view('Hampel\KnownBots:Tools\KnownBotsList', 'hampel_knownbots_list', $viewParams);
	}

    public function actionHampelKnownBotsFetch()
    {
        $this->setSectionContext('hampelKnownBotsList');

        $botData = $this->getApi()->fetchBots();

        if (is_null($botData))
        {
            return $this->message(\XF::phrase('hampel_knownbots_no_updates_available'));
        }

        if ($botData === false)
        {
            return $this->message(\XF::phrase('hampel_knownbots_error_updating'));
        }

        $lang = \XF::language();
        $maps = $lang->numberFormat(count($botData['maps']));
        $bots = $lang->numberFormat(count($botData['bots']));
        $browsers = $lang->numberFormat(count($botData['browsers']));

        return $this->message(\XF::phrase('hampel_knownbots_successfully_updated', compact('maps', 'bots', 'browsers')));
    }

	public function actionHampelKnownBotsDetect()
	{
		$this->setSectionContext('hampelKnownBotsDetect');

		$useragent = '';
		$info = [];
		$processed = false;

		if ($this->isPost())
		{
			$robots = $this->getRobot();

			$useragent = $this->filter('useragent', 'str');

			$robot = $robots->userAgentMatchesRobot($useragent, false);

			if (!empty($robot))
			{
				$info = $robots->getRobotInfo($robot);
			}

			$processed = true;
		}

		$viewParams = compact('useragent', 'info', 'processed');
		return $this->view('Hampel\KnownBots:Tools\KnownBotsDetect', 'hampel_knownbots_detect', $viewParams);
	}

	public function actionHampelKnownBotsNew()
	{
		$this->setSectionContext('hampelKnownBotsNew');

        $data = $this->getRobot();
		$newBots = $this->getRepo()->getUserAgentsForDisplay();

        $recentBots = [];

        foreach ($newBots as $bot)
        {
            $info = [
                'user_agent' => $bot->user_agent,
                'title' => $bot->robot_key,
                'link' => '',
            ];

            if ($bot->robot_key)
            {
                $robot = $data->getRobotInfo($bot->robot_key);

                if ($robot)
                {
                    $info['title'] = $robot['title'];
                    $info['link'] = $robot['link'];
                }
            }

            $recentBots[] = $info;
        }

		$viewParams = compact('recentBots');
		return $this->view('Hampel\KnownBots:Tools\KnownBotsNew', 'hampel_knownbots_new', $viewParams);
	}

	public function actionHampelKnownBotsEmail()
	{
		$log = $this->getLogger();
		$this->setSectionContext('hampelKnownBotsNew');

		$emailTo = EmailNewBots::getAddresses();

		if (!EmailNewBots::isEnabled() || empty($emailTo))
		{
			return $this->message(\XF::phrase('hampel_knownbots_email_not_configured'));
		}

		$repo = $this->getRepo();
		$bots = $repo->getUserAgentsForEmail();

		if (empty($bots))
		{
			return $this->message(\XF::phrase('hampel_knownbots_email_none_found'));
		}

		$log->info("Email new bots: manually sending detected bots", compact('emailTo', 'bots'));

		$service = $this->getBotMailerService();

		$service->setToEmail($emailTo);
		$service->setBots($bots);
		if ($service->mailBots())
		{
            $rows = $repo->markUserAgentsSent();
            $log->info('Marked user agents sent', compact('rows'));
		}

		return $this->message(\XF::phrase('hampel_knownbots_email_sent', ['email' => $emailTo]));
	}

    public function actionHampelKnownBotsPurge()
    {
        $days = StoreUserAgents::daysUntilPurge();

        if ($days == 0) return; // stop if we're not automatically purging old agents

        $rows = self::getRepo()->purgeUserAgents($days);

        return $this->message(\XF::phrase('hampel_knownbots_deleted', compact('rows')));
    }

    public function actionHampelKnownBotsClear()
    {
        $rows = self::getRepo()->clearAllUserAgents();

        return $this->message(\XF::phrase('hampel_knownbots_cleared', compact('rows')));
    }

	public function actionHampelKnownBotsMd()
	{
		$robot = $this->getRobot();

		$agents = $robot->getRobotUserAgents();
		ksort($agents);

		$bots = [];

		foreach ($agents as $agent)
		{
			$bots[$agent] = $robot->getRobotInfo($agent);
		}

		$updated = $this->getCache()->getLastChecked();

		$viewParams = compact('bots', 'updated');

		return $this->view('Hampel\KnownBots:Tools\KnownBotsGenerateMd', 'hampel_knownbots_md', $viewParams);
	}


	// -------------------------------------------------

    protected function formatTime($timestamp)
    {
        $dt = new \DateTime();
        $dt->setTimezone(\XF::language()->getTimeZone());
        $dt->setTimestamp($timestamp);
        return $dt->format(\DateTimeInterface::COOKIE);
    }

	/**
	 * @return BotMailer
	 */
	protected function getBotMailerService()
	{
		return $this->app->service('Hampel\KnownBots:BotMailer');
	}

	/**
	 * @return Log
	 */
	protected function getLogger()
	{
		return $this->app['knownbots.log'];
	}

	/**
	 * @return Robot
	 */
	protected function getRobot()
	{
		return $this->app()->data('XF:Robot');
	}

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return $this->app['knownbots.cache'];
    }

    /**
     * @return Api
     */
    protected function getApi()
    {
        return $this->app['knownbots.api'];
    }

    /**
     * @return Agent
     */
    protected function getRepo()
    {
        return $this->app->repository('Hampel\KnownBots:Agent');
    }
}

