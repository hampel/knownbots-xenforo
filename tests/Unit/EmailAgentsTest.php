<?php namespace Tests\Unit;

use Hampel\KnownBots\Cli\Command\EmailAgents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\TestCase;

/**
 * known-bots:email reports how many addresses it reached, and fails when it reached none - rather
 * than claiming a send that never happened.
 */
class EmailAgentsTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesMail();
        $this->fakesErrors();

        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);
        $this->mockRepository('Hampel\KnownBots:Agent', function ($mock)
        {
            $mock->allows('getUserAgentsForSending')->andReturns(['agent-one', 'agent-two']);
        });
    }

    public function test_reports_each_address_reached()
    {
        $tester = new CommandTester(new EmailAgents());

        $this->assertSame(Command::SUCCESS, $tester->execute(['address' => 'one@example.com, two@example.com']));
        $this->assertStringContainsString('Sent 2 agents via email to 2 address(es)', $tester->getDisplay());
        $this->assertMailSentTimes(2);
    }

    public function test_fails_when_no_address_is_valid()
    {
        $tester = new CommandTester(new EmailAgents());

        $this->assertSame(Command::FAILURE, $tester->execute(['address' => 'nobody']));
        $this->assertStringContainsString("Nothing sent to 'nobody'", $tester->getDisplay());
        $this->assertMailNotSent();
    }
}
