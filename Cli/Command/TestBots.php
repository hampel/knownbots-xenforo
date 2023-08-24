<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Api\BotFetcher;
use Hampel\KnownBots\Entity\Agent;
use Hampel\KnownBots\XF\Data\Robot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Db\Schema\Create;

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

        $userAgent = $input->getArgument('agent');

	    $robot = $robots->userAgentMatchesRobot($userAgent);

	    if (empty($robot))
        {
            $stripped = $robots->userAgentMatchesValidBrowser($userAgent);
            if (empty($stripped))
            {
                $output->writeln("<info>User agent is a valid browser</info>");
                return 0;
            }

            $output->writeln("<info>Unknown user agent - remainder after stripping valid browser components:</info>");
            $output->writeln("[{$stripped}]");
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