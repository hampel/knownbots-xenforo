# Testing Known Bots

What this add-on touches, what breaks quietly, what can be checked mechanically, and what
still needs a person.

Commands are written from the add-on root (`src/addons/Hampel/KnownBots`), so `cmd.php` is
four levels up — the same relative path `build.json` uses. It resolves the install from its
own location, not the working directory.

## Surfaces

**Class extensions** (4) — all use the pre-2.3 core class names deliberately, because those
alias forward on 2.3 while the 2.3 names do not exist on 2.2.

| Extends | Why |
|---|---|
| `XF\Data\Robot` | the whole of detection: `userAgentMatchesRobot()`, `getRobotUserAgents()`, `getRobotList()` |
| `XF\Repository\SessionActivity` | `getOnlineCounts()` — adds a `robots` count to the online figures |
| `XF\Admin\Controller\Tools` | the four admin tools (list, detect, new, generate-md) |
| `XF\Admin\Controller\Option` | the two-step API setup flow behind the custom option template |

**Code event listener** (1) — `app_setup` → `Listener::appSetup`, registering three
sub-containers: `knownbots.log`, `knownbots.cache`, `knownbots.api`.

**Template modifications** (2) — both against core widget templates:
`widget_members_online` (a `preg_replace` on the block-footer counter) and
`widget_online_statistics` (a `str_replace` on a multi-line literal). Both are gated on the
"show robot statistics" option.

**Cron** (3) — `FetchBots::fetchBots`, `SendAgents::send`, `SendAgents::purgeAgents`. The run
times of the first two are randomised by `Setup.php` on every install and upgrade, so sites
do not all call the API in the same minute. The times in `_output/cron_entries/` are seeds,
not settings.

**Other artifacts** — 5 options in 1 group, 1 admin permission (`knownbots`), 5 admin
navigation entries, 7 admin templates, 57 phrases.

**Schema** — one table, `xf_knownbots_agent`, keyed on `user_agent` as `varbinary(512)`.

**Filesystem** — `internal-data://knownbots.json` (the payload) and five generated PHP files
under `code-cache://known_bots/`.

**Optional integration** — the Monolog Logging Service add-on (`Hampel/Monolog`). When it is
absent the `knownbots.log` container entry resolves to `null` and every call is a silent
no-op, which `SubContainer\Log::log()` guards for.

## Fragile points

**The template modifications are the thing most likely to break on an XF upgrade, silently.**
Both target core widget templates by matching their markup — one by regex, one by an exact
multi-line literal including its indentation. If XenForo edits either template the
modification stops applying, the add-on keeps working, and the only symptom is that robot
counts quietly vanish from the widgets. Nothing errors. Re-check both against the current
core templates after every XF upgrade.

**Detection reads the code cache, never the JSON.** `internal-data://knownbots.json` is the
payload as fetched; the five files under `code-cache://known_bots/` are what
`userAgentMatchesRobot()` actually consults. Editing the JSON changes nothing until
`known-bots:load` rebuilds the cache. A missing cache degrades to the core bot list rather
than to no detection, so a broken rebuild is easy to miss.

**Detection runs on every guest page view**, which is why the ordering inside
`userAgentMatchesRobot()` is cheap-first and why the cache is `include`d rather than read.
Changes there are performance-sensitive out of proportion to their size.

**The payload shape is pinned.** `SubContainer\Api::isValid()` requires `version === 3` and
type-checks every element of all five lists. Any change to what the API returns is a change
there too, and an invalid payload is rejected wholesale — bots are not partially updated.

**Options are arrays, and the obvious way to set one in a test does nothing.**
`\XF::options()->someOption['enabled'] = false` modifies a copy and is discarded —
`XF\Options` extends `ArrayObject` and its `&offsetGet()` returns a local. Use the test
framework's `setOption()`, which does the read-modify-write. A test that gets this wrong does
not fail; it asserts against the default and passes.

**`build.json`'s `exec` steps cannot fail the build.** The release builder ends each in
`passthru()` and discards the exit status. The first step downloads the bot payload from the
production API straight into the build, and the second prints what it bundled — build date and
counts — or a warning that the file is missing or invalid; neither can stop a bad zip being
written. **Read that line in the build output, and check the zip** — see *Automated* below. The
download deliberately bypasses the add-on's own API client: that client honours a
`knownBotsApi` override, so on a development install pointed at another API server it would
bundle that server's payload instead.

**Dev-only files reach the zip unless `build.json` removes them, and order matters.** The
`rm` lines must precede the `mv *.md` line: after it, a file has already been renamed into the
build root and the `rm` matches nothing while reporting success. Note also that the builder
walks the filesystem, so `.gitignore` does not protect anything, and it excludes an entry
whose own basename starts with a dot but not that entry's children — which is how
`.phpunit.cache/test-results` gets in.

**An upgrade does not delete files the new version removed.** The extractor writes the zip's
entries and never acts on deletions, so a class dropped in a release survives on an upgraded
install while being absent from a fresh one. Inert only as long as nothing references it; if
a removal matters, `Setup::upgrade()` has to do it.

## Automated

```bash
composer install                 # vendor/ is gitignored
vendor/bin/phpunit               # whole suite
vendor/bin/phpunit --testsuite Unit
vendor/bin/phpunit --filter RobotTest
```

`phpunit/phpunit` is pinned to `^12.0` in `require-dev`, and `phpunit.xml` keeps
`failOnPhpunitDeprecation="true"` — which the test framework's own shipped config dropped, because
that attribute arrived in PHPUnit 11 and fails schema validation on 10.5, where `failOnWarning`
then turns it into a failed run with every test passing. The pin is what makes keeping the
attribute safe: this add-on cannot land on 10.5. If the pin is ever loosened, drop the attribute
in the same change.

Read the **count** from a per-suite run, not its exit code: `failOnEmptyTestSuite` fires when
the whole run collects nothing, never when one suite does, so a Feature suite that collects
nothing still exits 0 while Unit passes.

The suite loads only this add-on — `$addonsToLoad` in `tests/TestCase.php`, which is the whole
of it: the framework boots the application itself and installs the filtered extension before
`app_setup` fires, so listeners, class extensions and Composer autoloading are all isolated by
that one property. Without it, booting the app registers every active add-on's Composer
autoloader and a sibling's vendored PHPUnit can be loaded instead of this one's, which kills
the run before the first test.

**What the suite reaches.** Every admin action is dispatched or called directly, so each
action's own `assertAdminPermission('knownbots')` runs; the agent repository's hand-written SQL
and the session-activity count rewrite run against real rows inside a rolled-back transaction;
both widget template modifications are rendered with the option on and off; the CLI commands run
through Symfony's tester; and both cron entry points run with their services mocked.

Two things to know before adding to it:

- **Name the base controller, not this add-on's class.** `callAction('XF:Tools', …)` is correct;
  naming `Hampel\KnownBots\XF\Admin\Controller\Tools` is refused from test framework 5.13.0
  with a `LogicException` naming the class to pass instead. Before 5.13.0 it was worse than an
  error: it needed XenForo to have built the `XFCP_Tools` proxy already, which only an earlier
  dispatch in the same run does, so such a test passed in a full run and failed under `--filter`.
  Naming the base is also the stronger assertion — XenForo resolves it to the most derived class,
  so the action running at all proves the extension applied.
- **`Repository\Agent::addUserAgent()` dates its rows from the system clock**, with
  `mktime(0, 0, 0)` rather than `\XF::$time`, so `setTestTime()` cannot move it. Seed a row
  directly when a test needs a different day. `purgeUserAgents()` does use `\XF::$time`.

**Detection, against real strings:**

```bash
php ../../../../cmd.php known-bots:test "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
php ../../../../cmd.php known-bots:parse --agents /path/to/access.log
```

**The release zip** — the only thing that settles what `build.json` did:

```bash
php ../../../../cmd.php xf-addon:build-release Hampel/KnownBots
unzip -Z1 _releases/<file>.zip | grep -E '(^|/)\.[^/]*'      # want no output
unzip -Z1 _releases/<file>.zip | grep -E 'KnownBots/(tests|vendor|phpunit|composer|CLAUDE|TESTING)'
```

Use `unzip -Z1`, not `unzip -l | awk '{print $4}'` — the latter truncates any path containing
a space and carries the header lines through. The zip root should hold only `README.md`,
`BOTS.md`, `CHANGELOG.md` and `LICENSE.md`; everything else belongs under `upload/`.

**The bundled bot payload must be production's.** A fresh install copies it in and records its build
date as the last check, so a payload dated after production's is never refreshed: the fetch cron's
`If-Modified-Since` gets `304 Not Modified` until production publishes something newer. Compare the
two — the lines must match:

```bash
unzip -p _releases/<file>.zip 'upload/src/addons/Hampel/KnownBots/knownbots.json' \
  | php -r '$d = json_decode(stream_get_contents(STDIN), true); printf("%s %d bots\n", gmdate("Y-m-d H:i", $d["built"]), count($d["bots"]));'
curl -s -H 'Accept: application/json' https://knownbots.hampel.io/api/v3/bots \
  | php -r '$d = json_decode(stream_get_contents(STDIN), true); printf("%s %d bots\n", gmdate("Y-m-d H:i", $d["built"]), count($d["bots"]));'
```

`BOTS.md`, at the zip root, is generated by the "Known Bots markdown" admin tool from the bot data on the
install that runs it — not from the payload in the zip — so regenerate it from an install holding
production's data before releasing.

**Build only with the version cycle open.** The zip is named from `addon.json`'s version
string alone and renames over whatever is already at that path, with no prompt and no backup.
On a tree still carrying the last released version, a test build destroys the real artifact.

**Mail, without sending anything.** The mailer's transport is a constructor argument held by
value, so replacing the container entry after the mailer has been resolved silently has no
effect — `decache('mailer')` is required. XF 2.3 typehints `AbstractTransport`, not
`TransportInterface`, so a collector must extend the abstract class.

```php
$collector = new class extends \Symfony\Component\Mailer\Transport\AbstractTransport {
    public array $sent = [];
    protected function doSend(\Symfony\Component\Mailer\SentMessage $m): void {
        $this->sent[] = $m->getOriginalMessage();
    }
    public function __toString(): string { return 'collector://'; }
};
$config = $app->config(); $config['enableMailQueue'] = false;
$app->container()->set('config', $config);
$app->container()->set('mailer.transport', $collector);
$app->container()->decache('mailer');
```

**Anything keyed on recency** — shift time in-process rather than creating content:
`\XF::$time` is a plain public static, set once at startup, so assigning it is the whole
mechanism. A development forum whose newest content is a year old will short-circuit every
recency path and report "works, no output", which is a false pass.

**Artifact drift** — every `_output/` file should have a `_metadata.json` entry and vice
versa. Hand-created artifact types (options, option groups, permissions, content types) are
the ones that drift, because `xf-make:*` writes the record, the file and the entry together
while `xf-dev:import` reads metadata and never creates it. Repair with
`$app->developmentOutput()->export($entity)` for the affected entity — not by hand-writing an
entry, which produces a correct hash of whatever the file already says, and not with
`xf-dev:rebuild-metadata`, which takes no arguments and so rewrites every add-on in the
install while skipping the missing entries it appears to address.

## Needs a human

**How the widgets look on a real page.** That both template modifications apply, and that the
robot count appears only with "show robot statistics" enabled, is covered by
`WidgetRobotStatsTest`. What it cannot settle is the rendered page around them — navigation,
header and footer come from XenForo's own app classes rather than from these templates — so
after a XenForo upgrade, still look at Members Online and Online Statistics in a browser.

**The API setup flow against a real licence.** Its shape is covered by `AdminApiSetupTest` with
the API mocked: the guard that stops another option being driven through it, each branch of the
setup step, and the token being written back into the option value. What no test can do is post
a genuine XenForo licence validation token to the author's API, which writes to a live service.

**The upgrade path from the last published release.** Install the previous release zip, then
upgrade to the new one, and confirm `Setup.php`'s steps run. A development install will not do
this on its own: `xf-addon:upgrade` sees `_output/` and imports from the working copy instead
of the zip, so the upgrade completes without ever reading the release's data. A disposable
install built from the release zip is the right target, but only with development mode **off**
in its config. In development mode, any add-on-owned entity saved during install is written out
to the installed add-on's `_output/` unless that save switches it off. This add-on's `Setup`
does switch it off for the two cron entries it randomises, but a sandbox should not depend on
every save doing so: once `_output/` exists, the upgrade takes the `xf-dev:import` shortcut and
deletes anything `_output/` lacks. Confirm the upgrade printed `Importing add-on data` and did
**not** print `All data imported`; the second is the shortcut.

**After an upgrade, look for files the new version removed** — they will still be present.
List what the release dropped, then grep the source and `_output/class_extensions/` and
`_output/code_event_listeners/` for those class names. A hit is a live reference to a file the
new version does not ship.

**Anything on XenForo 2.2.** The suite proves 2.3 only, and structurally cannot do otherwise:
the test framework needs PHP 8.3, and the tests lean on 2.3 behaviour - route dispatch resolving
the controller names 2.3 renamed, and `$xf` inside a render. So every automated check here is a
statement about 2.3.

Three things in the add-on differ by version, and a 2.2 sandbox is what settles them:

- **the CLI commands**, which extend the add-on's own `Cli\Command\AbstractCommand` because
  XenForo's arrived in 2.3. Run all nine on 2.2 and check `cmd.php list` shows them;
- **the e-mail attachment**, which branches on the mail stack - Symfony Mailer on 2.3,
  SwiftMailer on 2.2. Only the 2.3 branch is reachable from the suite, since Swift is not
  installed on a 2.3 forum at all, so the 2.2 branch has no automated cover anywhere;
- **`Setup`**, where the upgrade clean-up is gated on the running version. Both sides of that
  gate *are* covered by `SetupUpgradeCleanUpTest`, which moves `\XF::$versionId`, but a real 2.2
  install is what proves the install and upgrade complete.

Both dist archives are on this machine, so the sandbox is cheap; use PHP 8.2 or lower for a 2.2
instance.

**The upgrade from the XenForo 1 add-on, which no install on hand can present.** `addon.json`
carries `legacy_addon_id`, so on a forum that came through XenForo's own 1.5 to 2.x upgrade with
the old add-on still recorded, `AddOn::__construct()` matches that installed row and this add-on
**upgrades** rather than installs. The XF1 add-on declared `version_id="1"`, so every version-gated
step runs: `upgrade5000031Step1()` creates the agent table, and `postUpgrade()` sees a previous
version of 1 and runs the v6 e-mail cleanup as well.

Reasoning says the risk is low and says why: the XF1 add-on was a single `load_class` listener over
`XenForo_Session` with no tables of its own, so the one schema step creates a table that cannot
already exist, and the e-mail cleanup reads an option that exists with its default by then. What no
test here can settle is the starting state itself — whether the core upgrade leaves that add-on row
in place, and what it does to an option or a user field belonging to it. A snapshot of a real forum
already upgraded to 2.x, with the add-on still at its 1.x state, restored into a disposable
install, is the only honest test. Treat the path as untested rather than as low risk.

**Interaction with other add-ons.** The suite loads only this add-on and the development
install has one fixed set, so a conflict with another add-on first appears in production. A
disposable install with both is the only way to test a specific pairing. Relevant here because
this add-on extends four core classes, two of which — the admin Tools and Option controllers —
are commonly extended, so on a real forum it is usually one link in a chain.

**Judgement.** Whether a newly detected user agent is really a bot, and whether a detection
regex is too broad, are not decidable by test. The "Test bot detection" admin tool exists for
exactly this.
