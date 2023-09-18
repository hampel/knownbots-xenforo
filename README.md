Known Bots for XenForo 2.x
==========================

This XenForo 2.x addon adds additional definitions for bot detection in sessions.

By [Simon Hampel](mailto:simon@hampelgroup.com).

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

Verion 5 is a complete rewrite from previous versions - with bot detection now up to 15x faster than the previous 
method. Bot detection is also more sophisticated - with a secondary regex-based detection system to help identify
complex bot strings that can't be matched using the simple text string matches used by default.

If no bots are detected, the addon will check the user agent against a list of valid browser regex strings - and if not
considered to be a valid user-driven browser, it will store the information in the database temporarily before sending
the information back to the addon author via email for further analysis.

Previously, the system only sent back user agents which matched key words: bot|crawl|spider - this new detection method
is significantly more comprehensive and allows much greater accuracy for detecting new bots that may not identify 
themselves using the traditional keywords.

### Options

#### Show robot statistics

Enable to show robot statistics in the sidebar widgets

#### Fetch new bots

Enable to automatically fetch new bot identifiers from the [KnownBots API](https://knownbots.hampel.io/api/bots) 
maintained by the addon author. If you disable this option, you must set up your own system to update the known bots 
defnitions - see the Command Line Interface section for options to assist here.

#### Store user agents in database and purge after

Enable to store unknown and bot user agents in the database for further analysis. This should be used in conjunction
with the "Email user agents" option to send unknown user agents back to the addon author for further analysis and
identification of new bots. You may also manually send user agent information to the addon author via the addon 
discussion thread - use the "List detected bots" tool to show unknown user agents.

With this option enabled, you may also choose how long to retain user agent records before they are automatically
purged from the database. This uses a "last seen" mechanism to maintain the list of recently seen bots - only bots not
recently seen will be purged. For busy sites, consider lowering this value from the default 90 days, to reduce the size
of data stored. Set days to zero to never purge user agent data (not recommended).

#### Email user agents

Enable to periodically email user agent strings to the addon author for further analysis to detect previously 
unidentified bots. This option only has effect is the "Store user agents in database" option is also enabled.

By default, the system sends emails directly to the addon author at `knownbots@hampel.io` - but you may have it send 
email to any address if you want to check what information the emails contain. You may manually forward the email to 
the above address, or else post user agent strings in the support thread for analysis.

You may specify multiple email addresses separated by commas - all addresses after the first one are bcc'd a copy of
the email, so specify `knownbots@hampel.io` first and then any other email addresses you want to receive the email at, 
the recipient list will not be visible to the addon author.

Please read the privacy statement below.

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

The addon looks for the file `internat_data/knownbots.json` - if you download it manually and place the updated version
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

### Privacy

#### Executive summary

Depending on the options enabled, there is some information collected about sites using this 
addon by the addon author - but it is only ever used for anonymous analytics and for troubleshooting purposes and never 
disclosed to third parties, nor is it ever used for marketing or any purposes other than for the operation of this 
addon.

The addon author commits to being a "good citizen" in regard to how data is used and collected and will gladly answer 
any questions about how our systems work. We also provide options to disable or bypass certain functionality if you
remain uncomfortable about using the systems as designed.

#### Details

With the **"Fetch new bots"** option enabled, the addon will automatically, send a request to the [KnownBots API](https://knownbots.hampel.io/api/bots),
downloading an updated list of bot definitions. You may query that API directly at any time to see what is contained in
the data returned.

API calls by the addon are made using the standard "untrusted" HTTP client built into XenForo, which means they are 
forwarded through a proxy server if you have one configured. Standard web server log files on the API server will 
contain information about requests made, including the IP address of your server (or proxy if used), and your forum 
name as supplied in the user agent of the XenForo HTTP client.

HTTP server log information is used solely for analytics and troubleshooting purposes and is never made available to 
third parties.

By enabling the **"Store user agents in database"** and **"Email user agents"** options, the following will occur:

1. user agents that have not been detected as a bot or as a valid browser, will be stored in the database
2. once per day an email will be sent to the email addresses defined in the options, containing a list of these user 
   agents
3. these user agent strings will be collated by the addon author and used to identify new bots, or to refine the valid
   browser detection system

The emails sent contain only a list of user agent strings. There is no information contained which may allow the 
recipient to fingerprint a specific user who has accessed your site - there is no IP address or user information
contained in the data.

Other than a list of user agent strings, the only information contained in the email will be those automatically added
to the email header by the forum mailer and SMTP servers.

Emails are sent to `knownbots@hampel.io` by default and any emails sent to this address will _only_ be used for the 
purpose of identifying new bots to add to this addon. Email addresses will never be sold or added to any marketing 
lists - not even ours.

If there are issues detected from the emails you are sending us, we may email you establish communication - 
but that will be on a case-by-case basis and only for the purposes of troubleshooting the operation of this addon.

You may check the information contained in the emails by adding your own email address to the addon options - the text
field accepts a comma-separated list of addresses. For example, set the email address to:

`knownbots@hampel.io, me@example.com`

Make sure the first email address is the addon-author (if specified) - all email addresses after the first entry will 
be bcc'd - and so the addon author will not have any visibility of who else has received the emails.

Note that emails received by the addon author at the `knownbots@hampel.io` address are automatically processed by [API
injection](https://support.sparkpost.com/docs/tech-resources/inbound-email-relay-webhook) from our email service 
provider (SparkPost) and are never stored in an inbox or read by a human.

The email sender address is logged for troubleshooting purposes - but is never used for any other purpose.

Do not email this address with questions about the addon or to communicate with the addon author - any emails not
containing valid user agent information are silently discarded by the automated handler. Please contact the addon 
author via the XenForo forum addon discussion thread or private message. 
