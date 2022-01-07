<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\SubContainer\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchBots extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:fetch')
			->setDescription('Fetch and update bots lists')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                "Force update"
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    $force = $input->getOption('force');

		$bots = $this->getApi()->fetchBots($force);

		if (is_null($bots))
        {
            $output->writeln("No updates available");
            return 0;
        }

		if ($bots === false)
        {
            $output->writeln("<error>Error processing updates - check XenForo logs</error>");
            return 1;
        }

        $output->writeln("Loaded maps: " . count($bots['maps']));
        $output->writeln("Loaded bots: " . count($bots['bots']));
        $output->writeln("Loaded generic maps: " . count($bots['generic']));
        $output->writeln("Loaded false positives: " . count($bots['falsepos']));
        $output->writeln("Loaded ignored: " . count($bots['ignored']));
        $output->writeln("Last checked: {$bots['built']}");

		return 0;
	}

    /**
     * @return Api
     */
    protected function getApi()
    {
        return \XF::app()->container('knownbots.api');
    }
}