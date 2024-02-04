<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\XF\Data\Robot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:import')
			->setDescription("Import user agents from a text file (one UA per line)")
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                "Path to file to read from, or enter a hyphen - to read from stdin"
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    /** @var Robot $robots */
	    $robots = \XF::app()->data('XF:Robot');

        $file = $input->getArgument('file');
        if ($file == '-')
        {
            $stream = STDIN;
        }
        else
        {
            if (!is_readable($file))
            {
                $output->writeln("<error>Error: No such file or file unreadable</error>");
                return 1;
            }
            $stream = fopen($file, "r");
        }

        $count = 0;

        while ($line = trim(fgets($stream)))
        {
            $userAgent = $line;

            if (empty($userAgent)) continue;
            $count++;

            $robotKey = $robots->userAgentMatchesRobot($userAgent, true, false);

            $robot = str_pad($robotKey, 25);
            $output->writeln("[{$robot}] {$userAgent}");
        }

        if ($file != '-')
        {
            fclose($stream);
        }

        $output->writeln("Processed {$count} user agents");

		return 0;
	}
}