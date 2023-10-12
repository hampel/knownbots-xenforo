<?php namespace Hampel\KnownBots\Option;

use XF\Option\AbstractOption;

class SendUserAgents extends AbstractOption
{
	/**
	 * @return bool
	 */
	public static function get()
	{
		return \XF::options()->knownbotsSendUserAgents;
	}

    public static function set(array $optionValue)
    {
        return \XF::repository('XF:Option')->updateOption('knownbotsSendUserAgents', $optionValue);
    }

	/**
	 * @return bool
	 */
	public static function isEnabled()
	{
		$sendUserAgents = self::get();
		return $sendUserAgents['enabled'] == 1;
	}

	public static function validationToken()
	{
        $sendUserAgents = self::get();

        if (!$sendUserAgents['enabled'])
        {
            return null;
        }

		return $sendUserAgents['validation_token'] ?? null;
	}

    public static function apiToken()
    {
        $sendUserAgents = self::get();

        if (!$sendUserAgents['enabled'])
        {
            return null;
        }

        return $sendUserAgents['api_token'] ?? null;
    }
}
