<?php namespace Hampel\KnownBots\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Agent extends Entity
{
    public function isOutdated()
    {
        return $this->last_updated < mktime(0, 0, 0);
    }

    protected function _preSave()
    {
        $this->last_updated = mktime(0, 0, 0);
    }

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_knownbots_agent';
		$structure->shortName = 'Hampel\KnownBots:Agent';
		$structure->primaryKey = 'user_agent';
		$structure->columns = [
			'user_agent' => ['type' => self::BINARY, 'maxLength' => 512, 'required' => true],
            'robot_key' => ['type' => self::STR, 'maxLength' => 25, 'nullable' => true],
            'last_updated' => ['type' => self::UINT, 'default' => 0],
            'sent' => ['type' => self::BOOL, 'default' => false]
		];

		return $structure;
	}
}
