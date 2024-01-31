<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Option\EmailUserAgents;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\Repository\Agent;
use Hampel\KnownBots\Service\UserAgentMailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EmailAgents extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:email')
            ->addArgument(
                'address',
                InputArgument::OPTIONAL,
                "Email address to send to"
            )
			->setDescription('Send user agents via email');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        if (!StoreUserAgents::isEnabled())
        {
            $output->writeln("Storing user agents is disabled - aborting");
            return 1;
        }

        $agents = self::getAgentRepo()->getUserAgentsForSending();
        if (empty($agents))
        {
            $output->writeln("No user agents to send - aborting");
            return 0;
        }

        $email = $input->getArgument('address') ?? EmailUserAgents::getAddress();

        $mailer = $this->getUserAgentMailerService();
        $mailer->setToEmail($email);
        $mailer->setUserAgents($agents);
        $mailer->mailUserAgents();

        $count = count($agents);

        $output->writeln("Sent {$count} agents via email to {$email}");
		return 0;
	}

    /**
     * @return UserAgentMailer
     */
    protected function getUserAgentMailerService()
    {
        return \XF::service('Hampel\KnownBots:UserAgentMailer');
    }

    /**
     * @return Agent
     */
    protected function getAgentRepo()
    {
        return \XF::repository('Hampel\KnownBots:Agent');
    }
}