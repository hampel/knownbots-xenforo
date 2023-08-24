<?php namespace Hampel\KnownBots\Api;

use Hampel\KnownBots\SubContainer\Log;
use XF\Util\File;

class BotFetcher
{
    /**
     * @var \XF\App
     */
    protected $app;

    /** @var string */
    protected $baseUrl = 'https://knownbots.hampel.io/api';

    /** @var bool */
    protected $trustedUrl = false;

    public function __construct(\XF\App $app)
    {
        $this->app = $app;
    }

    /**
     * @param $url string Base URL for API calls
     * @param $trusted bool Should the base url be considered  trusted? Default is false, set to true only for testing environments
     * @return void
     */
    public function setUrl($url, $trusted = false)
    {
        $this->baseUrl = rtrim($url, '/');
        $this->trustedUrl = $trusted;
    }

    public function fetch($lastChecked, $force = false)
    {
        $log = $this->getLogger();

        $url = $this->buildUrl($force ? 0 : $lastChecked);

        $log->debug('Fetching updated bots', compact('url'));

        $destination = File::getNamedTempFile(sprintf("knownbots-%s.json", \XF::$time));

        if ($this->trustedUrl)
        {
            // only used when we set a manual API url, used to bypass checks so we can use localhost for testing
            $response = $this->app->http()->reader()->get($url, [], $destination, [], $error);
        }
        else
        {
            // treat URLs as untrusted by default, will run through proxy server if configured
            $response = $this->app->http()->reader()->getUntrusted($url, [], $destination, [], $error);
        }

        if(!$response)
        {
            $log->error('Error fetching bots', compact('url', 'destination', 'error'));
            \XF::logError($error);
            return false;
        }

        return json_decode(file_get_contents($destination), true);
    }

    protected function buildUrl($since)
    {
        return "{$this->baseUrl}/bots" . ($since > 0 ? ("?" . http_build_query(compact('since'))) : '');
    }

    /**
     * @return Log
     */
    protected function getLogger()
    {
        return $this->app['knownbots.log'];
    }
}
