<?php namespace Hampel\KnownBots\Option;

use XF\Option\AbstractOption;

class FetchNewBots extends AbstractOption
{
	/**
	 * @return bool
	 */
	public static function get()
	{
		return \XF::options()->knownbotsFetchNewBots;
	}

	/**
	 * @return bool
	 */
	public static function isEnabled()
	{
        return self::get();
	}
}
