<?php namespace Hampel\KnownBots\Option;

use XF\Option\AbstractOption;

class ShowBotStats extends AbstractOption
{
	/**
	 * @return bool
	 */
	public static function get()
	{
		return (bool)\XF::options()->knownbotsShowBotStats;
	}

	/**
	 * @return bool
	 */
	public static function isEnabled()
	{
		return self::get();
	}
}
