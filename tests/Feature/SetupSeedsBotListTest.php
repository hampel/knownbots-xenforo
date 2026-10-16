<?php namespace Tests\Feature;

use Hampel\KnownBots\Setup;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use Tests\TestCase;
use XF\DevelopmentOutput;

/**
 * An upgrade must seed the bot list, not assume an earlier install already did.
 *
 * A forum that came through the XenForo 1 to 2 upgrade carries a legacy_addon_id, so XenForo
 * renames the legacy row and runs the UPGRADE steps: install() and postInstall() never execute,
 * and nothing has ever written internal-data://knownbots.json. postUpgrade() used to call only
 * loadBots(), which is guarded on that file existing, so the whole thing was a silent no-op and
 * the forum detected no bots at all until the fetch cron happened to run. Found 2026-10-16 by
 * the ZooChat XF1 to XF2 upgrade harness against a real production snapshot.
 *
 * These tests drive the branch where the add-on directory holds no bundled knownbots.json, which
 * is the case in a development checkout - the file is downloaded into the zip at build time. That
 * branch fetches from the API instead, and reaching it at all is the thing that was missing: it
 * proves postUpgrade() now tries to seed, where before it did nothing whatsoever.
 */
class SetupSeedsBotListTest extends TestCase
{
    use UsesDatabaseTransactions;

    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesJobs();
        // every case here ends with no readable bot list, so the real server error log must not
        // be written to
        $this->fakesErrors();

        $this->mock('development.output', DevelopmentOutput::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->allows('isEnabled')->andReturns(false);
        });
    }

    public function test_an_upgrade_seeds_the_bot_list_when_nothing_has_ever_written_it()
    {
        $this->fileIsAbsent();

        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->allows('loadBots')->andReturns([]);
            $mock->allows('updateBots');
            // the seed: with no bundled file to copy, the list is fetched instead
            $mock->expects('fetchBots')->with(true)->once();
        });

        $this->invokePostUpgrade();
    }

    public function test_an_upgrade_leaves_a_bot_list_that_is_already_there_alone()
    {
        $this->fileIsPresent();

        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->allows('loadBots')->andReturns([]);
            $mock->allows('updateBots');
            // an XF2 to XF2 upgrade must behave exactly as it did before: no seeding, no fetch
            $mock->expects('fetchBots')->never();
        });

        $this->invokePostUpgrade();
    }

    public function test_a_bot_list_that_could_not_be_loaded_is_reported()
    {
        // the API refuses to seed, so the file is still missing when loadBots() runs - the state
        // that used to pass in silence
        $this->fileIsAbsent();

        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->allows('loadBots')->andReturns([]);
            $mock->allows('updateBots');
            $mock->allows('fetchBots')->andReturns(false);
        });

        $this->invokePostUpgrade();

        $this->assertErrorLogged('Known Bots: no bot list to load - detection will not work until the fetch cron runs');
    }

    // ------------------------------------------------------------------

    private function fileIsAbsent() : void
    {
        $this->mockFs('internal-data', function ($mock)
        {
            $mock->allows('has')->andReturns(false);
        });
    }

    private function fileIsPresent() : void
    {
        $this->mockFs('internal-data', function ($mock)
        {
            $mock->allows('has')->andReturns(true);
        });
    }

    private function invokePostUpgrade() : void
    {
        $setup = new Setup($this->app()->addOnManager()->getById('Hampel/KnownBots'), $this->app());

        $stateChanges = [];
        // a previous version above 6000030, so the one-off v6 e-mail cleanup does not run
        $setup->postUpgrade(6010370, $stateChanges);
    }
}
