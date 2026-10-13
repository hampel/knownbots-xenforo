<?php namespace Tests\Unit;

use Hampel\KnownBots\SubContainer\Log;
use Psr\Log\AbstractLogger;
use Psr\Log\NullLogger;
use Tests\TestCase;

/**
 * The logging facade asks the optional Monolog Logging Service add-on for a channel, and has to
 * work on both of that add-on's interfaces - `channel()` from its 5.0, `newChannel()` from its 4.x
 * - as well as on a forum where it is not installed at all.
 *
 * None of that can be reached through the real add-on: `$addonsToLoad` isolates this suite to
 * KnownBots, so `monolog` is never in the container here. The two fakes below stand in for the two
 * interfaces, which is also the only way to assert WHICH method was called - the real add-on
 * answers both.
 */
class LogSubContainerTest extends TestCase
{
    /**
     * A PSR-3 logger that keeps what it was given.
     *
     * `: void` matches psr/log 3.x, where `LoggerInterface::log()` declares it. On the 1.1.4 this
     * install ships the parent declares no return type, and PHP lets a child add one.
     */
    protected function recorder()
    {
        return new class extends AbstractLogger
        {
            public $records = [];

            public function log($level, $message, array $context = []): void
            {
                $this->records[] = ['level' => $level, 'message' => $message, 'context' => $context];
            }
        };
    }

    /** The logging add-on at 5.x: `channel()`, with `newChannel()` kept as a deprecated alias. */
    protected function fiveX($logger)
    {
        return new class($logger)
        {
            public $asked = [];
            private $logger;

            public function __construct($logger) { $this->logger = $logger; }

            public function channel(string $name)
            {
                $this->asked[] = ['channel', $name];
                return $this->logger;
            }

            public function newChannel($name)
            {
                $this->asked[] = ['newChannel', $name];
                return $this->logger;
            }
        };
    }

    /** The logging add-on at 4.x: `newChannel()` only. */
    protected function fourX($logger)
    {
        return new class($logger)
        {
            public $asked = [];
            private $logger;

            public function __construct($logger) { $this->logger = $logger; }

            public function newChannel($name)
            {
                $this->asked[] = ['newChannel', $name];
                return $this->logger;
            }
        };
    }

    protected function subContainer()
    {
        // the sub-container reads `monolog` off the application container it is handed, so
        // anything swapped in has to be in place before this is built
        return new Log($this->app()->container(), $this->app());
    }

    public function test_resolves_a_null_logger_when_the_logging_add_on_is_absent()
    {
        $this->assertFalse(
            $this->app()->container()->offsetExists('monolog'),
            'the test is meaningless if something has registered monolog'
        );

        $this->assertInstanceOf(NullLogger::class, $this->subContainer()['log']);
    }

    public function test_logging_without_the_logging_add_on_throws_nothing()
    {
        $log = $this->subContainer();

        // the whole facade, because before the NullLogger each of these null-checked for itself
        $log->emergency('emergency');
        $log->alert('alert');
        $log->critical('critical');
        $log->error('error');
        $log->warning('warning');
        $log->notice('notice');
        $log->info('info');
        $log->debug('debug');
        $log->log('info', 'log');

        $this->assertInstanceOf(NullLogger::class, $log['log']);
    }

    public function test_asks_for_a_channel_through_the_5x_interface_when_it_is_offered()
    {
        $recorder = $this->recorder();
        $monolog = $this->swap('monolog', $this->fiveX($recorder));

        $logger = $this->subContainer()['log'];

        $this->assertSame($recorder, $logger);
        $this->assertSame([['channel', 'knownbots']], $monolog->asked);
    }

    public function test_falls_back_to_newChannel_on_the_older_logging_add_on()
    {
        $recorder = $this->recorder();
        $monolog = $this->swap('monolog', $this->fourX($recorder));

        $logger = $this->subContainer()['log'];

        $this->assertSame($recorder, $logger);
        $this->assertSame([['newChannel', 'knownbots']], $monolog->asked);
    }

    public function test_the_channel_is_named_knownbots_on_both_interfaces()
    {
        // forum owners target a channel by name in config.php, so the name is a published
        // interface and renaming it would silently detach whatever they have set
        foreach ([$this->fiveX($this->recorder()), $this->fourX($this->recorder())] AS $monolog)
        {
            $this->swap('monolog', $monolog);
            $this->subContainer()['log'];

            $this->assertSame('knownbots', $monolog->asked[0][1]);
        }
    }

    public function test_every_level_reaches_the_logger_with_its_message_and_context()
    {
        $recorder = $this->recorder();
        $this->swap('monolog', $this->fiveX($recorder));

        $log = $this->subContainer();
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

        foreach ($levels AS $level)
        {
            $log->$level("message for {$level}", ['level' => $level]);
        }

        $expected = [];
        foreach ($levels AS $level)
        {
            $expected[] = [
                'level' => $level,
                'message' => "message for {$level}",
                'context' => ['level' => $level],
            ];
        }

        $this->assertSame($expected, $recorder->records);
    }
}
