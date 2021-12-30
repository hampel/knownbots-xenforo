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
        $this->newTables();
	}

    public function postInstall(array &$stateChanges)
    {
        /** @var BotFetcher $service */
        $service = \XF::service('Hampel\KnownBots:BotFetcher');
        $service->updateBots($service->loadBots(), true);
    }

    // ################################ UPGRADE TO 4.0.0b1 ##################

    public function upgrade4000031Step1()
    {
        $this->newTables();
    }

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

    // ################################ HELPERS ##################

	protected function newTables()
    {
        $this->schemaManager()->createTable('xf_knownbots_map', function (Create $table) {
            $table->addColumn('map_id', 'int')->primaryKey();
            $table->addColumn('search_key', 'varchar', 64);
            $table->addColumn('robot_key', 'varchar', 25);
            $table->addColumn('created_at', 'int')->nullable();
            $table->addColumn('updated_at', 'int')->nullable();
            $table->addColumn('deleted_at', 'int')->nullable();

            $table->addUniqueKey('search_key');
        });

        $this->schemaManager()->createTable('xf_knownbots_bot', function (Create $table) {
            $table->addColumn('bot_id', 'int')->primaryKey();
            $table->addColumn('robot_key', 'varchar', 25);
            $table->addColumn('title', 'text');
            $table->addColumn('link', 'text');
            $table->addColumn('created_at', 'int')->nullable();
            $table->addColumn('updated_at', 'int')->nullable();
            $table->addColumn('deleted_at', 'int')->nullable();

            $table->addUniqueKey('robot_key');
        });
    }
}