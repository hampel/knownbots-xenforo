<?php namespace Tests\Feature;

use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use Tests\TestCase;

/**
 * reprocessUserAgents() against the real xf_knownbots_agent table, inside a transaction that is
 * rolled back.
 *
 * This is the remedy for the XF1 takeover defect fixed in 6.3.1, and it is tested because it is
 * about to be recommended to site owners. A forum that took that upgrade on 6.3.0 or earlier ran
 * with an empty code cache, so the browser rules were empty, step 6 of userAgentMatchesRobot()
 * could never match, and every ordinary visitor was stored as an unidentified agent. Seeding the
 * definitions does not clear those rows - Setup::loadBots() calls updateBots(), which does not
 * reprocess - so known-bots:reprocess is what clears them, and that claim needs measuring rather
 * than asserting.
 */
class AgentReprocessTest extends TestCase
{
    use UsesDatabaseTransactions;

    private const TABLE = 'xf_knownbots_agent';

    private const BROWSER = 'Mozilla/5.0 (X11; Linux x86_64) Firefox/120.0';
    private const BOT = 'Mozilla/5.0 (compatible; FooBot/1.0)';
    private const IGNORED = 'Mozilla/5.0 ignore-me-xyz';

    protected function setUp() : void
    {
        parent::setUp();

        $db = $this->app()->db();
        $this->assertTrue($db->inTransaction(), 'Not inside a transaction - refusing to clear the table');
        $db->query('DELETE FROM ' . self::TABLE);

        $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });

        // the definitions a repaired forum now holds. allows() rather than expects(): reprocess
        // consults each list once per stored agent
        $this->mock('knownbots.cache', Cache::class, function ($mock)
        {
            $mock->allows('loadBotData')->with('maps')->andReturns(['foobot' => 'foobot']);
            $mock->allows('loadBotData')->with('bots')->andReturns(['foobot' => ['title' => 'Foo Bot', 'link' => null]]);
            $mock->allows('loadBotData')->with('complex')->andReturns([]);
            $mock->allows('loadBotData')->with('ignored')->andReturns(['ignore-me-xyz']);
            $mock->allows('loadBotData')->with('browsers')->andReturns([
                'Mozilla/\d\.\d',
                '\(X11; Linux x86_64\)',
                'Firefox/[\d.]+',
            ]);
        });
    }

    public function test_reprocessing_deletes_an_ordinary_browser_stored_as_unidentified()
    {
        // exactly what an XF1-takeover forum accumulated: a real browser, no robot key
        $this->repo()->addUserAgent(self::BROWSER, null);
        $this->assertDatabaseHas(self::TABLE, ['user_agent' => self::BROWSER, 'robot_key' => null]);

        $this->robot()->reprocessUserAgents();

        $this->assertDatabaseMissing(self::TABLE, ['user_agent' => self::BROWSER]);
    }

    public function test_reprocessing_promotes_an_agent_now_recognised_as_a_bot()
    {
        $this->repo()->addUserAgent(self::BOT, null);

        $this->robot()->reprocessUserAgents();

        $this->assertDatabaseHas(self::TABLE, ['user_agent' => self::BOT, 'robot_key' => 'foobot']);
    }

    public function test_reprocessing_deletes_an_agent_now_matching_an_ignored_rule()
    {
        $this->repo()->addUserAgent(self::IGNORED, null);

        $this->robot()->reprocessUserAgents();

        $this->assertDatabaseMissing(self::TABLE, ['user_agent' => self::IGNORED]);
    }

    public function test_the_three_outcomes_together_leave_only_the_bot()
    {
        $this->repo()->addUserAgent(self::BROWSER, null);
        $this->repo()->addUserAgent(self::BOT, null);
        $this->repo()->addUserAgent(self::IGNORED, null);
        $this->assertDatabaseCount(self::TABLE, 3);

        $this->robot()->reprocessUserAgents();

        $this->assertDatabaseCount(self::TABLE, 1);
        $this->assertDatabaseHas(self::TABLE, ['user_agent' => self::BOT, 'robot_key' => 'foobot']);
    }

    // ------------------------------------------------------------------

    private function repo()
    {
        return $this->app()->repository('Hampel\KnownBots:Agent');
    }

    private function robot()
    {
        return $this->app()->data('XF:Robot');
    }
}
