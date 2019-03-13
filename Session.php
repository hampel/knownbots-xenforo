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
		'adbeat' => 'adbeat',
		'ahrefsbot' => 'ahrefs',
		'aiohttp' => 'aiohttp',
		'archive.org_bot' => 'archive.org',
		'apache-httpclient' => 'apache-httpclient',
		'applebot' => 'applebot',
		'baiduspider' => 'baidu',
		'bingbot' => 'bing',
		'bingpreview' => 'bing',
		'binglocalsearch' => 'bing',
		'brokenlinkcheck' => 'brokenlinkcheck',
		'companybook-crawler' => 'companybook',
		'contacts-crawler' => 'scrapinghub',
		'crawler4j' => 'crawler4j',
		'cukbot' => 'cukbot',
		'dalvik' => 'dalvik',
		'dataprovider' => 'dataprovider',
		'dispatch' => 'dispatch',
		'domainappender' => 'domainappender',
		'e.ventures' => 'eventures',
		'facebookexternalhit' => 'facebookextern',
		'garlikcrawler' => 'garlikcrawler',
		'go-http-client' => 'go-http-client',
		'googlebot' => 'google',
		'httrack' => 'httrack',
		'ia_archiver' => 'alexa',
		'indeedbot' => 'indeedbot',
		'just-crawling' => 'just-crawling',
		'knowknot' => 'knowknot',
		'linguee' => 'linguee',
		'linkdexbot' => 'linkdexbot',
		'linkpadbot' => 'linkpadbot',
		'ltx71' => 'ltx71',
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
		'outclicksbot' => 'outclicksbot',
		'panscient' => 'panscient',
		'pcore-http' => 'pcore-http',
		'php' => 'php',
		'pinterestbot' => 'pinterest',
		'proximic' => 'proximic',
		'python-requests' => 'python-requests',
		'quick-crawler' => 'scrapinghub',
		're-re studio' => 're-re studio',
		'scoutjet' => 'scoutjet',
		'scrapy' => 'scrapy',
		'semrushbot' => 'semrushbot',
		'seokicks-robot' => 'seokicks-robot',
		'sitesucker' => 'sitesucker',
		'smtbot' => 'smtbot',
		'sogou web spider' => 'sogou',
		'spbot' => 'spbot',
		'sqlmap' => 'sqlmap',
		'statuscake/virusscanner' => 'statuscake',
		'symfony browserkit' => 'symfony browserkit',
		'symfony2 browserkit' => 'symfony browserkit',
		'um-ln' => 'ubermetrics-technologies',
		'v-bot' => 'voyager',
		'vegi bot' => 'vegibot',
		'wget' => 'wget',
		'wonderbot' => 'wonderbot',
		'wotbox' => 'wotbox',
		'xenu link sleuth' => 'xenu link sleuth',
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
		'adbeat' => [
			'title' => 'Adbeat',
			'link' => 'http://adbeat.com/policy',
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
		'baidu' => [
			'title' => 'Baidu',
			'link' => 'http://www.baidu.com/search/spider.htm'
		],
		'bing' => [
			'title' => 'Bing',
			'link' => 'http://www.bing.com/bingbot.htm'
		],
		'brandwatch' => [
			'title' => 'Brandwatch',
			'link' => 'http://www.brandwatch.com/how-it-works/gathering-data/'
		],
		'brokenlinkcheck' => [
			'title' => 'BrokenLinkCheck.com',
			'link' => 'http://brokenlinkcheck.com/'
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
		'httrack' => [
			'title' => 'HTTrack',
			'link' => 'http://www.httrack.com/'
		],
		'indeedbot' => [
			'title' => 'IndeedBot',
			'link' => 'http://indeedbot.com/'
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
		'proximic' => [
			'title' => 'Proximic',
			'link' => 'http://www.proximic.com/info/spider.php'
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
		'semrushbot' => [
			'title' => 'SemrushBot',
			'link' => 'http://www.semrush.com/bot.html',
		],
		'seokicks-robot' => [
			'title' => 'SEOkicks-Robot',
			'link' => 'http://www.seokicks.de/robot.html',
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
