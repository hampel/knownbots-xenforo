<?php namespace Hampel\KnownBots\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Map extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_knownbots_map';
        $structure->shortName = 'Hampel\KnownBots:Map';
        $structure->primaryKey = 'map_id';
        $structure->columns = [
            'map_id' => ['type' => self::UINT],
            'search_key' => ['type' => self::STR, 'maxLength' => 64, 'required' => true],
            'robot_key' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
            'created_at' => ['type' => self::UINT, 'nullable' => true],
            'updated_at' => ['type' => self::UINT, 'nullable' => true],
            'deleted_at' => ['type' => self::UINT, 'nullable' => true],
        ];
        $structure->relations = [
            'Bot' => [
                'entity' => 'Hampel\KnownBots:Bot',
                'type' => self::TO_ONE,
                'conditions' => 'robot_key',
                'primary' => false
            ],
        ];

        return $structure;
    }
}
