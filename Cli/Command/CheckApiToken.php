<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\Service\ApiTokenChecker;
use Hampel\KnownBots\SubContainer\Api;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\AbstractCommand;

class CheckApiToken extends AbstractCommand
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
            return self::FAILURE;
        }

        $checker = $this->getApiTokenCheckerService();
        $checker->setApiToken(SendUserAgents::apiToken());
        $checker->setValidationToken(SendUserAgents::validationToken());

        $response = $checker->checkToken($input->getOption('revalidate'));

        if ($response === false)
        {
            $output->writeln($checker->getError());
            return self::FAILURE;
        }

        $output->writeln("Token is valid for domain {$response}");
		return self::SUCCESS;
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