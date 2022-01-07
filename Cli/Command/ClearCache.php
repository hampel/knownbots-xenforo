<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\SubContainer\Cache;
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
		$this->getCache()->clearUserAgents();

		return 0;
	}

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return \XF::app()->container('knownbots.cache');
    }
}