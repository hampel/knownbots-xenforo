<?php namespace Hampel\KnownBots\Repository;

use XF\Mvc\Entity\Repository;

class Agent extends Repository
{
    public function addUserAgent($userAgent)
    {
        // only store the first 512 characters of the UserAgent string to prevent bad data causing issues
        $userAgent = substr($userAgent, 0, 512);
        $hash = hash("sha256", strtolower($userAgent));

        \XF::db()->query("
            INSERT IGNORE INTO xf_knownbots_agent (hash, user_agent)
            VALUES (?, ?)
        ", [$hash, $userAgent]);
    }

    public function getUserAgents()
    {
        return $this->agentFinder()->fetch()->pluckNamed('user_agent');
    }

    public function clearUserAgents()
    {
        $this->db()->delete('xf_knownbots_agent', null);
    }

    protected function agentFinder()
    {
        return $this->finder('Hampel\KnownBots:Agent');
    }
}