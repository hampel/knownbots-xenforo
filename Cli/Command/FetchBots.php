<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Service\BotFetcher;
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
			->setDescription('Fetch the latest bots lists')
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                "Fetch all bot data"
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    $all = $input->getOption('all');

		/** @var BotFetcher $fetcher */
		$fetcher = \XF::service('Hampel\KnownBots:BotFetcher');

		$fetcher->fetchBots($all);

		return 0;
	}
}