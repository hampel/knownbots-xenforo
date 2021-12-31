<?php

namespace Hampel\KnownBots;

use Hampel\KnownBots\Service\BotFetcher;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
	use StepRunnerUpgradeTrait;

	public function install(array $stepParams = [])
	{
	}

    public function postInstall(array &$stateChanges)
    {
        /** @var BotFetcher $service */
        $service = \XF::service('Hampel\KnownBots:BotFetcher');
        $service->updateBots($service->loadBots());

        // randomize cron run time
        $cron = $this->app()->find('XF:CronEntry', 'hampelKnownBotsFetchBots');
        if ($cron)
        {
            $hours = rand(0, 23);
            $minutes = rand(0, 59);

            $rules = $cron->run_rules;
            $rules['hours'] = [$hours];
            $rules['minutes'] = [$minutes];
            $cron->run_rules = $rules;
            $cron->save();
        }
    }

    // ################################ UPGRADE TO 4.0.0b1 ##################

    public function postUpgrade($previousVersion, array &$stateChanges)
    {
        /** @var BotFetcher $service */
        $service = \XF::service('Hampel\KnownBots:BotFetcher');
        $service->updateBots($service->loadBots());
    }

    // ################################ UNINSTALL ##################

	public function uninstall(array $stepParams = [])
	{
		$this->schemaManager()->dropTable('xf_knownbots_map');
		$this->schemaManager()->dropTable('xf_knownbots_bot');
	}

}