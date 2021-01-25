<?php namespace Hampel\KnownBots\XF\Data;

use Hampel\KnownBots\Repository\UserAgentCache;

class Robot extends XFCP_Robot
{
	public function getRobotUserAgents()
	{
		$newBots = [
			'3dd trunk' => '3dd trunk',
			'3w24bot' => '3w24bot',
			'360spider' => '360spider',
			'7siters' => '7siters',
			'aasa-bot' => 'aasa-bot',
			'accompanybot' => 'accompanybot',
			'adbeat' => 'adbeat',
			'addsugarspiderbot' => 'addsugarspiderbot',
			'adsbot-google-mobile' => 'adsbot-google-mobile',
			'adsbot-google' => 'adsbot-google', // this should come after adsbot-google-mobile
			'adsbot' => 'adsbot', // this is not Google
			'adscanner' => 'adscanner',
			'adstxtcrawler' => 'adstxtcrawler',
			'adstxtlab.com' => 'adstxtlab.com',
			'ag_dm_spider' => 'ag_dm_spider',
			'ahrefsbot' => 'ahrefs',
			'aihitbot' => 'aihitbot',
			'aiohttp' => 'aiohttp',
			'amazon-advertising-ad-standards-bot' => 'amazon-advertising-ad-standards-bot',
			'amazonbot' => 'amazonbot',
			'anderspinkbot' => 'anderspinkbot',
			'annuairefrancais.fr' => 'annuairefrancais.fr',
			'ant.com beta' => 'ant.com beta',
			'antbot/1.0' => 'antbot/1.0',
			'apache-httpclient' => 'apache-httpclient',
			'applebot' => 'applebot',
			'applenewsbot' => 'applenewsbot',
			'archive.org_bot' => 'archive.org',
			'archivebot' => 'archiveteam',
			'arquivo-web-crawler' => 'arquivo-web-crawler',
			'aspiegelbot' => 'aspiegelbot',
			'auto spider' => 'auto spider',
			'avocetcrawler' => 'avocetcrawler',
			'awariosmartbot' => 'awariosmartbot',
			'awariorssbot' => 'awariorssbot',
			'aylien' => 'aylien',
			'b2b bot' => 'b2b bot',
			'barkrowler' => 'barkrowler',
			'becomebot' => 'becomebot',
			'better uptime bot' => 'better uptime bot',
			'bha2r_bot' => 'bha2r_bot',
			'bidswitchbot' => 'bidswitchbot',
			'bingpreview' => 'bing',
			'binglocalsearch' => 'bing',
			'bitlybot' => 'bitlybot',
			'bl.uk_lddc_bot' => 'bl.uk_lddc_bot',
			'blexbot' => 'blexbot',
			'bnf.fr_bot' => 'bnf.fr_bot',
			'boardreader' => 'boardreader',
			'boitho.com-dc' => 'boitho.com-dc',
			'bomborabot' => 'bomborabot',
			'botw spider' => 'botw spider',
			'brandverity' => 'brandverity',
			'brokenlinkcheck' => 'brokenlinkcheck',
			'browserspybot' => 'browserspybot',
			'btcrawler' => 'btcrawler',
			'bublupbot' => 'bublupbot',
			'buck' => 'buck',
			'buzzbot' => 'buzzbot',
			'bytespider' => 'bytespider',
			'c-t bot' => 'c-t bot',
			'castlebot' => 'castlebot',
			'ccbot' => 'ccbot',
			'centro ads.txt crawler' => 'centro ads.txt crawler',
			'checkbot/1' => 'checkbot',
			'checkmarknetwork' => 'checkmarknetwork',
			'chimebot' => 'chimebot',
			'cincraw' => 'cincraw',
			'cis455crawler' => 'cis455crawler',
			'cispa webcrawler' => 'cispa webcrawler',
			'claritybot' => 'claritybot',
			'clickagy intelligence bot' => 'clickagy',
			'cliqzbot' => 'cliqzbot',
			'cloudservermarketspider' => 'cloudservermarketspider',
			'cms crawler' => 'cms crawler',
			'coccocbot' => 'coccocbot',
			'cocolyzebot' => 'cocolyzebot',
			'cognitiveseo.com' => 'cognitiveseo.com',
			'coibotparser' => 'coibotparser',
			'companybook-crawler' => 'companybook',
			'contacts-crawler' => 'scrapinghub',
			'contxbot' => 'contxbot',
			'crawler4j' => 'crawler4j',
			'crawler_eb_germany_2.0' => 'crawler_eb_germany_2.0',
			'crawlson' => 'crawlson',
			'criteobot' => 'criteobot',
			'crowdtanglebot' => 'crowdtanglebot',
			'crsspxlbot' => 'crsspxlbot',
			'crystalsemanticsbot' => 'crystalsemanticsbot',
			'cukbot' => 'cukbot',
			'curious george' => 'curious george',
			'curl' => 'curl',
			'custom-crawler' => 'custom-crawler',
//			'dalvik' => 'dalvik',
			'danibot' => 'danibot',
			'datagnionbot' => 'datagnionbot',
			'dataprovider' => 'dataprovider',
			'dcrawl' => 'dcrawl',
			'demandbasepublisheranalyzer' => 'demandbasepublisheranalyzer',
			'deskyobot' => 'deskyobot',
			'df bot' => 'df bot',
			'diffbot' => 'diffbot',
			'dingtalkbot-linkservice' => 'dingtalkbot-linkservice',
			'discobot' => 'discobot',
			'discordbot' => 'discordbot',
			'discoverspider' => 'discoverspider',
			'dispatch' => 'dispatch',
			'dle_spider.exe' => 'dle_spider.exe',
			'dnsresearchbot' => 'dnsresearchbot',
			'domainappender' => 'domainappender',
			'domainspider-bot' => 'domainspider-bot',
			'domainstatsbot' => 'domainstatsbot',
			'dotbot' => 'dotbot',
			'dragonbot' => 'dragonbot',
			'dropboxpreviewbot' => 'dropboxpreviewbot',
			'duckduckbot' => 'duckduckbot',
			'duckduckgo-favicons-bot' => 'duckduckgo-favicons',
			'dumbot' => 'dumbot',
			'duplexweb-google' => 'duplexweb-google',
			'dy robot' => 'dy robot',
			'dyno mapper crawler' => 'dyno mapper crawler',
			'e.ventures' => 'eventures',
			'earwigbot' => 'earwigbot',
			'elmer, the thinglink imagebot' => 'elmer, the thinglink imagebot',
			'elisabot' => 'elisabot',
			'envolk[its]spider' => 'envolk[its]spider',
			'em-crawler' => 'em-crawler',
			'everyfeed-spider' => 'everyfeed-spider',
			'exabot' => 'exabot',
			'experiancrawluk' => 'experiancrawluk',
			'eyeotabot' => 'eyeotabot',
			'ezlynx' => 'ezlynx',
			'ezooms' => 'ezooms',
			'fast-webcrawler' => 'fast-webcrawler',
			'feedfetcher-google' => 'feedfetcher-google',
			'feedlybot' => 'feedlybot',
			'feedsearch bot' => 'feedsearch bot',
			'feedsearch-crawler' => 'feedsearch-crawler',
			'fess' => 'fess',
			'ffzbot' => 'ffzbot',
			'finditanswersbot' => 'finditanswersbot',
			'flockbrain robot' => 'flockbrain robot',
			'foocrawlerbot' => 'foocrawlerbot',
			'fullstorybot' => 'fullstorybot',
			'fyrebot' => 'fyrebot',
			'garlikcrawler' => 'garlikcrawler',
			'gdark-spider' => 'gdark-spider',
			'geograph linkcheck bot' => 'geograph linkcheck bot',
			'germcrawler' => 'germcrawler',
			'gethpinfo.com-bot' => 'gethpinfo.com-bot',
			'getintent crawler' => 'getintent',
			'gg peekbot' => 'gg peekbot',
			'gigabot' => 'gigabot',
			'girafabot' => 'girafabot',
			'gnowitnewsbot' => 'gnowitnewsbot',
			'go-http-client' => 'go-http-client',
			'google-adwords-instant' => 'google-adwords-instant',
			'google-read-aloud' => 'google-read-aloud',
			'google favicon' => 'google-favicon',
			'googebot malware scanning' => 'googebot malware scanning',
			'gowikibot' => 'gowikibot',
			'grapeshotcrawler' => 'grapeshotcrawler',
			'graydon bot' => 'graydon bot',
			'grfzbot' => 'grfzbot',
			'gulper web bot' => 'gulper web bot',
			'gumgum-bot' => 'gumgum-bot',
			'hatena' => 'hatena',
			'hetrixtools' => 'hetrixtools',
			'hoaxybot' => 'hoaxybot',
			'hrankbot' => 'hrankbot',
			'httrack' => 'httrack',
			'huaweiwebcatbot' => 'huaweiwebcatbot',
			'hubpages' => 'hubpages',
			'hubspot crawler' => 'hubspot crawler',
			'hubspot url validation check' => 'hubspot url validation check',
			'hypestat' => 'hypestat',
			'hyscore' => 'hyscore',
			'i-market-bot' => 'i-market-bot',
			'ias-ir' => 'admantx',
			'ias-or' => 'admantx',
			'ias-va' => 'admantx',
			'ias_crawler' => 'ias_crawler',
			'icc-crawler' => 'icc-crawler',
			'iccrawler' => 'iccrawler',
			'ichiro' => 'ichiro',
			'image size by siteimprove.com' => 'image size by siteimprove.com',
			'impact radius compliance bot' => 'impact radius compliance bot',
			'implisensebot' => 'implisensebot',
			'indeedbot' => 'indeedbot',
			'infoobot' => 'infoobot',
			'intelx.io_bot' => 'intelx.io_bot',
			'internet-structure-research-project-bot' => 'isrpb',
			'internetnz' => 'internetnz',
			'ioncrawl' => 'ioncrawl',
			'irlbot' => 'irlbot',
			'istellabot' => 'istellabot',
			'jasper\'s lil\' bot' => 'jaspers lil bot',
			'jetslide' => 'jetslide',
			'jobboersebot' => 'jobboersebot',
			'jooblebot' => 'jooblebot',
			'jpg-newsbot' => 'jpg-newsbot',
			'js-crawler' => 'js-crawler',
			'jugendschutzprogramm-crawler' => 'jugendschutzprogramm',
			'just-crawling' => 'just-crawling',
			'k7mlwcbot' => 'k7mlwcbot',
			'kauaibot' => 'kauaibot',
			'kingbot' => 'kingbot',
			'knot group' => 'knot group',
			'knowknot' => 'knowknot',
			'konturbot' => 'konturbot',
			'krzana bot' => 'krzana bot',
			'krzana-rss-bot' => 'krzana-rss-bot',
			'lamarkbot' => 'lamarkbot',
			'lawinsiderbot' => 'lawinsiderbot',
			'leuchtfeuer crawler' => 'leuchtfeuer crawler',
			'ldspider' => 'ldspider',
			'letsearchbot' => 'letsearchbot',
			'lightspeedsystemscrawler' => 'lightspeedsystemscrawler',
			'leikibot' => 'leikibot',
			'linespider' => 'linespider',
			'linguee' => 'linguee',
			'linkcheck by siteimprove.com' => 'linkcheck by siteimprove.com',
			'linkdexbot' => 'linkdexbot',
			'linkfluence' => 'linkfluence',
			'linkisbot' => 'linkisbot',
			'linkpadbot' => 'linkpadbot',
			'livelapbot' => 'livelapbot',
			'lmspider' => 'lmspider',
			'loaderio;verification-bot' => 'loaderio;verification-bot',
			'looid.com crawler' => 'looid.com crawler',
			'ltx71' => 'ltx71',
			'lufsbot' => 'lufsbot',
			'magibot' => 'magibot',
			'mail.ru' => 'mail.ru',
			'makemoneyteamworkbot' => 'makemoneyteamworkbot',
			'marketingminer bot' => 'marketingminer bot',
			'mastodon' => 'mastodon',
			'mauibot' => 'mauibot',
			'maxpointcrawler' => 'maxpointcrawler',
			'mbcrawler' => 'mbcrawler',
			'media-bot' => 'media-bot',
			'mediacloud bot for open academic research' => 'mediacloud bot for open academic research',
			'mediapartners-google' => 'mediapartners-google',
			'mediatoolkitbot' => 'mediatoolkitbot',
			'mediumbot-metatagfetcher' => 'mediumbot-metatagfetcher',
			'metadata-downloader-bot' => 'metadata-downloader-bot',
			'megaindex' => 'megaindex',
			'metajobbot' => 'metajobbot',
			'mfibot' => 'mfibot',
			'microadbot' => 'microadbot',
			'microsoft office protocol discovery' => 'microsoft-office',
			'microsoft office' => 'microsoft-office',
			'mindupbot' => 'mindupbot',
			'miralinks robot' => 'miralinks robot',
			'mixrankbot' => 'mixrankbot',
			'mlbot' => 'mlbot',
			'moatbot' => 'moatbot',
			'mohawk-crawler' => 'mohawk-crawler',
			'mojeekbot' => 'mojeekbot',
			'monsidobot' => 'monsidobot',
			'msc crawl project radboud university' => 'msc crawl project radboud university',
			'msiecrawler' => 'msiecrawler',
			'my nutch spider' => 'my nutch spider',
			'mybot' => 'mybot',
			'nekstbot' => 'nekstbot',
			'nesotebot' => 'nesotebot',
			'netestate ne crawler' => 'netestate ne crawler',
			'neticle crawler' => 'neticle crawler',
			'netpeakcheckerbot' => 'netpeakcheckerbot',
			'netseer crawler' => 'netseer crawler',
			'netvibes' => 'netvibes',
			'networking4all bot' => 'networking4all bot',
			'newsgator' => 'newsgator',
			'nextcloud server crawler' => 'nextcloud server crawler',
			'nfwebcrawler' => 'nfwebcrawler',
			'niuebot' => 'niuebot',
			'nimbostratus-bot' => 'nimbostratus',
			'ninjbot' => 'ninjbot',
			'ninjabot' => 'ninjabot',
			'niocbot' => 'niocbot',
			'nixstatsbot' => 'nixstatsbot',
			'nlnz_iaharvester' => 'nlnz',
			'nusearch spider' => 'nusearch spider',
			'obot/2.3.1' => 'obot',
			'ocarinabot' => 'ocarinabot',
			'odklbot' => 'odklbot',
			'omgili' => 'omgili',
			'onalyticabot' => 'onalyticabot',
			'online-webceo-bot' => 'online-webceo-bot',
			'open web analytics bot' => 'open web analytics bot',
			'ottobot' => 'ottobot',
			'outclicksbot' => 'outclicksbot',
			'page audit bot' => 'page audit bot',
			'pagepeeker' => 'pagepeeker',
			'pagething.com' => 'pagething.com',
			'pandalytics' => 'pandalytics',
			'panscient' => 'panscient',
			'paperlibot' => 'paperlibot',
			'parsijoobot' => 'parsijoobot',
			'pcore-http' => 'pcore-http',
			'pdf drive crawler' => 'pdf drive crawler',
			'petalbot' => 'petalbot',
			'pigafetta' => 'pigafetta',
			'pingdom.com_bot' => 'pingdom.com_bot',
			'pingdompagespeed' => 'pingdompagespeed',
			'pinterestbot' => 'pinterest',
			'pilicanbot' => 'pilicanbot',
			'pimeyes.com' => 'pimeyes.com',
			'planckspider' => 'planckspider',
			'pleskbot' => 'pleskbot',
			'plurkbot' => 'plurkbot',
			'pmoz.info odp link checker' => 'pmoz.info odp link checker',
			'politecrawl' => 'politecrawl',
			'popscreen bot' => 'popscreen bot',
			'postmanruntime' => 'postman',
			'prft-bot' => 'prft-bot',
			'psbot' => 'psbot',
			'pubmatic crawler bot' => 'pubmatic crawler bot',
			'pulno' => 'pulno',
			'pulsepoint-ads.txt-crawler' => 'pulsepoint-ads.txt-crawler',
			'python-requests' => 'python-requests',
			'pywebcopybot' => 'pywebcopybot',
			'quantcastbot' => 'quantcastbot',
			'queryseekerspider' => 'queryseekerspider',
			'quetextbot' => 'quetextbot',
			'quick-crawler' => 'scrapinghub',
			'quora-bot' => 'quora-bot',
			'qwantbot' => 'qwantbot',
			'qwarrycrawler' => 'qwarrycrawler',
			'r6_commentreader' => 'r6_commentreader',
			'r6_feedfetcher' => 'r6_feedfetcher',
			'radian6_default_' => 'radian6_default_',
			'randomsurfer' => 'randomsurfer',
			'rankurbot' => 'rankurbot',
			'rasabot' => 'rasabot',
			'ravencrawler' => 'ravencrawler',
			'rc-crawler' => 'rc-crawler',
			're-re studio' => 're-re studio',
			'reachabilitycheckbot' => 'reachabilitycheckbot',
			'redditbot' => 'redditbot',
			'redirectbot' => 'redirectbot',
			'refindbot' => 'refindbot',
			'relemindbot' => 'relemindbot',
			'rely bot' => 'rely bot',
			'riverbot' => 'riverbot',
			'rogerbot' => 'rogerbot',
			'rssingbot' => 'rssingbot',
			'rssmicro.com' => 'rssmicro.com',
			'rytebot' => 'rytebot',
			'sabsimbot' => 'sabsimbot',
			'safednsbot' => 'safednsbot',
			'sbooksnet' => 'sbooksnet',
			'sc_bot' => 'sc_bot',
			'scanmine newsspider' => 'scanmine newsspider',
			'scooperbot' => 'scooperbot',
			'scrapy' => 'scrapy',
			'screaming frog seo spider' => 'screaming frog seo spider',
			'scribbr-citation-bot' => 'scribbr-citation-bot',
			'se ranking gentle bot' => 'se ranking gentle bot',
			'seebot.org' => 'seebot.org',
			'seekport crawler' => 'seekport',
			'seewithkids' => 'seewithkids',
			'semantic-visions.com' => 'semantic-visions',
			'semanticbot' => 'semanticbot',
			'semanticscholarbot' => 'semanticscholarbot',
			'semrushbot' => 'semrushbot',
			'seo-audit-check-bot' => 'seo-audit-check-bot',
			'seobilitybot' => 'seobilitybot',
			'seoclaritycrawl' => 'seoclaritycrawl',
			'seodiver' => 'seodiver',
			'seokicks' => 'seokicks',
			'seolizer' => 'seolizer',
			'serendeputybot' => 'serendeputybot',
			'serpstatbot' => 'serpstatbot',
			'serptimizerbot' => 'serptimizerbot',
			'seznambot' => 'seznambot',
			'sidetrade indexer bot' => 'sidetrade indexer bot',
			'sirdatabot' => 'sirdatabot',
			'simpleanalyticsbot' => 'simpleanalyticsbot',
			'siteanalyzerbot' => 'siteanalyzerbot',
			'sitecheck-sitecrawl' => 'sitecheck-sitecrawl',
			'sitecheckerbotcrawler' => 'sitecheckerbotcrawler',
			'siteguru linkchecker' => 'siteguru linkchecker',
			'sitelockspider' => 'sitelockspider',
			'sitesucker' => 'sitesucker',
			'skimbot' => 'skimbot',
			'slack-imgproxy' => 'slack-imgproxy',
			'slackbot-linkexpanding' => 'slackbot-linkexpanding',
			'slackbot' => 'slackbot',
			'smtbot' => 'smtbot',
			'snapbot' => 'snapbot',
			'snappreviewbot' => 'snappreviewbot',
			'sogou pic spider' => 'sogou pic spider',
			'solomonobot' => 'solomonobot',
			'somdsearchbot' => 'somdsearchbot',
			'sottopop' => 'sottopop',
			'spbot' => 'spbot',
			'special_archiver' => 'special_archiver',
			'speedy spider' => 'speedy spider',
			'spiderling' => 'spiderling',
			'sqlmap' => 'sqlmap',
			'squidbot' => 'squidbot',
			'ssブログ rsscrawler' => 'ssblog',
			'sserobots' => 'sserobots',
			'startmebot' => 'startmebot',
			'statdom.ru' => 'statdom.ru',
			'statonlinerubot' => 'statonlinerubot',
			'statuscake/virusscanner' => 'statuscake',
			'stormcrawler' => 'stormcrawler',
			'suggybot' => 'suggybot',
			'summalybot' => 'summalybot',
			'superbot' => 'superbot',
			'superfeedr bot' => 'superfeedr bot',
			'superpagesbot' => 'superpagesbot',
			'supremesearch.net' => 'supremesearch.net',
			'surdotlybot' => 'surdotlybot',
			'symfony browserkit' => 'symfony browserkit',
			'symfony2 browserkit' => 'symfony browserkit',
			'synthesio crawler' => 'synthesio crawler',
			'tapatalk cloudsearch platform' => 'tapatalk',
			'telegrambot' => 'telegrambot',
			'temeliobot-keyword-scrapper' => 'temeliobot-keyword-scrapper',
			'testbot' => 'testbot',
			'testcrawler' => 'testcrawler',
			'tineye-bot' => 'tineye-bot',
			'tkbot' => 'tkbot',
			'tmmbot' => 'tmmbot',
			'todoexpertosbot' => 'todoexpertosbot',
			'tokenspider' => 'tokenspider',
			'tombapublicwebcrawler' => 'tombapublicwebcrawler',
			'tombot' => 'tombot',
			'toutiaospider' => 'toutiaospider',
			'tpradstxtcrawler' => 'tpradstxtcrawler',
			'tracemyfile' => 'tracemyfile',
			'trendictionbot' => 'trendictionbot',
			'trendkite-akashic-crawler' => 'trendkite-akashic-crawler',
			'triplecheckerrobot' => 'triplecheckerrobot',
			'turnitinbot' => 'turnitinbot',
			'tweetmemebot' => 'tweetmemebot',
			'twitterbot' => 'twitterbot',
			'ttd-content' => 'ttd-content',
			'ucmore crawler app' => 'ucmore crawler app',
			'um-ln' => 'ubermetrics-technologies',
			'uipbot' => 'uipbot',
			'uptimebot' => 'uptimebot',
			'uptimerobot' => 'uptimerobot',
			'v-bot' => 'voyager',
			'vebidoobot' => 'vebidoobot',
			'vegi bot' => 'vegibot',
			'velenpublicwebcrawler' => 'velenpublicwebcrawler',
			'virusdie crawler' => 'virusdie crawler',
			'viulinkcrawler' => 'viulinkcrawler',
			'vkrobot' => 'vkrobot',
			'voilabot' => 'voilabot',
			'voluumdsp-content-bot' => 'voluumdsp',
			'vsusearchspider' => 'vsusearchspider',
			'vuhuvbot' => 'vuhuvbot',
			'webcrawl.net' => 'webcrawl.net',
			'webgains-bot' => 'webgains-bot',
			'webliobot' => 'webliobot',
			'webmoney megastock robot' => 'webmoney megastock robot',
			'website-audit.be crawler' => 'website-audit.be',
			'webzip' => 'webzip',
			'who.is bot' => 'who.is bot',
			'wiederfreibot' => 'wiederfreibot',
			'wikido' => 'wikido',
			'willie irc bot' => 'willie irc bot',
			'winhttp' => 'winhttp',
			'wlc pywikibot' => 'wlc pywikibot',
			'womlpefactory' => 'womlpefactory',
			'wonderbot' => 'wonderbot',
			'wordchampbot' => 'wordchampbot',
			'wotbox' => 'wotbox',
			'wp.com feedbot' => 'wp.com feedbot',
			'xaldon webspider' => 'xaldon webspider',
			'x28-job-bot' => 'x28-job-bot',
			'xenu link sleuth' => 'xenu link sleuth',
			'xovionpagecrawler' => 'xovionpagecrawler',
			'xyz spider' => 'xyz spider',
			'y!j-asr' => 'yahoo japan',
			'yellowbrandprotectionbot' => 'yellowbrandprotectionbot',
			'yesslebot' => 'yesslebot',
			'yeti/1.0' => 'yeti/1.0',
			'yetibot' => 'yetibot',
			'yisouspider' => 'yisouspider',
			'yacybot' => 'yacybot',
			'your-search-bot' => 'your-search-bot',
			'zombiebot' => 'zombiebot',
			'zoombot' => 'zoombot',
			'zoominfobot' => 'zoominfobot',
			'zumbot' => 'zumbot',
			'zyborg' => 'zyborg',
			'_zbot' => '_zbot',

			// these should appear after other bots

			'bot 1.0' => 'bot 1.0',
			'crawler/0.0.1' => 'crawler/0.0.1',
			'rss bot' => 'rss bot',
			'webspider 1.0' => 'webspider 1.0',

			'php' => 'php',
			'wget' => 'wget',
			'researchbot' => 'researchbot',
		];

		return array_merge(parent::getRobotUserAgents(), $newBots);
	}

	public function getFalsePositives()
	{
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

			'cubot a5', // Cubot phones
			'cubot cheetah 2',
			'cubot dinosaur',
			'cubot echo',
			'cubot_j3',
			'cubot j3 pro',
			'cubot h3',
			'cubot king kong',
			'cubot magic',
			'cubot_manito',
			'cubot max',
			'cubot note plus',
			'cubot_note_s',
			'cubot_nova',
			'cubot_p20',
			'cubot_power',
			'cubot r9',
			'cubot r11',
			'cubot x18',
			'cubot_x18_plus',

			'glx spideri', // GLX phones

			'dinobot 4k plus', // Dinobot Android TV

			'spider v7 build/lmy47i', // MyCell Spider v7 phone from Bangladesh
			'spider v7',
		];

		return $falsePositives;
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
			$falsePositives = $this->getFalsePositives();

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
			'3dd trunk' => [
				'title' => '3DD Trunk Crawler',
				'link' => 'https://trunk.3dd.nvdev.com',
			],
			'3w24bot' => [
				'title' => '3W24 Indexer Bot',
				'link' => 'https://3w24.com/addyoursite/',
			],
			'7siters' => [
				'title' => '7Siters',
				'link' => 'https://7ooo.ru/siters/',
			],
			'aasa-bot' => [
				'title' => 'Apple App Site Association CDN bot',
				'link' => 'https://developer.apple.com/library/archive/documentation/General/Conceptual/AppSearch/UniversalLinks.html',
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
			'addsugarspiderbot' => [
				'title' => 'IdealObserver AddSugar SpiderBot',
				'link' => 'https://www.idealobserver.com/',
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
			'adstxtlab.com' => [
				'title' => 'adstxtlab ads.txt crawler',
				'link' => 'http://adstxtlab.com/'
			],
			'ag_dm_spider' => [
				'title' => 'ag_dm_spider',
				'link' => '',
			],
			'ahrefs' => [
				'title' => 'AhrefsBot',
				'link' => 'https://ahrefs.com/robot',
			],
			'aihitbot' => [
				'title' => 'aiHitdata Company Data Bot',
				'link' => 'https://www.aihitdata.com/about',
			],
			'aiohttp' => [
				'title' => 'Async http client/server framework (aiohttp Python)',
				'link' => 'https://github.com/aio-libs/aiohttp',
			],
			'amazon-advertising-ad-standards-bot' => [
				'title' => 'Amazon Advertising Ad Standards bot',
				'link' => '',
			],
			'amazonbot' => [
				'title' => 'Amazonbot',
				'link' => 'https://developer.amazon.com/support/amazonbot',
			],
			'anderspinkbot' => [
				'title' => 'Anders Pink Bot',
				'link' => 'http://anderspink.com/bot.html',
			],
			'annuairefrancais.fr' => [
				'title' => 'The French Directory',
				'link' => 'http://annuairefrancais.fr/',
			],
			'antbot/1.0' => [
				'title' => 'Ant.com Antbot',
				'link' => 'http://www.ant.com',
			],
			'ant.com beta' => [
				'title' => 'Ant.com Antmark Crawler',
				'link' => 'https://www.ant.com',
			],
			'apache-httpclient' => [
				'title' => 'Apache HttpClient',
				'link' => 'https://hc.apache.org/',
			],
			'applebot' => [
				'title' => 'Applebot',
				'link' => 'http://www.apple.com/go/applebot',
			],
			'applenewsbot' => [
				'title' => 'Apple News Bot',
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
			'arquivo-web-crawler' => [
				'title' => 'Arquivo Web Archive Bot',
				'link' => 'http://arquivo.pt'
			],
			'aspiegelbot' => [
				'title' => 'Huawei AspiegelBot',
				'link' => 'https://aspiegel.com/about'
			],
			'auto spider' => [
				'title' => 'Auto Spider Exploit Scanner',
				'link' => ''
			],
			'avocetcrawler' => [
				'title' => 'AvocetCrawler',
				'link' => ''
			],
			'awariosmartbot' => [
				'title' => 'AwarioSmartBot',
				'link' => 'https://awario.com/bots.html'
			],
			'awariorssbot' => [
				'title' => 'AwarioRssBot',
				'link' => 'https://awario.com/bots.html'
			],
			'aylien' => [
				'title' => 'Aylien Bot',
				'link' => 'http://www.aylien.com/bot.html'
			],
			'b2b bot' => [
				'title' => 'B2B Bot',
				'link' => ''
			],
			'barkrowler' => [
				'title' => 'Barkrowler Metrics Bot',
				'link' => 'https://babbar.tech/crawler'
			],
			'becomebot' => [
				'title' => 'Become Bot',
				'link' => 'http://www.become.com/site_owners.html'
			],
			'better uptime bot' => [
				'title' => 'Better Uptime Bot',
				'link' => 'https://betteruptime.com/'
			],
			'bha2r_bot' => [
				'title' => 'BHa2R_bot',
				'link' => 'http://www.bha2r.xyz'
			],
			'bidswitchbot' => [
				'title' => 'Bidswitch Bot',
				'link' => 'https://www.bidswitch.com/'
			],
			'bitlybot' => [
				'title' => 'Bit.ly Link Checker',
				'link' => 'http://bit.ly/'
			],
			'bl.uk_lddc_bot' => [
				'title' => 'British Library Legal Deposit Libraries',
				'link' => 'http://vll-minos.bl.uk/aboutus/legaldeposit/websites/websites/faqswebmaster/'
			],
			'blexbot' => [
				'title' => 'BLEXBot Crawler',
				'link' => 'http://webmeup-crawler.com/'
			],
			'bnf.fr_bot' => [
				'title' => 'National Library of France (BnF) Website Capture Robot',
				'link' => 'https://www.bnf.fr/fr/capture-de-votre-site-web-par-le-robot-de-la-bnf'
			],
			'boardreader' => [
				'title' => 'BoardReader',
				'link' => 'http://spider.boardreader.com'
			],
			'boitho.com-dc' => [
				'title' => 'Boitho.com Web Crawler',
				'link' => 'http://www.boitho.com/dcbot.html'
			],
			'bomborabot' => [
				'title' => 'Bombora Bot',
				'link' => 'http://www.bombora.com/bot'
			],
			'bot' => [
				'title' => 'Generic Bot',
				'link' => ''
			],
			'bot 1.0' => [
				'title' => 'Unknown: Bot 1.0',
				'link' => ''
			],
			'botw spider' => [
				'title' => 'Best of the Web Spider',
				'link' => 'https://botw.org/'
			],
			'brandverity' => [
				'title' => 'BrandVerity',
				'link' => 'http://www.brandverity.com/why-is-brandverity-visiting-me'
			],
			'brokenlinkcheck' => [
				'title' => 'BrokenLinkCheck.com',
				'link' => 'http://brokenlinkcheck.com/'
			],
			'browserspybot' => [
				'title' => 'BrowserSpy.dk Bot',
				'link' => 'http://browserspy.dk/'
			],
			'btcrawler' => [
				'title' => 'Bluetooth Crawler',
				'link' => ''
			],
			'bublupbot' => [
				'title' => 'Bublup Bot',
				'link' => 'https://www.bublup.com/bublup-bot.html'
			],
			'buck' => [
				'title' => 'Buck Media Monitoring',
				'link' => 'https://app.hypefactors.com/media-monitoring/about.html'
			],
			'buzzbot' => [
				'title' => 'BuzzStream Bot',
				'link' => 'http://www.buzzstream.com'
			],
			'bytespider' => [
				'title' => 'Bytespider',
				'link' => 'https://zhanzhang.toutiao.com/'
			],
			'c-t bot' => [
				'title' => 'C-T Bot',
				'link' => ''
			],
			'castlebot' => [
				'title' => 'Castlebot',
				'link' => ''
			],
			'ccbot' => [
				'title' => 'Common Crawl Bot',
				'link' => 'http://commoncrawl.org/faq/'
			],
			'centro ads.txt crawler' => [
				'title' => 'Centro ads.txt crawler',
				'link' => 'https://www.centro.net/news/centro-aligns-with-iab-tech-labs-ads-txt-project-designed-to-reduce-ad-fraud-and-improve-ad-transparency'
			],
			'checkbot' => [
				'title' => 'Checkbot',
				'link' => 'https://www.checkbot.io'
			],
			'checkmarknetwork' => [
				'title' => 'CheckMark Network Crawler',
				'link' => 'http://www.checkmarknetwork.com/spider.html'
			],
			'chimebot' => [
				'title' => 'Amazon Chime Bot',
				'link' => 'https://docs.aws.amazon.com/chime/latest/APIReference/API_CreateBot.html'
			],
			'cincraw' => [
				'title' => 'Cincraw Crawler',
				'link' => 'https://cincrawdata.net/bot/'
			],
			'cis455crawler' => [
				'title' => 'CIS 455 Crawler',
				'link' => 'https://www.cis.upenn.edu/~cis455/'
			],
			'cispa webcrawler' => [
				'title' => 'CISPA - Helmholtz-Zentrum for Information Security Vulnerability Research Bot',
				'link' => 'https://vuln-notify-checker.cispa.saarland'
			],
			'claritybot' => [
				'title' => 'seoClarity Bot',
				'link' => 'https://www.seoclarity.net/bot.html'
			],
			'clickagy' => [
				'title' => 'Clickagy Intelligence Bot',
				'link' => 'https://www.clickagy.com/'
			],
			'cliqzbot' => [
				'title' => 'Cliqz Search Engine bot',
				'link' => 'http://cliqz.com/company/cliqzbot'
			],
			'cloudservermarketspider' => [
				'title' => 'Cloud Server Market Spider',
				'link' => 'http://cloudservermarket.com/spider.html'
			],
			'cms crawler' => [
				'title' => 'CMS Crawler',
				'link' => 'http://www.cmscrawler.com'
			],
			'coccocbot' => [
				'title' => 'Coc Coc Bot',
				'link' => 'http://help.coccoc.com/searchengine'
			],
			'cocolyzebot' => [
				'title' => 'Cocolyzebot',
				'link' => 'https://cocolyze.com/en/cocolyzebot'
			],
			'cognitiveseo.com' => [
				'title' => 'cognitiveSEO Bot',
				'link' => 'http://cognitiveseo.com/bot.html'
			],
			'coibotparser' => [
				'title' => 'COIBot Parser',
				'link' => ''
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
			'crawler/0.0.1' => [
				'title' => 'Unknown Crawler/0.0.1',
				'link' => ''
			],
			'crawler_eb_germany_2.0' => [
				'title' => 'Crawler EB Germany',
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
			'criteobot' => [
				'title' => 'Criteo Bot',
				'link' => 'https://www.criteo.com/'
			],
			'crowdtanglebot' => [
				'title' => 'CrowdTangle Social Media Link Check Bot',
				'link' => 'https://help.crowdtangle.com/en/articles/3009319-crowdtangle-bot'
			],
			'crsspxlbot' => [
				'title' => 'Cross Pixel Bot',
				'link' => 'http://www.crosspixel.net/'
			],
			'crystalsemanticsbot' => [
				'title' => 'Crystal Semantics Bot',
				'link' => 'http://www.crystalsemantics.com/user-agent/'
			],
			'cukbot' => [
				'title' => 'CukBot - Companies in the UK',
				'link' => 'https://www.companiesintheuk.co.uk/bot.html'
			],
			'curious george' => [
				'title' => 'Authoritas Curious George Crawler',
				'link' => 'https://www.authoritas.com/crawl/'
			],
			'curl' => [
				'title' => 'curl library',
				'link' => 'https://curl.haxx.se/'
			],
			'custom-crawler' => [
				'title' => 'Custom Crawler',
				'link' => ''
			],
//			'dalvik' => [
//				'title' => 'Android Dalvik',
//				'link' => 'https://source.android.com/devices/tech/dalvik/'
//			],
			'danibot' => [
				'title' => 'Danibot Slack Bot',
				'link' => 'https://hackage.haskell.org/package/danibot'
			],
			'datagnionbot' => [
				'title' => 'datagnionbot link crawler',
				'link' => 'http://www.datagnion.com/bot.html'
			],
			'dataprovider' => [
				'title' => 'Dataprovider.com',
				'link' => 'https://www.dataprovider.com/spider/'
			],
			'dcrawl' => [
				'title' => 'dcrawl domain crawler',
				'link' => 'https://github.com/kgretzky/dcrawl'
			],
			'demandbasepublisheranalyzer' => [
				'title' => 'Demandbase Publisher Analyzer',
				'link' => 'https://support.demandbase.com/hc/en-us/articles/360000794803-What-is-DemandbaseSiteAnalyzer-DemandbaseBot-'
			],
			'deskyobot' => [
				'title' => 'Deskyobot',
				'link' => 'https://www.deskyo.com/bot'
			],
			'diffbot' => [
				'title' => 'Diffbot Crawler',
				'link' => 'http://www.diffbot.com'
			],
			'df bot' => [
				'title' => 'DF Bot',
				'link' => ''
			],
			'dingtalkbot-linkservice' => [
				'title' => 'Ding Talk Bot Text Link Card Generator',
				'link' => 'https://open-doc.dingtalk.com/microapp/faquestions/ftpfeu'
			],
			'discobot' => [
				'title' => 'Discovery Engine DiscoBot',
				'link' => 'http://discoveryengine.com/discobot.html'
			],
			'discordbot' => [
				'title' => 'Discordbot',
				'link' => 'https://discordapp.com'
			],
			'discoverspider' => [
				'title' => 'Discover Spider',
				'link' => 'http://www.discover.com'
			],
			'dotbot' => [
				'title' => 'Moz Link Index Bot',
				'link' => 'https://moz.com/help/moz-procedures/crawlers/dotbot'
			],
			'dispatch' => [
				'title' => 'dispatch',
				'link' => ''
			],
			'dle_spider.exe' => [
				'title' => 'DLE Spider',
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
			'domainspider-bot' => [
				'title' => 'Domain Spider Bot',
				'link' => ''
			],
			'domainstatsbot' => [
				'title' => 'DomainStatsBot',
				'link' => 'https://domainstats.com/pages/our-bot'
			],
			'dragonbot' => [
				'title' => 'Dragon Metrics Bot',
				'link' => 'http://www.dragonmetrics.com'
			],
			'dropboxpreviewbot' => [
				'title' => 'Dropbox Preview Bot',
				'link' => 'https://www.dropbox.com/'
			],
			'duckduckbot' => [
				'title' => 'DuckDuckBot',
				'link' => 'http://duckduckgo.com/duckduckbot.html'
			],
			'duckduckgo-favicons' => [
				'title' => 'DuckDuck Favicons Bot',
				'link' => 'http://duckduckgo.com'
			],
			'dumbot' => [
				'title' => 'DumBot',
				'link' => ''
			],
			'duplexweb-google' => [
				'title' => 'Google Duplex on the Web',
				'link' => 'http://www.google.com/bot.html'
			],
			'dy robot' => [
				'title' => 'DY Robot',
				'link' => ''
			],
			'dyno mapper crawler' => [
				'title' => 'Dyno Mapper Website Crawler',
				'link' => 'https://dynomapper.com/component/content/article?id=432:60-innovative-website-crawlers-for-content-monitoring'
			],
			'earwigbot' => [
				'title' => 'EarwigBot',
				'link' => 'https://github.com/earwig/earwigbot'
			],
			'elisabot' => [
				'title' => 'ElisaBot',
				'link' => ''
			],
			'elmer, the thinglink imagebot' => [
				'title' => 'Elmer, the Thinglink Image Fetcher',
				'link' => 'https://support.thinglink.com/hc/en-us/articles/360025958773-elmer'
			],
			'em-crawler' => [
				'title' => 'em-crawler Ruby library',
				'link' => 'https://github.com/dinesh/em-crawler'
			],
			'envolk[its]spider' => [
				'title' => 'Envolk Spider',
				'link' => 'http://www.envolk.com/envolkspider.html'
			],
			'eventures' => [
				'title' => 'e.ventures Investment Crawler',
				'link' => 'https://www.eventures.vc/'
			],
			'everyfeed-spider' => [
				'title' => 'Everyfeed Spider',
				'link' => 'http://www.everyfeed.com'
			],
			'exabot' => [
				'title' => 'Exalead Exabot',
				'link' => 'https://www.exalead.com/search/webmasterguide'
			],
			'experiancrawluk' => [
				'title' => 'Experian Crawl UK',
				'link' => 'mailto:andrew.swanton@phgroup.com'
			],
			'eyeotabot' => [
				'title' => 'Eyeota Bot',
				'link' => 'https://www.eyeota.com/'
			],
			'ezooms' => [
				'title' => 'Moz Ezooms',
				'link' => 'mailto:ezooms.bot@gmail.com'
			],
			'ezlynx' => [
				'title' => 'Ezoicbot',
				'link' => 'http://www.ezoic.com/bot.html'
			],
			'fast-webcrawler' => [
				'title' => 'FAST-WebCrawler',
				'link' => 'http://www.alltheweb.com/help/webmaster/crawler'
			],
			'feedfetcher-google' => [
				'title' => 'Google Feedfetcher',
				'link' => 'http://www.google.com/feedfetcher.html'
			],
			'feedlybot' => [
				'title' => 'Feedly Fetcher',
				'link' => 'https://www.feedly.com/fetcher.html'
			],
			'feedsearch bot' => [
				'title' => 'Feedsearch Bot',
				'link' => 'https://pypi.org/project/feedsearch-crawler/'
			],
			'feedsearch-crawler' => [
				'title' => 'Feedsearch Crawler',
				'link' => 'https://pypi.org/project/feedsearch-crawler'
			],
			'fess' => [
				'title' => 'Fess Search Server Robot',
				'link' => 'http://fess.codelibs.org/bot.html'
			],
			'ffzbot' => [
				'title' => 'FrankerFaceZ Bot',
				'link' => 'https://www.frankerfacez.com'
			],
			'finditanswersbot' => [
				'title' => 'Find IT Answers Bot',
				'link' => 'http://finditanswers.com/'
			],
			'flockbrain robot' => [
				'title' => 'FlockBrain Robot',
				'link' => 'https://www.flockbrain.com/'
			],
			'foocrawlerbot' => [
				'title' => 'FooCrawlerBot',
				'link' => ''
			],
			'fullstorybot' => [
				'title' => 'FullStory Bot',
				'link' => 'https://www.fullstory.com'
			],
			'fyrebot' => [
				'title' => 'FyreBot',
				'link' => ''
			],
			'garlikcrawler' => [
				'title' => 'GarlikCrawler',
				'link' => 'http://garlik.com/'
			],
			'gdark-spider' => [
				'title' => 'Gdark Spider',
				'link' => ''
			],
			'geograph linkcheck bot' => [
				'title' => 'Geograph LinkCheck Bot',
				'link' => 'https://www.geograph.org.uk/help/bot'
			],
			'germcrawler' => [
				'title' => 'GermCrawler',
				'link' => ''
			],
			'gethpinfo.com-bot' => [
				'title' => 'getHPinfo Web Server Info Bot',
				'link' => 'https://gethpinfo.com/'
			],
			'getintent' => [
				'title' => 'GetIntent Crawler',
				'link' => 'http://getintent.com/bot.html'
			],
			'gg peekbot' => [
				'title' => 'GG PeekBot',
				'link' => 'https://www.gg.pl/'
			],
			'gigabot' => [
				'title' => 'Gigablast Crawler',
				'link' => 'https://www.gigablast.com/'
			],
			'girafabot' => [
				'title' => 'Girafabot',
				'link' => 'http://www.girafa.com'
			],
			'gnowitnewsbot' => [
				'title' => 'Gnowit News bot',
				'link' => 'https://www.gnowit.com/'
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
			'googebot malware scanning' => [
				'title' => 'Googlebot Malware Scanning',
				'link' => ''
			],
			'gowikibot' => [
				'title' => 'Gowiki Search Engine Bot',
				'link' => 'http://www.gowikibot.com'
			],
			'grapeshotcrawler' => [
				'title' => 'Oracle Data Cloud Crawler (Grapeshot)',
				'link' => 'http://www.grapeshot.co.uk/crawler.php'
			],
			'graydon bot' => [
				'title' => 'Graydon Bot',
				'link' => 'http://www.graydon.nl/'
			],
			'grfzbot' => [
				'title' => 'GRFZbot',
				'link' => ''
			],
			'gulper web bot' => [
				'title' => 'Gulper Web Bot',
				'link' => 'http://www.ecsl.cs.sunysb.edu/~maxim/cgi-bin/link/gulperbot'
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
			'hoaxybot' => [
				'title' => 'Hoaxy Bot - Indiana University CNetS',
				'link' => 'http://cnets.indiana.edu'
			],
			'hrankbot' => [
				'title' => 'HRank Hosting Ranking Bot',
				'link' => 'https://www.hrank.com/bot'
			],
			'httrack' => [
				'title' => 'HTTrack Website Copier',
				'link' => 'http://www.httrack.com/'
			],
			'huaweiwebcatbot' => [
				'title' => 'Huawei Web Security Bot',
				'link' => 'https://isecurity.huawei.com/'
			],
			'hubpages' => [
				'title' => 'HubPages Crawler',
				'link' => 'https://hubpages.com/help/crawlingpolicy'
			],
			'hubspot crawler' => [
				'title' => 'HubSpot Crawler',
				'link' => 'mailto:web-crawlers@hubspot.com'
			],
			'hubspot url validation check' => [
				'title' => 'HubSpot Url validation check',
				'link' => 'mailto:web-crawlers+url-validation@hubspot.com'
			],
			'hypestat' => [
				'title' => 'HypeStat bot',
				'link' => 'https://hypestat.com/bot'
			],
			'hyscore' => [
				'title' => 'hyScore.io crawler',
				'link' => 'https://hyscore.io/crawler/'
			],
			'i-market-bot' => [
				'title' => 'i-market-bot',
				'link' => ''
			],
			'ias_crawler' => [
				'title' => 'Integral Ad Science Crawler',
				'link' => 'http://integralads.com/site-indexing-policy/'
			],
			'icc-crawler' => [
				'title' => 'Universal Communication Research Institute ICC Crawler',
				'link' => 'http://ucri.nict.go.jp/en/icccrawler.html/'
			],
			'iccrawler' => [
				'title' => 'IC Center Bot',
				'link' => 'http://www.iccenter.net/bot.htm'
			],
			'ichiro' => [
				'title' => 'Goo Webcrawler (Ichiro)',
				'link' => 'http://help.goo.ne.jp/door/crawler.html'
			],
			'image size by siteimprove.com' => [
				'title' => 'Siteimprove Image Size QA Bot',
				'link' => 'https://support.siteimprove.com/hc/en-gb/articles/115000082872-Quality-Assurance-Technical-Specifications'
			],
			'implisensebot' => [
				'title' => 'ImplisenseBot',
				'link' => ''
			],
			'indeedbot' => [
				'title' => 'IndeedBot',
				'link' => 'http://indeedbot.com/'
			],
			'infoobot' => [
				'title' => 'Infoo.nl Bot',
				'link' => 'https://www.infoo.nl/bot.html'
			],
			'internetnz' => [
				'title' => 'InternetNZ Webscan',
				'link' => 'https://zonescan.nzrs.net.nz'
			],
			'istellabot' => [
				'title' => 'iStella Bot',
				'link' => 'http://www.tiscali.it/'
			],
			'jaspers lil bot' => [
				'title' => 'Jasper\'s Website Finder Bot',
				'link' => 'https://jasper-search.com/bot_info.html'
			],
			'jetslide' => [
				'title' => 'JetSlide Crawler',
				'link' => 'http://jetsli.de/crawler',
			],
			'jooblebot' => [
				'title' => 'Jooble Job Search Bot',
				'link' => 'http://jooble.org/jooblebot',
			],
			'jpg-newsbot' => [
				'title' => 'VIP nytt jpg-newsbot',
				'link' => 'https://vipnytt.no/bots/',
			],
			'js-crawler' => [
				'title' => 'Node.js Crawler Library',
				'link' => 'https://www.npmjs.com/package/js-crawler',
			],
			'impact radius compliance bot' => [
				'title' => 'Impact Radius Compliance Bot',
				'link' => 'https://impact.com/',
			],
			'intelx.io_bot' => [
				'title' => 'Intelligence X search engine bot',
				'link' => 'https://intelx.io',
			],
			'ioncrawl' => [
				'title' => 'IonCrawl',
				'link' => '',
			],
			'irlbot' => [
				'title' => 'Internet Research Lab at Texas A&M University',
				'link' => 'http://irl.cs.tamu.edu/crawler/',
			],
			'isrpb' => [
				'title' => 'Internet Structure Research Project Bot',
				'link' => '',
			],
			'jobboersebot' => [
				'title' => 'Xing Job Exchange Bot',
				'link' => 'http://www.jobboerse.com/bot.htm',
			],
			'jugendschutzprogramm' => [
				'title' => 'JusProg Parental Control',
				'link' => 'https://www.jugendschutzprogramm.de/'
			],
			'just-crawling' => [
				'title' => 'Just-Crawling',
				'link' => ''
			],
			'kauaibot' => [
				'title' => 'KauaiBot',
				'link' => ''
			],
			'k7mlwcbot' => [
				'title' => 'K7 Computing K7MLWCBot',
				'link' => 'http://www.k7computing.com'
			],
			'kingbot' => [
				'title' => 'KingBot',
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
			'konturbot' => [
				'title' => 'KonturBot',
				'link' => 'http://kontur.ru'
			],
			'krzana bot' => [
				'title' => 'Krzana Newsgathering Bot',
				'link' => 'https://krzana.com/'
			],
			'krzana-rss-bot' => [
				'title' => 'Krzana RSS Bot',
				'link' => 'https://krzana.com/'
			],
			'lamarkbot' => [
				'title' => 'LAMARK Bot',
				'link' => 'https://lamark.fr/'
			],
			'lawinsiderbot' => [
				'title' => 'Law Insider Bot',
				'link' => 'http://www.lawinsider.com/about'
			],
			'ldspider' => [
				'title' => 'Linked Data LDSpider',
				'link' => 'http://code.google.com/p/ldspider/wiki/robots'
			],
			'leikibot' => [
				'title' => 'Leikibot',
				'link' => 'http://www.leiki.com'
			],
			'letsearchbot' => [
				'title' => 'LetSearch.ru Bot',
				'link' => 'https://letsearch.ru/bots'
			],
			'leuchtfeuer crawler' => [
				'title' => 'Leuchtfeuer Crawler',
				'link' => ''
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
			'linkcheck by siteimprove.com' => [
				'title' => 'Siteimprove LinkCheck QA Bot',
				'link' => 'https://support.siteimprove.com/hc/en-gb/articles/115000082872-Quality-Assurance-Technical-Specifications'
			],
			'linkdexbot' => [
				'title' => 'Linkdex Bot',
				'link' => 'http://www.linkdex.com/bots/'
			],
			'linkfluence' => [
				'title' => 'Linkfluence',
				'link' => 'http://linkfluence.com/'
			],
			'linkisbot' => [
				'title' => 'Linkis Bot',
				'link' => 'mailto:bot@linkis.com'
			],
			'linkpadbot' => [
				'title' => 'Linkpad Bot',
				'link' => 'https://linkpad.org/en-au/help/'
			],
			'livelapbot' => [
				'title' => 'Livelap Crawler: LivelapBot',
				'link' => 'https://site.livelap.com/crawler'
			],
			'lmspider' => [
				'title' => 'Scansoft (Nuance) LM Spider',
				'link' => 'mailto:lmspider@scansoft.com'
			],
			'loaderio;verification-bot' => [
				'title' => 'Loader.io load testing service verification bot',
				'link' => 'https://loader.io/'
			],
			'looid.com crawler' => [
				'title' => 'looid.com crawler',
				'link' => 'https://www.looid.com/'
			],
			'ltx71' => [
				'title' => 'LTX71 Security Research Bot',
				'link' => 'http://ltx71.com/'
			],
			'lufsbot' => [
				'title' => 'Lufs Bot',
				'link' => 'http://www.lufs.org/bot.html'
			],
			'magibot' => [
				'title' => 'MagiBot (Matarael) Web Crawler',
				'link' => 'https://magi.com/bots',
			],
			'mail.ru' => [
				'title' => 'Mail.RU',
				'link' => 'http://go.mail.ru/help/robots',
			],
			'makemoneyteamworkbot' => [
				'title' => 'Make Money Team Workbot',
				'link' => 'https://mylinkback.com',
			],
			'marketingminer bot' => [
				'title' => 'Marketing Miner Bot',
				'link' => 'https://www.marketingminer.com/',
			],
			'mastodon' => [
				'title' => 'Mastodon',
				'link' => 'https://joinmastodon.org/',
			],
			'mauibot' => [
				'title' => 'MauiBot',
				'link' => 'mailto:crawler.feedback+dc@gmail.com',
			],
			'maxpointcrawler' => [
				'title' => 'MaxPoint Interactive Crawler',
				'link' => 'mailto:maxpoint.crawler@maxpointinteractive.com',
			],
			'mbcrawler' => [
				'title' => 'MBCrawler',
				'link' => 'https://monitorbacklinks.com/robot',
			],
			'media-bot' => [
				'title' => 'Media Bot',
				'link' => '',
			],
			'mediacloud bot for open academic research' => [
				'title' => 'Media Cloud bot for Open Academic Research',
				'link' => 'http://mediacloud.org',
			],
			'mediapartners-google' => [
				'title' => 'Mobile AdSense',
				'link' => 'http://www.google.com/bot.html',
			],
			'mediatoolkitbot' => [
				'title' => 'Mediatoolkitbot',
				'link' => 'https://www.mediatoolkit.com/robot',
			],
			'mediumbot-metatagfetcher' => [
				'title' => 'MediumBot Metatag Fetcher',
				'link' => 'https://medium.com/',
			],
			'megaindex' => [
				'title' => 'MegaIndex Crawler',
				'link' => 'http://megaindex.com/crawler',
			],
			'metadata-downloader-bot' => [
				'title' => 'Linqia Metadata Downloader Bot',
				'link' => 'mailto:eng@linqia.com',
			],
			'metajobbot' => [
				'title' => 'MetaJobBot Job Crawler',
				'link' => 'https://www.metajob.de/crawler',
			],
			'mfibot' => [
				'title' => 'mfibot',
				'link' => 'http://www.mfisoft.ru/analyst/',
			],
			'microadbot' => [
				'title' => 'MicroAd Bot',
				'link' => 'https://www.microad.co.jp/contact/',
			],
			'microsoft-office' => [
				'title' => 'Microsoft Office Protocol Discovery',
				'link' => 'http://support.microsoft.com/kb/838028',
			],
			'mindupbot' => [
				'title' => 'mindUp Data Butler Bot',
				'link' => 'https://www.datenbutler.de/',
			],
			'miralinks robot' => [
				'title' => 'Miralinks Robot',
				'link' => 'https://www.miralinks.ru/',
			],
			'mixrankbot' => [
				'title' => 'MixrankBot',
				'link' => 'mailto:crawler@mixrank.com',
			],
			'mlbot' => [
				'title' => 'MLBot',
				'link' => '',
			],
			'moatbot' => [
				'title' => 'Moat Analytics Bot',
				'link' => 'https://moat.com/',
			],
			'mohawk-crawler' => [
				'title' => 'Mohawk Search Crawler (Super AI Search)',
				'link' => 'https://github.com/stingraze/mohawk-crawler',
			],
			'mojeekbot' => [
				'title' => 'MojeekBot',
				'link' => 'https://www.mojeek.com/bot.html',
			],
			'monsidobot' => [
				'title' => 'Monsidobot',
				'link' => 'http://monsido.com/bot.html',
			],
			'msc crawl project radboud university' => [
				'title' => 'MSC Crawl Project - Raboud University',
				'link' => ''
			],
			'msiecrawler' => [
				'title' => 'MSIE Offline Website Crawler',
				'link' => 'http://nekst.ipipan.waw.pl/nekstbot/'
			],
			'mybot' => [
				'title' => 'mybot',
				'link' => ''
			],
			'my nutch spider' => [
				'title' => 'My Nutch Spider',
				'link' => 'http://nutch.apache.org/'
			],
			'nekstbot' => [
				'title' => 'NekstBOT',
				'link' => 'http://nekst.ipipan.waw.pl/nekstbot/'
			],
			'nesotebot' => [
				'title' => 'Nesote Bot',
				'link' => 'http://www.inoutscripts.com/bot.html'
			],
			'netestate ne crawler' => [
				'title' => 'netEstate NE Crawler',
				'link' => 'http://www.website-datenbank.de/'
			],
			'neticle crawler' => [
				'title' => 'Neticle Online Media Monitoring Bot',
				'link' => 'http://bot.neticle.hu/'
			],
			'netpeakcheckerbot' => [
				'title' => 'Netpeak Checker SERP Scraping',
				'link' => 'https://netpeaksoftware.com/blog/netpeak-checker-3-0-serp-scraping'
			],
			'netseer crawler' => [
				'title' => 'Netseer Crawler',
				'link' => 'http://www.netseer.com/crawler.html'
			],
			'netvibes' => [
				'title' => 'Netvibes Crawler',
				'link' => 'http://www.netvibes.com'
			],
			'networking4all bot' => [
				'title' => 'Networking4All Bot',
				'link' => 'https://verzamelgids.nl/'
			],
			'newsgator' => [
				'title' => 'Newsgator',
				'link' => 'http://www.newsgator.com'
			],
			'nextcloud server crawler' => [
				'title' => 'Nextcloud Server Crawler',
				'link' => 'https://github.com/nextcloud/server'
			],
			'nfwebcrawler' => [
				'title' => 'NF Web Crawler',
				'link' => ''
			],
			'niuebot' => [
				'title' => 'NiueBot',
				'link' => ''
			],
			'nimbostratus' => [
				'title' => 'Cloud System Networks Monitoring Bot (Nimbostratus)',
				'link' => 'http://cloudsystemnetworks.com/'
			],
			'ninjbot' => [
				'title' => 'Internet Marketing Ninjas Bot',
				'link' => 'http://www.webuildpages.com'
			],
			'ninjabot' => [
				'title' => 'Ninjabot',
				'link' => ''
			],
			'niocbot' => [
				'title' => 'niocBot',
				'link' => 'https://nioc.de/bot'
			],
			'nixstatsbot' => [
				'title' => 'NIXStatsbot',
				'link' => 'https://www.nixstats.com/bot.html'
			],
			'nlnz' => [
				'title' => 'National Library of New Zealand Web Domain Harvest',
				'link' => 'https://natlib.govt.nz/publishers-and-authors/web-harvesting/domain-harvest'
			],
			'nusearch spider' => [
				'title' => 'NuSearch Spider',
				'link' => 'http://www.nusearch.com'
			],
			'obot' => [
				'title' => 'IBM Germany R&D oBot web crawler',
				'link' => 'http://www.xforce-security.com/crawler/'
			],
			'ocarinabot' => [
				'title' => 'Ocarinabot',
				'link' => ''
			],
			'odklbot' => [
				'title' => 'OdklBot',
				'link' => 'mailto:klass@odnoklassniki.ru'
			],
			'omgili' => [
				'title' => 'Omgili Bot',
				'link' => 'https://webhose.io/blog/api/what-is-the-omgili-bot-and-why-is-it-crawling-your-website/'
			],
			'onalyticabot' => [
				'title' => 'Onalytica Bot',
				'link' => 'https://onalytica.com/'
			],
			'online-webceo-bot' => [
				'title' => 'WebCEO SEO Bot',
				'link' => 'http://online.webceo.com'
			],
			'open web analytics bot' => [
				'title' => 'Open Web Analytics Bot',
				'link' => 'https://github.com/Open-Web-Analytics/Open-Web-Analytics'
			],
			'ottobot' => [
				'title' => 'AskGus OttoBot',
				'link' => 'http://bot.askg.us/'
			],
			'outclicksbot' => [
				'title' => 'OutclicksBot',
				'link' => 'https://www.outclicks.net/agent/fkn6dy'
			],
			'page audit bot' => [
				'title' => 'Page Audit Bot',
				'link' => 'https://page-audit.com'
			],
			'pagepeeker' => [
				'title' => 'PagePeeker Website Thumbnailing Robot',
				'link' => 'https://pagepeeker.com/robots/'
			],
			'pagething.com' => [
				'title' => 'PageThing Indexer',
				'link' => 'https://pagething.com/'
			],
			'parsijoobot' => [
				'title' => 'Parsijoo Bot',
				'link' => 'http://www.parsijoo.ir/'
			],
			'pandalytics' => [
				'title' => 'Pandalytics Domain Crawler',
				'link' => 'https://domainsbot.com/pandalytics/'
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
			'pdf drive crawler' => [
				'title' => 'PDF Drive Crawler',
				'link' => 'https://www.pdfdrive.com/'
			],
			'petalbot' => [
				'title' => 'Aspiegel PetalBot',
				'link' => 'http://aspiegel.com/petalbot'
			],
			'php' => [
				'title' => 'PHP',
				'link' => ''
			],
			'pigafetta' => [
				'title' => 'Pigafetta Visual SEO Studio crawler',
				'link' => 'http://visual-seo.com/pigafetta-bot'
			],
			'pilicanbot' => [
				'title' => 'PilicanBot',
				'link' => 'https://pilican.com/'
			],
			'pimeyes.com' => [
				'title' => 'PimEyes Face Search Engine',
				'link' => 'https://pimeyes.com/'
			],
			'pingdom.com_bot' => [
				'title' => 'Pingdom.com Bot',
				'link' => 'http://www.pingdom.com/'
			],
			'pingdompagespeed' => [
				'title' => 'Pingdom Page Speed Bot',
				'link' => 'http://www.pingdom.com/'
			],
			'pinterest' => [
				'title' => 'Pinterest crawler',
				'link' => 'http://www.pinterest.com/bot.html'
			],
			'planckspider' => [
				'title' => 'PlanckRe Insurance Spider',
				'link' => 'https://s3.amazonaws.com/spiderplanck/readme'
			],
			'pleroma' => [
				'title' => 'Pleroma Bot',
				'link' => 'https://pleroma.social/'
			],
			'pleskbot' => [
				'title' => 'PleskBot',
				'link' => ''
			],
			'plurkbot' => [
				'title' => 'PlurkBot',
				'link' => 'https://www.plurk.com/'
			],
			'pmoz.info odp link checker' => [
				'title' => 'pmoz.info ODP link checker',
				'link' => 'http://pmoz.info/doc/botinfo.htm'
			],
			'politecrawl' => [
				'title' => 'PoliteCrawl',
				'link' => ''
			],
			'popscreen bot' => [
				'title' => 'PopScreen Video Discovery Bot',
				'link' => ''
			],
			'postman' => [
				'title' => 'Postman API Client',
				'link' => 'https://github.com/postmanlabs/postman-runtime'
			],
			'prft-bot' => [
				'title' => 'PRFT Bot',
				'link' => ''
			],
			'psbot' => [
				'title' => 'Picsearch PsBot',
				'link' => 'http://www.picsearch.com/bot.html'
			],
			'pubmatic crawler bot' => [
				'title' => 'PubMatic ads.txt crawler',
				'link' => 'https://pubmatic.com/blog/auto-scaling-ads-txt-crawler/'
			],
			'pulno' => [
				'title' => 'Pulno SEO Analysis Bot',
				'link' => 'http://www.pulno.com/bot.html'
			],
			'pulsepoint-ads.txt-crawler' => [
				'title' => 'PulsePoint ads.txt Crawler',
				'link' => 'https://docs.pulsepoint.com/display/PDB/Ads.txt+Compliance'
			],
			'python-requests' => [
				'title' => 'Python Requests Library',
				'link' => 'https://requests.readthedocs.io/en/master/'
			],
			'pywebcopybot' => [
				'title' => 'PyWebCopy Website Cloning Bot',
				'link' => 'https://pypi.org/project/pywebcopy/'
			],
			'quantcastbot' => [
				'title' => 'Quantcast Bot',
				'link' => 'http://www.quantcast.com/bot'
			],
			'queryseekerspider' => [
				'title' => 'QuerySeekerSpider',
				'link' => 'http://queryseeker.com/bot.html'
			],
			'quetextbot' => [
				'title' => 'Quetext Plagiarism Checker Bot',
				'link' => 'https://www.quetext.com/quetextbot'
			],
			'quora-bot' => [
				'title' => 'Quora Bot',
				'link' => 'http://www.quora.com'
			],
			'qwantbot' => [
				'title' => 'Qwantbot',
				'link' => 'https://qwantbot.net/'
			],
			'qwarrycrawler' => [
				'title' => 'QwarryCrawler',
				'link' => ''
			],
			'r6_commentreader' => [
				'title' => 'Radian6 Comment Reader',
				'link' => 'https://www.radian6.com/crawler'
			],
			'r6_feedfetcher' => [
				'title' => 'Radian6 Feed Fetcher',
				'link' => 'https://www.radian6.com/crawler'
			],
			'radian6_default_' => [
				'title' => 'Radian6 Default Crawler',
				'link' => 'https://www.radian6.com/crawler'
			],
			'randomsurfer' => [
				'title' => 'Random Surfer Bot',
				'link' => 'https://random.surf/bot'
			],
			'rankurbot' => [
				'title' => 'Rankur Reputation Management Bot',
				'link' => 'http://rankur.com'
			],
			'rasabot' => [
				'title' => 'Rasa Bot',
				'link' => 'https://rasa.com/'
			],
			'ravencrawler' => [
				'title' => 'Raven Tools Website SEO Auditor',
				'link' => 'https://raventools.com/seo-website-auditor/'
			],
			'rc-crawler' => [
				'title' => 'RC Crawler',
				'link' => ''
			],
			're-re studio' => [
				'title' => 'Re-re Studio',
				'link' => 'http://re-re.ru/'
			],
			'reachabilitycheckbot' => [
				'title' => 'Reachability Check Bot',
				'link' => ''
			],
			'redditbot' => [
				'title' => 'Reddit Bot',
				'link' => 'http://www.reddit.com/feedback'
			],
			'redirectbot' => [
				'title' => 'Redirect Bot',
				'link' => 'https://sourceforge.net/projects/redirectbot/'
			],
			'refindbot' => [
				'title' => 'Refind Bot',
				'link' => 'https://refind.com/'
			],
			'relemindbot' => [
				'title' => 'relemindbot',
				'link' => 'https://relemind.com/impressum/'
			],
			'rely bot' => [
				'title' => 'Rely Bot',
				'link' => ''
			],
			'researchbot' => [
				'title' => 'ResearchBot',
				'link' => ''
			],
			'riverbot' => [
				'title' => 'RiverBot',
				'link' => 'http://www.useriver.com/bot.html'
			],
			'rogerbot' => [
				'title' => 'Rogerbot Moz Pro Campaign site audit',
				'link' => 'https://moz.com/help/moz-procedures/crawlers/rogerbot'
			],
			'rss bot' => [
				'title' => 'RSS Bot for MacOS',
				'link' => 'https://apps.apple.com/us/app/rss-bot-news-notifier/id605732865'
			],
			'rssingbot' => [
				'title' => 'RSSing.com Bot',
				'link' => 'http://www.rssing.com'
			],
			'rssmicro.com' => [
				'title' => 'RSS Micro Feed Bot',
				'link' => 'http://rssmicro.com/',
			],
			'rytebot' => [
				'title' => 'Ryte Bot',
				'link' => 'https://bot.ryte.com/',
			],
			'sabsimbot' => [
				'title' => 'Sabsim Web Search Bot',
				'link' => 'https://sabsim.com',
			],
			'safednsbot' => [
				'title' => 'SafeDNS Categorization Crawler',
				'link' => 'https://www.safedns.com/searchbot',
			],
			'seodiver' => [
				'title' => 'SEO DIVER',
				'link' => 'http://www.seodiver.com/bot',
			],
			'sbooksnet' => [
				'title' => 's-books.net crawler',
				'link' => 'http://s-books.net/crawl_policy',
			],
			'sc_bot' => [
				'title' => 'sc_bot',
				'link' => '',
			],
			'scanmine newsspider' => [
				'title' => 'ScanMine News Spider',
				'link' => 'http://www.scanmine.com',
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
			'scribbr-citation-bot' => [
				'title' => 'Scribbr Citation Bot',
				'link' => 'https://www.scribbr.com/',
			],
			'se ranking gentle bot' => [
				'title' => 'SE Ranking Gentle Bot',
				'link' => 'https://seranking.com/website-audit.html',
			],
			'seebot.org' => [
				'title' => 'SeeBot',
				'link' => 'http://seebot.org',
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
			'seo-audit-check-bot' => [
				'title' => 'SEO Audit Check Bot',
				'link' => 'https://vonino.fr/seo-audit-check-bot-et-seo-audit-of-site/',
			],
			'seobilitybot' => [
				'title' => 'Seobility Bot',
				'link' => 'https://www.seobility.net/sites/bot.html',
			],
			'seoclaritycrawl' => [
				'title' => 'seoClarity Crawler',
				'link' => 'https://www.seoclarity.net/technology/site-audits/',
			],
			'seokicks' => [
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
			'serpstatbot' => [
				'title' => 'Serpstat backlink tracking bot',
				'link' => 'http://serpstatbot.com/',
			],
			'serptimizerbot' => [
				'title' => 'SERPtimizer Bot',
				'link' => 'http://serptimizer.com/serptimizer-bot',
			],
			'seznambot' => [
				'title' => 'SeznamBot',
				'link' => 'https://napoveda.seznam.cz/en/seznamcz-web-search/',
			],
			'sidetrade indexer bot' => [
				'title' => 'Sidetrade Indexer Bot',
				'link' => 'https://www.sidetrade.com/',
			],
			'simpleanalyticsbot' => [
				'title' => 'Simple Analytics Bot',
				'link' => 'https://simpleanalytics.com/',
			],
			'sirdatabot' => [
				'title' => 'Sirdata Bot',
				'link' => 'https://www.sirdata.com/home/',
			],
			'siteanalyzerbot' => [
				'title' => 'SiteAnalyzer Web Crawler',
				'link' => 'https://site-analyzer.pro/',
			],
			'sitecheck-sitecrawl' => [
				'title' => 'Siteimprove QA Crawler',
				'link' => 'https://support.siteimprove.com/hc/en-gb/articles/115000082872-Quality-Assurance-Technical-Specifications',
			],
			'sitecheckerbotcrawler' => [
				'title' => 'Sitechecker SEO Analyzer',
				'link' => 'http://sitechecker.pro',
			],
			'siteguru linkchecker' => [
				'title' => 'SiteGuru Link Checker',
				'link' => 'https://www.siteguru.co/crawler',
			],
			'sitelockspider' => [
				'title' => 'SiteLock Spider',
				'link' => 'https://www.sitelock.com/',
			],
			'sitesucker' => [
				'title' => 'SiteSucker for macOS',
				'link' => 'http://ricks-apps.com/osx/sitesucker/',
			],
			'skimbot' => [
				'title' => 'Skimlinks Skimbot',
				'link' => 'https://skimlinks.com/',
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
			'snapbot' => [
				'title' => 'SnapBot',
				'link' => 'http://www.snapchat.com'
			],
			'snappreviewbot' => [
				'title' => 'SnapPreviewBot',
				'link' => ''
			],
			'sogou pic spider' => [
				'title' => 'Sogou Search Engine Pic Spider',
				'link' => 'http://www.sogou.com/docs/help/webmasters.htm#07'
			],
			'solomonobot' => [
				'title' => 'Solomono Bot',
				'link' => 'http://www.solomono.ru'
			],
			'somdsearchbot' => [
				'title' => 'SomdSearchBot',
				'link' => ''
			],
			'sottopop' => [
				'title' => 'Sottopop UpContent Page Requester',
				'link' => 'https://upcontent.com/robots'
			],
			'spbot' => [
				'title' => 'OpenLink Profiler SEO Analyser Bot',
				'link' => 'http://OpenLinkProfiler.org/bot'
			],
			'special_archiver' => [
				'title' => 'Archive.org bot',
				'link' => 'http://www.archive.org/details/archive.org_bot'
			],
			'speedy spider' => [
				'title' => 'Entireweb Speedy Spider',
				'link' => 'http://www.entireweb.com/about/search_tech/speedy_spider/'
			],
			'spider' => [
				'title' => 'Generic Spider',
				'link' => ''
			],
			'spiderling' => [
				'title' => 'Big Web Corpora Natural Language Processing Bot',
				'link' => 'http://nlp.fi.muni.cz/projects/biwec/'
			],
			'sqlmap' => [
				'title' => 'sqlmap Automatic SQL injection and database takeover tool',
				'link' => 'http://sqlmap.org'
			],
			'squidbot' => [
				'title' => 'Squidbot',
				'link' => ''
			],
			'ssblog' => [
				'title' => 'Seesaa SS Blog RSS Crawler',
				'link' => 'https://blog.ss-blog.jp/'
			],
			'sserobots' => [
				'title' => 'SSERobots',
				'link' => ''
			],
			'startmebot' => [
				'title' => 'startmebot',
				'link' => 'https://start.me/bot'
			],
			'statdom.ru' => [
				'title' => 'Domains of Russia Bot',
				'link' => 'http://statdom.ru/bot.html'
			],
			'statonlinerubot' => [
				'title' => 'StatOnlineRuBot',
				'link' => 'https://statonline.ru/'
			],
			'statuscake' => [
				'title' => 'StatusCake VirusScanner',
				'link' => 'https://statuscake.com/automaton/virus.txt'
			],
			'stormcrawler' => [
				'title' => 'StormCrawler Open Source Crawler SDK',
				'link' => 'http://stormcrawler.net/'
			],
			'suggybot' => [
				'title' => 'SuggyBot',
				'link' => 'https://blog.suggy.com/was-ist-suggy/suggy-webcrawler/'
			],
			'summalybot' => [
				'title' => 'SummalyBot',
				'link' => 'https://github.com/syuilo/summaly'
			],
			'superbot' => [
				'title' => 'SuperBot',
				'link' => ''
			],
			'superfeedr bot' => [
				'title' => 'Superfeedr RSS API Bot',
				'link' => 'http://superfeedr.com/'
			],
			'superpagesbot' => [
				'title' => 'Super Pages Bot',
				'link' => ''
			],
			'supremesearch.net' => [
				'title' => 'Supreme Search Engine Bot',
				'link' => 'https://supremesearch.net/'
			],
			'surdotlybot' => [
				'title' => 'SurdotlyBot Security Analysis',
				'link' => 'http://sur.ly/bot.html'
			],
			'symfony browserkit' => [
				'title' => 'Symfony BrowserKit',
				'link' => 'https://symfony.com/doc/current/components/browser_kit.html'
			],
			'synthesio crawler' => [
				'title' => 'Synthesio Crawler',
				'link' => 'https://www.synthesio.com/blog/instagram-analytics-for-business/'
			],
			'tapatalk' => [
				'title' => 'Tapatalk CloudSearch Platform',
				'link' => 'http://www.tapatalk.com/bot.html'
			],
			'telegrambot' => [
				'title' => 'TelegramBot (like TwitterBot)',
				'link' => 'https://telegram.org/blog/link-preview'
			],
			'temeliobot-keyword-scrapper' => [
				'title' => 'Temeliobot Keyword Scrapper',
				'link' => ''
			],
			'testbot' => [
				'title' => 'Test Bot',
				'link' => ''
			],
			'testcrawler' => [
				'title' => 'Test Crawler',
				'link' => ''
			],
			'tineye-bot' => [
				'title' => 'TinEye Open Image Search Crawler',
				'link' => 'http://www.tineye.com/crawler.html'
			],
			'tkbot' => [
				'title' => 'TkBot',
				'link' => ''
			],
			'tmmbot' => [
				'title' => 'Travel and Make Money TMMbot',
				'link' => 'http://www.dslab.ch'
			],
			'todoexpertosbot' => [
				'title' => 'TodoExpertosBot',
				'link' => 'https://www.todoexpertos.com/'
			],
			'tokenspider' => [
				'title' => 'Token Spider',
				'link' => ''
			],
			'tombapublicwebcrawler' => [
				'title' => 'Tomba Public Web Crawler',
				'link' => 'https://tombascraper.com'
			],
			'tombot' => [
				'title' => 'Tombot',
				'link' => 'https://www.tombot.ai/'
			],
			'toutiaospider' => [
				'title' => 'Toutiao Spider',
				'link' => 'http://web.toutiao.com/media_cooperation/'
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
			'trendkite-akashic-crawler' => [
				'title' => 'Trendkite Akashic Crawler',
				'link' => ''
			],
			'triplecheckerrobot' => [
				'title' => 'TripleChecker Website Spelling and Grammar Error Checker',
				'link' => 'https://www.triplechecker.com/'
			],
			'ttd-content' => [
				'title' => 'theTradeDesk Content Scraper',
				'link' => 'https://www.thetradedesk.com/general/ttd-content'
			],
			'turnitinbot' => [
				'title' => 'Turnitin Anti-plagiarism Bot',
				'link' => 'https://turnitin.com/robot/crawlerinfo.html'
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
			'ucmore crawler app' => [
				'title' => 'UCMore Crawler',
				'link' => 'http://ucmore.com/'
			],
			'uipbot' => [
				'title' => 'Semasio uipbot',
				'link' => 'mailto:uipbot@semasio.net'
			],
			'uptimebot' => [
				'title' => 'UptimeBot',
				'link' => 'https://uptime.bot/'
			],
			'uptimerobot' => [
				'title' => 'UptimeRobot',
				'link' => 'http://www.uptimerobot.com/'
			],
			'vebidoobot' => [
				'title' => 'Vebidoo Bot',
				'link' => 'https://blog.vebidoo.de/vebidoobot/'
			],
			'vegibot' => [
				'title' => 'Vegi bot',
				'link' => 'mailto:abuse-report@terrykyleseoagency.com'
			],
			'velenpublicwebcrawler' => [
				'title' => 'Velen Public Web Crawler',
				'link' => 'https://velen.io'
			],
			'virusdie crawler' => [
				'title' => 'VirusDie Antivirus Crawler',
				'link' => 'https://virusdie.com/'
			],
			'viulinkcrawler' => [
				'title' => 'searchVIU Link Crawler',
				'link' => 'https://www.searchviu.com'
			],
			'vkrobot' => [
				'title' => 'VKRobot',
				'link' => ''
			],
			'voilabot' => [
				'title' => 'VoilaBot',
				'link' => 'mailto:support.voilabot@orange-ftgroup.com'
			],
			'voluumdsp' => [
				'title' => 'VoluumDSP Content Bot',
				'link' => 'mailto:dsp-dev@codewise.com'
			],
			'voyager' => [
				'title' => 'Voyager Bot',
				'link' => 'mailto:bot@voyagerx.com'
			],
			'vsusearchspider' => [
				'title' => 'VsuSearchSpider',
				'link' => ''
			],
			'vuhuvbot' => [
				'title' => 'Vuhuvbot',
				'link' => 'http://vuhuv.com/bot.html'
			],
			'webcrawl.net' => [
				'title' => 'WebCrawl.net',
				'link' => ''
			],
			'webgains-bot' => [
				'title' => 'Webgains Bot',
				'link' => 'https://www.webgains.com/public/en/'
			],
			'webliobot' => [
				'title' => 'Webliobot',
				'link' => 'http://www.weblio.jp/info/crawler.jsp'
			],
			'webmoney megastock robot' => [
				'title' => 'WebMoney Megastock Robot',
				'link' => 'https://megastock.com/'
			],
			'website-audit.be' => [
				'title' => 'Website Audit Crawler',
				'link' => 'https://www.website-audit.be/faq'
			],
			'webspider 1.0' => [
				'title' => 'Web Spider 1.0',
				'link' => ''
			],
			'webzip' => [
				'title' => 'Spidersoft WebZIP',
				'link' => 'http://www.spidersoft.com/'
			],
			'wget' => [
				'title' => 'Linux Wget',
				'link' => 'https://www.gnu.org/software/wget/'
			],
			'who.is bot' => [
				'title' => 'who.is bot',
				'link' => ''
			],
			'wiederfreibot' => [
				'title' => 'Wieder Frei Bot',
				'link' => 'http://twitter.com/wiederfrei'
			],
			'wikido' => [
				'title' => 'WikiDo Event Crawler',
				'link' => 'https://www.wikido.com/wikido.php'
			],
			'willie irc bot' => [
				'title' => 'Willie IRC Bot',
				'link' => 'https://github.com/jantman/willie'
			],
			'winhttp' => [
				'title' => 'Microsoft WinHttp',
				'link' => 'https://docs.microsoft.com/en-us/windows/win32/winhttp/about-winhttp'
			],
			'wlc pywikibot' => [
				'title' => 'wlc Pywikibot',
				'link' => 'https://pypi.org/project/pywikibot/'
			],
			'womlpefactory' => [
				'title' => 'WompleFactory',
				'link' => 'http://www.womple.com/bot.html'
			],
			'wonderbot' => [
				'title' => 'wonderbot',
				'link' => 'https://wonder-bot.com/'
			],
			'wordchampbot' => [
				'title' => 'WordChampBot',
				'link' => 'http://www.wordchamp.com/'
			],
			'wotbox' => [
				'title' => 'Wotbox',
				'link' => 'http://www.wotbox.com/bot/'
			],
			'wp.com feedbot' => [
				'title' => 'Automattic WordPress Feedbot',
				'link' => 'https://wp.com'
			],
			'x28-job-bot' => [
				'title' => 'x28 JobagentBot',
				'link' => 'http://x28.ch/bot.html'
			],
			'xaldon webspider' => [
				'title' => 'Xendon WebSpider website mirroring software',
				'link' => 'http://www.xaldon.de/node/32'
			],
			'xenu link sleuth' => [
				'title' => 'Xenu Link Sleuth',
				'link' => 'http://home.snafu.de/tilman/xenulink.html'
			],
			'xovionpagecrawler' => [
				'title' => 'Xovi Ion Page Crawler',
				'link' => 'http://www.xovi.de/'
			],
			'xyz spider' => [
				'title' => 'XYZ Spider',
				'link' => ''
			],
			'yahoo japan' => [
				'title' => 'Yahoo! Japan Web Crawler',
				'link' => 'https://www.yahoo-help.jp/app/answers/detail/p/595/a_id/42716/'
			],
			'yeti/1.0' => [
				'title' => 'Yeti - Naver',
				'link' => 'http://help.naver.com/robots/'
			],
			'yetibot' => [
				'title' => 'YetiBOT',
				'link' => 'http://yetibot.ovh'
			],
			'yisouspider' => [
				'title' => 'YisouSpider Search Engine Bot (Shenma)',
				'link' => 'https://m.sm.cn/'
			],
			'yacybot' => [
				'title' => 'YaCy Search Engine Bot',
				'link' => 'http://yacy.net/bot.html'
			],
			'yellowbrandprotectionbot' => [
				'title' => 'Yellow Brand Protection Bot',
				'link' => 'https://www.yellowbp.com/bot.html'
			],
			'yesslebot' => [
				'title' => 'Yessle Search Engine Bot',
				'link' => 'https://www.yessle.nl'
			],
			'your-search-bot' => [
				'title' => 'Your Search Bot',
				'link' => ''
			],
			'zombiebot' => [
				'title' => 'Zombiebot backlink checker',
				'link' => 'http://www.zombiedomain.net/robot/'
			],
			'zoombot' => [
				'title' => 'SEOZoom Bot',
				'link' => 'https://www.seozoom.co.uk/seo-spider/'
			],
			'zoominfobot' => [
				'title' => 'ZoominfoBot',
				'link' => 'mailto:zoominfobot@zoominfo.com'
			],
			'zumbot' => [
				'title' => 'ZUM Internet Bot',
				'link' => 'https://www.zuminternet.com/en'
			],
			'zyborg' => [
				'title' => 'Zyborg Dead Link Checker',
				'link' => 'http://www.wisenutbot.com'
			],
			'_zbot' => [
				'title' => '_zbot',
				'link' => ''
			],
		];

		return array_merge(parent::getRobotList(), $newBots);
	}
}
