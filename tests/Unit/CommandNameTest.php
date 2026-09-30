<?php namespace Tests\Unit;

use Hampel\KnownBots\Cli\Command\AbstractCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Every CLI command's declared name, and the base class they all extend. The name is an identifier, not documentation: one that
 * carried its argument placeholder - 'known-bots:test {user-agent}' - ran only because Symfony
 * resolved the real name as an abbreviation of it, and would have stopped working as soon as
 * another command name shared that prefix.
 */
class CommandNameTest extends TestCase
{
    #[DataProvider('commandClasses')]
    public function test_the_declared_name_is_a_plain_command_name(string $class, string $expected)
    {
        $name = (new $class())->getName();

        $this->assertSame($expected, $name);
        $this->assertDoesNotMatchRegularExpression('/[\s{}<>\[\]]/', $name, 'a name should carry no argument placeholder');
    }

    #[DataProvider('commandClasses')]
    public function test_the_command_extends_this_addons_own_base_class(string $class, string $expected)
    {
        // NOT XF\Cli\Command\AbstractCommand: that class and the status constants its commands
        // return are XenForo 2.3 only, and this add-on supports 2.2 as well. The add-on's own base
        // supplies both, so a command moved back onto XenForo's would break every 2.2 install.
        $this->assertInstanceOf(AbstractCommand::class, new $class());
    }

    #[DataProvider('commandClasses')]
    public function test_the_status_constants_a_command_returns_resolve(string $class, string $expected)
    {
        $this->assertSame(0, $class::SUCCESS);
        $this->assertSame(1, $class::FAILURE);
        $this->assertSame(2, $class::INVALID);
    }

    public static function commandClasses() : array
    {
        return [
            'check-token' => ['Hampel\KnownBots\Cli\Command\CheckApiToken', 'known-bots:check-token'],
            'email' => ['Hampel\KnownBots\Cli\Command\EmailAgents', 'known-bots:email'],
            'fetch' => ['Hampel\KnownBots\Cli\Command\FetchBots', 'known-bots:fetch'],
            'import' => ['Hampel\KnownBots\Cli\Command\Import', 'known-bots:import'],
            'load' => ['Hampel\KnownBots\Cli\Command\LoadBots', 'known-bots:load'],
            'parse' => ['Hampel\KnownBots\Cli\Command\ParseLogs', 'known-bots:parse'],
            'reprocess' => ['Hampel\KnownBots\Cli\Command\ReprocessUserAgents', 'known-bots:reprocess'],
            'send' => ['Hampel\KnownBots\Cli\Command\SendAgents', 'known-bots:send'],
            'test' => ['Hampel\KnownBots\Cli\Command\TestBots', 'known-bots:test'],
        ];
    }
}
