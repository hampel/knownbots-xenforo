<?php namespace Hampel\KnownBots\SubContainer;

use XF\SubContainer\AbstractSubContainer;

class Log extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;

		$container['log'] = function($c)
		{
			if ($this->parent->offsetExists('monolog'))
			{
				return $this->parent['monolog']->newChannel('knownbots');
			}
		};
	}

	public function emergency($message, array $context = array())
	{
		return $this->log('emergency', $message, $context);
	}

	public function alert($message, array $context = array())
	{
		return $this->log('alert', $message, $context);
	}

	public function critical($message, array $context = array())
	{
		return $this->log('critical', $message, $context);
	}

	public function error($message, array $context = array())
	{
		return $this->log('error', $message, $context);
	}

	public function warning($message, array $context = array())
	{
		return $this->log('warning', $message, $context);
	}

	public function notice($message, array $context = array())
	{
		return $this->log('notice', $message, $context);
	}

	public function info($message, array $context = array())
	{
		return $this->log('info', $message, $context);
	}

	public function debug($message, array $context = array())
	{
		return $this->log('debug', $message, $context);
	}


	/**
	 * @param $level
	 * @param $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function log($level, $message, array $context = array())
	{
		/** @var \Monolog\Logger $logger */
		$logger = $this->container['log'];
		if ($logger)
		{
			return $logger->log($level, $message, $context);
		}

		// logger isn't set up
		return false;
	}
}
