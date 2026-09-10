<?php namespace Tests\Feature;

use Hampel\KnownBots\Setup;
use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use XF\DevelopmentOutput;

/**
 * Setup randomises two cron entries' run times on install and upgrade. That is a per-site value,
 * not the add-on's definition, so the save must not write development output: in development
 * mode it would create _output/ in the installed add-on, and the next upgrade would then sync
 * from that partial copy and delete the cron entry it lacks.
 *
 * Runs against the real cron rows inside a rolled-back transaction, with the development-output
 * service replaced, so nothing is written to the database or to disk.
 */
class SetupCronRandomisingTest extends TestCase
{
    use UsesDatabaseTransactions;

    #[DataProvider('randomisedCrons')]
    public function test_randomising_a_cron_saves_it_without_writing_development_output(string $method, string $entryId)
    {
        // behave as an install running in development mode does, but record export() instead of
        // writing. isEnabled() must be true: if it were not, the write would be skipped whether
        // or not Setup switches it off, and this would pass for the wrong reason
        $this->mock('development.output', DevelopmentOutput::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->allows('isEnabled')->andReturns(true);
            $mock->expects('export')->never();
        });

        // an impossible time, so a valid one afterwards proves the randomise-and-save path ran
        $this->setRunTime($entryId, 99, 99);

        $this->invokeSetup($method);

        [$hours, $minutes] = $this->runTime($entryId);
        $this->assertGreaterThanOrEqual(0, $hours);
        $this->assertLessThanOrEqual(23, $hours);
        $this->assertGreaterThanOrEqual(0, $minutes);
        $this->assertLessThanOrEqual(59, $minutes);
    }

    public static function randomisedCrons() : array
    {
        return [
            'fetch cron' => ['randomizeFetchCron', 'hampelKnownBotsFetchBots'],
            'send cron' => ['randomizeSendCron', 'hampelKnownBotsUserAgents'],
        ];
    }

    // ------------------------------------------------------------------

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

    private function invokeSetup(string $method) : void
    {
        $setup = new Setup($this->app()->addOnManager()->getById('Hampel/KnownBots'), $this->app());

        (new \ReflectionMethod($setup, $method))->invoke($setup);
    }
}
