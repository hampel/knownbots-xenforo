<?php namespace Hampel\KnownBots\SubContainer;

use Hampel\KnownBots\Api\BotFetcher;
use League\Flysystem\FilesystemInterface;
use XF\SubContainer\AbstractSubContainer;

class Api extends AbstractSubContainer
{
    public function initialize()
    {
        $container = $this->container;

        $container['bots'] = function($c)
        {
            $fetcher = new BotFetcher($this->app);

            $customApi = $this->app->config('knownBotsApi');
            if ($customApi)
            {
                $fetcher->setUrl($customApi, true);
            }

            return $fetcher;
        };

        $container['local'] = function($c)
        {
            return "internal-data://knownbots.json";
        };
    }

    /**
     * @return BotFetcher
     */
    protected function botFetcher()
    {
        return $this->container['bots'];
    }

    protected function local()
    {
        return $this->container['local'];
    }

    public function fetchBots($force = false)
    {
        $log = $this->getLogger();
        $lastChecked = $this->getCache()->getLastChecked();

        $bots = $this->botFetcher()->fetch($lastChecked, $force);

        if (!$bots)
        {
            $log->error("No data returned from BotFetcher");
            \XF::logError("No data returned from BotFetcher");
            return false;
        }

        $status = $bots['status'] ?? '';

        if ($status == 'no updates')
        {
            $log->debug('No bot updates available');
            return null;
        }
        elseif ($status == 'OK')
        {
            if (!$this->isValid($bots))
            {
                $log->error("Invalid bot data returned", $bots);
                \XF::logError("Invalid bot data returned from api call");
                return false;
            }

            $this->storeBots($bots); // write to fs
            $this->updateBots($bots); // generate code cache

            return $bots;
        }
        else
        {
            $log->error('Invalid status returned from api call', compact('status'));
            \XF::logError("Invalid status returned from api call: [{$status}]");

            return false;
        }
    }

    public function updateBots(array $bots)
    {
        if (empty($bots)) return;

        $cache = $this->getCache();

        $cache->rebuildBotCache($bots);
        $cache->setLastChecked($bots['built']);

        $this->getLogger()->debug('Bots updated', [
            'built' => $bots['built'],
            'maps' => count($bots['maps']),
            'bots' => count($bots['bots']),
            'generic' => count($bots['generic']),
            'ignored' => count($bots['ignored']),
            'browsers' => count($bots['browsers']),
        ]);
    }

    public function storeBots(array $bots)
    {
        return $this->fs()->put($this->local(), json_encode($bots, JSON_PRETTY_PRINT));
    }

    public function loadBots()
    {
        $bots = json_decode($this->fs()->read($this->local()), true);
        return $this->isValid($bots) ? $bots : [];
    }

    public function removeBots()
    {
        return $this->fs()->delete($this->local());
    }

    protected function isValid(array $bots)
    {
        \XF::dump($bots);

        return isset($bots['status']) &&
            $bots['status'] == 'OK' &&
            isset($bots['built']) &&
            isset($bots['maps']) &&
            isset($bots['bots']) &&
            isset($bots['generic']) &&
            isset($bots['ignored']) &&
            isset($bots['browsers']) &&
            is_int($bots['built']) &&
            is_array($bots['maps']) &&
            is_array($bots['bots']) &&
            is_array($bots['generic']) &&
            is_array($bots['ignored']) &&
            is_array($bots['browsers']);
    }

    /**
     * @return Log
     */
    protected function getLogger()
    {
        return $this->parent['knownbots.log'];
    }

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return $this->parent['knownbots.cache'];
    }

    /**
     * @return FilesystemInterface
     */
    protected function fs()
    {
        return $this->app->fs();
    }


}
