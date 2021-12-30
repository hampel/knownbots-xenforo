<?php namespace Hampel\KnownBots\Cron;

use Hampel\KnownBots\Option\FetchNewBots;
use Hampel\KnownBots\Service\BotFetcher;
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

        /** @var BotFetcher $service */
        $service = \XF::service('Hampel\KnownBots:BotFetcher');
        $service->fetchBots();
    }

    /**
     * @return Log
     */
    protected static function getLogger()
    {
        return \XF::app()->get('knownbots.log');
    }
}
