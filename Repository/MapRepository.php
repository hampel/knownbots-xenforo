<?php namespace Hampel\KnownBots\Repository;

use XF\Mvc\Entity\Repository;

class MapRepository extends Repository
{
    public function updateMaps($maps, $all)
    {
        if (count($maps) == 0)
        {
            return 0; // don't bother if we have no data
        }

        $db = $this->db();

        $data = [];

        foreach ($maps as $map) {
            $data[] = [
                'map_id' => $map['id'],
                'search_key' => $map['search_key'],
                'robot_key' => $map['robot_key'],
                'created_at' => $map['created_at'] ? strtotime($map['created_at']) : null,
                'updated_at' => $map['updated_at'] ? strtotime($map['updated_at']) : null,
                'deleted_at' => $map['deleted_at'] ? strtotime($map['deleted_at']) : null,
            ];
        }

        $replaceInto = true;
        if ($all) {
            $db->emptyTable('xf_knownbots_map');
            $replaceInto = false;
        }

        return $db->insertBulk('xf_knownbots_map', $data, $replaceInto);
    }
}
