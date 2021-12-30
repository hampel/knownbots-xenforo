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

		$count = $fetcher->updateBots($fetcher->loadBots(), true);
		$output->writeln("Loaded maps: {$count['maps']}");
		$output->writeln("Loaded bots: {$count['bots']}");
		$output->writeln("Loaded false positives: {$count['falsepos']}");

		return 0;
	}
}