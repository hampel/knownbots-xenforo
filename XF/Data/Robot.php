<?php namespace Hampel\KnownBots\XF\Data;

use Hampel\KnownBots\Repository\UserAgentCache;

class Robot extends XFCP_Robot
{
	public function getRobotUserAgents()
	{
		$newBots = [
			'7siters' => '7siters',
			'accompanybot' => 'accompanybot',
			'adbeat' => 'adbeat',
			'adsbot-google-mobile' => 'adsbot-google-mobile',
			'adsbot-google' => 'adsbot-google', // this should come after adsbot-google-mobile
			'adscanner' => 'adscanner',
			'ahrefsbot' => 'ahrefs',
			'aiohttp' => 'aiohttp',
			'archive.org_bot' => 'archive.org',
			'archivebot' => 'archiveteam',
			'aspiegelbot' => 'aspiegelbot',
			'apache-httpclient' => 'apache-httpclient',
			'applebot' => 'applebot',
			'awariosmartbot' => 'awariosmartbot',
			'bingpreview' => 'bing',
			'binglocalsearch' => 'bing',
			'blexbot' => 'blexbot',
			'bomborabot' => 'bomborabot',
			'brandverity' => 'brandverity',
			'brokenlinkcheck' => 'brokenlinkcheck',
			'buck' => 'buck',
			'bytespider' => 'bytespider',
			'cliqzbot' => 'cliqzbot',
			'coccocbot' => 'coccocbot',
			'cocolyzebot' => 'cocolyzebot',
			'companybook-crawler' => 'companybook',
			'contacts-crawler' => 'scrapinghub',
			'contxbot' => 'contxbot',
			'crawler4j' => 'crawler4j',
			'cukbot' => 'cukbot',
			'curl' => 'curl',
			'dalvik' => 'dalvik',
			'dataprovider' => 'dataprovider',
			'demandbasepublisheranalyzer' => 'demandbasepublisheranalyzer',
			'discordbot' => 'discordbot',
			'dispatch' => 'dispatch',
			'domainappender' => 'domainappender',
			'domainstatsbot' => 'domainstatsbot',
			'dotbot' => 'dotbot',
			'duckduckbot' => 'duckduckbot',
			'duplexweb-google' => 'duplexweb-google',
			'e.ventures' => 'eventures',
			'exabot' => 'exabot',
			'ezooms' => 'ezooms',
			'feedfetcher-google' => 'feedfetcher-google',
			'garlikcrawler' => 'garlikcrawler',
			'getintent crawler' => 'getintent',
			'go-http-client' => 'go-http-client',
			'google-read-aloud' => 'google-read-aloud',
			'google favicon' => 'google-favicon',
			'grapeshotcrawler' => 'grapeshotcrawler',
			'httrack' => 'httrack',
			'ias-ir' => 'admantx',
			'ias-or' => 'admantx',
			'ias-va' => 'admantx',
			'indeedbot' => 'indeedbot',
			'internetnz' => 'internetnz',
			'just-crawling' => 'just-crawling',
			'knowknot' => 'knowknot',
			'leikibot' => 'leikibot',
			'linguee' => 'linguee',
			'linkdexbot' => 'linkdexbot',
			'linkfluence' => 'linkfluence',
			'linkpadbot' => 'linkpadbot',
			'ltx71' => 'ltx71',
			'mail.ru' => 'mail.ru',
			'mbcrawler' => 'mbcrawler',
			'mediatoolkitbot' => 'mediatoolkitbot',
			'megaindex' => 'megaindex',
			'mfibot' => 'mfibot',
			'microsoft office protocol discovery' => 'microsoft-office',
			'microsoft office' => 'microsoft-office',
			'mixrankbot' => 'mixrankbot',
			'mojeekbot' => 'mojeekbot',
			'monsidobot' => 'monsidobot',
			'nekstbot' => 'nekstbot',
			'nlnz_iaharvester' => 'nlnz',
			'omgili' => 'omgili',
			'outclicksbot' => 'outclicksbot',
			'panscient' => 'panscient',
			'pcore-http' => 'pcore-http',
			'php' => 'php',
			'pinterestbot' => 'pinterest',
			'postmanruntime' => 'postman',
			'python-requests' => 'python-requests',
			'quantcastbot' => 'quantcastbot',
			'quick-crawler' => 'scrapinghub',
			're-re studio' => 're-re studio',
			'relemindbot' => 'relemindbot',
			'scrapy' => 'scrapy',
			'seekport crawler' => 'seekport',
			'seewithkids' => 'seewithkids',
			'semanticscholarbot' => 'semanticscholarbot',
			'semrushbot' => 'semrushbot',
			'seokicks-robot' => 'seokicks-robot',
			'serendeputybot' => 'serendeputybot',
			'seznambot' => 'seznambot',
			'sitesucker' => 'sitesucker',
			'smtbot' => 'smtbot',
			'spbot' => 'spbot',
			'special_archiver' => 'special_archiver',
			'sqlmap' => 'sqlmap',
			'startmebot' => 'startmebot',
			'statuscake/virusscanner' => 'statuscake',
			'symfony browserkit' => 'symfony browserkit',
			'symfony2 browserkit' => 'symfony browserkit',
			'tracemyfile' => 'tracemyfile',
			'trendictionbot' => 'trendictionbot',
			'triplecheckerrobot' => 'triplecheckerrobot',
			'tweetmemebot' => 'tweetmemebot',
			'twitterbot' => 'twitterbot',
			'ttd-content' => 'ttd-content',
			'um-ln' => 'ubermetrics-technologies',
			'uipbot' => 'uipbot',
			'uptimerobot' => 'uptimerobot',
			'v-bot' => 'voyager',
			'vegi bot' => 'vegibot',
			'voluumdsp-content-bot' => 'voluumdsp',
			'wget' => 'wget',
			'winhttp' => 'winhttp',
			'wonderbot' => 'wonderbot',
			'wotbox' => 'wotbox',
			'xenu link sleuth' => 'xenu link sleuth',
			'telegrambot' => 'telegrambot',
			'zoominfobot' => 'zoominfobot',
		];

		return array_merge(parent::getRobotUserAgents(), $newBots);
	}

	public function userAgentMatchesRobot($userAgent)
	{
		$robotName = parent::userAgentMatchesRobot($userAgent);

		if (!empty($robotName))
		{
			// if we already found a robot, we're done
			return $robotName;
		}

		if (preg_match(
			'#(bot|crawl|spider)#i',
			strtolower($userAgent),
			$match
		))
		{
			// generic bot/crawler/spider match ... better check for false positives

			$falsePositives = [
				'idbot553plus build/mra58k', // Logicom BOT phones
				'b bot 50 build/mra58k',
				'b bot 550 build/mra58k',
				'idbot553 build/mra58k',
				'm bot 551 build/mra58k',
				'power bot build/mra58k',
				'id bot 53 build/nrd90m',
				'id bot 53+ build/nrd90m',
				'm bot 51 build/nrd90m',
				'm bot 54 build/nrd90m',
				'm bot 60 build/nrd90m',
			];

			if (preg_match(
				'#(' . implode('|', array_map('preg_quote', $falsePositives)) . ')#i',
				strtolower($userAgent)
			))
			{
				// anything that matches our false positive list is considered not a bot
				return '';
			}
			else
			{
				// we found an actual generic bot/crawler/spider match

				// add it to the cache
				/** @var UserAgentCache $repo */
				$repo = \XF::repository('Hampel\KnownBots:UserAgentCache');
				$repo->addUserAgent($userAgent);

				// return generic bot string
				return $match[1];
			}
		}
		else
		{
			return '';
		}
	}

	public function getRobotList()
	{
		$newBots = [
			'7siters' => [
				'title' => '7Siters',
				'link' => 'https://7ooo.ru/siters/',
			],
			'accompanybot' => [
				'title' => 'AccompanyBot',
				'link' => '',
			],
			'adbeat' => [
				'title' => 'Adbeat',
				'link' => 'http://adbeat.com/policy',
			],
			'adsbot-google-mobile' => [
				'title' => 'AdsBot-Google-Mobile',
				'link' => 'http://www.google.com/mobile/adsbot.html',
			],
			'adsbot-google' => [
				'title' => 'AdsBot-Google',
				'link' => 'http://www.google.com/adsbot.html',
			],
			'adscanner' => [
				'title' => 'AdScanner',
				'link' => 'http://seocompany.store'
			],
			'ahrefs' => [
				'title' => 'Ahrefs',
				'link' => 'https://ahrefs.com/robot',
			],
			'aiohttp' => [
				'title' => 'aiohttp',
				'link' => 'https://github.com/aio-libs/aiohttp',
			],
			'apache-httpclient' => [
				'title' => 'Apache-HttpClient',
				'link' => 'https://hc.apache.org/',
			],
			'applebot' => [
				'title' => 'Applebot',
				'link' => 'http://www.apple.com/go/applebot',
			],
			'archive.org' => [
				'title' => 'Archive-It',
				'link' => 'http://archive-it.org/files/site-owners.html'
			],
			'archiveteam' => [
				'title' => 'Archive Team',
				'link' => 'https://www.archiveteam.org/index.php?title=ArchiveBot'
			],
			'aspiegelbot' => [
				'title' => 'AspiegelBot',
				'link' => 'https://aspiegel.com/about'
			],
			'awariosmartbot' => [
				'title' => 'AwarioSmartBot',
				'link' => 'https://awario.com/bots.html'
			],
			'blexbot' => [
				'title' => 'BLEXBot Crawler',
				'link' => 'http://webmeup-crawler.com/'
			],
			'bomborabot' => [
				'title' => 'BomboraBot',
				'link' => 'http://www.bombora.com/bot'
			],
			'bot' => [
				'title' => 'Generic Bot',
				'link' => ''
			],
			'brandverity' => [
				'title' => 'BrandVerity',
				'link' => 'http://www.brandverity.com/why-is-brandverity-visiting-me'
			],
			'brokenlinkcheck' => [
				'title' => 'BrokenLinkCheck.com',
				'link' => 'http://brokenlinkcheck.com/'
			],
			'buck' => [
				'title' => 'Buck',
				'link' => 'https://app.hypefactors.com/media-monitoring/about.html'
			],
			'bytespider' => [
				'title' => 'Bytespider',
				'link' => 'https://zhanzhang.toutiao.com/'
			],
			'cliqzbot' => [
				'title' => 'Cliqzbot',
				'link' => 'http://cliqz.com/company/cliqzbot'
			],
			'coccocbot' => [
				'title' => 'Coc Coc Bot',
				'link' => 'http://help.coccoc.com/searchengine'
			],
			'cocolyzebot' => [
				'title' => 'Cocolyzebot',
				'link' => ' https://cocolyze.com/bot'
			],
			'companybook' => [
				'title' => 'Companybook',
				'link' => 'https://www.companybooknetworking.com/'
			],
			'contxbot' => [
				'title' => 'Amazon contxbot',
				'link' => 'https://affiliate-program.amazon.com/help/node/topic/GT98G5PPRERNVZ2C'
			],
			'crawl' => [
				'title' => 'Generic Crawler',
				'link' => ''
			],
			'crawler4j' => [
				'title' => 'crawler4j',
				'link' => 'http://code.google.com/p/crawler4j/'
			],
			'cukbot' => [
				'title' => 'CukBot',
				'link' => 'https://www.companiesintheuk.co.uk/bot.html'
			],
			'curl' => [
				'title' => 'curl',
				'link' => 'https://curl.haxx.se/'
			],
			'dalvik' => [
				'title' => 'Dalvik',
				'link' => 'https://source.android.com/devices/tech/dalvik/'
			],
			'dataprovider' => [
				'title' => 'Dataprovider',
				'link' => 'https://www.dataprovider.com/'
			],
			'demandbasepublisheranalyzer' => [
				'title' => 'DemandbasePublisherAnalyzer',
				'link' => 'http://www.demandbase.com'
			],
			'discordbot' => [
				'title' => 'Discordbot',
				'link' => 'https://discordapp.com'
			],
			'dotbot' => [
				'title' => 'DotBot',
				'link' => 'http://www.opensiteexplorer.org/dotbot'
			],
			'dispatch' => [
				'title' => 'dispatch',
				'link' => ''
			],
			'domainappender' => [
				'title' => 'DomainAppender',
				'link' => 'http://www.profound.net/domainappender'
			],
			'domainstatsbot' => [
				'title' => 'DomainStatsBot',
				'link' => 'https://domainstats.com/pages/our-bot'
			],
			'duckduckbot' => [
				'title' => 'DuckDuckBot',
				'link' => 'http://duckduckgo.com/duckduckbot.html'
			],
			'duplexweb-google' => [
				'title' => 'Duplex on the Web',
				'link' => 'https://support.google.com/webmasters/answer/9467408'
			],
			'eventures' => [
				'title' => 'e.ventures Investment Crawler',
				'link' => 'https://www.eventures.vc/'
			],
			'exabot' => [
				'title' => 'Exabot',
				'link' => 'http://www.exabot.com/go/robot'
			],
			'ezooms' => [
				'title' => 'Ezooms',
				'link' => 'mailto:ezooms.bot@gmail.com'
			],
			'feedfetcher-google' => [
				'title' => 'Google Feedfetcher',
				'link' => 'http://www.google.com/feedfetcher.html'
			],
			'garlikcrawler' => [
				'title' => 'GarlikCrawler',
				'link' => 'http://garlik.com/'
			],
			'getintent' => [
				'title' => 'GetIntent Crawler',
				'link' => 'http://getintent.com/bot.html'
			],
			'grapeshotcrawler' => [
				'title' => 'GrapeshotCrawler',
				'link' => 'http://www.grapeshot.co.uk/crawler.php'
			],
			'go-http-client' => [
				'title' => 'Go-http-client',
				'link' => 'https://golang.org/pkg/net/http/'
			],
			'google-favicon' => [
				'title' => 'Google Favicon',
				'link' => 'https://support.google.com/webmasters/answer/1061943'
			],
			'google-read-aloud' => [
				'title' => 'Google Real Aloud',
				'link' => 'https://support.google.com/webmasters/answer/1061943'
			],
			'httrack' => [
				'title' => 'HTTrack',
				'link' => 'http://www.httrack.com/'
			],
			'admantx' => [
				'title' => 'ADmantX Service Fetcher',
				'link' => 'https://www.admantx.com/service-fetcher.html'
			],
			'indeedbot' => [
				'title' => 'IndeedBot',
				'link' => 'http://indeedbot.com/'
			],
			'internetnz' => [
				'title' => 'InternetNZ Webscan',
				'link' => 'https://zonescan.nzrs.net.nz'
			],
			'just-crawling' => [
				'title' => 'Just-Crawling',
				'link' => ''
			],
			'knowknot' => [
				'title' => 'Knowknot',
				'link' => 'http://knowknot.com/faq.htm'
			],
			'leikibot' => [
				'title' => 'Leikibot',
				'link' => 'http://www.leiki.com'
			],
			'linguee' => [
				'title' => 'Linguee Bot',
				'link' => 'http://www.linguee.com/bot'
			],
			'linkdexbot' => [
				'title' => 'linkdexbot',
				'link' => 'http://www.linkdex.com/bots/'
			],
			'linkfluence' => [
				'title' => 'Linkfluence',
				'link' => 'http://linkfluence.com/'
			],
			'linkpadbot' => [
				'title' => 'LinkpadBot',
				'link' => 'http://www.linkpad.ru'
			],
			'ltx71' => [
				'title' => 'LTX71',
				'link' => 'http://ltx71.com/'
			],
			'mail.ru' => [
				'title' => 'Mail.RU',
				'link' => 'http://go.mail.ru/help/robots',
			],
			'mbcrawler' => [
				'title' => 'MBCrawler',
				'link' => 'https://monitorbacklinks.com/robot',
			],
			'mediatoolkitbot' => [
				'title' => 'Mediatoolkitbot',
				'link' => 'mailto:complaints@mediatoolkit.com',
			],
			'megaindex' => [
				'title' => 'MegaIndex',
				'link' => 'http://megaindex.com/crawler',
			],
			'mfibot' => [
				'title' => 'mfibot',
				'link' => 'http://www.mfisoft.ru/analyst/',
			],
			'microsoft-office' => [
				'title' => 'Microsoft Office Protocol Discovery',
				'link' => 'http://support.microsoft.com/kb/838028',
			],
			'mixrankbot' => [
				'title' => 'MixrankBot',
				'link' => 'mailto:crawler@mixrank.com',
			],
			'mojeekbot' => [
				'title' => 'MojeekBot',
				'link' => 'https://www.mojeek.com/bot.html',
			],
			'monsidobot' => [
				'title' => 'Monsidobot',
				'link' => 'http://monsido.com/bot.html',
			],
			'nekstbot' => [
				'title' => 'Nekstbot',
				'link' => 'http://nekst.ipipan.waw.pl/nekstbot/'
			],
			'nlnz' => [
				'title' => 'NLNZ_IAHarvester2017',
				'link' => 'https://natlib.govt.nz/publishers-and-authors/web-harvesting/domain-harvest'
			],
			'omgili' => [
				'title' => 'omgili',
				'link' => 'http://omgili.com'
			],
			'outclicksbot' => [
				'title' => 'OutclicksBot',
				'link' => 'https://www.outclicks.net/agent/fkn6dy'
			],
			'panscient' => [
				'title' => 'panscient.com',
				'link' => 'http://panscient.com/'
			],
			'pcore-http' => [
				'title' => 'Pcore-HTTP',
				'link' => ''
			],
			'php' => [
				'title' => 'PHP',
				'link' => ''
			],
			'pinterest' => [
				'title' => 'Pinterestbot',
				'link' => 'http://www.pinterest.com/bot.html'
			],
			'postman' => [
				'title' => 'Postman',
				'link' => 'https://github.com/postmanlabs/postman-runtime'
			],
			'quantcastbot' => [
				'title' => 'Quantcastbot',
				'link' => 'http://www.quantcast.com/bot'
			],
			're-re studio' => [
				'title' => 'Re-re Studio',
				'link' => 'http://re-re.ru/'
			],
			'relemindbot' => [
				'title' => 'relemindbot',
				'link' => 'https://relemind.com/impressum/'
			],
			'scrapinghub' => [
				'title' => 'Scrapinghub',
				'link' => 'https://scrapinghub.com/',
			],
			'scrapy' => [
				'title' => 'Scrapy',
				'link' => 'http://scrapy.org',
			],
			'seekport' => [
				'title' => 'Seekport Crawler',
				'link' => 'http://seekport.com/',
			],
			'seewithkids' => [
				'title' => 'SeeWithKids.com Crawler',
				'link' => 'http://seewithkids.com/bot',
			],
			'semanticscholarbot' => [
				'title' => 'SemanticScholarBot',
				'link' => 'https://www.semanticscholar.org/crawler',
			],
			'semrushbot' => [
				'title' => 'SemrushBot',
				'link' => 'http://www.semrush.com/bot.html',
			],
			'seokicks-robot' => [
				'title' => 'SEOkicks-Robot',
				'link' => 'http://www.seokicks.de/robot.html',
			],
			'serendeputybot' => [
				'title' => 'SerendeputyBot',
				'link' => 'http://serendeputy.com/about/serendeputy-bot',
			],
			'seznambot' => [
				'title' => 'SeznamBot',
				'link' => 'http://napoveda.seznam.cz/en/seznambot-intro/',
			],
			'sitesucker' => [
				'title' => 'SiteSucker for OS X',
				'link' => 'http://ricks-apps.com/osx/sitesucker/',
			],
			'smtbot' => [
				'title' => 'SMTBot',
				'link' => 'https://www.similartech.com/smtbot'
			],
			'spbot' => [
				'title' => 'SMTBot',
				'link' => 'http://OpenLinkProfiler.org/bot'
			],
			'special_archiver' => [
				'title' => 'Archive.org bot',
				'link' => 'http://www.archive.org/details/archive.org_bot'
			],
			'spider' => [
				'title' => 'Generic Spider',
				'link' => ''
			],
			'sqlmap' => [
				'title' => 'sqlmap',
				'link' => 'http://sqlmap.org'
			],
			'startmebot' => [
				'title' => 'startmebot',
				'link' => 'https://start.me/bot'
			],
			'statuscake' => [
				'title' => 'StatusCake VirusScanner',
				'link' => 'https://statuscake.com/automaton/virus.txt'
			],
			'symfony browserkit' => [
				'title' => 'Symfony BrowserKit',
				'link' => 'https://symfony.com/doc/current/components/browser_kit.html'
			],
			'tracemyfile' => [
				'title' => 'TraceMyFile',
				'link' => 'https://www.tracemyfile.com/'
			],
			'trendictionbot' => [
				'title' => 'TrendictionBot',
				'link' => 'http://www.trendiction.de/bot'
			],
			'triplecheckerrobot' => [
				'title' => 'TripleCheckerRobot',
				'link' => 'https://www.triplechecker.com/'
			],
			'ttd-content' => [
				'title' => 'theTradeDesk Content Scraper',
				'link' => 'https://www.thetradedesk.com/general/ttd-content'
			],
			'tweetmemebot' => [
				'title' => 'TweetmemeBot',
				'link' => 'http://datasift.com/bot.html'
			],
			'twitterbot' => [
				'title' => 'Twitterbot',
				'link' => ''
			],
			'ubermetrics-technologies' => [
				'title' => 'Ubermetrics',
				'link' => 'https://www.ubermetrics-technologies.com/'
			],
			'uipbot' => [
				'title' => 'Semasio uipbot',
				'link' => 'mailto:uipbot@semasio.net'
			],
			'uptimerobot' => [
				'title' => 'UptimeRobot',
				'link' => 'http://www.uptimerobot.com/'
			],
			'vegibot' => [
				'title' => 'Vegi bot',
				'link' => 'mailto:abuse-report@terrykyleseoagency.com'
			],
			'voluumdsp' => [
				'title' => 'VoluumDSP Content Bot',
				'link' => 'mailto:dsp-dev@codewise.com'
			],
			'voyager' => [
				'title' => 'Voyager Bot',
				'link' => 'mailto:bot@voyagerx.com'
			],
			'wget' => [
				'title' => 'Linux Wget',
				'link' => 'https://www.gnu.org/software/wget/'
			],
			'winhttp' => [
				'title' => 'Microsoft WinHttp',
				'link' => 'https://docs.microsoft.com/en-us/windows/win32/winhttp/about-winhttp'
			],
			'wonderbot' => [
				'title' => 'wonderbot',
				'link' => 'https://wonder-bot.com/'
			],
			'wotbox' => [
				'title' => 'Wotbox',
				'link' => 'http://www.wotbox.com/bot/'
			],
			'xenu link sleuth' => [
				'title' => 'Xenu Link Sleuth',
				'link' => 'http://home.snafu.de/tilman/xenulink.html'
			],
			'telegrambot' => [
				'title' => 'TelegramBot (like TwitterBot)',
				'link' => 'https://telegram.org/blog/link-preview'
			],
			'zoominfobot' => [
				'title' => 'ZoominfoBot',
				'link' => 'mailto:zoominfobot@zoominfo.com'
			],
		];

		return array_merge(parent::getRobotList(), $newBots);
	}
}
