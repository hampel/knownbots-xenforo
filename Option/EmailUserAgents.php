<?php namespace Hampel\KnownBots\Option;

use XF\Option\AbstractOption;

class EmailUserAgents extends AbstractOption
{
	/**
	 * @return bool
	 */
	public static function get()
	{
		return \XF::options()->knownbotsEmailUserAgents;
	}

	/**
	 * @return bool
	 */
	public static function isEnabled()
	{
		$emailNewBots = self::get();
		return $emailNewBots['enabled'] == 1;
	}

	public static function getAddresses()
	{
		$emailNewBots = self::get();

        $addresses = array_map('trim', explode(',', $emailNewBots['email'] ?? ''));
        // return our array of addresses, or if empty, an array containing the board contact email address
        return !empty($addresses) ? $addresses : [\XF::options()->contactEmailAddress];
	}
}
