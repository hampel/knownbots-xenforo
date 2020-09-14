<?php namespace Hampel\KnownBots;

use Hampel\KnownBots\SubContainer\Log;

class Listener
{
	public static function appSetup(\XF\App $app)
	{
		$container = $app->container();

		$container['knownbots.log'] = function(\XF\Container $c) use ($app)
		{
			$class = $app->extendClass(Log::class);
			return new $class($c, $app);
		};
	}
}
