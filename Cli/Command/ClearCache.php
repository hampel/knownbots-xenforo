<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Repository\UserAgentCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCache extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:clear-cache')
			->setDescription('Clears the cache of newly dectected bots');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var UserAgentCache $repo */
		$repo = \XF::repository('Hampel\KnownBots:UserAgentCache');

		$repo->clearCache();

		return 0;
	}
}