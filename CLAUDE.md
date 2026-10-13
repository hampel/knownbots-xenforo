# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is, and what is documented elsewhere

The **Known Bots** XenForo add-on (`Hampel/KnownBots`) — its own git repository, checked out at
`src/addons/Hampel/KnownBots` inside a XenForo install.

Read the install root's `AGENTS.md` for everything install-wide: `cmd.php` signatures, the
`_output/` ⇄ database import boundary, the version bump that opens a cycle of new work, the
`xf-dev:export` prohibition, and the `.agents/skills/` files that are not auto-registered. None of
that is repeated here.

- `README.md` is customer-facing: every option, every CLI command with worked examples, and the
  privacy statement covering what the add-on sends to the author's API. Change it when behaviour
  visible to a site owner changes.
- `CHANGELOG.md` is hand-maintained, one section per release.
- `BOTS.md` is **generated**, not written — the *Known Bots markdown* admin tool renders the
  current bot list as markdown for pasting in. That is an admin control panel route
  (`admin.php?tools/hampel-knownbots-md`, `XF\Admin\Controller\Tools::actionHampelKnownBotsMd`),
  not a path in this repository. Do not hand-edit the file. Three traps if you regenerate it: the
  entry count is the number of **distinct robot keys in `maps`**, not any count the payload
  reports; the order is by map needle, not by key; and the header date is the payload's own
  `built`, rendered in the admin's timezone, not the day you ran the tool.

## Commands

```bash
composer install                              # vendor/ is gitignored; required before tests
vendor/bin/phpunit
vendor/bin/phpunit --testsuite Unit           # or Feature
vendor/bin/phpunit --filter RobotTest
```

`cmd.php` resolves the install from its own location rather than the working directory, so XF
commands run unchanged from the add-on root — four levels down from the install root, the same
relative path `build.json` uses:

```bash
php ../../../../cmd.php xf-dev:import --addon=Hampel/KnownBots
php ../../../../cmd.php known-bots:test "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
```

Classes in `Cli/Command/` are discovered automatically by XF; all are named `known-bots:*`
(`fetch`, `load`, `reprocess`, `test`, `check-token`, `send`, `email`, `parse`, `import`, and the
self-check pair `config` and `validate`). All but the last two duplicate the logic of a cron method
or admin action so it can be run by hand or from a system cron — a change to one usually needs the
same change in the other. See `README.md` for options.

## Self-checks, and why they set the PHP floor

`known-bots:config` prints every setting and where it comes from; `known-bots:validate` exercises
the real thing. The `xenforo-addon-validate` skill is the authority on the pattern, the exit
contract and how to test it, and `README.md` documents both commands for a site owner. What is
specific to this add-on:

- **It earns the pair on all three of the skill's counts.** Two opaque credentials (the licence
  validation token a customer enters and the API token the add-on writes back), three cron entries
  doing unattended work, and state spread across the options page, `config.php`,
  `internal_data/knownbots.json` and the code cache. Nothing in the admin control panel puts that
  on one screen.
- **`Cli/RendersReport.php` is `Hampel/Monolog`'s file, verbatim apart from the namespace.** Do not
  improve the copy here - fix Monolog's and take it again, which is the whole reason it is copied
  rather than written. `tests/Feature/CommandsTest.php` is the test its own comments refer to.
- **That copy is why `addon.json` requires PHP 7.4**, which is above XenForo 2.2's own floor of
  7.0. The trait uses a typed static property and eight `void` return types. Decided by Simon on
  2026-10-13, following `Hampel/Monolog`, which declares exactly XenForo 2.2.0+ with PHP 7.4+ and
  is the add-on that supplies this one's logging. The cost is real and was accepted knowingly: a
  2.2 forum on PHP 7.3 or older can no longer install the add-on, and 6.2.0 - the release that
  restored 2.2 support eleven days earlier - stays as the last one that runs there.
- **`Config::CRON_ENTRIES` is a hard-coded list of the three entries**, because both commands
  report an entry that has gone missing and a list read from the forum cannot say that something is
  absent. `tests/Unit/CronEntriesTest.php` keeps it in step with `_output/cron_entries/`.
- **`validate` checks the API token without revalidating.** Revalidation writes a new token into
  the option value, and a command that reports on the configuration must not change it.
  `known-bots:check-token --revalidate` is the command that repairs a token.
- **The definitions-are-stale warning is set at two years, deliberately generously.** The API
  publishes a new payload rarely - production's payload was built in October 2025 and was still
  the newest a year later - so a threshold of weeks or months would warn on every healthy forum,
  and an alert that always fires is one nobody reads.

## Architecture

### Bot data: three stages, and detection reads only the last

1. **The API** (`https://knownbots.hampel.io/api/v3/bots`) — `Api/KnownBots.php`, conditional on
   `If-Modified-Since` from the `last-checked` simple-cache value unless forced.
2. **`internal_data/knownbots.json`** — the canonical payload written to the abstracted filesystem
   (`internal-data://knownbots.json`).
3. **`internal_data/code_cache/known_bots/{maps,bots,complex,ignored,browsers}.php`** — the JSON
   exploded into five `var_export`ed PHP files, read back with `include` so opcache holds them.
   `Cache/CodeCache.php` bypasses Flysystem on read for exactly that reason.

**Editing the JSON changes nothing on its own.** `known-bots:load` (or `SubContainer\Api::updateBots()`)
must rebuild the code cache before detection sees it.

The payload shape is pinned: `SubContainer\Api::isValid()` requires `version === 3` and the six keys,
and type-checks every element. Any change to what the API returns is a change here and a version bump
in the payload.

### Detection: `XF/Data/Robot.php`

Extends core `XF:Robot` and runs on **every guest page view**, which is why the ordering in
`userAgentMatchesRobot()` is deliberate and cheap-first:

1. simple substring match against `maps` (lowercased needles) → bot
2. regex match against `complex` → bot
3. logged-in visitor → stop (members are not bots, and this is the hot path)
4. "store user agents" option off → stop (nothing downstream would use the answer)
5. matches an `ignored` regex → stop, do not store
6. matches a `browsers` regex → a real browser, stop
7. otherwise unknown — store it for later analysis and sending

Step 6 works by subtraction: every browser regex is stripped from the string and what remains must be
empty. `getRobotUserAgents()` and `getRobotList()` fall back to the core lists when the cache is empty,
so a broken cache degrades to stock XenForo rather than detecting nothing.

`reprocessUserAgents()` re-runs stored agents against fresh definitions, promoting unknowns to bots and
deleting anything now recognised as ignored or a browser.

### Sub-containers

`Listener::appSetup` (on the `app_setup` event) registers three, reachable as `$this->app['knownbots.api']`
or `\XF::app()->container('knownbots.cache')`:

| Key | Class | Notes |
|---|---|---|
| `knownbots.log` | `SubContainer/Log.php` | PSR-3 shim over the optional Monolog Logging Service add-on, asking it for a channel through `channel()` (its 5.x) or `newChannel()` (4.x), whichever it has. **Silently a no-op when that add-on is absent** — it resolves to a `NullLogger`, so callers never null-check and must never rely on it for user-visible feedback. |
| `knownbots.cache` | `SubContainer/Cache.php` | Wraps `CodeCache` (bot data) and `SimpleCache` (`last-checked`). |
| `knownbots.api` | `SubContainer/Api.php` | The orchestration layer: fetch → validate → store → rebuild cache → reprocess. |

`SubContainer\Api` also honours two `src/config.php` overrides, commented out there by default:
`$config['knownBotsApi']` points at a local API **and flips the HTTP client from untrusted to
trusted** so localhost is reachable; `$config['knownBotsDomain']` fakes the board URL for license
validation. Neither belongs in production.

### Errors: the type carries the retry policy

`Exception/KnownBotsException` → `Request` / `Server` / `Customer` → `Unauthorized` (a subclass of
`Customer`). `Api/KnownBots.php` throws; every caller catches. The convention every caller follows:

- `ServerException` (HTTP 5xx) is transient — log a warning, no XF error log entry.
- `UnauthorizedException` (401) means revalidate. `Service/ApiTokenChecker` and
  `Service/UserAgentSender` implement the same three-step dance: try, revalidate on 401, retry once;
  on the second failure everything is an error.
- Anything else — log an error *and* `\XF::logException()`.

Revalidation writes the new API token back into the option value via `SubContainer\Api::updateApiToken()`
→ `Option\SendUserAgents::set()`. **The API token lives inside the `knownbotsSendUserAgents` option**,
alongside the license validation token the customer entered.

### Options

Never read `\XF::options()` directly — each option has a static wrapper in `Option/` (`isEnabled()`,
`daysUntilPurge()`, `apiToken()`, …) and those are what the rest of the code calls. The API setup
option uses a custom template flow: `XF/Admin/Controller/Option.php` adds `knownbotsApiSetup` /
`knownbotsApiConfigure` actions, gated by `assertKnownbotsApiOption()` checking
`edit_format_params === 'option_template_knownBotsApiToken'`.

### Storage

`xf_knownbots_agent`, primary key `user_agent` as `varbinary(512)`. `Repository/Agent::addUserAgent()`
is hand-written SQL, not the entity, and returns **0 = skipped, 1 = inserted, 2 = updated** — callers
branch on that number. `last_updated` is always truncated to `mktime(0, 0, 0)`, so a user agent seen a
thousand times in a day causes one write; the same truncation is enforced in `Entity/Agent::_preSave()`.
Agents are marked `sent = 1` in one bulk update at the end of `Cron\SendAgents::send()`; a failed
API send aborts before that point, while an e-mail failure is not checked.

### Cron

Three entries in `_output/cron_entries/`, two of which (`FetchBots`, `SendAgents::send`) have their
run times **randomised by `Setup.php` on every install and upgrade** — so customer sites do not all hit
the API in the same minute. Do not "fix" the times in the JSON; they are seeds.

## Traps

- **`build.json` downloads the bot payload from the production API** straight into the build, so a
  release build needs production reachable. It deliberately does not go through `known-bots:fetch`: the
  add-on's client honours a `knownBotsApi` override, so a build on a development install pointed at
  another API server would bundle that server's payload - and if that payload is dated after
  production's, a fresh install never refreshes it, because the fetch cron's `If-Modified-Since` gets a
  `304`. `Setup::postInstall()` loads the bundled snapshot when present, and falls back to fetching
  when installing from `_output/` instead of a zip.
- **The e-mail path is deprecated** (superseded by the API in v6) but still live: `Cron\SendAgents::sendEmail`,
  `Service/UserAgentMailer`, `Option\EmailUserAgents`, `known-bots:email`. It is kept for site owners
  mailing themselves; do not extend it.
- Detection changes need `known-bots:test` against real strings and the `RobotTest` suite; the test
  framework (`hampel/xenforo-test-framework`) mocks the sub-container directly —
  `$this->mock('knownbots.cache', Cache::class)`, `$this->mockRepository(...)`, `$this->fakesSimpleCache()`.
