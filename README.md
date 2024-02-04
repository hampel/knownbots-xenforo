Known Bots for XenForo 2.x
==========================

This XenForo 2.x addon adds additional definitions for bot detection in sessions.

By [Simon Hampel](https://xenforo.com/community/members/sim.4264/).

### Requirements

This addon requires PHP 7.0 or higher and works on XenForo 2.x

### Usage

When you look at Current Visitors, you'll see additional robots identified - also look at the "Robots" list on that page
 http://www.example.com/community/online/?type=robot

A count of robots online is added to the Members online and Online statistics widgets. This can be disabled using the
options.

There are several tools provided in the admin area Tools section to help manage the data for this addon:

 * __List detected bots__ will show the most recent 100 bots detected on your site, plus any additional user agents that
   have not yet been identified. This tool requires the "Store user agents in database" option to be enabled.
 * __Test bot detection__ allows you to enter a user agent string and have the system check whether it detects it as a bot
   against the current known definitions
 * __Show list of Known Bots__ lists all the bots included in the definition file with links to more information where 
   available

### Change log

Verion 6 is a complete rewrite from previous versions - with bot detection now up to 15x faster than the previous 
method. Bot detection is also more sophisticated - with a secondary regex-based detection system to help identify
complex bot strings that can't be matched using the simple text string matches used by default.

If no bots are detected, the addon will check the user agent against a list of valid browser regex strings - and if not
considered to be a valid user-driven browser, it will store the information in the database temporarily before sending
the information back to the addon author via email for further analysis.

Previously, the system only sent back user agents which matched key words: bot|crawl|spider - this new detection method
is significantly more comprehensive and allows much greater accuracy for detecting new bots that may not identify 
themselves using the traditional keywords.

Also new in this version is the deprecation of the email sending facility - new user agents are now sent directly via 
API, with authentication facilitated by the XenForo license validation system and very easy to configure.

### Options

#### Show robot statistics

Enable to show robot statistics in the sidebar widgets

#### Fetch new bots

Enable to automatically fetch new bot identifiers from the [KnownBots API](https://knownbots.hampel.io/api/bots) 
maintained by the addon author. If you disable this option, you must set up your own system to update the known bots 
defnitions - see the Command Line Interface section for options to assist here.

#### Store user agents in database and purge after

Enable to store unknown and bot user agents in the database for further analysis. This should be used in conjunction
with the "Send user agent via API" and/or "Email user agents" option to send unknown user agents back to the addon 
author for further analysis and identification of new bots. You may also manually send user agent information to the 
addon author via the addon discussion thread - use the "List detected bots" tool to show unknown user agents.

With this option enabled, you may choose how long to retain user agent records before they are automatically
purged from the database. This uses a "last seen" mechanism to maintain the list of recently seen bots - only bots not
recently seen will be purged. For busy sites, consider lowering this value from the default 90 days, to reduce the size
of data stored. Set days to zero to never purge user agent data (not recommended).

#### Send user agents via API

New in version 6, enabling this option will send unidentified user agents back to the addon author for further analysis
and identification of new bots.

To configure the API, enter the License validation token for your site, found in the 
[XenForo customer](https://xenforo.com/customers/) interrface. The validation token will be sent to the 
[XenForo customer validation API](https://xenforo.com/customer-api/) and if valid, an KnownBots API token will be 
generated and returned back for subsequent authentication purposes.

With a validated license, the authentication process is automatic. API tokens are regenerated every 28 days and are
re-authenticated automatically. Customer details are automatically purged from the KnownBots database after 30 days of 
inactivity (see privacy details below). Regenerating your license validation token will automatically cause API 
revalidation to fail and customer details to be purged - unless you re-configure the addon options with the new license
validation token.

#### Email user agents

**_Note:_ emailing user agents to the addon author is now deprecated**. The email interface will remain operational for a
short period to allow time for addon users to upgrade to version 6, but will soon be deactivated at which point emails 
sent to the `knownbots@hampel.io` address will start bouncing back as undeliverable. 

This option will remain available to allow users to periodically send emails to an address of their choosing for 
monitoring newly detected user agents. This option only has effect if the "Store user agents in database" option is 
also enabled. You may specify multiple email addresses separated by commas.

### Logging

Install the [Monolog Logging Service](https://xenforo.com/community/resources/monolog-logging-service.6080/) addon to log information about emails sent and API queries made. 

### Command Line Interface

There are CLI tools provided which may be helpful for certain use-cases.

#### Fetch Bots

Fetch new bots via the API. Use this to run your own cron - does the same task that the supplied XenForo Cron job does.

```bash
$ php cmd.php known-bots:fetch
```
Use the `-f` option to force updates, bypassing the "last updated" flag and re-fetching the latest bot definitions.

If using this command via your own cron - be sure to disable the supplied XenForo Cron job 
titled "Known Bots: Fetch New Bots from API".

#### Load Bots

Load bots data from the `knownbots.json` file already on the filesystem.

```bash
$ php cmd.php known-bots:load
```

The addon looks for the file `internal_data/knownbots.json` - if you download it manually and place the updated version
in this location, you may then execute the above command to load the data without calling the API.

#### Reprocess User Agents

Cycle through user agents stored in the database and update the definitions based on the latest bot detection data.

```bash
$ php cmd.php known-bots:reprocess
```
By default, only "unknown" user agents are reprocessed. Use the `-a` option to reprocess all known and unknown user 
agents - useful if a bot was misidentified but has been updated in the latest API data.

#### Test Bots

Test the supplied user agent string and identify whether it is detected as a bot.

```bash
$ php cmd.php known-bots:test {user-agent}
```
Don't forget to quote user agent strings that contain spaces. For example:

```bash
$ php cmd.php known-bots:test "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
Found robot: [ahrefs]
Title: AhrefsBot
```

Use the `-s` option to save your user agent strings to the database for further processing.

#### Check API Token
(New in v6) Test if the KnownBots API Token is valid. 

```bash
$ php cmd.php known-bots:check-token
```
Use the `-r` or `--revalidate` option to automatically attempt to revalidate and generate a new API token if 
athentication fails.

#### Send agents
(New in v6) Send newly detected user agents to the KnownBots API. Does the same thing the cron job does - but can be run externally
via a system cron or manually for testing purposes.

```bash
$ php cmd.php known-bots:send
```

#### Parse Log Files

Reads web server log file information and lists all detected bots.

```bash
$ php cmd.php known-bots:parse {file}
```

Use the `-a` or `--agents` option to simply list all user agents found in the supplied log file - no bot detection occurs

Command will also accept data from stdin if you specify a hyphen as the file name.

Note that this tool assumes you are using standard "Apache" log file format, which is used by most web servers.

Usage examples:

List all user agents. Invalid (or no) data will be returned if you are not using standard Apache log file format.
```bash
$ php cmd.php known-bots:parse --agents /var/log/nginx/xenforo/xenforo.access.log
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36
Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1
Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Mobile Safari/537.36
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36 Edg/94.0.992.37
...
```

List bots:
```bash
$ php cmd.php known-bots:parse /var/log/nginx/xenforo/xenforo.access.log
[postman                  ] PostmanRuntime/7.28.4
[guzzlehttp               ] GuzzleHttp/7
[postman                  ] PostmanRuntime/7.29.0
[curl                     ] curl/7.68.0
...
```

Examples of reading data from stdin:
```bash
$ cat /var/log/nginx/xenforo/xenforo.access.log | php cmd.php known-bots:parse -
...

$ php cmd.php known-bots:parse - < /var/log/nginx/xenforo/xenforo.access.log
...

$ tail -f /var/log/nginx/xenforo/xenforo.access.log | php cmd.php known-bots:parse -
```

### Privacy

#### Privacy Summary

Depending on the options enabled, there is some information collected about sites using this 
addon by the addon author - but it is only ever used for anonymous analytics and for troubleshooting purposes and never 
disclosed to third parties, nor is it ever used for marketing or any purposes other than for the operation of this 
addon.

Options are provided to disable or bypass certain functionality if you remain uncomfortable about using the systems as 
designed.

#### Details

With the **"Fetch new bots"** option enabled, the addon will automatically, send a request to the [KnownBots API](https://knownbots.hampel.io/api/bots),
downloading an updated list of bot definitions. You may query that API directly at any time to see what is contained in
the data returned.

API calls by the addon are made using the standard "untrusted" HTTP client built into XenForo, which means they are 
forwarded through a proxy server if you have one configured. Standard web server log files on the API server will 
contain information about requests made, including the IP address of your server (or proxy if used), and your forum 
name as supplied in the user agent of the XenForo HTTP client. For example:

```
2400:8907:e001:xx::xxx - - [15/Aug/2023:00:00:07 +0000] "GET /api/bots?since=1691729327 HTTP/1.1" 200 119647 "-" "XenForo/2.x (https://www.example.com)"
```

HTTP server log information is used solely for analytics and troubleshooting purposes and is never made available to 
third parties.

Enabling the **"Send user agents via API"** setting requires the submission of a XenForo license validation token, 
retrieved from your customer account on the XenForo website. This token is intended to be supplied to 3rd party addon
authors for the purposes of validating XenForo licenses. In this case, it is being utilised as a simple authentication
mechanism. You may regenerate your license validation token at any time via the XenForo customer interface, which will
automatically cause authentication against the KnownBots API to fail once the existing API token expires.

API tokens expire every 28 days and are automatically regenerated, provided the XenForo license validation token remains
valid. Customer details retrieved from the [XenForo license validation API](https://xenforo.com/customer-api/) are 
automatically purged from the database after 30 days of inactivity.

When validating licenses, only the license validation token and forum URL are sent to the KnownBots API. Once validation
is complete, only the generated API token and a list of new user agents are sent to the server. Web sever logs similar
to those described above are generated for all API calls.

As of version 6, no information is sent to the addon author via email.
