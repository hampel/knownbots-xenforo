<?php namespace Hampel\KnownBots\XF\Data;

class Robot extends XFCP_Robot
{
	public function getRobotUserAgents()
	{
		$newBots = [
			'7siters' => '7siters',
			'adbeat' => 'adbeat',
			'adscanner' => 'adscanner',
			'ahrefsbot' => 'ahrefs',
			'aiohttp' => 'aiohttp',
			'archivebot' => 'archiveteam',
			'apache-httpclient' => 'apache-httpclient',
			'applebot' => 'applebot',
			'bingpreview' => 'bing',
			'binglocalsearch' => 'bing',
			'brandverity' => 'brandverity',
			'brokenlinkcheck' => 'brokenlinkcheck',
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
			'garlikcrawler' => 'garlikcrawler',
			'go-http-client' => 'go-http-client',
			'httrack' => 'httrack',
			'indeedbot' => 'indeedbot',
			'just-crawling' => 'just-crawling',
			'knowknot' => 'knowknot',
			'linguee' => 'linguee',
			'linkdexbot' => 'linkdexbot',
			'linkpadbot' => 'linkpadbot',
			'ltx71' => 'ltx71',
			'mail.ru' => 'mail.ru',
			'megaindex' => 'megaindex',
			'mfibot' => 'mfibot',
			'microsoft office protocol discovery' => 'microsoft-office',
			'mixrankbot' => 'mixrankbot',
			'nlnz_iaharvester' => 'nlnz',
			'outclicksbot' => 'outclicksbot',
			'panscient' => 'panscient',
			'pcore-http' => 'pcore-http',
			'php' => 'php',
			'pinterestbot' => 'pinterest',
			'python-requests' => 'python-requests',
			'quick-crawler' => 'scrapinghub',
			're-re studio' => 're-re studio',
			'scrapy' => 'scrapy',
			'semanticscholarbot' => 'semanticscholarbot',
			'semrushbot' => 'semrushbot',
			'seokicks-robot' => 'seokicks-robot',
			'sitesucker' => 'sitesucker',
			'smtbot' => 'smtbot',
			'spbot' => 'spbot',
			'sqlmap' => 'sqlmap',
			'statuscake/virusscanner' => 'statuscake',
			'symfony browserkit' => 'symfony browserkit',
			'symfony2 browserkit' => 'symfony browserkit',
			'tracemyfile' => 'tracemyfile',
			'um-ln' => 'ubermetrics-technologies',
			'v-bot' => 'voyager',
			'vegi bot' => 'vegibot',
			'wget' => 'wget',
			'wonderbot' => 'wonderbot',
			'wotbox' => 'wotbox',
			'xenu link sleuth' => 'xenu link sleuth',
			'telegrambot' => 'telegrambot',
		];

		return array_merge(parent::getRobotUserAgents(), $newBots);
	}

	public function getRobotList()
	{
		$newBots = [
			'7siters' => [
				'title' => '7Siters',
				'link' => 'https://7ooo.ru/siters/',
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
			'apache-httpclient' => [
				'title' => 'Apache-HttpClient',
				'link' => 'https://hc.apache.org/',
			],
			'applebot' => [
				'title' => 'Applebot',
				'link' => 'http://www.apple.com/go/applebot',
			],
			'archiveteam' => [
				'title' => 'Archive Team',
				'link' => 'https://www.archiveteam.org/index.php?title=ArchiveBot'
			],
			'brandverity' => [
				'title' => 'BrandVerity',
				'link' => 'http://www.brandverity.com/why-is-brandverity-visiting-me'
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
			'duckduckbot' => [
				'title' => 'DuckDuckBot',
				'link' => 'http://duckduckgo.com/duckduckbot.html'
			],
			'eventures' => [
				'title' => 'e.ventures Investment Crawler',
				'link' => 'https://www.eventures.vc/'
			],
			'exabot' => [
				'title' => 'Exabot',
				'link' => 'http://www.exabot.com/go/robot'
			],
			'garlikcrawler' => [
				'title' => 'GarlikCrawler',
				'link' => 'http://garlik.com/'
			],
			'go-http-client' => [
				'title' => 'Go-http-client',
				'link' => 'https://golang.org/pkg/net/http/'
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
			're-re studio' => [
				'title' => 'Re-re Studio',
				'link' => 'http://re-re.ru/'
			],
			'scrapinghub' => [
				'title' => 'Scrapinghub',
				'link' => 'https://scrapinghub.com/',
			],
			'scrapy' => [
				'title' => 'Scrapy',
				'link' => 'http://scrapy.org',
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
			'tracemyfile' => [
				'title' => 'TraceMyFile',
				'link' => 'https://www.tracemyfile.com/'
			],
			'ubermetrics-technologies' => [
				'title' => 'Ubermetrics',
				'link' => 'https://www.ubermetrics-technologies.com/'
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
			'telegrambot' => [
				'title' => 'TelegramBot (like TwitterBot)',
				'link' => 'https://telegram.org/blog/link-preview'
			],
		];

		return array_merge(parent::getRobotList(), $newBots);
	}
}
