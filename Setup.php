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
        if ($fs->has(self::BOTPATH))
        {
            $fs->delete(self::BOTPATH);
        }

        // remove code cache files
        foreach (['maps', 'bots', 'generic', 'falsepos', 'ignored'] as $type)
        {
            $path = "code-cache://known_bots/{$type}.php";
            if ($fs->has($path))
            {
                $fs->delete($path);
            }
        }

        $fs->deleteDir("code-cache://known_bots");
    }

    // ################################ Helpers ##################

    protected function loadBots()
    {
        if ($this->app->fs()->has(self::BOTPATH))
        {
            $fetcher = $this->getApi();
            $fetcher->updateBots($fetcher->loadBots());
        }
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