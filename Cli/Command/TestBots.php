<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\XF\Data\Robot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    /** @var Robot $robots */
	    $robots = \XF::app()->data('XF:Robot');

	    $robot = $robots->userAgentMatchesRobot($input->getArgument('agent'));

	    if (empty($robot))
        {
            $output->writeln("No bot detected");
            return 0;
        }

        $output->writeln("Found robot: [{$robot}]");
        $info = $robots->getRobotInfo($robot);

        if ($info)
        {
            $output->writeln("Title: {$info['title']}");
        }

		return 0;
	}
}