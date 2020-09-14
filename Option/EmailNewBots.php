<?php namespace Hampel\KnownBots\Option;

use XF\Option\AbstractOption;

class EmailNewBots extends AbstractOption
{
	/**
	 * @return bool
	 */
	public static function get()
	{
		return \XF::options()->knownbotsEmailNewBots;
	}

	/**
	 * @return bool
	 */
	public static function isEnabled()
	{
		$emailNewBots = self::get();
		return $emailNewBots['enabled'] == 1;
	}

	public static function getAddress()
	{
		$emailNewBots = self::get();
		return $emailNewBots['email'] ?? "";
	}
}
