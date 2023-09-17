<?php namespace Hampel\KnownBots\Cron;

use Hampel\KnownBots\Option\EmailUserAgents;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\Service\BotMailer;
use Hampel\KnownBots\SubContainer\Log;
use Hampel\KnownBots\Repository\Agent;

class NewUserAgents
{ 
	public static function sendEmail()
	{
		$log = self::getLog();

		if (!EmailUserAgents::isEnabled())
		{
			$log->info("Email user agents: disabled - aborting");
			return;
		}

		$emailTo = EmailUserAgents::getAddresses();
		if (empty($emailTo))
		{
			$log->info("Email user agents: no email address configured - aborting");
			return;
		}

		$repo = self::getAgentRepo();

		$bots = $repo->getUserAgentsForEmail();
		if (empty($bots))
		{
			$log->info("Email user agents: no bots found - aborting");
			return;
		}

		$log->info("Email user agents: sending detected bots", compact('emailTo', 'bots'));

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
