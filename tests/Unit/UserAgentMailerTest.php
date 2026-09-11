<?php namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

/**
 * Emailing the stored user agents. The address comes from the "Email user agents" option or the
 * command line, and may be a list: XenForo's mailer takes one recipient per message, so each
 * valid address gets its own, and an invalid entry is reported rather than sinking the rest.
 */
class UserAgentMailerTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesMail();
        $this->fakesErrors();
    }

    public function test_a_single_address_gets_one_message_with_the_agents_attached()
    {
        $this->assertSame(1, $this->mailer('admin@example.com', ['agent-one', 'agent-two'])->mailUserAgents());

        $this->assertSame(['admin@example.com'], $this->recipients());

        $attachments = $this->getSentMail()[0]->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertSame('agent-one' . PHP_EOL . 'agent-two' . PHP_EOL, $attachments[0]->getBody());

        $this->assertNoErrorsLogged();
    }

    #[DataProvider('addressLists')]
    public function test_each_address_in_a_list_gets_its_own_message(string $list, array $expected)
    {
        $this->assertSame(count($expected), $this->mailer($list)->mailUserAgents());

        $this->assertSame($expected, $this->recipients());
        $this->assertNoErrorsLogged();
    }

    public static function addressLists() : array
    {
        return [
            'comma separated' => ['one@example.com,two@example.com', ['one@example.com', 'two@example.com']],
            'comma and space' => ['one@example.com, two@example.com', ['one@example.com', 'two@example.com']],
            'semicolon separated' => ['one@example.com; two@example.com', ['one@example.com', 'two@example.com']],
            'blank entries ignored' => [' ,one@example.com,, ,two@example.com, ', ['one@example.com', 'two@example.com']],
            'duplicates sent once' => ['one@example.com, two@example.com, one@example.com', ['one@example.com', 'two@example.com']],
        ];
    }

    public function test_an_invalid_entry_is_logged_by_name_and_the_rest_are_still_sent()
    {
        $this->assertSame(1, $this->mailer('not-an-address, one@example.com')->mailUserAgents());

        $this->assertSame(['one@example.com'], $this->recipients());
        $this->assertErrorLogged("Known Bots: skipped invalid email address 'not-an-address' when emailing user agents");
    }

    #[DataProvider('nothingValid')]
    public function test_nothing_is_sent_when_no_entry_is_a_valid_address(string $list, int $errors)
    {
        $this->assertSame(0, $this->mailer($list)->mailUserAgents());

        $this->assertMailNotSent();
        $this->assertCount($errors, $this->getErrors());
    }

    public static function nothingValid() : array
    {
        return [
            'empty' => ['', 0],
            'only separators' => [' , ; ', 0],
            'only invalid entries' => ['nobody, @example.com', 2],
        ];
    }

    // ------------------------------------------------------------------

    private function mailer(string $address, array $agents = ['agent-one'])
    {
        $mailer = $this->app()->service('Hampel\KnownBots:UserAgentMailer');
        $mailer->setToEmail($address);
        $mailer->setUserAgents($agents);

        return $mailer;
    }

    private function recipients() : array
    {
        return array_map(function (Email $email)
        {
            $to = $email->getTo();
            $this->assertCount(1, $to, 'each message should have exactly one recipient');

            return $to[0]->getAddress();
        }, $this->getSentMail());
    }
}
