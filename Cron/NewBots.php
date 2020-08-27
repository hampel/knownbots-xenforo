<?php namespace Hampel\KnownBots\Cron;

use Hampel\KnownBots\Repository\UserAgentCache;
use Hampel\KnownBots\Service\BotMailer;

class NewBots
{ 
	public static function sendWeeklyEmail()
	{
		$app = \XF::app();

		$emailNewBots = $app->options()->knownbotsEmailNewBots;
		if (!empty($emailNewBots['email']))
		{
			/** @var UserAgentCache $repo */
			$repo = $app->repository('Hampel\KnownBots:UserAgentCache');

			$bots = $repo->getUserAgents();

			if (!empty($bots))
			{
				/** @var BotMailer $service */
				$service = $app->service('Hampel\KnownBots:BotMailer');

				$service->setToEmail($emailNewBots['email']);
				$service->setBots($bots);
				if ($service->mailBots())
				{
					$repo->clearCache();
				}
			}
		}
	}
}
