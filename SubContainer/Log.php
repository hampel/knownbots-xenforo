<?php namespace Hampel\KnownBots\SubContainer;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use XF\SubContainer\AbstractSubContainer;

/**
 * A PSR-3 facade over the optional Monolog Logging Service add-on (`Hampel/Monolog`).
 *
 * Without that add-on there is no logger to call, which used to be every caller's problem: the
 * container entry resolved to null and `log()` checked for it on every call. It resolves to a
 * NullLogger instead, so a forum without Monolog takes the same path as one with it and discards
 * the record at the end of it.
 *
 * None of these methods returns anything. Monolog's own `Logger::log()` has returned void since
 * Monolog 2, which is what every supported version of the logging add-on ships, so the `bool` this
 * class used to document had not been true for years.
 */
class Log extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;

		$container['log'] = function($c)
		{
			if (!$this->parent->offsetExists('monolog'))
			{
				return new NullLogger();
			}

			$monolog = $this->parent['monolog'];

			// channel() arrived in the logging add-on's 5.0; newChannel() is the 4.x name, kept
			// there as a deprecated alias. Asking which one exists is what lets this add-on
			// install on a forum running either - an addon.json requirement would refuse 4.x
			return method_exists($monolog, 'channel')
				? $monolog->channel('knownbots')
				: $monolog->newChannel('knownbots');
		};
	}

	public function emergency($message, array $context = array()): void
	{
		$this->log('emergency', $message, $context);
	}

	public function alert($message, array $context = array()): void
	{
		$this->log('alert', $message, $context);
	}

	public function critical($message, array $context = array()): void
	{
		$this->log('critical', $message, $context);
	}

	public function error($message, array $context = array()): void
	{
		$this->log('error', $message, $context);
	}

	public function warning($message, array $context = array()): void
	{
		$this->log('warning', $message, $context);
	}

	public function notice($message, array $context = array()): void
	{
		$this->log('notice', $message, $context);
	}

	public function info($message, array $context = array()): void
	{
		$this->log('info', $message, $context);
	}

	public function debug($message, array $context = array()): void
	{
		$this->log('debug', $message, $context);
	}

	public function log($level, $message, array $context = array()): void
	{
		/** @var LoggerInterface $logger */
		$logger = $this->container['log'];

		$logger->log($level, $message, $context);
	}
}
