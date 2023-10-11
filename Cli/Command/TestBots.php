<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\XF\Data\Robot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestBots extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:test {user-agent}')
			->setDescription('Test the robot detection class')
            ->addArgument(
                'agent',
                InputArgument::REQUIRED,
                "User agent to test"
            )
            ->addOption(
                'save',
                's',
                InputOption::VALUE_NONE,
                'Save user agents to database'
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    /** @var Robot $robots */
	    $robots = \XF::app()->data('XF:Robot');

        $userAgent = $input->getArgument('agent');
        $save = $input->getOption('save');

	    $robot = $robots->userAgentMatchesRobot($userAgent, $save);

	    if (empty($robot))
        {
            if ($robots->userAgentMatchesIgnored($userAgent))
            {
                $output->writeln("<info>User agent matches ignored list</info>");
                return 0;
            }

            if ($robots->userAgentMatchesValidBrowser($userAgent))
            {
                $output->writeln("<info>User agent is a valid browser</info>");
                return 0;
            }

            $output->writeln("<info>Unknown user agent - further analysis required</info>");
            return 0;
        }

        $output->writeln("<info>Found robot: [{$robot}]</info>");
        $info = $robots->getRobotInfo($robot);

        if ($info)
        {
            $output->writeln("Title: {$info['title']}");
        }

		return 0;
	}
}