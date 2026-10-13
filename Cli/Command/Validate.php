<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Cli\RendersReport;
use Hampel\KnownBots\Option\EmailUserAgents;
use Hampel\KnownBots\Option\FetchNewBots;
use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\Service\ApiTokenChecker;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\XF\Data\Robot;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Whether this forum can actually detect bots: the definitions are there and current, the code
 * cache detection reads was built from them, a known bot is recognised and a real browser is not,
 * the cron entries that keep all of it up to date exist, and the API credential works.
 *
 * The API token check is a send - it reaches the author's API with this forum's credential - and
 * it is the only proof that the credential works, so it runs by default. --unattended skips it,
 * for a deploy or cron gate nobody is watching; the checks that are facts about the configuration
 * still run. It deliberately does not revalidate on a refusal: revalidation writes a new token
 * into the options, and a command that reports on the configuration must not change it. Use
 * known-bots:check-token --revalidate for that.
 *
 * Exit code: 1 if any check failed, 0 otherwise - a warning does not fail, so a gate can rely on
 * it. --strict makes a warning exit 2, for a monitor that wants to hear of one; it changes nothing
 * else, so a monitor passes --unattended as well.
 */
class Validate extends AbstractCommand
{
	use RendersReport;

	/**
	 * A user agent every current definition set recognises as a real browser. If this stops being
	 * recognised the browsers list has a problem, and the consequence is that real visitors get
	 * stored and sent as unidentified agents.
	 */
	private const BROWSER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)'
		. ' Chrome/131.0.0.0 Safari/537.36';

	/**
	 * Definitions older than this have stopped arriving, whatever the fetch cron says.
	 *
	 * Deliberately generous, because the API publishes a new payload rarely - production's current
	 * payload was built in October 2025 and was still the newest a year later - so a threshold of
	 * weeks or months would warn on every healthy forum, and an alert that always fires is one
	 * nobody reads. Two years is well past any normal gap and is reached only where the fetch has
	 * genuinely never worked, which on a fresh install means the payload bundled with the release.
	 */
	private const STALE_DAYS = 730;

	protected function configure()
	{
		$this
			->setName('known-bots:validate')
			->setDescription('Check that bot detection works: the definitions, the code cache, and the API token')
			->addOption('unattended', null, InputOption::VALUE_NONE,
				'Skip the API token check, which reaches the API - for a gate nobody is watching')
			->addOption('strict', null, InputOption::VALUE_NONE,
				'Exit 2 when there are warnings and no failures, instead of 0');
	}

	protected function errorLogPrefix(): string
	{
		return 'Known Bots: ';
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->report = $output;
		$unattended = (bool) $input->getOption('unattended');

		$this->checkSection('Environment');
		$this->probe('environment', function ()
		{
			$this->environment();
		});

		// facts about the configuration: these report under --unattended too, so they stay above
		// the one check that reaches the API
		$this->checkSection('Bot definitions');
		$bots = $this->probe('definitions', function ()
		{
			return $this->definitions();
		});

		$this->checkSection('Detection');
		$this->probe('detection', function ()
		{
			$this->detection();
		});

		$this->checkSection('Stored user agents');
		$this->probe('storage', function ()
		{
			$this->storage();
		});

		$this->checkSection('Cron');
		$this->probe('cron', function ()
		{
			$this->cron();
		});

		$this->checkSection('Email');
		$this->probe('email', function ()
		{
			$this->email();
		});

		$this->checkSection('API');
		$this->probe('api settings', function ()
		{
			$this->apiSettings();
		});

		if ($unattended)
		{
			$this->checkSkip('API token check', 'skipped - --unattended');
		}
		else
		{
			$this->probe('API token check', function ()
			{
				$this->apiToken();
			});
		}

		$this->report->writeln('');
		if ($this->checksFailed())
		{
			$this->report->writeln('<error>Validation failed - bot detection is not working as configured</error>');
		}
		else
		{
			$this->report->writeln($this->checksWarned() ? '<comment>Validated, with warnings</comment>' : '<info>Validated</info>');
		}

		return $this->checkExitCode((bool) $input->getOption('strict'));
	}

	private function environment(): void
	{
		$this->checkOk('XenForo', \XF::$version);

		// the log sub-container is a silent no-op without the Monolog add-on. Nothing is broken by
		// that - logging is optional here - but nothing this add-on logs is recorded either, which
		// is worth knowing before anyone goes looking for a log entry
		\XF::app()->offsetExists('monolog')
			? $this->checkOk('logging', 'the Monolog Logging Service add-on is installed')
			: $this->checkSkip('logging', 'no Monolog Logging Service - nothing this add-on logs is recorded');
	}

	/**
	 * The three stages bot data passes through. Detection reads only the last of them, so a payload
	 * that never reached the code cache leaves the forum running stock XenForo detection - which
	 * looks like the add-on working and finds a fraction of the bots.
	 *
	 * @return array the stored payload, or [] if there is not a usable one
	 */
	private function definitions(): array
	{
		$app = \XF::app();
		$path = 'internal-data://knownbots.json';

		if (!$app->fs()->has($path))
		{
			$this->checkFail('payload', "{$path} is not there - run known-bots:fetch");
			$bots = [];
		}
		else
		{
			// loadBots() answers [] for a payload that fails the version and type checks
			$bots = $this->getApi()->loadBots();
			$bots
				? $this->checkOk('payload', count($bots['bots']) . ' bots, built ' . $this->time($bots['built']))
				: $this->checkFail('payload', "{$path} is corrupt or not version 3 - run known-bots:fetch --force");
		}

		if ($bots)
		{
			$age = (int) floor((\XF::$time - $bots['built']) / 86400);
			$age > self::STALE_DAYS
				? $this->checkWarn('age', "the definitions were built {$age} days ago - check the fetch cron"
					. ' and try known-bots:fetch --force')
				: $this->checkOk('age', "built {$age} day(s) ago");
		}

		$missing = [];
		$counts = [];
		foreach (Config::CACHE_KEYS AS $key)
		{
			$data = $this->getCache()->loadBotData($key);
			$data === null ? $missing[] = $key : $counts[$key] = count($data);
		}

		if ($missing)
		{
			$this->checkFail('code cache', implode(', ', $missing)
				. ' not built - detection has fallen back to stock XenForo. Run known-bots:load');
		}
		else
		{
			$this->checkOk('code cache', implode(', ', array_map(function ($key, $count)
			{
				return "{$count} {$key}";
			}, array_keys($counts), $counts)));
		}

		// updateBots() sets last-checked to the payload's own build time as it rebuilds the cache,
		// so the two disagreeing means the cache was built from a different payload than the one
		// on disk - editing the JSON by hand is the usual way to get here
		$lastChecked = (int) $this->getCache()->getLastChecked();
		if (!$bots || $missing)
		{
			$this->checkSkip('cache is current', 'nothing to compare');
		}
		elseif ($lastChecked == $bots['built'])
		{
			$this->checkOk('cache is current', 'built from the payload on disk');
		}
		else
		{
			$this->checkWarn('cache is current', 'the code cache was built from a different payload ('
				. ($lastChecked ? $this->time($lastChecked) : 'never recorded')
				. ') than the one on disk - run known-bots:load');
		}

		return $bots;
	}

	/**
	 * Detection exercised against the definitions this forum actually holds, on the two answers
	 * that matter: a known bot is a bot, and a real browser is not. Nothing is stored - the save
	 * argument is false - so this leaves no row behind.
	 */
	private function detection(): void
	{
		$robot = $this->getRobot();

		$maps = $this->getCache()->loadBotData('maps');
		if (!$maps)
		{
			$this->checkSkip('a known bot', 'no bot definitions to test against');
		}
		else
		{
			$needle = (string) array_key_first($maps);
			$expected = $maps[$needle];
			$matched = $robot->userAgentMatchesRobot($needle, false);

			$matched === $expected
				? $this->checkOk('a known bot', "'{$needle}' detected as {$expected}")
				: $this->checkFail('a known bot', "'{$needle}' should be {$expected} and was "
					. ($matched === '' ? 'not detected at all' : var_export($matched, true)));
		}

		// a browser must not be a bot, and must be recognised as a browser. The second half is what
		// decides whether an unknown agent is stored, and userAgentMatchesRobot() cannot reach it
		// with saving off, so it is asked directly
		$asRobot = $robot->userAgentMatchesRobot(self::BROWSER, false);
		if ($asRobot !== '')
		{
			$this->checkFail('a real browser', 'a current Chrome user agent was detected as the bot '
				. var_export($asRobot, true));
		}
		elseif ($robot->userAgentMatchesValidBrowser(self::BROWSER))
		{
			$this->checkOk('a real browser', 'a current Chrome user agent is recognised as a browser');
		}
		else
		{
			// detection still answers correctly for bots, so this is not a failure - but real
			// visitors will be stored as unidentified and sent to the author
			$this->checkWarn('a real browser', 'a current Chrome user agent is not recognised as a browser'
				. ' - real visitors will be stored as unidentified. Run known-bots:fetch --force');
		}
	}

	private function storage(): void
	{
		if (!StoreUserAgents::isEnabled())
		{
			$this->checkSkip('table', 'storing user agents is off');
			return;
		}

		$db = \XF::db();
		$rows = (int) $db->fetchOne('SELECT COUNT(*) FROM xf_knownbots_agent');
		$unsent = (int) $db->fetchOne('SELECT COUNT(*) FROM xf_knownbots_agent WHERE sent = 0');
		$this->checkOk('table', "{$rows} row(s), {$unsent} not yet sent");

		$days = (int) StoreUserAgents::daysUntilPurge();
		$days > 0
			? $this->checkOk('retention', "{$days} day(s)")
			: $this->checkWarn('retention', '0 - stored user agents are never purged, so the table only grows');
	}

	/**
	 * The three cron entries, by name. An upgrade that ran against a partial development copy of
	 * this add-on deleted the purge entry outright, and nothing on any screen says an entry that
	 * should exist is gone - so this is the check that is hardest to do any other way.
	 */
	private function cron(): void
	{
		$entries = \XF::finder('XF:CronEntry')
			->where('entry_id', array_keys(Config::CRON_ENTRIES))
			->fetch();

		foreach (Config::CRON_ENTRIES AS $entryId => $label)
		{
			$entry = $entries[$entryId] ?? null;

			if (!$entry)
			{
				$this->checkFail($label, "the {$entryId} cron entry is missing"
					. ' - run xf:addon-rebuild Hampel/KnownBots');
				continue;
			}

			if (!$entry->active)
			{
				$this->checkWarn($label, 'the cron entry is not active, so it will never run');
				continue;
			}

			$this->checkOk($label, 'active, next ' . ($entry->next_run ? $this->time($entry->next_run) : 'never'));
		}

		// the fetch cron is the only thing that keeps the definitions current on an unattended site
		if (!FetchNewBots::isEnabled())
		{
			$this->checkWarn('fetch new bots', 'the option is off, so the definitions will never be'
				. ' updated - detection will get worse as new bots appear');
		}
	}

	/**
	 * The email address list, which may hold several addresses separated by commas or semicolons.
	 * An invalid entry is skipped by the mailer rather than failing the send, so the only way to
	 * learn about one is to look - which is what this does, without sending anything.
	 */
	private function email(): void
	{
		if (!EmailUserAgents::isEnabled())
		{
			$this->checkSkip('addresses', 'emailing user agents is off');
			return;
		}

		$validator = \XF::app()->validator(\XF\Validator\Email::class);

		$valid = [];
		$invalid = [];
		// the same split as Service\UserAgentMailer::getValidAddresses()
		foreach (preg_split('/[,;]/', (string) EmailUserAgents::getAddress()) AS $address)
		{
			$address = trim($address);
			if ($address === '')
			{
				continue;
			}

			$validator->isValid($address) ? $valid[$address] = $address : $invalid[] = $address;
		}

		if (!$valid)
		{
			$this->checkFail('addresses', $invalid
				? 'no valid address among ' . count($invalid) . ' entries, so nothing is ever emailed'
				: 'no address set, so nothing is ever emailed');
		}
		elseif ($invalid)
		{
			$this->checkWarn('addresses', count($valid) . ' valid, and ' . count($invalid)
				. ' that will be skipped and reported in the server error log: ' . implode(', ', $invalid));
		}
		else
		{
			$this->checkOk('addresses', count($valid) . ' address(es), one email each');
		}
	}

	/**
	 * What the API is configured with, short of using it. The two config.php overrides are
	 * development-only and point this forum somewhere other than production, which is worth saying
	 * out loud on any forum that has them.
	 */
	private function apiSettings(): void
	{
		$app = \XF::app();

		if (!SendUserAgents::isEnabled())
		{
			$this->checkSkip('sending', 'sending user agents is off');
		}
		else
		{
			$option = (array) SendUserAgents::get();
			$this->checkOk('sending', 'on');

			((string) ($option['validation_token'] ?? '')) === ''
				? $this->checkFail('validation token', 'not set, so a token can never be issued or renewed'
					. ' - enter the XenForo licence validation token in the options')
				: $this->checkOk('validation token', 'set');

			((string) ($option['api_token'] ?? '')) === ''
				? $this->checkWarn('stored API token', 'not set yet - it is issued on the first send or token check')
				: $this->checkOk('stored API token', 'set');
		}

		$endpoint = (string) $this->getApi()->getEndpoint();
		$app->config('knownBotsApi')
			? $this->checkWarn('endpoint', "{$endpoint}, from \$config['knownBotsApi'] - a development"
				. ' override that also trusts the URL. It does not belong on a live forum')
			: $this->checkOk('endpoint', $endpoint);

		$app->config('knownBotsDomain')
			? $this->checkWarn('validation domain', $this->getApi()->domain() . ", from \$config['knownBotsDomain']"
				. ' - a development override. It does not belong on a live forum')
			: $this->checkOk('validation domain', (string) $this->getApi()->domain());
	}

	/**
	 * The one check that reaches the API. Only the API's answer proves the stored token works, so
	 * it runs by default and --unattended skips it.
	 */
	private function apiToken(): void
	{
		if (!SendUserAgents::isEnabled())
		{
			$this->checkSkip('API token check', 'sending user agents is off');
			return;
		}

		$db = \XF::db();
		$errorsBefore = (int) $db->fetchOne('SELECT MAX(error_id) FROM xf_error_log');

		$checker = $this->getApiTokenCheckerService();
		$checker->setApiToken(SendUserAgents::apiToken());
		$checker->setValidationToken(SendUserAgents::validationToken());

		// false: do not revalidate. Revalidation writes a new token into the options, and this
		// command reports on the configuration rather than changing it
		$domain = $checker->checkToken(false);

		$failed = $domain === false;
		$failed
			? $this->checkFail('API token check', 'the API refused it: ' . strtok((string) $checker->getError(), "\n")
				. ' - try known-bots:check-token --revalidate')
			: $this->checkOk('API token check', "accepted by the API for domain {$domain}");

		$errors = $db->fetchAll('SELECT message FROM xf_error_log WHERE error_id > ? ORDER BY error_id', $errorsBefore);
		if (!$errors)
		{
			$this->checkOk('server error log', 'nothing logged during the check');
		}
		elseif ($failed)
		{
			// the refusal above logs its own exception, so these rows are already reported
			$this->checkSkip('server error log', count($errors) . ' new, accounted for by the failure above');
		}
		else
		{
			$this->checkFail('server error log', count($errors) . ' new: ' . strtok($errors[0]['message'], "\n"));
		}
	}

	private function time(int $timestamp): string
	{
		return gmdate('d M Y H:i', $timestamp) . ' UTC';
	}

	/**
	 * @return Api
	 */
	protected function getApi()
	{
		return \XF::app()->container('knownbots.api');
	}

	/**
	 * @return Cache
	 */
	protected function getCache()
	{
		return \XF::app()->container('knownbots.cache');
	}

	/**
	 * @return Robot
	 */
	protected function getRobot()
	{
		return \XF::app()->data('XF:Robot');
	}

	/**
	 * @return ApiTokenChecker
	 */
	protected function getApiTokenCheckerService()
	{
		return \XF::service('Hampel\KnownBots:ApiTokenChecker');
	}
}
