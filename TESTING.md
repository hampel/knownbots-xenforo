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
`passthru()` and discards the exit status. The first step fetches bot definitions from the
live API and the second copies the result into the zip, so a network failure yields a release
whose bundled `knownbots.json` is stale or missing, with no error. **Always unzip a release
and look** — see *Automated* below.

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

Read the **count** from a per-suite run, not its exit code: `failOnEmptyTestSuite` fires when
the whole run collects nothing, never when one suite does, so a Feature suite that collects
nothing still exits 0 while Unit passes.

The suite loads only this add-on (`$addonsToLoad` in `tests/TestCase.php`, plus the
`xf-addons` option passed by `tests/CreatesApplication.php` — both are needed, either alone
does nothing). Without it, booting the app registers every active add-on's Composer
autoloader and a sibling's vendored PHPUnit can be loaded instead of this one's, which kills
the run before the first test.

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

**The two widget template modifications actually applying.** The rendered markup could in
principle be asserted, but there is no route-dispatching test harness here, so today this is
a browser check: with "show robot statistics" enabled and at least one robot in
`xf_session_activity`, the Members Online and Online Statistics widgets should show a robot
count. Check after every XenForo upgrade — this is the surface most likely to have broken.

**The API setup flow**, at the `knownbotsSendUserAgents` option. It posts a real XenForo
licence validation token to the author's API and stores the returned token back into the
option value. It cannot be exercised without a real licence, and it writes to a live service.

**The upgrade path from the last published release.** Install the previous release zip, then
upgrade to the new one, and confirm `Setup.php`'s steps run. A development install will not
do this on its own: `xf-addon:upgrade` sees `_output/` and imports from the working copy
instead of the zip, so the upgrade completes without ever reading the release's data. A
disposable install built from the release zip has no `_output/` and therefore takes the real
path with nothing to configure.

**After an upgrade, look for files the new version removed** — they will still be present.
List what the release dropped, then grep the source and `_output/class_extensions/` and
`_output/code_event_listeners/` for those class names. A hit is a live reference to a file the
new version does not ship.

**Interaction with other add-ons.** The suite loads only this add-on and the development
install has one fixed set, so a conflict with another add-on first appears in production. A
disposable install with both is the only way to test a specific pairing. Relevant here because
this add-on extends four core classes, two of which — the admin Tools and Option controllers —
are commonly extended, so on a real forum it is usually one link in a chain.

**Judgement.** Whether a newly detected user agent is really a bot, and whether a detection
regex is too broad, are not decidable by test. The "Test bot detection" admin tool exists for
exactly this.
