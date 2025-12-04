<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\SubContainer\Api;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\AbstractCommand;

class ReprocessUserAgents extends AbstractCommand
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

		return self::SUCCESS;
	}

    /**
     * @return Api
     */
    protected function getApi()
    {
        return \XF::app()->container('knownbots.api');
    }
}