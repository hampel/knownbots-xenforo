<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\SubContainer\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadBots extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:load')
			->setDescription('Load the latest bots lists from the filesystem');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    $fetcher = $this->getApi();

		$bots = $fetcher->loadBots();

		$fetcher->updateBots($bots);

		$output->writeln("Loaded maps: " . count($bots['maps']));
		$output->writeln("Loaded bots: " . count($bots['bots']));
        $output->writeln("Loaded generic maps: " . count($bots['generic']));
		$output->writeln("Loaded false positives: " . count($bots['falsepos']));
        $output->writeln("Loaded ignored bots: " . count($bots['ignored']));
		$output->writeln("Last checked: {$bots['built']}");

		return 0;
	}

    /**
     * @return Api
     */
    protected function getApi()
    {
        return $this->app['knownbots.api'];
    }
}