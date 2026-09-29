<?php namespace Tests\Feature;

use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use Tests\TestCase;

/**
 * The session activity extension, which rewrites XenForo's online counts so robots are counted
 * separately instead of being lumped in with guests. Real rows in a rolled-back transaction: the
 * whole change is one SQL statement, so nothing short of running it proves anything.
 */
class SessionActivityCountsTest extends TestCase
{
    use UsesDatabaseTransactions;

    public function test_robots_are_counted_separately_when_the_option_is_on()
    {
        $this->setOption('knownbotsShowBotStats', true);
        $this->seedSession('a-guest', 0, '');
        $this->seedSession('a-robot', 0, 'testbot');
        $this->seedSession('another-robot', 0, 'testbot2');

        $counts = $this->repo()->getOnlineCounts();

        $this->assertArrayHasKey('robots', $counts);
        $this->assertGreaterThanOrEqual(2, $counts['robots']);
        $this->assertGreaterThanOrEqual(1, $counts['guests']);
    }

    public function test_xenforos_own_counts_are_used_when_the_option_is_off()
    {
        $this->setOption('knownbotsShowBotStats', false);
        $this->seedSession('a-robot', 0, 'testbot');

        $counts = $this->repo()->getOnlineCounts();

        // XenForo's own query returns no robots key at all
        $this->assertArrayNotHasKey('robots', $counts);
        $this->assertArrayHasKey('total', $counts);
    }

    public function test_a_robot_is_counted_in_neither_the_total_nor_the_guests()
    {
        $this->setOption('knownbotsShowBotStats', true);
        $this->seedSession('a-guest', 0, '');
        $this->seedSession('a-robot', 0, 'testbot');

        $counts = $this->repo()->getOnlineCounts();

        // the invariant the rewritten query exists for, and it does not depend on what else the
        // live forum is writing while the test runs: robots are outside the visitor total
        $this->assertSame(
            (int) $counts['total'],
            (int) $counts['members'] + (int) $counts['guests'],
            'the visitor total should be members plus guests, with robots counted apart'
        );
        $this->assertGreaterThanOrEqual(1, $counts['robots']);
    }

    public function test_an_expired_session_is_not_counted()
    {
        $this->setOption('knownbotsShowBotStats', true);
        $before = (int) $this->repo()->getOnlineCounts()['robots'];

        $this->seedSession('an-old-robot', 0, 'testbot', \XF::$time - 86400);

        $this->assertSame($before, (int) $this->repo()->getOnlineCounts()['robots']);
    }

    // ------------------------------------------------------------------

    private function repo()
    {
        return $this->app()->repository('XF:SessionActivity');
    }

    private function seedSession(string $key, int $userId, string $robotKey, ?int $viewDate = null) : void
    {
        $this->app()->db()->insert('xf_session_activity', [
            'user_id' => $userId,
            'unique_key' => $key,
            'ip' => '',
            'controller_name' => 'XF:Index',
            'controller_action' => 'Index',
            'view_state' => 'valid',
            'params' => '',
            'view_date' => $viewDate ?? \XF::$time,
            'robot_key' => $robotKey,
        ]);
    }
}
