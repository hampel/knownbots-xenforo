<?php namespace Hampel\KnownBots\SubContainer;

use Hampel\KnownBots\Api\BotFetcher;
use Hampel\KnownBots\Option\StoreUserAgents;
use Hampel\KnownBots\XF\Data\Robot;
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

        if ($bots === false)
        {
            // there was an error fetching bots
            return false;
        }

        if (empty($bots))
        {
            // there was no data returned
            return null;
        }

        if (!$this->isValid($bots))
        {
            $log->error("Invalid bot data returned", $bots);
            \XF::logError("Invalid bot data returned from api call");
            return false;
        }

        $this->storeBots($bots); // write to fs
        $this->updateBots($bots); // generate code cache

        $this->reprocessUserAgents();

        return $bots;
    }

    public function storeBots(array $bots)
    {
        return $this->fs()->put($this->local(), json_encode($bots, JSON_PRETTY_PRINT));
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
            'complex' => count($bots['complex']),
            'ignored' => count($bots['ignored']),
            'browsers' => count($bots['browsers']),
        ]);
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
        $test = isset($bots['version']) &&
            is_int($bots['version']) &&
            $bots['version'] == 2 &&
            isset($bots['built']) &&
            isset($bots['maps']) &&
            isset($bots['bots']) &&
            isset($bots['complex']) &&
            isset($bots['ignored']) &&
            isset($bots['browsers']) &&
            is_int($bots['built']) &&
            is_array($bots['maps']) &&
            is_array($bots['bots']) &&
            is_array($bots['complex']) &&
            is_array($bots['ignored']) &&
            is_array($bots['browsers']);

        if ($test)
        {
            foreach ($bots['maps'] as $key => $value)
            {
                if (!is_string($key)) return false;
                if (!is_string($value)) return false;
            }
            foreach ($bots['bots'] as $key => $value)
            {
                if (!is_string($key)) return false;
                if (!is_array($value)) return false;
                foreach ($value as $k => $v)
                {
                    if (!is_string($k)) return false;
                    if (!is_string($v) && !is_null($v)) return false;
                }
            }
            foreach ($bots['complex'] as $key => $value)
            {
                if (!is_string($key)) return false;
                if (!is_string($value)) return false;
            }
            foreach ($bots['ignored'] as $key => $value)
            {
                if (!is_numeric($key)) return false;
                if (!is_string($value)) return false;
            }
            foreach ($bots['browsers'] as $key => $value)
            {
                if (!is_numeric($key)) return false;
                if (!is_string($value)) return false;
            }
        }

        return $test;
    }

    public function reprocessUserAgents($onlyNull = true)
    {
        if (StoreUserAgents::isEnabled())
        {
            return $this->robots()->reprocessUserAgents($onlyNull);
        }
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

    /**
     * @return Robot
     */
    protected function robots()
    {
        return $this->app->data('XF:Robot');
    }
}
