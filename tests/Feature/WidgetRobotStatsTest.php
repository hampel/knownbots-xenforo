<?php namespace Tests\Feature;

use Tests\TestCase;

/**
 * The two template modifications that add robot counts to the online widgets. Both are gated on
 * the "Show robot statistics" option, so each is rendered with the option both ways: the negative
 * assertion alone would also pass against a template that rendered nothing.
 */
class WidgetRobotStatsTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        // a template error is logged to the real error log rather than thrown
        $this->fakesErrors();
    }

    public function test_both_template_modifications_apply()
    {
        $this->renderTemplate('public:widget_online_statistics', $this->statisticsParams());
        $this->renderTemplate('public:widget_members_online', $this->membersOnlineParams());

        $this->assertTemplateModificationApplied('knownbots_widget_online_statistics');
        $this->assertTemplateModificationApplied('knownBots_widget_members_online');
        $this->assertNoTemplateErrors();
    }

    public function test_the_statistics_widget_shows_the_robot_count_when_the_option_is_on()
    {
        $this->setOption('knownbotsShowBotStats', true);

        $html = $this->renderTemplate('public:widget_online_statistics', $this->statisticsParams());

        $this->assertSee($html, '4,242');
        $this->assertNoUnresolvedPhrases($html, 'hampel_knownbots_');
        $this->assertNoTemplateErrors();
    }

    public function test_the_statistics_widget_omits_the_robot_count_when_the_option_is_off()
    {
        $this->setOption('knownbotsShowBotStats', false);

        $html = $this->renderTemplate('public:widget_online_statistics', $this->statisticsParams());

        $this->assertDontSee($html, '4,242');
        $this->assertSee($html, '8,000');   // the core total still renders, so the absence means something
        $this->assertNoTemplateErrors();
    }

    public function test_the_members_online_widget_counts_robots_when_the_option_is_on()
    {
        $this->setOption('knownbotsShowBotStats', true);

        $html = $this->renderTemplate('public:widget_members_online', $this->membersOnlineParams());

        $this->assertNoUnresolvedPhrases($html, 'hampel_knownbots_');
        $this->assertSeeText($html, '4,242');
        $this->assertNoTemplateErrors();
    }

    public function test_the_members_online_widget_omits_robots_when_the_option_is_off()
    {
        $this->setOption('knownbotsShowBotStats', false);

        $html = $this->renderTemplate('public:widget_members_online', $this->membersOnlineParams());

        $this->assertDontSeeText($html, '4,242');
        $this->assertNoTemplateErrors();
    }

    public function test_the_robot_count_comes_from_the_modification_rather_than_the_core_template()
    {
        $this->setOption('knownbotsShowBotStats', true);

        $with = $this->renderTemplate('public:widget_online_statistics', $this->statisticsParams());
        $without = $this->renderTemplateWithoutModification(
            'knownbots_widget_online_statistics', $this->statisticsParams()
        );

        // rendering the same template with this add-on's modification disabled is what proves the
        // robot row is ours: the option being on is not enough to produce it
        $this->assertSee($with, '4,242');
        $this->assertDontSee($without, '4,242');
        $this->assertSee($without, '8,000', true, 'the core template still renders without the modification');
        $this->assertNoTemplateErrors();
    }

    public function test_the_members_online_robot_line_comes_from_the_modification()
    {
        $this->setOption('knownbotsShowBotStats', true);

        $without = $this->renderTemplateWithoutModification(
            'knownBots_widget_members_online', $this->membersOnlineParams()
        );

        $this->assertDontSeeText($without, '4,242');
        $this->assertNoTemplateErrors();
    }

    // ------------------------------------------------------------------

    private function statisticsParams() : array
    {
        return [
            'counts' => ['total' => 8000, 'members' => 10, 'guests' => 7748, 'robots' => 4242],
        ];
    }

    private function membersOnlineParams() : array
    {
        return [
            'online' => [
                'counts' => ['total' => 8000, 'members' => 10, 'guests' => 7748, 'robots' => 4242],
                'records' => [],
            ],
            'title' => 'Members online',
        ];
    }
}
