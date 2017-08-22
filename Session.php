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
		'ahrefsbot' => 'ahrefs',
		'archive.org_bot' => 'archive.org',
		'baiduspider' => 'baidu',
		'bingbot' => 'bing',
		'binglocalsearch' => 'bing',
		'crawler4j' => 'crawler4j',
		'facebookexternalhit' => 'facebookextern',
		'googlebot' => 'google',
		'ia_archiver' => 'alexa',
		'linguee' => 'linguee',
		'linkpadbot' => 'linkpadbot',
		'ltx71' => 'ltx71',
		'magpie-crawler' => 'brandwatch',
		'mediapartners-google' => 'google-adsense',
		'mixrankbot' => 'mixrankbot',
		'mj12bot' => 'mj12',
		'msnbot' => 'msnbot',
		'nlnz_iaharvester' => 'nlnz',
		'outclicksbot' => 'outclicksbot',
		'proximic' => 'proximic',
		'scoutjet' => 'scoutjet',
		'semrushbot' => 'semrushbot',
		'seokicks-robot' => 'seokicks-robot',
		'sitesucker' => 'sitesucker',
		'smtbot' => 'smtbot',
		'sogou web spider' => 'sogou',
		'statuscake/virusscanner' => 'statuscake',
		'vegi bot' => 'vegibot',
		'wotbox' => 'wotbox',
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
		'ahrefs' => [
			'title' => 'Ahrefs',
			'link' => 'https://ahrefs.com/robot',
		],
		'alexa' => [
			'title' => 'Alexa',
			'link' => 'http://www.alexa.com/help/webmasters',
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
		'crawler4j' => [
			'title' => 'crawler4j',
			'link' => 'http://code.google.com/p/crawler4j/'
		],
		'facebookextern' => [
			'title' => 'Facebook',
			'link' => 'http://www.facebook.com/externalhit_uatext.php'
		],
		'google' => [
			'title' => 'Google',
			'link' => 'https://support.google.com/webmasters/answer/182072'
		],
		'google-adsense' => [
			'title' => 'Google AdSense',
			'link' => 'https://support.google.com/webmasters/answer/182072'
		],
		'linguee' => [
			'title' => 'Linguee Bot',
			'link' => 'http://www.linguee.com/bot'
		],
		'linkpadbot' => [
			'title' => 'LinkpadBot',
			'link' => 'http://www.linkpad.ru'
		],
		'ltx71' => [
			'title' => 'LTX71',
			'link' => 'http://ltx71.com/'
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
		'proximic' => [
			'title' => 'Proximic',
			'link' => 'http://www.proximic.com/info/spider.php'
		],
		'scoutjet' => [
			'title' => 'Blekko',
			'link' => 'http://www.scoutjet.com/',
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
			'link' => '',
		],
		'sogou' => [
			'title' => 'Sogou',
			'link' => 'http://www.sogou.com/docs/help/webmasters.htm#07'
		],
		'smtbot' => [
			'title' => 'SMTBot',
			'link' => 'https://www.similartech.com/smtbot'
		],
		'statuscake' => [
			'title' => 'StatusCake VirusScanner',
			'link' => 'https://statuscake.com/automaton/virus.txt'
		],
		'unknown' => [
			'title' => 'Unknown',
			'link' => ''
		],
		'vegibot' => [
			'title' => 'Vegi bot',
			'link' => 'mailto:abuse-report@terrykyleseoagency.com'
		],
		'wotbox' => [
			'title' => 'Wotbox',
			'link' => 'http://www.wotbox.com/bot/'
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
}
