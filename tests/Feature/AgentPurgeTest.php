<?php namespace Tests\Feature;

use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use Tests\TestCase;

/**
 * Runs against the real xf_knownbots_agent table, inside a transaction that is rolled back after
 * each test, so nothing written here survives.
 */
class AgentPurgeTest extends TestCase
{
    use UsesDatabaseTransactions;

    private const TABLE = 'xf_knownbots_agent';

    private const DAY = 86400;

    protected function setUp() : void
    {
        parent::setUp();

        $db = $this->app()->db();

        // refuse to clear the table unless the rollback is in place - this is the real table
        $this->assertTrue($db->inTransaction(), 'Not inside a transaction - refusing to clear the table');

        // DELETE, never $db->emptyTable() or the repository's clearAllUserAgents(): both use
        // TRUNCATE, which is DDL, commits implicitly, and would escape the rollback
        $db->query('DELETE FROM ' . self::TABLE);
    }

    public function test_purge_deletes_agents_older_than_retention_and_keeps_recent_ones()
    {
        $this->insertAgent('OldBot/1.0', \XF::$time - 100 * self::DAY);
        $this->insertAgent('RecentBot/1.0', \XF::$time - 10 * self::DAY);

        $rows = $this->agentRepo()->purgeUserAgents(90);

        $this->assertSame(1, $rows);
        $this->assertDatabaseMissing(self::TABLE, ['user_agent' => 'OldBot/1.0']);
        $this->assertDatabaseHas(self::TABLE, ['user_agent' => 'RecentBot/1.0']);
    }

    public function test_purge_with_a_very_long_retention_deletes_nothing()
    {
        // a longer retention must never delete more - comparing last_updated against the
        // retention period itself, rather than a cutoff timestamp, deletes every row once the
        // period exceeds the current time
        $this->insertAgent('OldBot/1.0', \XF::$time - 100 * self::DAY);
        $this->insertAgent('RecentBot/1.0', \XF::$time - 10 * self::DAY);

        $rows = $this->agentRepo()->purgeUserAgents(100000);

        $this->assertSame(0, $rows);
        $this->assertDatabaseCount(self::TABLE, 2);
    }

    // ------------------------------------------------------------------

    private function insertAgent(string $userAgent, int $lastUpdated) : void
    {
        $this->app()->db()->insert(self::TABLE, [
            'user_agent' => $userAgent,
            'robot_key' => null,
            'last_updated' => $lastUpdated,
            'sent' => 1,
        ]);
    }

    private function agentRepo()
    {
        return $this->app()->repository('Hampel\KnownBots:Agent');
    }
}
