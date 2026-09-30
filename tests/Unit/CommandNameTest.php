<?php namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Every CLI command's declared name. The name is an identifier, not documentation: one that
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
