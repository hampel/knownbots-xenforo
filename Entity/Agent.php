<?php namespace Hampel\KnownBots\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Agent extends Entity
{
    protected function verifyUserAgent(&$user_agent)
    {
        $this->hash = hash("sha256", strtolower($user_agent));

        return true;
    }

    protected function verifyHash(&$hash)
    {
        $userAgentHash = hash("sha256", strtolower($this->user_agent));

        if ($userAgentHash === $hash) return true;
    }

    protected function _preSave()
    {
        $this->last_update = mktime(0, 0, 0);
    }

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_knownbots_agent';
		$structure->shortName = 'Hampel\KnownBots:Agent';
		$structure->primaryKey = 'agent_id';
		$structure->columns = [
			'agent_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'hash' => ['type' => self::STR, 'maxLength' => 64],
			'user_agent' => ['type' => self::STR, 'required' => true],
            'robot_key' => ['type' => self::STR, 'maxLength' => 25, 'default' => ''],
            'last_updated' => ['type' => self::UINT, 'default' => 0],
            'sent' => ['type' => self::BOOL, 'default' => false]
		];

		return $structure;
	}
}
