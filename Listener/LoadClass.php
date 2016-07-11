<?php 

class KnownBots_Listener_LoadClass
{
	public static function load_class_session($class, array &$extend)
	{
		$extend[] = 'KnownBots_Session';
	}
}
