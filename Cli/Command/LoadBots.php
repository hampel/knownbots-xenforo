<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Service\BotFetcher;
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
		/** @var BotFetcher $fetcher */
		$fetcher = \XF::service('Hampel\KnownBots:BotFetcher');

		$bots = $fetcher->loadBots();

		$fetcher->updateBots($bots);

		$output->writeln("Loaded maps: " . count($bots['maps']));
		$output->writeln("Loaded bots: " . count($bots['bots']));
		$output->writeln("Loaded false positives: " . count($bots['falsepos']));
		$output->writeln("Last checked: {$bots['built']}");

		return 0;
	}
}