<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\XF\Data\Robot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParseLogs extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:parse')
			->setDescription("Parse web server log information and display bots detected")
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                "Path to log file to parse, or enter a hyphen - to read from stdin"
            )
            ->addOption(
                'agents',
                'a',
                InputOption::VALUE_NONE,
                "List user agents only - no bot detection"
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_REQUIRED,
                "Limit the number of agents found to prevent memory issues"
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

        $agentsOnly = $input->getOption('agents');
        $limit = intval($input->getOption('limit'));

        $pattern = '%^(?<client>\S+) (?<auth>\S+ \S+) \[(?<datetime>[^]]+)\] "(?:GET|POST|HEAD) (?<file>[^ ?"]+)\??(?<parameters>[^ ?"]+)? HTTP/[0-9.]+" (?<status>[0-9]+) (?<size>[-0-9]+) "(?<referrer>[^"]*)" "(?<useragent>[^"]*)"$%i';

        $agents = [];
        $count = 0;

        while ($line = trim(fgets($stream)))
        {
            if (empty(trim($line))) continue;

            if (preg_match($pattern, $line, $matches))
            {
                $userAgent = $matches['useragent'] ?? '';

                if ($userAgent)
                {
                    if ($agentsOnly)
                    {
                        // just output the user agent and continue

                        if (!in_array($userAgent, $agents))
                        {
                            $agents[] = $userAgent;
                            $output->writeln($userAgent);

                            $count++;
                            if ($limit && $count >= $limit)
                            {
                                // stop if we've reached our limit
                                break;
                            }
                        }

                        continue;
                    }

                    $robotKey = $robots->userAgentMatchesRobot($userAgent, false, false);

                    if ($robotKey)
                    {
                        if (!in_array($userAgent, $agents))
                        {
                            $agents[] = $userAgent;

                            $robot = str_pad($robotKey, 25);
                            $output->writeln("[{$robot}] {$userAgent}");

                            $count++;
                            if ($limit && $count >= $limit)
                            {
                                // stop if we've reached our limit
                                break;
                            }
                        }
                    }
                }
            }
        }

        if ($file != '-')
        {
            fclose($stream);
        }

		return 0;
	}
}