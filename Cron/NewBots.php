<?php namespace Hampel\KnownBots\Cron;

use Hampel\KnownBots\Option\EmailNewBots;
use Hampel\KnownBots\Service\BotMailer;
use Hampel\KnownBots\SubContainer\Log;
use Hampel\KnownBots\Repository\Agent;

class NewBots
{ 
	public static function sendWeeklyEmail()
	{
		$log = self::getLogger();

		if (!EmailNewBots::isEnabled())
		{
			$log->debug("Email new bots: disabled - aborting");
			return;
		}

		$emailTo = EmailNewBots::getAddress();
		if (empty($emailTo))
		{
			$log->debug("Email new bots: no email address configured - aborting");
			return;
		}

		$repo = self::getAgentRepo();

		$bots = $repo->getUserAgents();
		if (empty($bots))
		{
			$log->debug("Email new bots: no bots found - aborting");
			return;
		}

		$log->info("Email new bots: sending detected bots", compact('emailTo', 'bots'));

		$service = self::getBotMailerService();

		$service->setToEmail($emailTo);
		$service->setBots($bots);
		if ($service->mailBots())
		{
			$log->info("Clearing user agent cache");

            $repo->clearUserAgents();
		}
	}

	/**
	 * @return BotMailer
	 */
	protected static function getBotMailerService()
	{
		return \XF::service('Hampel\KnownBots:BotMailer');
	}

	/**
	 * @return Log
	 */
	protected static function getLogger()
	{
		return \XF::app()->container('knownbots.log');
	}

    /**
     * @return Agent
     */
    protected static function getAgentRepo()
    {
        return \XF::app()->repository('Hampel\KnownBots:Agent');
    }
}
