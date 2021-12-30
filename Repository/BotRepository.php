<?php namespace Hampel\KnownBots\Repository;

use XF\Mvc\Entity\Repository;

class BotRepository extends Repository
{
    public function updateBots($bots, $all)
    {
        if (count($bots) == 0)
        {
            return 0; // don't bother if we have no data
        }

        $db = $this->db();

        $data = [];

        foreach ($bots as $bot) {
            $data[] = [
                'bot_id' => $bot['id'],
                'robot_key' => $bot['robot_key'],
                'title' => $bot['title'],
                'link' => $bot['link'],
                'created_at' => $bot['created_at'] ? strtotime($bot['created_at']) : null,
                'updated_at' => $bot['updated_at'] ? strtotime($bot['updated_at']) : null,
                'deleted_at' => $bot['deleted_at'] ? strtotime($bot['deleted_at']) : null,
            ];
        }

        $replaceInto = true;
        if ($all) {
            $db->emptyTable('xf_knownbots_bot');
            $replaceInto = false;
        }

        return $db->insertBulk('xf_knownbots_bot', $data, $replaceInto);
    }
}
