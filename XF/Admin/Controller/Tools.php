<?php namespace Hampel\KnownBots\XF\Admin\Controller;

class Tools extends XFCP_Tools
{
	public function actionKnownBots()
	{
		$knownBots = $this->app()->data('XF:Robot')->getRobotList();

		ksort($knownBots);

		$viewParams = compact('knownBots');
		return $this->view('XF:Tools\KnownBots', 'knownbots_list', $viewParams);
	}
}

