<?php namespace Hampel\KnownBots\Cron;

use Hampel\KnownBots\Option\EmailNewBots;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\Service\BotMailer;
use Hampel\KnownBots\SubContainer\Log;
use Hampel\KnownBots\Repository\Agent;

class NewBots
{ 
	public static function sendWeeklyEmail()
	{
		$log = self::getLog();

		if (!EmailNewBots::isEnabled())
		{
			$log->debug("Email new bots: disabled - aborting");
			return;
		}

		$emailTo = EmailNewBots::getAddresses();
		if (empty($emailTo))
		{
			$log->debug("Email new bots: no email address configured - aborting");
			return;
		}

		$repo = self::getAgentRepo();

		$bots = $repo->getUserAgentsForEmail();
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
            $rows = $repo->markUserAgentsSent();
            $log->info('Marked user agents sent', compact('rows'));
		}
	}

    public static function purgeAgents()
    {
        $days = StoreUserAgents::daysUntilPurge();

        if ($days == 0) return; // stop if we're not automatically purging old agents

        $rows = self::getAgentRepo()->purgeUserAgents($days);

        self::getLog()->info("Purged old user agents",  compact('rows', 'days'));
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
	protected static function getLog()
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
