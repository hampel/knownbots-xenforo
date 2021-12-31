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