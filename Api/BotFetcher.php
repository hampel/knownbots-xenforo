<?php namespace Hampel\KnownBots\Api;

use Hampel\KnownBots\SubContainer\Cache;
use Hampel\KnownBots\SubContainer\Log;
use XF\Util\File;

class BotFetcher
{
    protected $url = "https://knownbots.hampel.io/api/bots";

    /**
     * @var \XF\App
     */
    protected $app;

    public function __construct(\XF\App $app)
    {
        $this->app = $app;
    }

    public function fetchBots($force)
    {
        $cache = $this->getCache();
        $log = $this->getLogger();

        $url = $this->buildUrl($force ? 0 : $cache->getLastChecked());

        $log->debug('Fetching updated bots', compact('url'));

        $destination = File::getNamedTempFile(sprintf("knownbots-%s.json", \XF::$time));

        if(!$response = $this->app->http()->reader()->getUntrusted($url, [], $destination, [], $error))
        {
            $log->error('Error fetching bots', compact('url', 'destination', 'error'));
            \XF::logError($error);
            return false;
        }

        return json_decode(file_get_contents($destination), true);
    }

    protected function buildUrl($since)
    {
        return $this->url . ($since > 0 ? ("?" . http_build_query(compact('since'))) : '');
    }

    /**
     * @return Log
     */
    protected function getLogger()
    {
        return $this->app['knownbots.log'];
    }

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return $this->app['knownbots.cache'];
    }
}
