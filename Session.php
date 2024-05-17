<?php

class KnownBots_Session extends XFCP_KnownBots_Session
{
	/**
	 * Also check this comprehensive list as a reference: https://github.com/JayBizzle/Crawler-Detect/blob/master/tests/crawlers.txt
	 */

	/**
	 * Known robot user agent substrings. Key is user agent substring, value is robot key name.
	 *
	 * There's a great list here: https://udger.com/resources/ua-list/crawlers
	 *
	 * @var array
	 */
	protected $_knownRobots = [
		'7siters' => '7siters',
        'accompanybot' => 'accompanybot',
		'adbeat' => 'adbeat',
		'adscanner' => 'adscanner',
		'ahrefsbot' => 'ahrefs',
		'aiohttp' => 'aiohttp',
		'archive.org_bot' => 'archive.org',
		'archivebot' => 'archiveteam',
		'apache-httpclient' => 'apache-httpclient',
		'applebot' => 'applebot',
		'baiduspider' => 'baidu',
		'bingbot' => 'bing',
		'bingpreview' => 'bing',
		'binglocalsearch' => 'bing',
		'brandverity' => 'brandverity',
		'brokenlinkcheck' => 'brokenlinkcheck',
        'bytespider' => 'bytespider',
		'companybook-crawler' => 'companybook',
		'contacts-crawler' => 'scrapinghub',
		'crawler4j' => 'crawler4j',
		'cukbot' => 'cukbot',
		'curl' => 'curl',
		'dalvik' => 'dalvik',
		'dataprovider' => 'dataprovider',
		'dispatch' => 'dispatch',
		'domainappender' => 'domainappender',
		'duckduckbot' => 'duckduckbot',
		'e.ventures' => 'eventures',
		'exabot' => 'exabot',
		'facebookexternalhit' => 'facebookextern',
		'garlikcrawler' => 'garlikcrawler',
		'go-http-client' => 'go-http-client',
		'googlebot' => 'google',
        'google-read-aloud' => 'google-read-aloud',
        'gptbot' => 'gptbot',
		'httrack' => 'httrack',
        'httprs' => 'httprs',
		'ia_archiver' => 'alexa',
		'indeedbot' => 'indeedbot',
        'java/11.0.10' => 'java',
		'just-crawling' => 'just-crawling',
		'knowknot' => 'knowknot',
		'linguee' => 'linguee',
		'linkdexbot' => 'linkdexbot',
		'linkpadbot' => 'linkpadbot',
		'ltx71' => 'ltx71',
        'lync/16.0' => 'lync',
		'magpie-crawler' => 'brandwatch',
		'mail.ru' => 'mail.ru',
		'mediapartners-google' => 'google-adsense',
		'megaindex' => 'megaindex',
		'mfibot' => 'mfibot',
		'microsoft office protocol discovery' => 'microsoft-office',
		'mixrankbot' => 'mixrankbot',
		'mj12bot' => 'mj12',
		'msnbot' => 'msnbot',
		'nlnz_iaharvester' => 'nlnz',
        'node-fetch' => 'node-fetch',
		'outclicksbot' => 'outclicksbot',
		'panscient' => 'panscient',
        'paracrawl' => 'paracrawl',
		'pcore-http' => 'pcore-http',
		'php' => 'php',
		'pinterestbot' => 'pinterest',
		'proximic' => 'proximic',
        'punkspider' => 'punkspider',
		'python-requests' => 'python-requests',
		'quick-crawler' => 'scrapinghub',
		're-re studio' => 're-re studio',
		'scoutjet' => 'scoutjet',
		'scrapy' => 'scrapy',
        'seekportbot' => 'seekportbot',
		'semanticscholarbot' => 'semanticscholarbot',
		'semrushbot' => 'semrushbot',
		'seokicks-robot' => 'seokicks-robot',
        'sidetrade indexer bot' => 'sidetrade-indexer-bot',
		'sitesucker' => 'sitesucker',
		'smtbot' => 'smtbot',
		'sogou web spider' => 'sogou',
		'spbot' => 'spbot',
		'sqlmap' => 'sqlmap',
		'statuscake/virusscanner' => 'statuscake',
		'symfony browserkit' => 'symfony browserkit',
		'symfony2 browserkit' => 'symfony browserkit',
		'tracemyfile' => 'tracemyfile',
        'thesafexinternetsearch' => 'thesafexinternetsearch',
		'um-ln' => 'ubermetrics-technologies',
		'v-bot' => 'voyager',
		'vegi bot' => 'vegibot',
		'wget' => 'wget',
        'winhttp' => 'winhttp',
		'wonderbot' => 'wonderbot',
		'wotbox' => 'wotbox',
		'xenu link sleuth' => 'xenu link sleuth',
        'yahoo link preview' => 'yahoo',
		'yahoo! slurp' => 'yahoo',
		'yandex' => 'yandex',

		/*'crawler',
		'php/',
		'zend_http_client',*/
	];

	/**
	 * Maps an robot key to info about it.
	 *
	 * @var array
	 */
	protected $_robotMap = [
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
		'alexa' => [
			'title' => 'Alexa',
			'link' => 'http://www.alexa.com/help/webmasters',
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
			'title' => 'Internet Archive',
			'link' => 'http://www.archive.org/details/archive.org_bot'
		],
		'archiveteam' => [
			'title' => 'Archive Team',
			'link' => 'https://www.archiveteam.org/index.php?title=ArchiveBot'
		],
		'baidu' => [
			'title' => 'Baidu',
			'link' => 'http://www.baidu.com/search/spider.htm'
		],
		'bing' => [
			'title' => 'Bing',
			'link' => 'http://www.bing.com/bingbot.htm'
		],
		'brandverity' => [
			'title' => 'BrandVerity',
			'link' => 'http://www.brandverity.com/why-is-brandverity-visiting-me'
		],
		'brandwatch' => [
			'title' => 'Brandwatch',
			'link' => 'http://www.brandwatch.com/how-it-works/gathering-data/'
		],
		'brokenlinkcheck' => [
			'title' => 'BrokenLinkCheck.com',
			'link' => 'http://brokenlinkcheck.com/'
		],
        'bytespider' => [
            'title' => 'Bytespider',
            'link' => 'mailto:spider-feedback@bytedance.com'
        ],
		'companybook' => [
			'title' => 'Companybook',
			'link' => 'https://www.companybooknetworking.com/'
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
		'dispatch' => [
			'title' => 'dispatch',
			'link' => ''
		],
		'domainappender' => [
			'title' => 'DomainAppender',
			'link' => 'http://www.profound.net/domainappender'
		],
		'eventures' => [
			'title' => 'e.ventures Investment Crawler',
			'link' => 'https://www.eventures.vc/'
		],
		'exabot' => [
			'title' => 'Exabot',
			'link' => 'http://www.exabot.com/go/robot'
		],
		'facebookextern' => [
			'title' => 'Facebook',
			'link' => 'http://www.facebook.com/externalhit_uatext.php'
		],
		'garlikcrawler' => [
			'title' => 'GarlikCrawler',
			'link' => 'http://garlik.com/'
		],
		'go-http-client' => [
			'title' => 'Go-http-client',
			'link' => 'https://golang.org/pkg/net/http/'
		],
		'google' => [
			'title' => 'Google',
			'link' => 'https://support.google.com/webmasters/answer/182072'
		],
		'google-adsense' => [
			'title' => 'Google AdSense',
			'link' => 'https://support.google.com/webmasters/answer/182072'
		],
        'google-read-aloud' => [
            'title' => 'Google Read Aloud',
            'link' => 'https://support.google.com/webmasters/answer/1061943'
        ],
        'gptbot' => [
            'title' => 'GPT Bot',
            'link' => 'https://openai.com/gptbot'
        ],
		'httrack' => [
			'title' => 'HTTrack',
			'link' => 'http://www.httrack.com/'
		],
        'httprs' => [
            'title' => 'httprs',
            'link' => ''
        ],
		'indeedbot' => [
			'title' => 'IndeedBot',
			'link' => 'http://indeedbot.com/'
		],
        'java' => [
            'title' => 'Java',
            'link' => ''
        ],
		'just-crawling' => [
			'title' => 'Just-Crawling',
			'link' => ''
		],
		'knowknot' => [
			'title' => 'Knowknot',
			'link' => 'http://knowknot.com/faq.htm'
		],
		'linguee' => [
			'title' => 'Linguee Bot',
			'link' => 'http://www.linguee.com/bot'
		],
		'linkdexbot' => [
			'title' => 'linkdexbot',
			'link' => 'http://www.linkdex.com/bots/'
		],
		'linkpadbot' => [
			'title' => 'LinkpadBot',
			'link' => 'http://www.linkpad.ru'
		],
		'ltx71' => [
			'title' => 'LTX71',
			'link' => 'http://ltx71.com/'
		],
        'lync' => [
            'title' => 'Lync',
            'link' => ''
        ],
		'mail.ru' => [
			'title' => 'Mail.RU',
			'link' => 'http://go.mail.ru/help/robots',
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
		'mj12' => [
			'title' => 'Majestic-12',
			'link' => 'http://majestic12.co.uk/bot.php',
		],
		'msnbot' => [
			'title' => 'MSN',
			'link' => 'http://search.msn.com/msnbot.htm'
		],
		'nlnz' => [
			'title' => 'NLNZ_IAHarvester2017',
			'link' => 'https://natlib.govt.nz/publishers-and-authors/web-harvesting/domain-harvest'
		],
        'node-fetch' => [
            'title' => 'Node Fetch',
            'link' => 'https://github.com/bitinn/node-fetch'
        ],
		'outclicksbot' => [
			'title' => 'OutclicksBot',
			'link' => 'https://www.outclicks.net/agent/fkn6dy'
		],
		'panscient' => [
			'title' => 'panscient.com',
			'link' => 'http://panscient.com/'
		],
        'paracrawl' => [
            'title' => 'Paracrawl',
            'link' => ''
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
		'proximic' => [
			'title' => 'Proximic',
			'link' => 'http://www.proximic.com/info/spider.php'
		],
        'punkspider' => [
            'title' => 'Punkspider',
            'link' => 'mailto:support@hyperiongray.com'
        ],
		're-re studio' => [
			'title' => 'Re-re Studio',
			'link' => 'http://re-re.ru/'
		],
		'scoutjet' => [
			'title' => 'Blekko',
			'link' => 'http://www.scoutjet.com/',
		],
		'scrapinghub' => [
			'title' => 'Scrapinghub',
			'link' => 'https://scrapinghub.com/',
		],
		'scrapy' => [
			'title' => 'Scrapy',
			'link' => 'http://scrapy.org',
		],
        'seekportbot' => [
            'title' => 'SeekportBot',
            'link' => 'https://bot.seekport.com',
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
        'sidetrade-indexer-bot' => [
            'title' => 'Sidetrade indexer bot',
            'link' => '',
        ],
		'sitesucker' => [
			'title' => 'SiteSucker for OS X',
			'link' => 'http://ricks-apps.com/osx/sitesucker/',
		],
		'sogou' => [
			'title' => 'Sogou',
			'link' => 'http://www.sogou.com/docs/help/webmasters.htm#07'
		],
		'smtbot' => [
			'title' => 'SMTBot',
			'link' => 'https://www.similartech.com/smtbot'
		],
		'spbot' => [
			'title' => 'SMTBot',
			'link' => 'http://OpenLinkProfiler.org/bot'
		],
		'sqlmap' => [
			'title' => 'sqlmap',
			'link' => 'http://sqlmap.org'
		],
		'statuscake' => [
			'title' => 'StatusCake VirusScanner',
			'link' => 'https://statuscake.com/automaton/virus.txt'
		],
		'symfony browserkit' => [
			'title' => 'Symfony BrowserKit',
			'link' => 'https://symfony.com/doc/current/components/browser_kit.html'
		],
        'thesafexinternetsearch' => [
            'title' => 'TheSafexInternetSearch',
            'link' => ''
        ],
		'tracemyfile' => [
			'title' => 'TraceMyFile',
			'link' => 'https://www.tracemyfile.com/'
		],
		'ubermetrics-technologies' => [
			'title' => 'Ubermetrics',
			'link' => 'https://www.ubermetrics-technologies.com/'
		],
		'unknown' => [
			'title' => 'Unknown',
			'link' => ''
		],
		'vegibot' => [
			'title' => 'Vegi bot',
			'link' => 'mailto:abuse-report@terrykyleseoagency.com'
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
            'title' => 'winHttp',
            'link' => ''
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
		'yahoo' => [
			'title' => 'Yahoo',
			'link' => 'http://help.yahoo.com/help/us/ysearch/slurp'
		],
		'yandex' => [
			'title' => 'Yandex',
			'link' => 'http://help.yandex.com/search/?id=1112030'
		],
	];

	public function getRobotMap()
	{
		return $this->_robotMap;
	}
}
