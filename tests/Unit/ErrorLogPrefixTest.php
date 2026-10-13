<?php namespace Tests\Unit;

use Tests\TestCase;

/**
 * Every row this add-on writes to XenForo's server error log begins with its own prefix.
 *
 * Hampel/EventLogging mirrors that log into a log channel and skips rows by these prefixes. A row
 * about a logging failure that lacks one is mirrored, alerted on through the handler that just
 * failed, and logged again - once per run, for as long as the fault lasts. The prefix is a string
 * literal at each call site, so nothing but a scan of the source can tell that one is missing.
 */
class ErrorLogPrefixTest extends TestCase
{
    private const PREFIX = 'Known Bots: ';

    public function test_every_server_error_log_call_carries_the_add_on_prefix()
    {
        $calls = 0;
        $problems = [];

        foreach ($this->sourceFiles() AS $file)
        {
            $source = file_get_contents($file);
            $relative = substr($file, strlen($this->addOnRoot()) + 1);

            foreach (['logError', 'logException'] AS $method)
            {
                // the arguments of each call, up to the first closing bracket - no call in this
                // add-on carries a ");" inside a string argument
                preg_match_all('/\\\\XF::' . $method . '\((.{0,160}?)\);/s', $source, $matches);

                foreach ($matches[1] AS $arguments)
                {
                    $calls++;

                    // the trait interpolates errorLogPrefix() rather than repeating the literal
                    if (strpos($arguments, self::PREFIX) === false
                        && strpos($arguments, 'errorLogPrefix()') === false)
                    {
                        $problems[] = "{$relative}: \\XF::{$method}({$arguments});";
                    }
                }
            }
        }

        $this->assertSame([], $problems, 'each of these needs the ' . var_export(self::PREFIX, true) . ' prefix');
        $this->assertGreaterThan(5, $calls, 'the scan found almost nothing - has it stopped reading the source?');
    }

    /**
     * Every PHP file the add-on ships, which is everything outside the test suite and its vendor
     * tree - neither of which ships, and both of which log errors on purpose.
     */
    private function sourceFiles(): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->addOnRoot(), \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator AS $file)
        {
            $path = $file->getPathname();
            if (substr($path, -4) != '.php')
            {
                continue;
            }

            $relative = substr($path, strlen($this->addOnRoot()) + 1);
            if (strpos($relative, 'tests/') === 0 || strpos($relative, 'vendor/') === 0
                || strpos($relative, '_build/') === 0 || strpos($relative, '_releases/') === 0)
            {
                continue;
            }

            $files[] = $path;
        }

        sort($files);

        return $files;
    }

    private function addOnRoot(): string
    {
        return dirname(__DIR__, 2);
    }
}
