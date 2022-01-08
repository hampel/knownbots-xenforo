<?php

namespace Hampel\KnownBots;

use Hampel\KnownBots\SubContainer\Api;
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
        $this->loadBots();

        $this->randomizeCron();
    }

    // ################################ UPGRADE TO 4.0.0b1 ##################

    public function postUpgrade($previousVersion, array &$stateChanges)
    {
        $this->loadBots();

        if ($previousVersion < 4000031)
        {
            $this->randomizeCron();
        }
    }

    // ################################ UNINSTALL ##################

	public function uninstall(array $stepParams = [])
	{
        $fs = $this->app->fs();

        // remove json EDIT: actually, don't - because if you do, then uninstalling and reinstalling without
        // re-uploading the archive zip will lead to an error
        // $fs->delete("internal-data://knownbots.json");

        // remove code cache files
        foreach (['maps', 'bots', 'generic', 'falsepos', 'ignored'] as $type)
        {
            $fs->delete("code-cache://known_bots/{$type}.php");
        }

        $fs->deleteDir("code-cache://known_bots");
    }

    // ################################ Helpers ##################

    protected function loadBots()
    {
        $fetcher = $this->getApi();
        $fetcher->updateBots($fetcher->loadBots());
    }

    protected function randomizeCron()
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

    /**
     * @return Api
     */
    protected function getApi()
    {
        return $this->app['knownbots.api'];
    }
}