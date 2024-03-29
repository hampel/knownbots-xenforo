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

	public static function getAddress()
	{
		$emailNewBots = self::get();

        if (empty($emailNewBots['email']))
        {
            return \XF::options()->contactEmailAddress;
        }

        return $emailNewBots['email'];
	}
}
