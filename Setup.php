<?php

namespace Hampel\KnownBots;

use Hampel\KnownBots\SubContainer\Api;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;
use XF\Util\File;

class Setup extends AbstractSetup
{
	use StepRunnerUpgradeTrait;

    const BOTPATH = 'internal-data://knownbots.json';

	public function install(array $stepParams = [])
	{
        $this->createAgentTable();
	}

    public function postInstall(array &$stateChanges)
    {
        $fs = $this->app->fs();
        if (!$fs->has(self::BOTPATH))
        {
            $sourceFile = sprintf("%s/knownbots.json", $this->addOn->getAddOnDirectory());
            File::copyFileToAbstractedPath($sourceFile, self::BOTPATH);

            $this->loadBots();
        }

        $this->randomizeFetchCron();
        $this->randomizeSendCron();
    }

    // ################################ UPGRADE ##################

    public function upgrade5000031Step1()
    {
        $this->createAgentTable();
    }

    public function postUpgrade($previousVersion, array &$stateChanges)
    {
        $this->loadBots();

        $this->randomizeFetchCron();
        $this->randomizeSendCron();

        // remove knownbots@hampel.io email addresses when upgrading to v6
        if ($previousVersion < 6000030)
        {
            $this->removeKnownBotsEmail();
        }
    }

    // ################################ UNINSTALL ##################

	public function uninstall(array $stepParams = [])
	{
        $fs = $this->app->fs();
        if ($fs->has(self::BOTPATH))
        {
            $fs->delete(self::BOTPATH);
        }

        // remove code cache files
        foreach (['maps', 'bots', 'complex', 'generic', 'ignored', 'browsers'] as $type)
        {
            $path = "code-cache://known_bots/{$type}.php";
            if ($fs->has($path))
            {
                $fs->delete($path);
            }
        }

        $fs->deleteDir("code-cache://known_bots");

        $this->schemaManager()->dropTable('xf_knownbots_agent');
    }

    // ################################ Helpers ##################

    protected function createAgentTable()
    {
        $this->schemaManager()->createTable('xf_knownbots_agent', function (Create $table) {
            $table->addColumn('user_agent', 'varbinary', 512)->primaryKey();
            $table->addColumn('robot_key', 'varchar', 25)->nullable();
            $table->addColumn('last_updated', 'int')->setDefault(0);
            $table->addColumn('sent', 'bool')->setDefault(0);

            $table->addKey('last_updated');
        });
    }

    protected function loadBots()
    {
        if ($this->app->fs()->has(self::BOTPATH))
        {
            $fetcher = $this->getApi();
            $fetcher->updateBots($fetcher->loadBots());
        }
    }

    protected function randomizeFetchCron()
    {
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

    protected function randomizeSendCron()
    {
        // randomize cron run time
        $cron = $this->app()->find('XF:CronEntry', 'hampelKnownBotsUserAgents');
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

    protected function removeKnownBotsEmail()
    {
        $emailOption = \XF::options()->knownbotsEmailUserAgents;

        if (isset($emailOption['email']) && !empty($emailOption['email']))
        {
            $addresses = array_map('trim', explode(',', $emailOption['email'] ?? ''));

            $initialCount = count($addresses);
            foreach ($addresses as $key => $address)
            {
                if ($address == 'knownbots@hampel.io')
                {
                    unset($addresses[$key]);
                }
            }

            if (count($addresses) < $initialCount)
            {
                if (count($addresses) == 0)
                {
                    $emailOption['enabled'] = 0;
                }
                $emailOption['email'] = implode(", ", $addresses);
                \XF::repository('XF:Option')->updateOption('knownbotsEmailUserAgents', $emailOption);
            }
        }
    }

    /**
     * @return Api
     */
    protected function getApi()
    {
        return $this->app['knownbots.api'];
    }
}