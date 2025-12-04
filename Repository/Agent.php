<?php namespace Hampel\KnownBots\Repository;

use XF\Mvc\Entity\Repository;

class Agent extends Repository
{
    public function addUserAgent($userAgent, $robot_key, $touch = true)
    {
        // stop if we have invalid UTF-8 characters
        if (mb_convert_encoding($userAgent, 'UTF-8', 'UTF-8') != $userAgent) return 0;

        $userAgent = trim(substr($userAgent, 0, 512));

        // stop if we have a zero length user agent after trimming
        if (strlen($userAgent) == 0) return 0;

        $agent = $this->db()->fetchRow("
            SELECT * FROM xf_knownbots_agent WHERE user_agent = ?
        ", [$userAgent]);

        $midnight = mktime(0, 0, 0);

        if ($agent)
        {
            $robot_key = $robot_key ?? $agent['robot_key'];

            if ($agent['robot_key'] != $robot_key || ($touch && $agent['last_updated'] < $midnight))
            {
                $this->db()->query("
                 UPDATE xf_knownbots_agent 
                 SET robot_key = ?,
                     last_updated = ?
                 WHERE user_agent = ?
                ", [$robot_key, $midnight, $userAgent]);

                return 2;
            }

            return 0;
        }

        $this->db()->query("
            INSERT IGNORE INTO xf_knownbots_agent (user_agent, robot_key, last_updated)
            VALUES (?, ?, ?)
        ", [$userAgent, $robot_key, $midnight]);

        return 1;
    }

    public function deleteUserAgent($userAgent)
    {
        $this->db()->query("
            DELETE FROM xf_knownbots_agent
            WHERE user_agent = ?
        ", [$userAgent]);
    }

    public function getUserAgentsForSending()
    {
        return $this->agentFinder()
            ->where('sent', 0)
            ->order('last_updated', 'DESC')
            ->fetch()
            ->pluckNamed('user_agent');
    }

    public function getUserAgentsForDisplay()
    {
        return $this
            ->agentFinder()
            ->whereOr(['robot_key', '!=', null], ['sent', '=', 0])
            ->order('last_updated', 'DESC')
            ->fetch(100);
    }

    public function getUserAgentsForReprocessing($onlyNull = true)
    {
        $query = $this->agentFinder();
        if ($onlyNull)
        {
            $query->where('robot_key', '=', null);
        }
        return $query->order('last_updated', 'DESC')->fetch();
    }

    public function markUserAgentsSent()
    {
        $query = $this->db()->query("
            UPDATE xf_knownbots_agent
            SET sent = 1
            WHERE sent = 0
        ");

        return $query->rowsAffected();
    }

    public function purgeUserAgents($days)
    {
        $offset = $days * 60 * 60 * 24;

        $query = $this->db()->query("
            DELETE FROM xf_knownbots_agent
            WHERE last_updated < ?
        ", [$offset]);

        return $query->rowsAffected();
    }

    public function clearAllUserAgents()
    {
        return $this->db()->emptyTable('xf_knownbots_agent');
    }

    protected function agentFinder()
    {
        return $this->finder('Hampel\KnownBots:Agent');
    }
}