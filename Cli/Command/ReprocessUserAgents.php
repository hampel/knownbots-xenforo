<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\SubContainer\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReprocessUserAgents extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:reprocess')
			->setDescription('Reprocess user agents')
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                "Reprocess all known and unknown user agents"
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $onlyNull = !$input->getOption('all');

		$this->getApi()->reprocessUserAgents($onlyNull);

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