<?php namespace Hampel\KnownBots\Cli\Command;

use Hampel\KnownBots\Cli\RendersReport;
use Hampel\KnownBots\Option\EmailUserAgents;
use Hampel\KnownBots\Option\FetchNewBots;
use Hampel\KnownBots\Option\SendUserAgents;
use Hampel\KnownBots\Option\ShowBotStats;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Cache;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * What this forum is configured to do about bots, and where each setting comes from - config.php
 * or the options page. It reads and prints; it writes nothing and sends nothing.
 * known-bots:validate is the command that finds out whether any of it works.
 *
 * Several of these answers are not on any one screen in the admin control panel: the two
 * config.php overrides are not in it at all, the bot definitions are three files deep, and the
 * API token is written back by the add-on rather than entered by anyone.
 */
class Config extends AbstractCommand
{
	use RendersReport;

	private const FROM_CONFIG = ' <fg=cyan>(config.php)</>';

	public const CACHE_KEYS = ['maps', 'bots', 'complex', 'ignored', 'browsers'];

	/**
	 * The cron entries this add-on ships, each with a label a reader can act on - the entry ids are
	 * what the admin control panel shows, and say nothing about what the entry does. known-bots:validate
	 * checks this list against what the forum has, so an entry that has gone missing is reported
	 * rather than merely absent from the report. CronEntriesTest keeps it in step with _output.
	 */
	public const CRON_ENTRIES = [
		'hampelKnownBotsFetchBots' => 'fetch new bots',
		'hampelKnownBotsUserAgents' => 'send user agents',
		'hampelKnownBotsPurgeAgent' => 'purge user agents',
	];

	protected function configure()
	{
		$this
			->setName('known-bots:config')
			->setDescription('Show the bot detection settings in use, and where each comes from');
	}

	protected function errorLogPrefix(): string
	{
		return 'Known Bots: ';
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->report = $output;
		$app = \XF::app();

		$this->heading('Known Bots');
		$addOn = $app->addOnManager()->getById('Hampel/KnownBots');
		$this->detail('add-on', $addOn ? (string) $addOn->getJson()['version_string'] : '');
		$this->detail('XenForo', \XF::$version);
		// the log sub-container is a silent no-op without the Monolog add-on, so nothing this
		// add-on logs is recorded anywhere - which is worth saying before any level is discussed
		$this->detail('logging', $app->offsetExists('monolog')
			? 'Monolog Logging Service is installed'
			: $this->muted('no Monolog Logging Service - nothing this add-on logs is recorded'));

		$this->heading('Detection');
		$this->detail('robot statistics', $this->yesNo(ShowBotStats::isEnabled()) . $this->fromOptions());
		$this->detail('fetch new bots', $this->yesNo(FetchNewBots::isEnabled()) . $this->fromOptions());
		$this->detail('store user agents', $this->yesNo(StoreUserAgents::isEnabled()) . $this->fromOptions());
		$days = (int) StoreUserAgents::daysUntilPurge();
		$this->detail('purge after', ($days > 0 ? "{$days} day(s)" : 'never - retention is 0') . $this->fromOptions());

		$this->heading('Bot definitions');
		$this->definitions();

		$this->heading('API');
		$this->detail('send user agents', $this->yesNo(SendUserAgents::isEnabled()) . $this->fromOptions());
		$this->detail('endpoint', $this->getApi()->getEndpoint()
			. ($app->config('knownBotsApi') ? self::FROM_CONFIG : ''));
		$this->detail('validation domain', $this->getApi()->domain()
			. ($app->config('knownBotsDomain') ? self::FROM_CONFIG : ' ' . $this->muted('(the board URL)')));
		// both are credentials: say whether they are set, never what they are. The endpoint above
		// is where they are sent, which is the part worth reading. Read through the option's own
		// value rather than validationToken()/apiToken(), which return null while sending is off -
		// a stored token is worth reporting either way
		$option = (array) SendUserAgents::get();
		$this->detail('validation token', $this->credential($option['validation_token'] ?? null));
		$this->detail('API token', $this->credential($option['api_token'] ?? null));

		$this->heading('Email');
		$this->detail('email user agents', $this->yesNo(EmailUserAgents::isEnabled()) . $this->fromOptions());
		$this->detail('to', EmailUserAgents::isEnabled() ? (string) EmailUserAgents::getAddress() : '');

		$this->heading('Stored user agents');
		$this->storage();

		$this->heading('Cron');
		$this->cron();

		$this->report->writeln('');

		return 0;
	}

	/**
	 * The three stages bot data passes through, in the order detection depends on them: the payload
	 * on the filesystem, and the code cache built from it that detection actually reads. Editing the
	 * payload changes nothing until the cache is rebuilt, which is why both are reported.
	 */
	private function definitions(): void
	{
		$app = \XF::app();
		$path = 'internal-data://knownbots.json';

		if (!$app->fs()->has($path))
		{
			$this->detail('payload', $this->muted("{$path} is not there"));
			$this->detail('built', '');
		}
		else
		{
			$bots = $this->getApi()->loadBots();
			$this->detail('payload', $path . ($bots ? '' : ' ' . $this->muted('- refused, see known-bots:validate')));
			$this->detail('built', $bots ? $this->time($bots['built']) : '');
		}

		$counts = [];
		$missing = [];
		foreach (self::CACHE_KEYS AS $key)
		{
			$data = $this->getCache()->loadBotData($key);
			if ($data === null)
			{
				$missing[] = $key;
				continue;
			}

			$counts[] = count($data) . ' ' . $key;
		}

		$cache = $counts ? implode(', ', $counts) : '';
		if ($missing)
		{
			$cache = ($cache ? $cache . '; ' : '') . 'missing: ' . implode(', ', $missing);
		}
		$this->detail('code cache', $cache);
		$this->detail('last checked', ($last = (int) $this->getCache()->getLastChecked()) ? $this->time($last) : '');
	}

	private function storage(): void
	{
		if (!StoreUserAgents::isEnabled())
		{
			$this->detail('rows', $this->muted('storing is off'));
			return;
		}

		$db = \XF::db();
		$this->detail('rows', (string) (int) $db->fetchOne('SELECT COUNT(*) FROM xf_knownbots_agent'));
		$this->detail('not yet sent', (string) (int) $db->fetchOne('SELECT COUNT(*) FROM xf_knownbots_agent WHERE sent = 0'));
		$this->detail('unidentified', (string) (int) $db->fetchOne('SELECT COUNT(*) FROM xf_knownbots_agent WHERE robot_key IS NULL'));
		$oldest = (int) $db->fetchOne('SELECT MIN(last_updated) FROM xf_knownbots_agent');
		$this->detail('oldest', $oldest ? $this->time($oldest) : '');
	}

	/**
	 * This add-on's own cron entries. The run times of two of them are randomised at install, so
	 * that every site does not reach the API in the same minute - a time that looks arbitrary is
	 * meant to be.
	 */
	private function cron(): void
	{
		$entries = \XF::finder('XF:CronEntry')
			->where('addon_id', 'Hampel/KnownBots')
			->order('entry_id')
			->fetch();

		if (!$entries->count())
		{
			$this->detail('entries', $this->muted('none - see known-bots:validate'));
			return;
		}

		foreach ($entries AS $entry)
		{
			$label = self::CRON_ENTRIES[$entry->entry_id] ?? $entry->entry_id;

			$this->detail($label, ($entry->active ? 'active' : 'not active')
				. ', next ' . ($entry->next_run ? $this->time($entry->next_run) : 'never'));
		}
	}

	private function time(int $timestamp): string
	{
		return gmdate('d M Y H:i', $timestamp) . ' UTC';
	}

	/**
	 * A credential, reported as set or not. detail() renders an empty value as `not set`.
	 */
	private function credential($value): string
	{
		return (string) $value === '' ? '' : 'set' . $this->fromOptions();
	}

	private function fromOptions(): string
	{
		return ' ' . $this->muted('(options)');
	}

	private function yesNo(bool $value): string
	{
		return $value ? 'yes' : 'no';
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
}
