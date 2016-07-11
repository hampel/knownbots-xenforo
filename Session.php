<?php

class KnownBots_Session extends XFCP_KnownBots_Session
{
	/**
	 * Known robot user agent substrings. Key is user agent substring, value is robot key name.
	 *
	 * There's a great list here: http://user-agent-string.info/list-of-ua/bots
	 *
	 * @var array
	 */
	protected $_knownRobots = array(
		'ahrefsbot' => 'ahrefs',
		'archive.org_bot' => 'archive.org',
		'baiduspider' => 'baidu',
		'bingbot' => 'bing',
		'facebookexternalhit' => 'facebookextern',
		'googlebot' => 'google',
		'ia_archiver' => 'alexa',
		'magpie-crawler' => 'brandwatch',
		'mediapartners-google' => 'google-adsense',
		'mj12bot' => 'mj12',
		'msnbot' => 'msnbot',
		'proximic' => 'proximic',
		'scoutjet' => 'scoutjet',
		'smtbot' => 'smtbot',
		'sogou web spider' => 'sogou',
		'yahoo! slurp' => 'yahoo',
		'yandex' => 'yandex',

		/*'crawler',
		'php/',
		'zend_http_client',*/
	);

	/**
	 * Maps an robot key to info about it.
	 *
	 * @var array
	 */
	protected $_robotMap = array(
		'ahrefs' => array(
			'title' => 'Ahrefs',
			'link' => 'https://ahrefs.com/robot',
		),
		'alexa' => array(
			'title' => 'Alexa',
			'link' => 'http://www.alexa.com/help/webmasters',
		),
		'archive.org' => array(
			'title' => 'Internet Archive',
			'link' => 'http://www.archive.org/details/archive.org_bot'
		),
		'baidu' => array(
			'title' => 'Baidu',
			'link' => 'http://www.baidu.com/search/spider.htm'
		),
		'bing' => array(
			'title' => 'Bing',
			'link' => 'http://www.bing.com/bingbot.htm'
		),
		'brandwatch' => array(
			'title' => 'Brandwatch',
			'link' => 'http://www.brandwatch.com/how-it-works/gathering-data/'
		),
		'facebookextern' => array(
			'title' => 'Facebook',
			'link' => 'http://www.facebook.com/externalhit_uatext.php'
		),
		'google' => array(
			'title' => 'Google',
			'link' => 'https://support.google.com/webmasters/answer/182072'
		),
		'google-adsense' => array(
			'title' => 'Google AdSense',
			'link' => 'https://support.google.com/webmasters/answer/182072'
		),
		'mj12' => array(
			'title' => 'Majestic-12',
			'link' => 'http://majestic12.co.uk/bot.php',
		),
		'msnbot' => array(
			'title' => 'MSN',
			'link' => 'http://search.msn.com/msnbot.htm'
		),
		'proximic' => array(
			'title' => 'Proximic',
			'link' => 'http://www.proximic.com/info/spider.php'
		),
		'scoutjet' => array(
			'title' => 'Blekko',
			'link' => 'http://www.scoutjet.com/',
		),
		'sogou' => array(
			'title' => 'Sogou',
			'link' => 'http://www.sogou.com/docs/help/webmasters.htm#07'
		),
		'smtbot' => array(
			'title' => 'SMTBot',
			'link' => 'https://www.similartech.com/smtbot'
		),
		'unknown' => array(
			'title' => 'Unknown',
			'link' => ''
		),
		'yahoo' => array(
			'title' => 'Yahoo',
			'link' => 'http://help.yahoo.com/help/us/ysearch/slurp'
		),
		'yandex' => array(
			'title' => 'Yandex',
			'link' => 'http://help.yandex.com/search/?id=1112030'
		)
	);
}
