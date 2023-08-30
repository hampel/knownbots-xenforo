<?php namespace Hampel\KnownBots\Repository;

use XF\Mvc\Entity\Repository;

class Agent extends Repository
{
    public function addUserAgent($userAgent, $robot_key = '')
    {
        // only store the first 512 characters of the UserAgent string to prevent bad data causing issues
        $userAgent = substr($userAgent, 0, 512);
        $hash = hash("sha256", strtolower($userAgent));
        $last_updated = mktime(0, 0, 0); // midnight today

        $query = \XF::db()->query("
            INSERT INTO xf_knownbots_agent (hash, user_agent, robot_key, last_updated)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                robot_key = VALUES(robot_key),
                last_updated = VALUES(last_updated)
        ", [$hash, $userAgent, $robot_key, $last_updated]);

        return $query->rowsAffected();
    }

    public function getUserAgentsForEmail()
    {
        return $this->agentFinder()
            ->where('sent', 0)
            ->order('last_updated', 'DESC')
            ->fetch()
            ->pluckNamed('user_agent');
    }

    public function getUserAgentsForDisplay()
    {
        return $this->agentFinder()->order('last_updated', 'DESC')->fetch(100);
    }

    public function markUserAgentsSent()
    {
        $query = \XF::db()->query("
            UPDATE xf_knownbots_agent
            SET sent = 1
            WHERE sent = 0
        ");

        return $query->rowsAffected();
    }

    public function purgeUserAgents($days)
    {
        $offset = $days * 60 * 60 * 24;

        $query = \XF::db()->query("
            DELETE FROM xf_knownbots_agent
            WHERE last_updated < ?
        ", [$offset]);

        return $query->rowsAffected();
    }

    public function clearAllUserAgents()
    {
        $query = \XF::db()->query("
            DELETE FROM xf_knownbots_agent
        ");

        return $query->rowsAffected();
    }

    protected function agentFinder()
    {
        return $this->finder('Hampel\KnownBots:Agent');
    }
}