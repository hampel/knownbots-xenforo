<?php namespace Tests\Feature;

use Hampel\KnownBots\Exception\CustomerException;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use Tests\TestCase;

/**
 * The API setup flow, which replaces the normal option editor for the "Send user agents" option.
 * It is guarded by the option's own edit format, so another option cannot be driven through it.
 *
 * Real option rows, inside a rolled-back transaction: the flow's whole purpose is writing the
 * validated API token back into the option value.
 */
class AdminApiSetupTest extends TestCase
{
    use UsesDatabaseTransactions;

    private const OPTION = 'knownbotsSendUserAgents';

    protected function setUp() : void
    {
        parent::setUp();

        $user = $this->actingAsMember(['is_admin' => true]);
        $this->setVisitorAdminPermissions($user, ['option']);
    }

    public function test_the_setup_page_renders_for_the_api_option()
    {
        $reply = $this->callAction('XF:Option', 'KnownbotsApiSetup', 'admin', [], ['option_id' => self::OPTION], 'GET');

        $this->assertReplyIsView($reply);
        $this->assertReplyTemplate($reply, 'option_knownbots_api_setup');
        $this->assertReplyParam($reply, 'option');
    }

    public function test_another_option_cannot_be_driven_through_the_setup_flow()
    {
        $reply = $this->callAction('XF:Option', 'KnownbotsApiSetup', 'admin', [], ['option_id' => 'boardTitle'], 'GET');

        $this->assertReplyIsError($reply, 403);
    }

    public function test_choosing_to_configure_shows_the_token_form()
    {
        $reply = $this->callAction(
            'XF:Option', 'KnownbotsApiSetup', 'admin', ['action' => 'update'], ['option_id' => self::OPTION]
        );

        $this->assertReplyIsView($reply);
        $this->assertReplyTemplate($reply, 'option_knownbots_api_configure');
        $this->assertSame('update', $this->replyParam($reply, 'action'));
    }

    public function test_choosing_to_disable_turns_the_option_off()
    {
        $reply = $this->callAction(
            'XF:Option', 'KnownbotsApiSetup', 'admin', ['action' => 'disabled'], ['option_id' => self::OPTION]
        );

        $this->assertReplyIsRedirect($reply);
        $this->assertFalse(\XF::options()->knownbotsSendUserAgents['enabled']);
    }

    public function test_leaving_it_unchanged_writes_nothing()
    {
        $before = \XF::options()->knownbotsSendUserAgents;

        $reply = $this->callAction(
            'XF:Option', 'KnownbotsApiSetup', 'admin', ['action' => 'unchanged'], ['option_id' => self::OPTION]
        );

        $this->assertReplyIsRedirect($reply);
        $this->assertSame($before, \XF::options()->knownbotsSendUserAgents);
    }

    public function test_a_validated_licence_token_stores_the_api_token_it_was_issued()
    {
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('validate')->with('licence-token')->once()->andReturns('issued-api-token');
        });

        $reply = $this->callAction(
            'XF:Option', 'KnownbotsApiConfigure', 'admin',
            ['validation_token' => 'licence-token'], ['option_id' => self::OPTION]
        );

        $this->assertReplyIsRedirect($reply);

        $option = \XF::options()->knownbotsSendUserAgents;
        $this->assertTrue((bool) $option['enabled']);
        $this->assertSame('issued-api-token', $option['api_token']);
        $this->assertSame('licence-token', $option['validation_token']);
    }

    public function test_a_refused_licence_token_reports_the_error_and_stores_nothing()
    {
        $before = \XF::options()->knownbotsSendUserAgents;

        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('validate')->once()->andThrow(new CustomerException('validating licence', 'not a valid token', 403));
        });

        $reply = $this->callAction(
            'XF:Option', 'KnownbotsApiConfigure', 'admin',
            ['validation_token' => 'nonsense'], ['option_id' => self::OPTION]
        );

        $this->assertReplyIsError($reply);
        $this->assertSame($before, \XF::options()->knownbotsSendUserAgents);
    }

    public function test_the_configure_step_refuses_a_get()
    {
        $reply = $this->callAction(
            'XF:Option', 'KnownbotsApiConfigure', 'admin', [], ['option_id' => self::OPTION], 'GET'
        );

        $this->assertReplyIsError($reply, 405);
    }
}
