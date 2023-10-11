<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Exception\KnownBotsException;
use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\SubContainer\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckApiToken extends Command
{
	protected function configure()
	{
		$this
			->setName('known-bots:check-token')
			->setDescription('Check API token');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        if (!SendUserAgents::isEnabled())
        {
            $output->writeln("API is not configured");
            return 1;
        }

        try
        {
            $response = $this->getApi()->checkApiToken(SendUserAgents::apiToken());
        }
        catch (KnownBotsException $e)
        {
            \XF::logException($e);
            $output->writeln($e->getMessage());
            return 1;
        }

        $domain = $response['domain'] ?? '#error#';

        $output->writeln("Token is valid for domain {$domain}");
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