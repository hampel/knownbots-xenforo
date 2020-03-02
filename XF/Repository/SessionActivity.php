<?php namespace Hampel\KnownBots\XF\Repository;

use Hampel\KnownBots\Option\ShowBotStats;

class SessionActivity extends XFCP_SessionActivity
{
	public function getOnlineCounts($onlineCutOff = null)
	{
		if (!ShowBotStats::isEnabled())
		{
			return parent::getOnlineCounts($onlineCutOff);
		}

		if ($onlineCutOff === null)
		{
			$onlineCutOff = \XF::$time - $this->options()->onlineStatusTimeout * 60;
		}

		/**
		 * This addon includes robots in the online counts
		 */
		return $this->db()->fetchRow("
			SELECT
				SUM(IF(user_id >= 0 AND robot_key = '', 1, 0)) AS total,
				SUM(IF(user_id > 0, 1, 0)) AS members,
				SUM(IF(user_id = 0 AND robot_key = '', 1, 0)) AS guests,
				SUM(IF(robot_key != '', 1, 0)) AS robots
			FROM xf_session_activity
			WHERE view_date >= ?
		", $onlineCutOff);
	}

}
