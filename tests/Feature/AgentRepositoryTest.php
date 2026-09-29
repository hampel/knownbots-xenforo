<?php namespace Tests\Feature;

use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use Tests\TestCase;

/**
 * The agent repository's hand-written SQL, against the real xf_knownbots_agent table inside a
 * transaction that is rolled back. addUserAgent() runs on every unrecognised guest page view and
 * returns 0 skipped / 1 inserted / 2 updated, which callers branch on.
 *
 * clearAllUserAgents() is deliberately not tested here: it is a TRUNCATE, which commits
 * implicitly and would both escape the rollback and empty the real table.
 */
class AgentRepositoryTest extends TestCase
{
    use UsesDatabaseTransactions;

    private const TABLE = 'xf_knownbots_agent';

    private const DAY = 86400;

    protected function setUp() : void
    {
        parent::setUp();

        $db = $this->app()->db();
        $this->assertTrue($db->inTransaction(), 'Not inside a transaction - refusing to clear the table');
        $db->query('DELETE FROM ' . self::TABLE);
    }

    public function test_a_new_user_agent_is_inserted_and_dated_to_midnight()
    {
        // NOTE: addUserAgent() dates the row with mktime(0, 0, 0), which reads the system clock
        // rather than \XF::$time - so setTestTime() cannot move it and the expectation has to be
        // today's midnight. purgeUserAgents() does use \XF::$time; see AgentPurgeTest
        $this->assertSame(1, $this->repo()->addUserAgent('NewBot/1.0', 'newbot'));

        $this->assertDatabaseHas(self::TABLE, [
            'user_agent' => 'NewBot/1.0',
            'robot_key' => 'newbot',
            'last_updated' => mktime(0, 0, 0),
            'sent' => 0,
        ]);
    }

    public function test_seeing_the_same_agent_again_the_same_day_writes_nothing()
    {
        $this->repo()->addUserAgent('NewBot/1.0', 'newbot');

        $this->assertSame(0, $this->repo()->addUserAgent('NewBot/1.0', 'newbot'));

        $this->assertDatabaseCount(self::TABLE, 1);
    }

    public function test_an_agent_last_seen_on_an_earlier_day_is_touched_forward()
    {
        // seeded directly, since the repository dates its own writes from the system clock
        $this->app()->db()->insert(self::TABLE, [
            'user_agent' => 'NewBot/1.0',
            'robot_key' => 'newbot',
            'last_updated' => mktime(0, 0, 0) - 3 * self::DAY,
            'sent' => 0,
        ]);

        $this->assertSame(2, $this->repo()->addUserAgent('NewBot/1.0', 'newbot'));

        $this->assertDatabaseHas(self::TABLE, [
            'user_agent' => 'NewBot/1.0',
            'last_updated' => mktime(0, 0, 0),
        ]);
    }

    public function test_an_agent_that_is_now_recognised_gets_its_robot_key()
    {
        $this->repo()->addUserAgent('MysteryBot/1.0', null);
        $this->assertDatabaseHas(self::TABLE, ['user_agent' => 'MysteryBot/1.0', 'robot_key' => null]);

        $this->assertSame(2, $this->repo()->addUserAgent('MysteryBot/1.0', 'mystery'));
        $this->assertDatabaseHas(self::TABLE, ['user_agent' => 'MysteryBot/1.0', 'robot_key' => 'mystery']);
    }

    public function test_an_agent_is_not_stored_without_a_touch_when_it_is_unchanged()
    {
        $this->repo()->addUserAgent('NewBot/1.0', 'newbot');

        $this->assertSame(0, $this->repo()->addUserAgent('NewBot/1.0', 'newbot', false));
    }

    public function test_invalid_and_empty_user_agents_are_refused()
    {
        $this->assertSame(0, $this->repo()->addUserAgent("Bad\xC3\x28Bot", 'bad'), 'invalid UTF-8');
        $this->assertSame(0, $this->repo()->addUserAgent('   ', 'blank'), 'whitespace only');
        $this->assertSame(0, $this->repo()->addUserAgent('', 'empty'), 'empty');

        $this->assertDatabaseCount(self::TABLE, 0);
    }

    public function test_an_overlong_user_agent_is_truncated_to_the_column_width()
    {
        $this->assertSame(1, $this->repo()->addUserAgent(str_repeat('a', 600), 'long'));

        $this->assertDatabaseHas(self::TABLE, ['user_agent' => str_repeat('a', 512)]);
    }

    public function test_marking_agents_sent_covers_only_the_unsent_ones()
    {
        $this->repo()->addUserAgent('BotOne/1.0', 'one');
        $this->repo()->addUserAgent('BotTwo/1.0', 'two');

        $this->assertSame(2, $this->repo()->markUserAgentsSent());
        $this->assertDatabaseCount(self::TABLE, 2, ['sent' => 1]);

        $this->assertSame(0, $this->repo()->markUserAgentsSent(), 'nothing left to mark');
    }

    public function test_only_unsent_agents_are_offered_for_sending()
    {
        $this->repo()->addUserAgent('BotOne/1.0', 'one');
        $this->repo()->markUserAgentsSent();
        $this->repo()->addUserAgent('BotTwo/1.0', 'two');

        $this->assertSame(['BotTwo/1.0'], array_values($this->repo()->getUserAgentsForSending()));
    }

    public function test_deleting_an_agent_removes_only_that_row()
    {
        $this->repo()->addUserAgent('BotOne/1.0', 'one');
        $this->repo()->addUserAgent('BotTwo/1.0', 'two');

        $this->repo()->deleteUserAgent('BotOne/1.0');

        $this->assertDatabaseMissing(self::TABLE, ['user_agent' => 'BotOne/1.0']);
        $this->assertDatabaseHas(self::TABLE, ['user_agent' => 'BotTwo/1.0']);
    }

    public function test_the_display_list_shows_identified_bots_and_anything_not_yet_sent()
    {
        $this->repo()->addUserAgent('KnownBot/1.0', 'known');
        $this->repo()->markUserAgentsSent();                  // sent, but identified
        $this->repo()->addUserAgent('UnknownAgent/1.0', null); // unidentified, unsent

        $agents = $this->repo()->getUserAgentsForDisplay()->pluckNamed('user_agent');

        $this->assertContains('KnownBot/1.0', $agents);
        $this->assertContains('UnknownAgent/1.0', $agents);
    }

    public function test_reprocessing_covers_unidentified_agents_by_default_and_all_on_request()
    {
        $this->repo()->addUserAgent('KnownBot/1.0', 'known');
        $this->repo()->addUserAgent('UnknownAgent/1.0', null);

        $unknownOnly = $this->repo()->getUserAgentsForReprocessing()->pluckNamed('user_agent');
        $this->assertSame(['UnknownAgent/1.0'], array_values($unknownOnly));

        $all = $this->repo()->getUserAgentsForReprocessing(false)->pluckNamed('user_agent');
        $this->assertCount(2, $all);
    }

    // ------------------------------------------------------------------

    private function repo()
    {
        return $this->app()->repository('Hampel\KnownBots:Agent');
    }
}
