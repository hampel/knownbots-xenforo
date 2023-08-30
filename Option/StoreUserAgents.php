<?php namespace Hampel\KnownBots\Option;

use XF\Option\AbstractOption;

class StoreUserAgents extends AbstractOption
{
	/**
	 * @return bool
	 */
	public static function get()
	{
		return \XF::options()->knownbotsStoreUserAgents;
	}

	/**
	 * @return bool
	 */
	public static function isEnabled()
	{
		$storeUserAgents = self::get();
		return $storeUserAgents['enabled'] == 1;
	}

	public static function daysUntilPurge()
	{
        $storeUserAgents = self::get();
		return $storeUserAgents['days'] ?? 90;
	}
}
