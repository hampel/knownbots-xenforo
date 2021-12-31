<?php namespace Hampel\KnownBots\Service;

use Hampel\KnownBots\Repository\BotFetcherCache;
use Hampel\KnownBots\SubContainer\Log;
use XF\Service\AbstractService;
use XF\Util\File;

class BotFetcher extends AbstractService
{
    protected $url = "https://knownbots.hampel.io/api/bots";

    protected $local = "internal-data://knownbots.json";

    public function fetchBots($force = false)
    {
        $cache = $this->getCacheRepo();
        $log = $this->getLogger();

        $since = $force ? 0 : $cache->getLastChecked();
        $url = $this->url . ($since > 0 ? ("?" . http_build_query(compact('since'))) : '');

        $log->debug('Fetching updated bots', compact('url'));

        $destination = File::getNamedTempFile(sprintf("knownbots-%s.json", \XF::$time));

        if(!$response = $this->app->http()->reader()->getUntrusted($url, [], $destination, [], $error))
        {
            $log->error('Error fetching bots', compact('url', 'destination', 'error'));
            \XF::logError($error);
            return false;
        }

        $bots = json_decode(file_get_contents($destination), true);

        $status = $bots['status'] ?? '';
        if ($status == 'no updates')
        {
            // no data returned
            $log->debug('No new bots found - aborting');
            return;
        }

        if (!$this->isValid($bots))
        {
            $log->error("Invalid data return from fetchBots", compact('url', 'bots'));
            \XF::logError("Invalid data returned from fetchBots [{$url}]");
            return false;
        }

        // save a copy of the full data dump - pretty printed to make it readable
        File::writeToAbstractedPath($this->local, json_encode($bots, JSON_PRETTY_PRINT));

        $this->updateBots($bots);

        return $bots;
    }

    public function updateBots(array $bots)
    {
        if (empty($bots)) return;

        $this->rebuildCodeCache($bots['maps'], 'maps');
        $this->rebuildCodeCache($bots['bots'], 'bots');

        $cache = $this->getCacheRepo();
        $cache->setFalsePositives($bots);

        $cache->setLastChecked($bots['built']);
        $this->getLogger()->debug('Bots updated', [
            'built' => $bots['built'],
            'maps' => count($bots['maps']),
            'bots' => count($bots['bots']),
            'falsepos' => count($bots['falsepos']),
        ]);
    }

    public function rebuildCodeCache(array $data, $type)
    {
        $path = "code-cache://known_bots/{$type}.php";

        $output = "<?php\nreturn " . var_export($data, true) . ';';

        File::writeToAbstractedPath($path, $output);
    }

    public function loadBots()
    {
        $bots = json_decode($this->app->fs()->read($this->local), true);
        return $this->isValid($bots) ? $bots : [];
    }

    protected function isValid(array $bots)
    {
        return isset($bots['status']) &&
            $bots['status'] == 'OK' &&
            isset($bots['built']) &&
            isset($bots['maps']) &&
            isset($bots['bots']) &&
            isset($bots['falsepos']) &&
            is_int($bots['built']) &&
            is_array($bots['maps']) &&
            is_array($bots['bots']) &&
            is_array($bots['falsepos']);
    }

    /**
     * @return BotFetcherCache
     */
    protected function getCacheRepo()
    {
        return $this->app->repository('Hampel\KnownBots:BotFetcherCache');
    }

    /**
     * @return Log
     */
    protected function getLogger()
    {
        return $this->app->get('knownbots.log');
    }

}
