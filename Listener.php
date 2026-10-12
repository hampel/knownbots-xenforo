<?php namespace Hampel\KnownBots;

use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Cache;
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

        $container['knownbots.cache'] = function(\XF\Container $c) use ($app)
        {
            $class = $app->extendClass(Cache::class);
            return new $class($c, $app);
        };

        $container['knownbots.api'] = function(\XF\Container $c) use ($app)
        {
            $class = $app->extendClass(Api::class);
            return new $class($c, $app);
        };
	}

	/**
	 * Declares which of this add-on's settings hold a credential, so that a reader of the forum's
	 * configuration can be shown the rest of them. The event is only fired where an add-on
	 * defines it, so this costs nothing on a forum that has none and adds no requirement.
	 *
	 * Both config keys are claimed with no secrets under them - one is an API base URL and the
	 * other a board hostname - because claiming a key is what gets its value shown rather than
	 * withheld. The e-mail option holds a recipient address, which is not a credential.
	 */
	public static function adminApiRedaction(array &$declarations)
	{
		$declarations['Hampel/KnownBots'] = [
			'options' => [
				'knownbotsSendUserAgents.validation_token',
				'knownbotsSendUserAgents.api_token',
			],
			'config' => [
				'knownBotsApi' => [],
				'knownBotsDomain' => [],
			],
		];
	}
}
