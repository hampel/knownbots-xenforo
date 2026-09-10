<?php namespace Tests\Unit;

use Hampel\KnownBots\Cli\Command\LoadBots;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\TestCase;

/**
 * Loading bot data from internal_data/knownbots.json - which an upgrade does, and which the
 * README invites site owners to replace by hand. A file that does not hold a valid payload must
 * be refused, not fatal. The filesystem is mocked, so the real file is never read or written.
 */
class LoadBotsTest extends TestCase
{
    #[DataProvider('unusableFiles')]
    public function test_load_bots_returns_nothing_for_a_file_without_a_valid_payload(string $contents)
    {
        $this->fileHolds($contents);

        $this->assertSame([], $this->app()['knownbots.api']->loadBots());
    }

    public static function unusableFiles() : array
    {
        return [
            'corrupt json' => ['{"version":3,"built":'],
            'empty file' => [''],
            'json scalar' => ['"ok"'],
            'json null' => ['null'],
            'valid json, wrong shape' => ['{"version":2}'],
        ];
    }

    public function test_load_bots_returns_a_valid_payload_intact()
    {
        $payload = [
            'version' => 3,
            'built' => 1700000000,
            'maps' => ['foobot' => 'foobot'],
            'bots' => ['foobot' => ['title' => 'Foo Bot', 'link' => null]],
            'complex' => [],
            'ignored' => [],
            'browsers' => [],
        ];
        $this->fileHolds(json_encode($payload));

        $this->assertSame($payload, $this->app()['knownbots.api']->loadBots());
    }

    public function test_the_load_command_refuses_a_file_without_a_valid_payload()
    {
        $this->fileHolds('{"version":3,"built":');

        $tester = new CommandTester(new LoadBots());

        $this->assertSame(Command::FAILURE, $tester->execute([]));
        $this->assertStringContainsString('No valid bot data', $tester->getDisplay());
    }

    // ------------------------------------------------------------------

    private function fileHolds(string $contents) : void
    {
        // Flysystem 1.x read(): has() for its presence assertion, then read()['contents']
        $this->mockFs('internal-data', function ($mock) use ($contents)
        {
            $mock->allows('has')->andReturns(true);
            $mock->allows('read')->andReturns(['type' => 'file', 'path' => 'knownbots.json', 'contents' => $contents]);
        });
    }
}
