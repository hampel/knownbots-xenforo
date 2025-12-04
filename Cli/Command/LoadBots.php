<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\SubContainer\Api;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\AbstractCommand;

class LoadBots extends AbstractCommand
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

        $checked = $this->formatTime($bots['built']);

		$output->writeln("Loaded maps: " . count($bots['maps']));
		$output->writeln("Loaded bots: " . count($bots['bots']));
        $output->writeln("Loaded complex: " . count($bots['complex']));
        $output->writeln("Loaded ignored: " . count($bots['ignored']));
        $output->writeln("Loaded browsers: " . count($bots['browsers']));
		$output->writeln("knownbots.json build date: {$checked}");

        $fetcher->reprocessUserAgents();

		return self::SUCCESS;
	}

    protected function formatTime($timestamp)
    {
        $dt = new \DateTime();
        $dt->setTimezone(\XF::language()->getTimeZone());
        $dt->setTimestamp($timestamp);
        return $dt->format(\DateTimeInterface::COOKIE);
    }

    /**
     * @return Api
     */
    protected function getApi()
    {
        return \XF::app()->container('knownbots.api');
    }
}