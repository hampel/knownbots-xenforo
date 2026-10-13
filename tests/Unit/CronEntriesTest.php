<?php namespace Tests\Unit;

use Hampel\KnownBots\Cli\Command\Config;
use Tests\TestCase;

/**
 * known-bots:config and known-bots:validate both work from a hard-coded list of the cron entries
 * this add-on ships, because they report on an entry that has gone missing - and a list derived
 * from the forum cannot say that something is absent.
 *
 * A hard-coded list goes stale silently, which is what this is for: the add-on's own definitions
 * are the answer, and on a development install they are in _output.
 */
class CronEntriesTest extends TestCase
{
    public function test_the_declared_cron_entries_are_the_ones_this_add_on_ships()
    {
        $shipped = [];
        foreach (glob(dirname(__DIR__, 2) . '/_output/cron_entries/*.json') ?: [] AS $file)
        {
            if (basename($file) != '_metadata.json')
            {
                $shipped[] = basename($file, '.json');
            }
        }

        sort($shipped);
        $declared = array_keys(Config::CRON_ENTRIES);
        sort($declared);

        $this->assertNotEmpty($shipped, '_output/cron_entries is empty - nothing was compared');
        $this->assertSame($shipped, $declared);
    }

    public function test_every_declared_cron_entry_has_a_label_a_reader_can_act_on()
    {
        $problems = [];
        foreach (Config::CRON_ENTRIES AS $entryId => $label)
        {
            // the entry ids are what the admin control panel shows and say nothing about what the
            // entry does, so a label that is just the id back again is no label at all
            if ($label === '' || $label === $entryId)
            {
                $problems[] = $entryId;
            }
        }

        $this->assertSame([], $problems);
    }
}
