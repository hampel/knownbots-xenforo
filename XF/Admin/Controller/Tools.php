<?php namespace Hampel\KnownBots\XF\Admin\Controller;

use Hampel\KnownBots\Option\EmailNewBots;
use Hampel\KnownBots\Service\BotMailer;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use XF\Data\Robot;

class Tools extends XFCP_Tools
{
	public function actionHampelKnownBotsList()
	{
		$this->setSectionContext('hampelKnownBotsList');

		$knownBots = $this->app()->data('XF:Robot')->getRobotList();

		ksort($knownBots);

		$viewParams = compact('knownBots');
		return $this->view('Hampel\KnownBots:Tools\KnownBotsList', 'hampel_knownbots_list', $viewParams);
	}

	public function actionHampelKnownBotsDetect()
	{
		$this->setSectionContext('hampelKnownBotsDetect');

		$useragent = '';
		$info = [];
		$processed = false;

		if ($this->isPost())
		{
			/** @var XF\Data\Robot $robots */
			$robots = $this->app()->data('XF:Robot');

			$useragent = $this->filter('useragent', 'str');

			$robot = $robots->userAgentMatchesRobot($useragent);

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

		$newBots = $this->getCache()->getUserAgents();

		sort($newBots, SORT_NATURAL | SORT_FLAG_CASE);

		$viewParams = compact('newBots');
		return $this->view('Hampel\KnownBots:Tools\KnownBotsNew', 'hampel_knownbots_new', $viewParams);
	}

	public function actionHampelKnownBotsEmail()
	{
		$log = $this->getLogger();
		$this->setSectionContext('hampelKnownBotsNew');

		$emailTo = EmailNewBots::getAddress();

		if (!EmailNewBots::isEnabled() || empty($emailTo))
		{
			return $this->message(\XF::phrase('hampel_knownbots_email_not_configured'));
		}

		$cache = $this->getCache();
		$bots = $cache->getUserAgents();

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
			$log->info("Clearing user agent cache");

			$cache->clearUserAgents();
		}

		return $this->message(\XF::phrase('hampel_knownbots_email_sent', ['email' => $emailTo]));

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

		$viewParams = compact('bots');

		return $this->view('Hampel\KnownBots:Tools\KnownBotsGenerateMd', 'hampel_knownbots_md', $viewParams);
	}


	// -------------------------------------------------

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
}

