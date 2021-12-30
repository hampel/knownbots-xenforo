<?php namespace Hampel\KnownBots\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildBots extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:build')
			->setDescription('Build the latest bots lists for distribution');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $destination = '_build/_files/internal_data/';

        mkdir($destination, 0777, true);

        $url = "https://knownbots.hampel.io/api/bots";

        if(!$response = \XF::app()->http()->reader()->getUntrusted($url, [], null, [], $error))
        {
            \XF::logError($error);
            return false;
        }

	    $bots = json_decode($response->getBody()->getContents(), true);

        file_put_contents("{$destination}knownbots.json", json_encode($bots, JSON_PRETTY_PRINT));

		return 0;
	}
}