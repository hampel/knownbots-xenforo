<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Option\EmailUserAgents;
use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\Repository\Agent;
use Hampel\KnownBots\Service\UserAgentMailer;
use Hampel\KnownBots\Service\UserAgentSender;
use Hampel\KnownBots\SubContainer\Log;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendAgents extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:send')
			->setDescription('Send user agents');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        if (!StoreUserAgents::isEnabled())
        {
            // we're not storing user agents, so nothing to send - silently abort
            $this->log('notice', "Send Agents: storing user agents disabled - aborting", $output);
            return 1;
        }

        if (!SendUserAgents::isEnabled() && !EmailUserAgents::isEnabled())
        {
            // we're not sending either, so abort
            $this->log('notice', "Send Agents: not configured to send via API or email - aborting", $output);
            return 1;
        }

        $repo = self::getAgentRepo();

        $agents = $repo->getUserAgentsForSending();
        if (empty($agents))
        {
            $this->log('info', "Send Agents: no user agents to send", $output);
            return 0;
        }

        if (SendUserAgents::isEnabled())
        {
            $response = $this->sendApi($agents);

            if ($response === false)
            {
                $this->log('notice', "Send Agents: sending via API failed", $output);
                return 1;
            }
        }

        if (EmailUserAgents::isEnabled())
        {
            $this->sendEmail($agents);
        }

        $rows = $repo->markUserAgentsSent();

        $this->log('info', "Marked {$rows} user agents as sent", $output);
		return 0;
	}

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


    protected function log($level, $message, OutputInterface $output)
    {
        $this->getLog()->log($level, $message);
        $output->writeln("<{$level}>{$message}</{$level}>");
    }

    /**
     * @return UserAgentSender
     */
    protected function getUserAgentSenderService()
    {
        return \XF::service('Hampel\KnownBots:UserAgentSender');
    }

    /**
     * @return UserAgentMailer
     */
    protected function getUserAgentMailerService()
    {
        return \XF::service('Hampel\KnownBots:UserAgentMailer');
    }

    /**
     * @return Log
     */
    protected function getLog()
    {
        return \XF::app()->container('knownbots.log');
    }

    /**
     * @return Agent
     */
    protected function getAgentRepo()
    {
        return \XF::repository('Hampel\KnownBots:Agent');
    }
}