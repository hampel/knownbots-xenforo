<?php namespace Tests\Feature;

use Hampel\KnownBots\Cli\Command\Config;
use Hampel\KnownBots\Cli\Command\Validate;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use Hampel\Testing\Concerns\UsesDatabaseTransactions;
use Tests\TestCase;

/**
 * known-bots:config - what this forum is told - and known-bots:validate - whether it can do it.
 *
 * Both read a good deal of real state, so the sub-containers are mocked to a known-good forum in
 * setUp() and each test breaks one thing. The cron checks read the real xf_cron_entry rows, which
 * is the point of them, so the transaction trait is here to roll back the one test that deletes
 * an entry.
 */
class CommandsTest extends TestCase
{
    use UsesDatabaseTransactions;

    /** A user agent the fixture's browser patterns strip to nothing - the subtraction in step 6. */
    private const BROWSER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)'
        . ' Chrome/131.0.0.0 Safari/537.36';

    /** @var array the five code cache datasets, as the command will read them back */
    private $cacheData;

    /** @var int */
    private $built;

    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesErrors();
        $this->fakesMail();

        $this->built = \XF::$time - 86400;

        $this->cacheData = [
            'maps' => ['examplebot' => 'example-bot'],
            'bots' => ['example-bot' => ['name' => 'Example Bot', 'url' => null]],
            'complex' => ['widgetbot/[0-9]' => 'widget-bot'],
            'ignored' => ['^ignore-me'],
            'browsers' => [
                'Mozilla/5\.0',
                '\(Windows NT 10\.0; Win64; x64\)',
                'AppleWebKit/537\.36',
                '\(KHTML, like Gecko\)',
                'Chrome/[\d.]+',
                'Safari/537\.36',
            ],
        ];

        // a forum where everything works: the options at their defaults, no development overrides,
        // and a code cache built from the payload on disk
        $this->setOption('knownbotsShowBotStats', true);
        $this->setOption('knownbotsFetchNewBots', true);
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 90]);
        $this->setOption('knownbotsSendUserAgents', ['enabled' => false, 'validation_token' => '', 'api_token' => '']);
        $this->setOption('knownbotsEmailUserAgents', ['enabled' => false, 'email' => '']);

        // this development install really does set both, so a baseline has to clear them
        $this->setConfig('knownBotsApi', null);
        $this->setConfig('knownBotsDomain', null);

        $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });

        $this->mockCache();
        $this->mockApi();
    }

    // ---------------------------------------------------------------- config

    public function test_config_shows_each_setting_and_where_it_comes_from()
    {
        // the endpoint value comes from the client; the (config.php) label comes from the key
        // being set at all, which is the distinction the line exists to draw
        $this->setConfig('knownBotsApi', 'http://bots.example.test/api');

        [$code, $output] = $this->invoke(new Config());

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/store user agents \.+ yes \(options\)/', $output);
        $this->assertMatchesRegularExpression('/purge after \.+ 90 day\(s\) \(options\)/', $output);
        $this->assertMatchesRegularExpression('/endpoint \.+ https:\/\/bots\.example\.test\/api \(config\.php\)/', $output);
        $this->assertMatchesRegularExpression('/send user agents \.+ no \(options\)/', $output);
        $this->assertStringContainsString('1 maps, 1 bots, 1 complex, 1 ignored, 6 browsers', $output);
        // an empty value reads as "not set", never as a blank that looks like a setting that is fine
        $this->assertMatchesRegularExpression('/API token \.+ not set/', $output);
    }

    public function test_config_says_a_credential_is_set_without_printing_it()
    {
        $this->setOption('knownbotsSendUserAgents', [
            'enabled' => true,
            'validation_token' => 'made-up-validation-token',
            'api_token' => '28|made-up-api-token',
        ]);

        [$code, $output] = $this->invoke(new Config());

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/validation token \.+ set \(options\)/', $output);
        $this->assertMatchesRegularExpression('/API token \.+ set \(options\)/', $output);
        $this->assertStringNotContainsString('made-up-validation-token', $output);
        $this->assertStringNotContainsString('made-up-api-token', $output);
    }

    public function test_config_reports_a_stored_token_while_sending_is_off()
    {
        // validationToken() and apiToken() answer null while the option is disabled, so reading
        // through them would report "not set" for a token that is sitting right there
        $this->setOption('knownbotsSendUserAgents', [
            'enabled' => false,
            'validation_token' => 'made-up-validation-token',
            'api_token' => '',
        ]);

        [, $output] = $this->invoke(new Config());

        $this->assertMatchesRegularExpression('/send user agents \.+ no/', $output);
        $this->assertMatchesRegularExpression('/validation token \.+ set/', $output);
    }

    public function test_config_writes_and_sends_nothing()
    {
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->expects('loadBots')->andReturns($this->payload());
            $mock->expects('getEndpoint')->andReturns('https://bots.example.test/api');
            $mock->expects('domain')->andReturns('https://forum.example.test/');
            // nothing that changes state
            $mock->expects('fetchBots')->never();
            $mock->expects('storeBots')->never();
            $mock->expects('updateBots')->never();
            $mock->expects('updateApiToken')->never();
            $mock->expects('removeBots')->never();
        });

        $rows = (int) $this->app()->db()->fetchOne('SELECT COUNT(*) FROM xf_knownbots_agent');

        $this->invoke(new Config());

        $this->assertSame($rows, (int) $this->app()->db()->fetchOne('SELECT COUNT(*) FROM xf_knownbots_agent'));
        $this->assertMailNotSent();
        $this->assertNoExceptionsLogged();
    }

    // -------------------------------------------------------------- validate

    public function test_validate_passes_on_a_healthy_forum()
    {
        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertStringContainsString('Validated', $output);
        $this->assertStringNotContainsString('[fail]', $output);
        $this->assertStringNotContainsString('[warn]', $output);
        $this->assertMatchesRegularExpression('/\[ ok \] a known bot +.*detected as example-bot/', $output);
        $this->assertMatchesRegularExpression('/\[ ok \] a real browser/', $output);
        $this->assertMatchesRegularExpression('/\[ ok \] code cache/', $output);
        $this->assertMatchesRegularExpression('/\[ ok \] cache is current/', $output);
    }

    public function test_validate_unattended_skips_the_api_check_and_sends_nothing()
    {
        $this->setOption('knownbotsSendUserAgents', [
            'enabled' => true, 'validation_token' => 'made-up-token', 'api_token' => 'made-up-api-token',
        ]);
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->expects('loadBots')->andReturns($this->payload());
            $mock->expects('getEndpoint')->andReturns('https://bots.example.test/api');
            $mock->expects('domain')->andReturns('https://forum.example.test/');
            // the one call that reaches the API
            $mock->expects('checkApiToken')->never();
        });

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[    \] API token check +skipped - --unattended/', $output);
        $this->assertMailNotSent();
    }

    public function test_the_api_token_check_runs_by_default_and_reports_what_the_api_said()
    {
        $this->setOption('knownbotsSendUserAgents', [
            'enabled' => true, 'validation_token' => 'made-up-token', 'api_token' => 'made-up-api-token',
        ]);
        $this->mockService('Hampel\KnownBots:ApiTokenChecker', function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->expects('checkToken')->with(false)->once()->andReturns('forum.example.test');
        });

        [$code, $output] = $this->invoke(new Validate());

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[ ok \] API token check +accepted by the API for domain forum\.example\.test/', $output);
    }

    /**
     * A refused token is a failure, and it must not be repaired: revalidation writes a new token
     * into the options, and a command that reports on the configuration does not change it.
     */
    public function test_a_refused_api_token_fails_without_revalidating()
    {
        $this->setOption('knownbotsSendUserAgents', [
            'enabled' => true, 'validation_token' => 'made-up-token', 'api_token' => 'made-up-api-token',
        ]);
        $this->mockService('Hampel\KnownBots:ApiTokenChecker', function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->expects('checkToken')->with(false)->once()->andReturns(false);
            $mock->expects('getError')->andReturns('unauthorized');
        });

        [$code, $output] = $this->invoke(new Validate());

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression('/\[fail\] API token check +the API refused it: unauthorized/', $output);
        $this->assertStringContainsString('Validation failed', $output);
    }

    public function test_a_missing_payload_fails()
    {
        $this->mockFs('internal-data', function ($mock)
        {
            $mock->expects('has')->andReturns(false);
        });

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression('/\[fail\] payload +.*is not there - run known-bots:fetch/', $output);
        // and the run carried on past it
        $this->assertStringContainsString('[ ok ] code cache', $output);
    }

    public function test_a_payload_the_add_on_refuses_fails()
    {
        $this->mockApi([]); // loadBots() answers [] for a payload that fails the version checks

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression('/\[fail\] payload +.*is corrupt or not version 3/', $output);
    }

    /**
     * Detection reads only the code cache, so a payload that never reached it leaves the forum
     * running stock XenForo detection - which looks exactly like the add-on working.
     */
    public function test_a_code_cache_that_was_not_built_fails()
    {
        unset($this->cacheData['browsers'], $this->cacheData['complex']);
        $this->mockCache();

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression('/\[fail\] code cache +complex, browsers not built/', $output);
        $this->assertStringContainsString('known-bots:load', $output);
    }

    public function test_a_cache_built_from_a_different_payload_warns()
    {
        $this->mockCache($this->built - 86400 * 30);

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);
        [$strict] = $this->invoke(new Validate(), ['--unattended' => true, '--strict' => true]);

        $this->assertSame(0, $code, 'a warning is not a failure');
        $this->assertSame(2, $strict, '--strict is what a monitor passes to hear about one');
        $this->assertMatchesRegularExpression('/\[warn\] cache is current +the code cache was built from a different payload/', $output);
    }

    public function test_definitions_that_have_stopped_arriving_warn()
    {
        $this->built = \XF::$time - 86400 * 1000;
        $this->mockApi();
        $this->mockCache($this->built);

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[warn\] age +the definitions were built 1000 days ago/', $output);
    }

    /**
     * A needle that is not lowercased can never match. userAgentMatchesSimpleBot() lowercases the
     * user agent and then looks for the needle as written - "assume all search strings are already
     * lowercase", as its own comment says - so one capital letter in a payload silently removes
     * that bot from detection, with every count and every screen still reporting it as present.
     */
    public function test_a_known_bot_that_is_not_detected_fails()
    {
        $this->cacheData['maps'] = ['ExampleBot' => 'example-bot'];
        $this->mockCache();
        $this->mockApi();

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression(
            "/\[fail\] a known bot +'ExampleBot' should be example-bot and was not detected at all/",
            $output
        );
    }

    public function test_a_browser_the_definitions_do_not_recognise_warns()
    {
        $this->cacheData['browsers'] = ['NothingLikeAChromeUserAgent'];
        $this->mockCache();

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        // detection still answers correctly for bots, so this is not a failure - but real visitors
        // get stored as unidentified and sent to the author
        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[warn\] a real browser +.*not recognised as a browser/', $output);
    }

    // ------------------------------------------------------------------ cron

    /**
     * The check that is hardest to do any other way. An upgrade that ran against a partial
     * development copy of this add-on deleted the purge entry outright, and no screen in the admin
     * control panel says that an entry which should exist is gone.
     */
    public function test_a_missing_cron_entry_fails()
    {
        $db = $this->app()->db();
        $this->assertTrue($db->inTransaction(), 'not inside a transaction - refusing to delete a cron entry');
        $db->delete('xf_cron_entry', 'entry_id = ?', 'hampelKnownBotsPurgeAgent');

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression('/\[fail\] purge user agents +the hampelKnownBotsPurgeAgent cron entry is missing/', $output);
        $this->assertStringContainsString('xf:addon-rebuild', $output);
        // the other two still report
        $this->assertStringContainsString('[ ok ] fetch new bots', $output);
    }

    public function test_an_inactive_cron_entry_warns()
    {
        $db = $this->app()->db();
        $this->assertTrue($db->inTransaction(), 'not inside a transaction - refusing to change a cron entry');
        $db->update('xf_cron_entry', ['active' => 0], 'entry_id = ?', 'hampelKnownBotsUserAgents');

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[warn\] send user agents +the cron entry is not active/', $output);
    }

    public function test_fetching_turned_off_warns_that_the_definitions_will_never_change()
    {
        $this->setOption('knownbotsFetchNewBots', false);

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[warn\] fetch new bots +the option is off/', $output);
    }

    // --------------------------------------------------------------- storage

    public function test_retention_of_zero_warns_that_nothing_is_ever_purged()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => true, 'days' => 0]);

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[warn\] retention +0 - stored user agents are never purged/', $output);
    }

    public function test_storing_turned_off_skips_the_table_checks()
    {
        $this->setOption('knownbotsStoreUserAgents', ['enabled' => false, 'days' => 90]);

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[    \] table +storing user agents is off/', $output);
    }

    // ----------------------------------------------------------------- email

    public function test_an_email_list_with_no_valid_address_fails()
    {
        $this->setOption('knownbotsEmailUserAgents', ['enabled' => true, 'email' => 'not-an-address, nor-this-one']);

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression('/\[fail\] addresses +no valid address among 2 entries/', $output);
        $this->assertMailNotSent();
    }

    public function test_an_email_list_with_one_bad_address_warns_and_names_it()
    {
        $this->setOption('knownbotsEmailUserAgents', [
            'enabled' => true, 'email' => 'bots@example.test; not-an-address',
        ]);

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[warn\] addresses +1 valid, and 1 that will be skipped/', $output);
        $this->assertStringContainsString('not-an-address', $output);
    }

    // ------------------------------------------------------------------- API

    public function test_sending_without_a_validation_token_fails()
    {
        $this->setOption('knownbotsSendUserAgents', ['enabled' => true, 'validation_token' => '', 'api_token' => '']);

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression('/\[fail\] validation token +not set, so a token can never be issued/', $output);
    }

    public function test_the_development_config_overrides_warn()
    {
        $this->setConfig('knownBotsApi', 'http://bots.example.test/api');
        $this->setConfig('knownBotsDomain', 'https://licensed.example.test/');

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(0, $code);
        $this->assertMatchesRegularExpression('/\[warn\] endpoint +.*knownBotsApi.*development/', $output);
        $this->assertMatchesRegularExpression('/\[warn\] validation domain +.*knownBotsDomain.*development/', $output);
        $this->assertStringContainsString('does not belong on a live forum', $output);
    }

    /**
     * No check may end the run: a validation that stops at its first problem hides the rest, on
     * the one occasion the whole list was wanted.
     */
    public function test_a_check_that_throws_does_not_end_the_run()
    {
        $this->mock('knownbots.api', Api::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
            $mock->expects('loadBots')->andThrow(new \RuntimeException('cannot read the payload'));
        });

        [$code, $output] = $this->invoke(new Validate(), ['--unattended' => true]);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('[fail] definitions', $output);
        $this->assertStringContainsString('cannot read the payload', $output);
        $this->assertStringContainsString('Cron', $output, 'the run carried on past it');

        $logged = array_column($this->getErrorFake()->getExceptions(), 'message');
        $this->assertCount(1, $logged);
        $this->assertStringStartsWith('Known Bots: known-bots:validate - definitions: ', $logged[0]);
    }

    // ----------------------------------------------------------------------

    private function payload() : array
    {
        return [
            'version' => 3,
            'built' => $this->built,
            'maps' => $this->cacheData['maps'] ?? [],
            'bots' => $this->cacheData['bots'] ?? [],
            'complex' => $this->cacheData['complex'] ?? [],
            'ignored' => $this->cacheData['ignored'] ?? [],
            'browsers' => $this->cacheData['browsers'] ?? [],
        ];
    }

    /**
     * @param int|null $lastChecked what the cache records the payload it was built from as - the
     *                             payload's own build time on a forum where known-bots:load ran
     */
    private function mockCache($lastChecked = null) : void
    {
        $data = $this->cacheData;
        $last = $lastChecked === null ? $this->built : $lastChecked;

        $this->mock('knownbots.cache', Cache::class, function ($mock) use ($data, $last)
        {
            $mock->shouldReceive('loadBotData')->andReturnUsing(function ($key) use ($data)
            {
                return $data[$key] ?? null;
            });
            $mock->shouldReceive('getLastChecked')->andReturn($last);
        });
    }

    /**
     * @param array|null $bots what loadBots() answers - [] is a payload the add-on refused
     */
    private function mockApi($bots = null) : void
    {
        $payload = $bots === null ? $this->payload() : $bots;

        $this->mock('knownbots.api', Api::class, function ($mock) use ($payload)
        {
            $mock->shouldIgnoreMissing();
            $mock->shouldReceive('loadBots')->andReturn($payload);
            $mock->shouldReceive('getEndpoint')->andReturn('https://bots.example.test/api');
            $mock->shouldReceive('domain')->andReturn('https://forum.example.test/');
        });
    }

    /**
     * Not run(): PHPUnit's own TestCase::run() is final, so a helper of that name here does not
     * even load - the same collision the command classes are tested against.
     */
    private function invoke($command, array $input = []) : array
    {
        $tester = $this->runConsoleCommand($command, $input, ['decorated' => false]);

        return [$tester->getStatusCode(), $tester->getDisplay()];
    }
}
