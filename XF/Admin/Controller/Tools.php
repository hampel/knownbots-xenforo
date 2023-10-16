<?php namespace Hampel\KnownBots\XF\Admin\Controller;

use Hampel\KnownBots\Exception\KnownBotsException;
use Hampel\KnownBots\Option\EmailUserAgents;
use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\Service\UserAgentMailer;
use Hampel\KnownBots\Service\UserAgentSender;
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

        try
        {
            $botData = $this->getApi()->fetchBots();
        }
        catch (KnownBotsException $e)
        {
            return $this->message(\XF::phrase('hampel_knownbots_error_updating', ['message' => $e->getMessage()]));
        }

        if (is_null($botData))
        {
            return $this->message(\XF::phrase('hampel_knownbots_no_updates_available'));
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
		$botInfo = [];
        $status = '';

		if ($this->isPost())
		{
			$robots = $this->getRobot();

			$useragent = $this->filter('useragent', 'str');

			$robot = $robots->userAgentMatchesRobot($useragent, false, false);

			if (!empty($robot))
			{
                $status = 'bot';
				$botInfo = $robots->getRobotInfo($robot);
                if (!$botInfo)
                {
                    $botInfo['title'] = $robot;
                    $botInfo['link'] = '';
                }
			}
            elseif ($robots->userAgentMatchesIgnored($useragent))
            {
                $status = 'ignored';
            }
            elseif ($robots->userAgentMatchesValidBrowser($useragent))
            {
                $status = 'browser';
            }
            else
            {
                $status = 'unknown';
            }
		}

		$viewParams = compact('useragent', 'botInfo', 'status');
		return $this->view('Hampel\KnownBots:Tools\KnownBotsDetect', 'hampel_knownbots_detect', $viewParams);
	}

	public function actionHampelKnownBotsNew()
	{
		$this->setSectionContext('hampelKnownBotsNew');

        $data = $this->getRobot();
		$newBots = $this->getAgentRepo()->getUserAgentsForDisplay();

        $bots = [];
        $unidentified = [];

        foreach ($newBots as $bot)
        {
            if ($bot->robot_key)
            {
                $robot = $data->getRobotInfo($bot->robot_key);

                $info = [
                    'user_agent' => $bot->user_agent,
                    'title' => $robot['title'] ?? $bot->robot_key,
                    'link' => $robot['link'] ?? '',
                ];

                $bots[$info['title']] = $info;
            }
            else
            {
                $unidentified[] = $bot->user_agent;
            }
        }

        ksort($bots, SORT_NATURAL | SORT_FLAG_CASE);
        natcasesort($unidentified);

		$viewParams = compact('bots', 'unidentified');
		return $this->view('Hampel\KnownBots:Tools\KnownBotsNew', 'hampel_knownbots_new', $viewParams);
	}

	public function actionHampelKnownBotsSend()
	{
		$this->setSectionContext('hampelKnownBotsNew');

		$repo = $this->getAgentRepo();

        $agents = $repo->getUserAgentsForSending();
        if (empty($agents))
        {
            return $this->message(\XF::phrase('hampel_knownbots_email_none_found'));
        }

        if (SendUserAgents::isEnabled())
        {
            $response = $this->sendApi($agents);

            if ($response === false)
            {
                return $this->message(\XF::phrase('hampel_knownbots_sending_api_failed'));
            }
        }

        if (EmailUserAgents::isEnabled())
        {
            $this->sendEmail($agents);
        }

        $rows = $repo->markUserAgentsSent();

		return $this->message(\XF::phrase('hampel_knownbots_agents_sent', compact('rows')));
	}

    public function actionHampelKnownBotsPurge()
    {
        $days = StoreUserAgents::daysUntilPurge();

        if ($days == 0) return; // stop if we're not automatically purging old agents

        $rows = self::getAgentRepo()->purgeUserAgents($days);

        return $this->message(\XF::phrase('hampel_knownbots_deleted', compact('rows', 'days')));
    }

    public function actionHampelKnownBotsClear()
    {
        $rows = self::getAgentRepo()->clearAllUserAgents();

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


	// -----------------------------------------------------------------------

    public function sendApi(array $agents)
    {
        $sender = $this->getUserAgentSenderService();
        $sender->setApiToken(SendUserAgents::apiToken());
        $sender->setValidationToken(SendUserAgents::validationToken());
        $sender->setUserAgents($agents);

        $count = count($agents);
        $this->getLog()->info("Sending user agents via API", compact('count'));

        return $sender->sendUserAgents();
    }

    public function sendEmail(array $agents)
    {
        $address = EmailUserAgents::getAddress();

        $mailer = $this->getUserAgentMailerService();
        $mailer->setToEmail($address);
        $mailer->setUserAgents($agents);

        $count = count($agents);
        $this->getLog()->info("Sending user agents via email", compact('count', 'address'));

        return $mailer->mailUserAgents();
    }

    protected function formatTime($timestamp)
    {
        $dt = new \DateTime();
        $dt->setTimezone(\XF::language()->getTimeZone());
        $dt->setTimestamp($timestamp);
        return $dt->format(\DateTimeInterface::COOKIE);
    }

    /**
     * @return UserAgentSender
     */
    protected static function getUserAgentSenderService()
    {
        return \XF::service('Hampel\KnownBots:UserAgentSender');
    }

    /**
     * @return UserAgentMailer
     */
    protected static function getUserAgentMailerService()
    {
        return \XF::service('Hampel\KnownBots:UserAgentMailer');
    }

	/**
	 * @return Log
	 */
	protected function getLog()
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
    protected function getAgentRepo()
    {
        return $this->app->repository('Hampel\KnownBots:Agent');
    }
}

