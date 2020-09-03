<?php namespace Hampel\KnownBots\XF\Data;

use Hampel\KnownBots\Repository\UserAgentCache;

class Robot extends XFCP_Robot
{
	public function getRobotUserAgents()
	{
		$newBots = [
			'360spider' => '360spider',
			'7siters' => '7siters',
			'accompanybot' => 'accompanybot',
			'adbeat' => 'adbeat',
			'adsbot-google-mobile' => 'adsbot-google-mobile',
			'adsbot-google' => 'adsbot-google', // this should come after adsbot-google-mobile
			'adsbot' => 'adsbot', // this is not Google
			'adscanner' => 'adscanner',
			'adstxtcrawler' => 'adstxtcrawler',
			'ahrefsbot' => 'ahrefs',
			'aiohttp' => 'aiohttp',
			'amazonbot' => 'amazonbot',
			'archive.org_bot' => 'archive.org',
			'archivebot' => 'archiveteam',
			'aspiegelbot' => 'aspiegelbot',
			'apache-httpclient' => 'apache-httpclient',
			'applebot' => 'applebot',
			'awariosmartbot' => 'awariosmartbot',
			'awariorssbot' => 'awariorssbot',
			'bingpreview' => 'bing',
			'binglocalsearch' => 'bing',
			'bitlybot' => 'bitlybot',
			'blexbot' => 'blexbot',
			'boardreader' => 'boardreader',
			'bomborabot' => 'bomborabot',
			'brandverity' => 'brandverity',
			'brokenlinkcheck' => 'brokenlinkcheck',
			'buck' => 'buck',
			'bytespider' => 'bytespider',
			'ccbot' => 'ccbot',
			'cincraw' => 'cincraw',
			'clickagy intelligence bot' => 'clickagy',
			'cliqzbot' => 'cliqzbot',
			'coccocbot' => 'coccocbot',
			'cocolyzebot' => 'cocolyzebot',
			'companybook-crawler' => 'companybook',
			'contacts-crawler' => 'scrapinghub',
			'contxbot' => 'contxbot',
			'crawler4j' => 'crawler4j',
			'crawlson' => 'crawlson',
			'crsspxlbot' => 'crsspxlbot',
			'cukbot' => 'cukbot',
			'curl' => 'curl',
			'dalvik' => 'dalvik',
			'datagnionbot' => 'datagnionbot',
			'dataprovider' => 'dataprovider',
			'demandbasepublisheranalyzer' => 'demandbasepublisheranalyzer',
			'deskyobot' => 'deskyobot',
			'df bot' => 'df bot',
			'discordbot' => 'discordbot',
			'dispatch' => 'dispatch',
			'dnsresearchbot' => 'dnsresearchbot',
			'domainappender' => 'domainappender',
			'domainstatsbot' => 'domainstatsbot',
			'dotbot' => 'dotbot',
			'duckduckbot' => 'duckduckbot',
			'duckduckgo-favicons-bot' => 'duckduckgo-favicons',
			'duplexweb-google' => 'duplexweb-google',
			'e.ventures' => 'eventures',
			'exabot' => 'exabot',
			'eyeotabot' => 'eyeotabot',
			'ezooms' => 'ezooms',
			'feedfetcher-google' => 'feedfetcher-google',
			'feedlybot' => 'feedlybot',
			'foocrawlerbot' => 'foocrawlerbot',
			'garlikcrawler' => 'garlikcrawler',
			'germcrawler' => 'germcrawler',
			'getintent crawler' => 'getintent',
			'go-http-client' => 'go-http-client',
			'google-adwords-instant' => 'google-adwords-instant',
			'google-read-aloud' => 'google-read-aloud',
			'google favicon' => 'google-favicon',
			'grapeshotcrawler' => 'grapeshotcrawler',
			'gumgum-bot' => 'gumgum-bot',
			'hatena' => 'hatena',
			'hetrixtools' => 'hetrixtools',
			'httrack' => 'httrack',
			'hubpages' => 'hubpages',
			'hubspot url validation check' => 'hubspot url validation check',
			'ias-ir' => 'admantx',
			'ias-or' => 'admantx',
			'ias-va' => 'admantx',
			'ias_crawler' => 'ias_crawler',
			'indeedbot' => 'indeedbot',
			'internet-structure-research-project-bot' => 'isrpb',
			'internetnz' => 'internetnz',
			'jugendschutzprogramm-crawler' => 'jugendschutzprogramm',
			'just-crawling' => 'just-crawling',
			'knot group' => 'knot group',
			'knowknot' => 'knowknot',
			'krzana bot' => 'krzana bot',
			'lightspeedsystemscrawler' => 'lightspeedsystemscrawler',
			'leikibot' => 'leikibot',
			'linespider' => 'linespider',
			'linguee' => 'linguee',
			'linkdexbot' => 'linkdexbot',
			'linkfluence' => 'linkfluence',
			'linkpadbot' => 'linkpadbot',
			'livelapbot' => 'livelapbot',
			'ltx71' => 'ltx71',
			'lufsbot' => 'lufsbot',
			'mail.ru' => 'mail.ru',
			'mastodon' => 'mastodon',
			'mbcrawler' => 'mbcrawler',
			'mediatoolkitbot' => 'mediatoolkitbot',
			'megaindex' => 'megaindex',
			'mfibot' => 'mfibot',
			'microsoft office protocol discovery' => 'microsoft-office',
			'microsoft office' => 'microsoft-office',
			'mixrankbot' => 'mixrankbot',
			'moatbot' => 'moatbot',
			'mojeekbot' => 'mojeekbot',
			'monsidobot' => 'monsidobot',
			'nekstbot' => 'nekstbot',
			'netestate ne crawler' => 'netestate ne crawler',
			'netpeakcheckerbot' => 'netpeakcheckerbot',
			'netvibes' => 'netvibes',
			'nimbostratus-bot' => 'nimbostratus',
			'nixstatsbot' => 'nixstatsbot',
			'nlnz_iaharvester' => 'nlnz',
			'obot/2.3.1' => 'obot',
			'odklbot' => 'odklbot',
			'omgili' => 'omgili',
			'outclicksbot' => 'outclicksbot',
			'pagepeeker' => 'pagepeeker',
			'panscient' => 'panscient',
			'paperlibot' => 'paperlibot',
			'pcore-http' => 'pcore-http',
			'petalbot' => 'petalbot',
			'php' => 'php',
			'pinterestbot' => 'pinterest',
			'pilicanbot' => 'pilicanbot',
			'pleskbot' => 'pleskbot',
			'politecrawl' => 'politecrawl',
			'postmanruntime' => 'postman',
			'pubmatic crawler bot' => 'pubmatic crawler bot',
			'python-requests' => 'python-requests',
			'quantcastbot' => 'quantcastbot',
			'quick-crawler' => 'scrapinghub',
			're-re studio' => 're-re studio',
			'relemindbot' => 'relemindbot',
			'rogerbot' => 'rogerbot',
			'scooperbot' => 'scooperbot',
			'scrapy' => 'scrapy',
			'screaming frog seo spider' => 'screaming frog seo spider',
			'seekport crawler' => 'seekport',
			'seewithkids' => 'seewithkids',
			'semantic-visions.com' => 'semantic-visions',
			'semanticbot' => 'semanticbot',
			'semanticscholarbot' => 'semanticscholarbot',
			'semrushbot' => 'semrushbot',
			'seokicks-robot' => 'seokicks-robot',
			'seolizer' => 'seolizer',
			'serendeputybot' => 'serendeputybot',
			'seznambot' => 'seznambot',
			'sirdatabot' => 'sirdatabot',
			'sitesucker' => 'sitesucker',
			'slack-imgproxy' => 'slack-imgproxy',
			'slackbot-linkexpanding' => 'slackbot-linkexpanding',
			'slackbot' => 'slackbot',
			'smtbot' => 'smtbot',
			'spbot' => 'spbot',
			'special_archiver' => 'special_archiver',
			'sqlmap' => 'sqlmap',
			'startmebot' => 'startmebot',
			'statuscake/virusscanner' => 'statuscake',
			'surdotlybot' => 'surdotlybot',
			'symfony browserkit' => 'symfony browserkit',
			'symfony2 browserkit' => 'symfony browserkit',
			'tapatalk cloudsearch platform' => 'tapatalk',
			'tpradstxtcrawler' => 'tpradstxtcrawler',
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
			'velenpublicwebcrawler' => 'velenpublicwebcrawler',
			'voluumdsp-content-bot' => 'voluumdsp',
			'wget' => 'wget',
			'winhttp' => 'winhttp',
			'wonderbot' => 'wonderbot',
			'wotbox' => 'wotbox',
			'xenu link sleuth' => 'xenu link sleuth',
			'yisouspider' => 'yisouspider',
			'telegrambot' => 'telegrambot',
			'vkrobot' => 'vkrobot',
			'wp.com feedbot' => 'wp.com feedbot',
			'zoombot' => 'zoombot',
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
				'cubot_j3', // Cubot phones
				'cubot_p20',
				'cubot x18',
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
			'360spider' => [
				'title' => '360Spider',
				'link' => 'https://www.360.cn/',
			],
			'7siters' => [
				'title' => '7Siters',
				'link' => 'https://7ooo.ru/siters/',
			],
			'accompanybot' => [
				'title' => 'AccompanyBot',
				'link' => '',
			],
			'admantx' => [
				'title' => 'ADmantX Service Fetcher',
				'link' => 'https://www.admantx.com/service-fetcher.html'
			],
			'adbeat' => [
				'title' => 'Adbeat',
				'link' => 'http://adbeat.com/policy',
			],
			'adsbot-google-mobile' => [
				'title' => 'AdsBot Google Mobile',
				'link' => 'http://www.google.com/mobile/adsbot.html',
			],
			'adsbot-google' => [
				'title' => 'AdsBot Google',
				'link' => 'http://www.google.com/adsbot.html',
			],
			'adsbot' => [
				'title' => 'AdsBot (unknown source)',
				'link' => '',
			],
			'adscanner' => [
				'title' => 'AdScanner',
				'link' => 'http://seocompany.store'
			],
			'adstxtcrawler' => [
				'title' => 'IAB ads.txt crawler',
				'link' => 'https://github.com/InteractiveAdvertisingBureau/adstxtcrawler'
			],
			'ahrefs' => [
				'title' => 'AhrefsBot',
				'link' => 'https://ahrefs.com/robot',
			],
			'aiohttp' => [
				'title' => 'Async http client/server framework (aiohttp Python)',
				'link' => 'https://github.com/aio-libs/aiohttp',
			],
			'amazonbot' => [
				'title' => 'Amazonbot',
				'link' => 'https://developer.amazon.com/support/amazonbot',
			],
			'apache-httpclient' => [
				'title' => 'Apache HttpClient',
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
				'title' => 'Archive Team ArchiveBot',
				'link' => 'https://www.archiveteam.org/index.php?title=ArchiveBot'
			],
			'aspiegelbot' => [
				'title' => 'Huawei AspiegelBot',
				'link' => 'https://aspiegel.com/about'
			],
			'awariosmartbot' => [
				'title' => 'AwarioSmartBot',
				'link' => 'https://awario.com/bots.html'
			],
			'awariorssbot' => [
				'title' => 'AwarioRssBot',
				'link' => 'https://awario.com/bots.html'
			],
			'bitlybot' => [
				'title' => 'Bit.ly Link Checker',
				'link' => 'http://bit.ly/'
			],
			'blexbot' => [
				'title' => 'BLEXBot Crawler',
				'link' => 'http://webmeup-crawler.com/'
			],
			'boardreader' => [
				'title' => 'BoardReader',
				'link' => 'http://spider.boardreader.com'
			],
			'bomborabot' => [
				'title' => 'Bombora Bot',
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
				'title' => 'Buck Media Monitoring',
				'link' => 'https://app.hypefactors.com/media-monitoring/about.html'
			],
			'bytespider' => [
				'title' => 'Bytespider',
				'link' => 'https://zhanzhang.toutiao.com/'
			],
			'ccbot' => [
				'title' => 'Common Crawl Bot',
				'link' => 'http://commoncrawl.org/faq/'
			],
			'cincraw' => [
				'title' => 'Cincraw Crawler',
				'link' => 'https://cincrawdata.net/bot/'
			],
			'clickagy' => [
				'title' => 'Clickagy Intelligence Bot',
				'link' => 'https://www.clickagy.com/'
			],
			'cliqzbot' => [
				'title' => 'Cliqz Search Engine bot',
				'link' => 'http://cliqz.com/company/cliqzbot'
			],
			'coccocbot' => [
				'title' => 'Coc Coc Bot',
				'link' => 'http://help.coccoc.com/searchengine'
			],
			'cocolyzebot' => [
				'title' => 'Cocolyzebot',
				'link' => 'https://cocolyze.com/en/cocolyzebot'
			],
			'companybook' => [
				'title' => 'Companybook Crawler',
				'link' => 'https://www.companybooknetworking.com/'
			],
			'contxbot' => [
				'title' => 'Amazon Recommendation Bot',
				'link' => 'https://affiliate-program.amazon.com/help/node/topic/GT98G5PPRERNVZ2C'
			],
			'crawl' => [
				'title' => 'Generic Crawler',
				'link' => ''
			],
			'crawlson' => [
				'title' => 'Crawlson Search Engine',
				'link' => 'https://www.crawlson.com/about'
			],
			'crawler4j' => [
				'title' => 'crawler4j open source Java crawler',
				'link' => 'http://code.google.com/p/crawler4j/'
			],
			'crsspxlbot' => [
				'title' => 'Cross Pixel Bot',
				'link' => 'http://www.crosspixel.net/'
			],
			'cukbot' => [
				'title' => 'CukBot - Companies in the UK',
				'link' => 'https://www.companiesintheuk.co.uk/bot.html'
			],
			'curl' => [
				'title' => 'curl library',
				'link' => 'https://curl.haxx.se/'
			],
			'dalvik' => [
				'title' => 'Android Dalvik',
				'link' => 'https://source.android.com/devices/tech/dalvik/'
			],
			'datagnionbot' => [
				'title' => 'datagnionbot link crawler',
				'link' => 'http://www.datagnion.com/bot.html'
			],
			'dataprovider' => [
				'title' => 'Dataprovider.com',
				'link' => 'https://www.dataprovider.com/spider/'
			],
			'demandbasepublisheranalyzer' => [
				'title' => 'Demandbase Publisher Analyzer',
				'link' => 'https://support.demandbase.com/hc/en-us/articles/360000794803-What-is-DemandbaseSiteAnalyzer-DemandbaseBot-'
			],
			'deskyobot' => [
				'title' => 'Deskyobot',
				'link' => 'https://www.deskyo.com/bot'
			],
			'df bot' => [
				'title' => 'DF Bot',
				'link' => ''
			],
			'discordbot' => [
				'title' => 'Discordbot',
				'link' => 'https://discordapp.com'
			],
			'dotbot' => [
				'title' => 'Moz Link Index Bot',
				'link' => 'https://moz.com/help/moz-procedures/crawlers/dotbot'
			],
			'dispatch' => [
				'title' => 'dispatch',
				'link' => ''
			],
			'dnsresearchbot' => [
				'title' => 'Ruhr-University Bochum DNS Research Bot',
				'link' => 'http://195.37.190.77/'
			],
			'domainappender' => [
				'title' => 'Profound Networks Domain Appender',
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
			'duckduckgo-favicons' => [
				'title' => 'DuckDuck Favicons Bot',
				'link' => 'http://duckduckgo.com'
			],
			'duplexweb-google' => [
				'title' => 'Google Duplex on the Web',
				'link' => 'https://support.google.com/webmasters/answer/9467408'
			],
			'eventures' => [
				'title' => 'e.ventures Investment Crawler',
				'link' => 'https://www.eventures.vc/'
			],
			'exabot' => [
				'title' => 'Exalead Exabot',
				'link' => 'https://www.exalead.com/search/webmasterguide'
			],
			'eyeotabot' => [
				'title' => 'Eyeota Bot',
				'link' => 'https://www.eyeota.com/'
			],
			'ezooms' => [
				'title' => 'Moz Ezooms',
				'link' => 'mailto:ezooms.bot@gmail.com'
			],
			'feedfetcher-google' => [
				'title' => 'Google Feedfetcher',
				'link' => 'http://www.google.com/feedfetcher.html'
			],
			'feedlybot' => [
				'title' => 'Feedly Fetcher',
				'link' => 'https://www.feedly.com/fetcher.html'
			],
			'foocrawlerbot' => [
				'title' => 'FooCrawlerBot',
				'link' => ''
			],
			'garlikcrawler' => [
				'title' => 'GarlikCrawler',
				'link' => 'http://garlik.com/'
			],
			'germcrawler' => [
				'title' => 'GermCrawler',
				'link' => ''
			],
			'getintent' => [
				'title' => 'GetIntent Crawler',
				'link' => 'http://getintent.com/bot.html'
			],
			'grapeshotcrawler' => [
				'title' => 'Oracle Data Cloud Crawler (Grapeshot)',
				'link' => 'http://www.grapeshot.co.uk/crawler.php'
			],
			'go-http-client' => [
				'title' => 'Go HTTP Client',
				'link' => 'https://golang.org/pkg/net/http/'
			],
			'google-adwords-instant' => [
				'title' => 'Google Adwords Instant',
				'link' => 'http://www.google.com/adsbot.html'
			],
			'google-favicon' => [
				'title' => 'Google Favicon fetcher',
				'link' => 'http://www.google.com/adsbot.html'
			],
			'google-read-aloud' => [
				'title' => 'Google Real Aloud',
				'link' => 'http://www.google.com/adsbot.html'
			],
			'gumgum-bot' => [
				'title' => 'GumGum Bot',
				'link' => 'https://gumgum.com/'
			],
			'hatena' => [
				'title' => 'Hatena::Russia::Crawler',
				'link' => ''
			],
			'hetrixtools' => [
				'title' => 'HetrixTools Uptime Monitoring Bot',
				'link' => 'https://hetrix.tools/uptime-monitoring-bot.html'
			],
			'httrack' => [
				'title' => 'HTTrack Website Copier',
				'link' => 'http://www.httrack.com/'
			],
			'hubpages' => [
				'title' => 'HubPages Crawler',
				'link' => 'https://hubpages.com/help/crawlingpolicy'
			],
			'hubspot url validation check' => [
				'title' => 'HubSpot Url validation check',
				'link' => 'web-crawlers+url-validation@hubspot.com'
			],
			'ias_crawler' => [
				'title' => 'Integral Ad Science Crawler',
				'link' => 'http://integralads.com/site-indexing-policy/'
			],
			'indeedbot' => [
				'title' => 'IndeedBot',
				'link' => 'http://indeedbot.com/'
			],
			'internetnz' => [
				'title' => 'InternetNZ Webscan',
				'link' => 'https://zonescan.nzrs.net.nz'
			],
			'isrpb' => [
				'title' => 'Internet Structure Research Project Bot',
				'link' => '',
			],
			'jugendschutzprogramm' => [
				'title' => 'JusProg Parental Control',
				'link' => 'https://www.jugendschutzprogramm.de/'
			],
			'just-crawling' => [
				'title' => 'Just-Crawling',
				'link' => ''
			],
			'knowknot' => [
				'title' => 'Knowknot API Spider',
				'link' => 'http://knowknot.com/faq.htm'
			],
			'knot group' => [
				'title' => 'KNOT@FIT Brno University of Technology',
				'link' => 'http://knot.fit.vutbr.cz/crawling/'
			],
			'krzana bot' => [
				'title' => 'Krzana Newsgathering Bot',
				'link' => 'https://krzana.com/'
			],
			'leikibot' => [
				'title' => 'Leikibot',
				'link' => 'http://www.leiki.com'
			],
			'lightspeedsystemscrawler' => [
				'title' => 'Lightspeed Systems Crawler',
				'link' => 'https://www.lightspeedsystems.com/'
			],
			'linespider' => [
				'title' => 'Linespider Search Robot',
				'link' => 'https://lin.ee/4dwxkth'
			],
			'linguee' => [
				'title' => 'Linguee Bot',
				'link' => 'http://www.linguee.com/bot'
			],
			'linkdexbot' => [
				'title' => 'Linkdex Bot',
				'link' => 'http://www.linkdex.com/bots/'
			],
			'linkfluence' => [
				'title' => 'Linkfluence',
				'link' => 'http://linkfluence.com/'
			],
			'linkpadbot' => [
				'title' => 'Linkpad Bot',
				'link' => 'https://linkpad.org/en-au/help/'
			],
			'livelapbot' => [
				'title' => 'Livelap Crawler: LivelapBot',
				'link' => 'https://site.livelap.com/crawler'
			],
			'ltx71' => [
				'title' => 'LTX71 Security Research Bot',
				'link' => 'http://ltx71.com/'
			],
			'lufsbot' => [
				'title' => 'Lufs Bot',
				'link' => 'http://www.lufs.org/bot.html'
			],
			'mail.ru' => [
				'title' => 'Mail.RU',
				'link' => 'http://go.mail.ru/help/robots',
			],
			'mastodon' => [
				'title' => 'Mastodon',
				'link' => 'https://joinmastodon.org/',
			],
			'mbcrawler' => [
				'title' => 'MBCrawler',
				'link' => 'https://monitorbacklinks.com/robot',
			],
			'mediatoolkitbot' => [
				'title' => 'Mediatoolkitbot',
				'link' => 'https://www.mediatoolkit.com/robot',
			],
			'megaindex' => [
				'title' => 'MegaIndex Crawler',
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
			'moatbot' => [
				'title' => 'Moat Analytics Bot',
				'link' => 'https://moat.com/',
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
				'title' => 'NekstBOT',
				'link' => 'http://nekst.ipipan.waw.pl/nekstbot/'
			],
			'netestate ne crawler' => [
				'title' => 'netEstate NE Crawler',
				'link' => 'http://www.website-datenbank.de/'
			],
			'netpeakcheckerbot' => [
				'title' => 'Netpeak Checker SERP Scraping',
				'link' => 'https://netpeaksoftware.com/blog/netpeak-checker-3-0-serp-scraping'
			],
			'netvibes' => [
				'title' => 'Netvibes Crawler',
				'link' => 'http://www.netvibes.com'
			],
			'nimbostratus' => [
				'title' => 'Cloud System Networks Monitoring Bot (Nimbostratus)',
				'link' => 'http://cloudsystemnetworks.com/'
			],
			'nixstatsbot' => [
				'title' => 'NIXStatsbot',
				'link' => 'https://www.nixstats.com/bot.html'
			],
			'nlnz' => [
				'title' => 'National Library of New Zealand Web Domain Harvest',
				'link' => 'https://natlib.govt.nz/publishers-and-authors/web-harvesting/domain-harvest'
			],
			'obot' => [
				'title' => 'IBM Germany R&D oBot web crawler',
				'link' => 'http://www.xforce-security.com/crawler/'
			],
			'odklbot' => [
				'title' => 'OdklBot',
				'link' => 'klass@odnoklassniki.ru'
			],
			'omgili' => [
				'title' => 'Omgili Bot',
				'link' => 'https://webhose.io/blog/api/what-is-the-omgili-bot-and-why-is-it-crawling-your-website/'
			],
			'outclicksbot' => [
				'title' => 'OutclicksBot',
				'link' => 'https://www.outclicks.net/agent/fkn6dy'
			],
			'pagepeeker' => [
				'title' => 'PagePeeker Website Thumbnailing Robot',
				'link' => 'https://pagepeeker.com/robots/'
			],
			'panscient' => [
				'title' => 'Panscient Crawler',
				'link' => 'http://panscient.com/'
			],
			'paperlibot' => [
				'title' => 'Paper.li bot',
				'link' => 'https://support.paper.li/entries/20023257-what-is-paper-li'
			],
			'pcore-http' => [
				'title' => 'Pcore-HTTP',
				'link' => ''
			],
			'petalbot' => [
				'title' => 'Aspiegel PetalBot',
				'link' => 'http://aspiegel.com/petalbot'
			],
			'php' => [
				'title' => 'PHP',
				'link' => ''
			],
			'pilicanbot' => [
				'title' => 'PilicanBot',
				'link' => 'https://pilican.com/'
			],
			'pinterest' => [
				'title' => 'Pinterest crawler',
				'link' => 'http://www.pinterest.com/bot.html'
			],
			'pleskbot' => [
				'title' => 'PleskBot',
				'link' => ''
			],
			'politecrawl' => [
				'title' => 'PoliteCrawl',
				'link' => ''
			],
			'postman' => [
				'title' => 'Postman API Client',
				'link' => 'https://github.com/postmanlabs/postman-runtime'
			],
			'pubmatic crawler bot' => [
				'title' => 'PubMatic ads.txt crawler',
				'link' => 'https://pubmatic.com/blog/auto-scaling-ads-txt-crawler/'
			],
			'quantcastbot' => [
				'title' => 'Quantcast Bot',
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
			'rogerbot' => [
				'title' => 'Rogerbot Moz Pro Campaign site audit',
				'link' => 'https://moz.com/help/moz-procedures/crawlers/rogerbot'
			],
			'rssingbot' => [
				'title' => 'RSSing.com Bot',
				'link' => 'http://www.rssing.com'
			],
			'scooperbot' => [
				'title' => 'Carma Scooperbot',
				'link' => 'https://customscoop.carma.com/',
			],
			'scrapinghub' => [
				'title' => 'Scrapinghub',
				'link' => 'https://scrapinghub.com/',
			],
			'scrapy' => [
				'title' => 'Scrapy',
				'link' => 'http://scrapy.org',
			],
			'screaming frog seo spider' => [
				'title' => 'Screaming Frog SEO Spider',
				'link' => 'https://www.screamingfrog.co.uk/seo-spider/',
			],
			'seekport' => [
				'title' => 'Seekport Crawler',
				'link' => 'http://seekport.com/',
			],
			'seewithkids' => [
				'title' => 'SeeWithKids Crawler',
				'link' => 'http://seewithkids.com/bot',
			],
			'semantic-visions' => [
				'title' => 'Semantic Visions',
				'link' => 'https://semantic-visions.com/',
			],
			'semanticbot' => [
				'title' => 'SemanticBot',
				'link' => 'https://sempi.tech/bot.html',
			],
			'semanticscholarbot' => [
				'title' => 'Semantic Scholar Bot',
				'link' => 'https://www.semanticscholar.org/crawler',
			],
			'semrushbot' => [
				'title' => 'SEMrushBot',
				'link' => 'http://www.semrush.com/bot.html',
			],
			'seokicks-robot' => [
				'title' => 'SEOkicks Robot',
				'link' => 'http://www.seokicks.de/robot.html',
			],
			'seolizer' => [
				'title' => 'SEOLizer-Bot',
				'link' => 'https://www.seolizer.de/bot.html',
			],
			'serendeputybot' => [
				'title' => 'SerendeputyBot',
				'link' => 'http://serendeputy.com/about/serendeputy-bot',
			],
			'seznambot' => [
				'title' => 'SeznamBot',
				'link' => 'https://napoveda.seznam.cz/en/seznamcz-web-search/',
			],
			'sirdatabot' => [
				'title' => 'Sirdata Bot',
				'link' => 'https://www.sirdata.com/home/',
			],
			'sitesucker' => [
				'title' => 'SiteSucker for macOS',
				'link' => 'http://ricks-apps.com/osx/sitesucker/',
			],
			'slack-imgproxy' => [
				'title' => 'Slack Image Proxy',
				'link' => 'https://api.slack.com/robots'
			],
			'slackbot-linkexpanding' => [
				'title' => 'SlackBot Link Expanding',
				'link' => 'https://api.slack.com/robots'
			],
			'slackbot' => [
				'title' => 'SlackBot',
				'link' => 'https://api.slack.com/robots'
			],
			'smtbot' => [
				'title' => 'SMTBot',
				'link' => 'https://www.similartech.com/smtbot'
			],
			'spbot' => [
				'title' => 'OpenLink Profiler SEO Analyser Bot',
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
				'title' => 'sqlmap Automatic SQL injection and database takeover tool',
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
			'surdotlybot' => [
				'title' => 'SurdotlyBot Security Analysis',
				'link' => 'http://sur.ly/bot.html'
			],
			'symfony browserkit' => [
				'title' => 'Symfony BrowserKit',
				'link' => 'https://symfony.com/doc/current/components/browser_kit.html'
			],
			'tapatalk' => [
				'title' => 'Tapatalk CloudSearch Platform',
				'link' => 'http://www.tapatalk.com/bot.html'
			],
			'tpradstxtcrawler' => [
				'title' => 'TPR Ads.txt Crawler',
				'link' => ''
			],
			'tracemyfile' => [
				'title' => 'TraceMyFile',
				'link' => 'https://www.tracemyfile.com/'
			],
			'trendictionbot' => [
				'title' => 'Trendiction-Bot',
				'link' => 'http://www.trendiction.de/bot'
			],
			'triplecheckerrobot' => [
				'title' => 'TripleChecker Website Spelling and Grammar Error Checker',
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
			'velenpublicwebcrawler' => [
				'title' => 'Velen Public Web Crawler',
				'link' => 'https://velen.io'
			],
			'vkrobot' => [
				'title' => 'VKRobot',
				'link' => ''
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
			'wp.com feedbot' => [
				'title' => 'Automattic WordPress Feedbot',
				'link' => 'https://wp.com'
			],
			'xenu link sleuth' => [
				'title' => 'Xenu Link Sleuth',
				'link' => 'http://home.snafu.de/tilman/xenulink.html'
			],
			'yisouspider' => [
				'title' => 'YisouSpider Search Engine Bot (Shenma)',
				'link' => 'https://m.sm.cn/'
			],
			'telegrambot' => [
				'title' => 'TelegramBot (like TwitterBot)',
				'link' => 'https://telegram.org/blog/link-preview'
			],
			'zoombot' => [
				'title' => 'SEOZoom Bot',
				'link' => 'https://www.seozoom.co.uk/seo-spider/'
			],
			'zoominfobot' => [
				'title' => 'ZoominfoBot',
				'link' => 'mailto:zoominfobot@zoominfo.com'
			],
		];

		return array_merge(parent::getRobotList(), $newBots);
	}
}
