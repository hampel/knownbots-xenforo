<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\Service\ApiTokenChecker;
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
			->setDescription('Test if KnownBots API token is valid')
            ->addOption(
                'revalidate',
                'r',
                InputOption::VALUE_NONE,
                "Attempt revalidation if authorization fails"
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        if (!SendUserAgents::isEnabled())
        {
            $output->writeln("API is not configured - please update KnownBots options");
            return 1;
        }

        $checker = $this->getApiTokenCheckerService();
        $checker->setApiToken(SendUserAgents::apiToken());
        $checker->setValidationToken(SendUserAgents::validationToken());

        $response = $checker->checkToken($input->getOption('revalidate'));

        if ($response === false)
        {
            $output->writeln($checker->getError());
            return 1;
        }

        $output->writeln("Token is valid for domain {$response}");
		return 0;
	}

    /**
     * @return Api
     */
    protected function getApi()
    {
        return \XF::app()->container('knownbots.api');
    }

    /**
     * @return ApiTokenChecker
     */
    protected function getApiTokenCheckerService()
    {
        return \XF::service('Hampel\KnownBots:ApiTokenChecker');
    }
}