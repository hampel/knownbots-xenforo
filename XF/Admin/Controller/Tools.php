<?php namespace Hampel\KnownBots\XF\Admin\Controller;

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
}

