<?php namespace Hampel\KnownBots\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Bot extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_knownbots_bot';
        $structure->shortName = 'Hampel\KnownBots:Bot';
        $structure->primaryKey = 'bot_id';
        $structure->columns = [
            'bot_id' => ['type' => self::UINT],
            'robot_key' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
            'title' => ['type' => self::STR],
            'link' => ['type' => self::STR],
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
