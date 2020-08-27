<?php namespace Hampel\KnownBots\XF\Admin\Controller;

use Hampel\KnownBots\Repository\UserAgentCache;
use Hampel\KnownBots\Service\BotMailer;

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

		$newBots = $this->getUserAgentRepo()->getUserAgents();

		sort($newBots, SORT_NATURAL | SORT_FLAG_CASE);

		$viewParams = compact('newBots');
		return $this->view('Hampel\KnownBots:Tools\KnownBotsNew', 'hampel_knownbots_new', $viewParams);
	}

	public function actionHampelKnownBotsEmail()
	{
		$this->setSectionContext('hampelKnownBotsNew');

		$emailNewBots = $this->app()->options()->knownbotsEmailNewBots;
		if (!empty($emailNewBots['email']))
		{
			/** @var UserAgentCache $repo */
			$repo = $this->getUserAgentRepo();

			$bots = $repo->getUserAgents();

			if (!empty($bots))
			{
				$service = $this->getBotMailerService();

				$service->setToEmail($emailNewBots['email']);
				$service->setBots($bots);
				$service->mailBots();

				$repo->clearCache();

				return $this->message(\XF::phrase('hampel_knownbots_email_sent', ['email' => $emailNewBots['email']]));
			}

			return $this->message(\XF::phrase('hampel_knownbots_email_none_found'));
		}

		return $this->message(\XF::phrase('hampel_knownbots_email_not_configured'));
	}

	/**
	 * @return UserAgentCache
	 */
	protected function getUserAgentRepo()
	{
		return $this->app->repository('Hampel\KnownBots:UserAgentCache');
	}

	/**
	 * @return BotMailer
	 */
	protected function getBotMailerService()
	{
		return $this->app->service('Hampel\KnownBots:BotMailer');
	}
}

