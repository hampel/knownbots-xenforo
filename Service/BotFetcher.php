<?php namespace Hampel\KnownBots\Service;

use Hampel\KnownBots\Repository\BotFetcherCache;
use Hampel\KnownBots\Repository\BotRepository;
use Hampel\KnownBots\Repository\MapRepository;
use Hampel\KnownBots\SubContainer\Log;
use XF\Service\AbstractService;
use XF\Util\File;

class BotFetcher extends AbstractService
{
    protected $url = "https://knownbots.hampel.io/api/bots";

    protected $local = "internal-data://knownbots.json";

    public function fetchBots($all = false)
    {
        $cache = $this->getCacheRepo();
        $log = $this->getLogger();

        $since = $all ? 0 : $cache->getLastChecked();
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

        if ($all)
        {
            // save a copy of the full data dump - pretty printed to make it readable
            File::writeToAbstractedPath($this->local, json_encode($bots, JSON_PRETTY_PRINT));
        }

        $count = $this->updateBots($bots, $all);

        $log->info('Updated map & bot data', $count);
    }

    public function updateBots(array $bots, $all = false)
    {
        $cache = $this->getCacheRepo();

        $count = [];
        $count['maps'] = $this->getMapRepository()->updateMaps($bots['maps'], $all);
        $count['bots'] = $this->getBotRepository()->updateBots($bots['bots'], $all);

        $cache->setFalsePositives($bots);
        $count['falsepos'] = count($bots['falsepos']);

        $this->getLogger()->debug('Setting last checked', ['built' => $bots['built']]);
        $cache->setLastChecked(strtotime($bots['built']));

        return $count;
    }

    public function loadBots()
    {
        return json_decode($this->app->fs()->read($this->local), true);
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

    /**
     * @return MapRepository
     */
    protected function getMapRepository()
    {
        return $this->app->repository('Hampel\KnownBots:MapRepository');
    }

    /**
     * @return BotRepository
     */
    protected function getBotRepository()
    {
        return $this->app->repository('Hampel\KnownBots:BotRepository');
    }
}
