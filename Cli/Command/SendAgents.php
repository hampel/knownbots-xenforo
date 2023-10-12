<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\Repository\Agent;
use Hampel\KnownBots\Service\UserAgentSender;
use Hampel\KnownBots\SubContainer\Api;
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
        if (!SendUserAgents::isEnabled())
        {
            $output->writeln("API is not configured - please update KnownBots options");
            return 1;
        }

        $repo = self::getAgentRepo();

        $agents = $repo->getUserAgentsForSending();
        if (empty($agents))
        {
            $output->writeln("No user agents found");
            return 0;
        }

        $sender = $this->getUserAgentSenderService();
        $sender->setApiToken(SendUserAgents::apiToken());
        $sender->setValidationToken(SendUserAgents::validationToken());
        $sender->setUserAgents($agents);
        $response = $sender->sendAgents();

        if ($response === false)
        {
            $output->writeln($sender->getError());
            return 1;
        }

        $count = $repo->markUserAgentsSent();

        $output->writeln("Marked {$count} user agents as sent");
		return 0;
	}

    /**
     * @return Api
     */
    protected function getApi()
    {
        return \XF::app()->container('knownbots.api');
    }

    /**
     * @return UserAgentSender
     */
    protected function getUserAgentSenderService()
    {
        return \XF::service('Hampel\KnownBots:UserAgentSender');
    }

    /**
     * @return Agent
     */
    protected function getAgentRepo()
    {
        return \XF::repository('Hampel\KnownBots:Agent');
    }
}