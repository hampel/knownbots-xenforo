<?php namespace Hampel\KnownBots\Cron;

use Hampel\KnownBots\Exception\KnownBotsException;
use Hampel\KnownBots\Exception\ServerException;
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
            $log->info("Fetch new bots: disabled - aborting");
            return;
        }

        try
        {
            self::getApi()->fetchBots();
        }
        catch (KnownBotsException $e)
        {
            if (get_class($e) == ServerException::class)
            {
                $log->warning($e->getMessage());
            }
            else
            {
                $log->error($e->getMessage());
                \XF::logException($e);
            }
        }
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
