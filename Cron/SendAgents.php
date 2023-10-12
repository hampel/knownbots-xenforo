<?php namespace Hampel\KnownBots\Cron;

use Hampel\KnownBots\Option\EmailUserAgents;
use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\Service\UserAgentMailer;
use Hampel\KnownBots\Service\UserAgentSender;
use Hampel\KnownBots\SubContainer\Log;
use Hampel\KnownBots\Repository\Agent;

class SendAgents
{
    public static function send()
    {
        $log = self::getLog();

        if (!StoreUserAgents::isEnabled())
        {
            // we're not storing user agents, so nothing to send - silently abort
            $log->info("Send Agents: storing user agents disabled - aborting");
            return;
        }

        if (!SendUserAgents::isEnabled() && !EmailUserAgents::isEnabled())
        {
            // we're not sending either, so abort
            $log->info("Send Agents: not configured to send via API or email - aborting");
            return;
        }

        $repo = self::getAgentRepo();

        $agents = $repo->getUserAgentsForSending();
        if (empty($agents))
        {
            $log->info("Send Agents: no user agents to send - aborting");
            return;
        }

        if (SendUserAgents::isEnabled())
        {
            $response = self::sendApi($agents);

            if ($response === false)
            {
                $log->info("Send Agents: sending via API failed - aborting");
                return;
            }
        }

        if (EmailUserAgents::isEnabled())
        {
            self::sendEmail($agents);
        }

        $rows = $repo->markUserAgentsSent();

        $log->info('Marked user agents sent', compact('rows'));
    }

	public static function sendApi(array $agents)
	{
        $sender = self::getUserAgentSenderService();
        $sender->setApiToken(SendUserAgents::apiToken());
        $sender->setValidationToken(SendUserAgents::validationToken());
        $sender->setUserAgents($agents);

        $count = count($agents);
        self::getLog()->info("Sending user agents via API", compact('count'));

        return $sender->sendAgents();
	}

    public static function sendEmail(array $agents)
    {
        $mailer = self::getBotMailerService();
        $mailer->setToEmail(EmailUserAgents::getAddresses());
        $mailer->setAgents($agents);

        $count = count($agents);
        self::getLog()->info("Sending user agents via email", compact('count'));

        return $mailer->mailAgents();
    }

    public static function purgeAgents()
    {
        $days = StoreUserAgents::daysUntilPurge();

        if ($days == 0) return; // stop if we're not automatically purging old agents

        $rows = self::getAgentRepo()->purgeUserAgents($days);

        self::getLog()->info("Purged old user agents",  compact('rows', 'days'));
    }

    // -------------------------------------------------------------------

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
        return \XF::repository('Hampel\KnownBots:Agent');
    }
}
