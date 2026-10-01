<?php namespace Tests\Feature;

use Hampel\KnownBots\Setup;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use XF\DevelopmentOutput;
use XF\Job\FileCleanUp;

/**
 * postUpgrade() queues XenForo's file clean-up, which only exists from XenForo 2.3:
 * AbstractSetup::enqueuePostUpgradeCleanUp() is absent on 2.2, so calling it unconditionally is a
 * fatal there. The call is gated on \XF::$versionId, and that gate is what these tests hold.
 *
 * Both branches are reachable on a 2.3 install because \XF::$versionId is a plain static, and the
 * framework restores it after each test.
 */
class SetupUpgradeCleanUpTest extends TestCase
{
    use UsesDatabaseTransactions;

    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesJobs();

        // postUpgrade() reloads the bot data and randomises two crons on the way through; neither
        // is what these tests are about
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            // updateBots() is array-typed, so the ignore-missing null will not do
            $mock->allows('loadBots')->andReturns([]);
            $mock->allows('updateBots');
        });
        $this->mock('development.output', DevelopmentOutput::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->allows('isEnabled')->andReturns(false);
        });
    }

    #[DataProvider('xenforoVersions')]
    public function test_the_file_clean_up_is_queued_only_from_xenforo_2_3(int $versionId, bool $expected)
    {
        $this->setStaticProperty(\XF::class, 'versionId', $versionId);

        $this->invokePostUpgrade();

        if ($expected)
        {
            $this->assertJobQueued(FileCleanUp::class);
        }
        else
        {
            $this->assertJobNotQueued(FileCleanUp::class);
        }
    }

    public static function xenforoVersions() : array
    {
        return [
            'XF 2.2.19' => [2021770, false],
            'XF 2.2.0' => [2020070, false],
            'XF 2.3.0' => [2030070, true],
            'XF 2.3.12' => [2031270, true],
        ];
    }

    public function test_the_crons_are_still_randomised_on_2_2()
    {
        $this->setStaticProperty(\XF::class, 'versionId', 2021770);
        $this->setRunTime('hampelKnownBotsFetchBots', 99, 99);

        $this->invokePostUpgrade();

        // only the clean-up is gated: the rest of postUpgrade() still runs on 2.2
        [$hours, $minutes] = $this->runTime('hampelKnownBotsFetchBots');
        $this->assertGreaterThanOrEqual(0, $hours);
        $this->assertLessThanOrEqual(23, $hours);
        $this->assertLessThanOrEqual(59, $minutes);
        $this->assertJobNotQueued(FileCleanUp::class);
    }

    private function setRunTime(string $entryId, int $hours, int $minutes) : void
    {
        $db = $this->app()->db();
        $rules = json_decode($db->fetchOne('SELECT run_rules FROM xf_cron_entry WHERE entry_id = ?', $entryId), true);
        $rules['hours'] = [$hours];
        $rules['minutes'] = [$minutes];
        $db->update('xf_cron_entry', ['run_rules' => json_encode($rules)], 'entry_id = ?', $entryId);
    }

    private function runTime(string $entryId) : array
    {
        $rules = json_decode(
            $this->app()->db()->fetchOne('SELECT run_rules FROM xf_cron_entry WHERE entry_id = ?', $entryId),
            true
        );

        return [$rules['hours'][0], $rules['minutes'][0]];
    }

    // ------------------------------------------------------------------

    private function invokePostUpgrade() : void
    {
        $setup = new Setup($this->app()->addOnManager()->getById('Hampel/KnownBots'), $this->app());

        $stateChanges = [];
        // a previous version above 6000030, so the one-off v6 e-mail cleanup does not run
        $setup->postUpgrade(6010370, $stateChanges);
    }
}
