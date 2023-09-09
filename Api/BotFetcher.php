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
    protected $baseUrl = 'https://knownbots.hampel.io/api/v2';

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

        $options = [];
        $since = null;
        if (!$force)
        {
            $since = date('D, d M Y H:i:s', $lastChecked) . " GMT";
            $options = ['headers' => ['If-Modified-Since' => $since]];
        }

        $log->info('Fetching updated bots', compact('since'));

        $url = "{$this->baseUrl}/bots";
        $destination = File::getNamedTempFile(sprintf("knownbots-%s.json", \XF::$time));

        if ($this->trustedUrl)
        {
            // only used when we set a manual API url, used to bypass checks so we can use localhost for testing
            $response = $this->app->http()->reader()->get($url, [], $destination, $options, $error);
        }
        else
        {
            // treat URLs as untrusted by default, will run through proxy server if configured
            $response = $this->app->http()->reader()->getUntrusted($url, [], $destination, $options, $error);
        }

        if(!$response)
        {
            $log->error('Error fetching bots', compact('url', 'destination', 'since', 'error'));
            \XF::logError($error);
            return false;
        }
        elseif ($response->getStatusCode() == 304)
        {
            $log->info('Bots not modified');
            return null;
        }

        return json_decode(file_get_contents($destination), true);
    }

    /**
     * @return Log
     */
    protected function getLogger()
    {
        return $this->app['knownbots.log'];
    }
}
