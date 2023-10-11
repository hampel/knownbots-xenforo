<?php namespace Hampel\KnownBots\Api;

use Hampel\KnownBots\SubContainer\Log;
use XF\Util\File;

class KnownBots
{
    /**
     * @var \XF\App
     */
    protected $app;

    /** @var string */
    protected $baseUrl;

    /** @var bool */
    protected $trustedUrl;

    public function __construct(\XF\App $app, $baseUrl = 'https://knownbots.hampel.io/api', $trustedUrl = false)
    {
        $this->app = $app;
        $this->baseUrl = $baseUrl;
        $this->trustedUrl = $trustedUrl;
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

        $url = "{$this->baseUrl}/v3/bots";

        $log->info('Fetching updated bots', compact('since', 'url'));

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

        $status = $response->getStatusCode();

        if ($status == 200)
        {
            // we're all good
            return json_decode(file_get_contents($destination), true);
        }

        $reason = $response->getReasonPhrase();

        if ($status == 304)
        {
            // not modified, no further action required
            $log->info('Bots not modified');
            return null;
        }
        elseif ($status >= 500)
        {
            // service unavailable or similar - log it, but don't create an error message, it's hopefully only temporary
            $log->warning('Server error fetching bots', compact('url', 'destination', 'since', 'reason'));
            return null;
        }
        else
        {
            // some other more serious error - will log it and create error message for XF logs
            \XF::logError("Could not fetch bots - request to [{$url}] returned status code [{$status}]: {$reason}");
            $log->error('Could not fetch bots', compact('url', 'destination', 'since', 'reason'));
            return false;
        }
    }

    /**
     * @return Log
     */
    protected function getLogger()
    {
        return $this->app['knownbots.log'];
    }
}
