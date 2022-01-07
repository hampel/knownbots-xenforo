<?php namespace Hampel\KnownBots\Cron;

use Hampel\KnownBots\Option\FetchNewBots;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Log;

class FetchBots
{
    public static function fetchBots()
    {
        $log = self::getLogger();

        if (!FetchNewBots::isEnabled())
        {
            $log->debug("Fetch new bots: disabled - aborting");
            return;
        }

        self::getApi()->fetchBots();
    }

    /**
     * @return Log
     */
    protected static function getLogger()
    {
        return \XF::app()->container('knownbots.log');
    }

    /**
     * @return Api
     */
    protected static function getApi()
    {
        return \XF::app()->container('knownbots.api');
    }
}
