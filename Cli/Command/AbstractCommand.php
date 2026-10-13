<?php namespace Hampel\KnownBots\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The base class for this add-on's CLI commands, in place of XF\Cli\Command\AbstractCommand.
 *
 * XenForo 2.3 added that class, and the SUCCESS / FAILURE / INVALID constants its commands
 * return. XenForo 2.2 has neither: its own commands extend Symfony's Command directly, and its
 * forked symfony/console does not define the constants. Extending Symfony's Command here and
 * supplying both keeps one set of command classes running on either line.
 *
 * The constants carry Symfony's own values, so declaring them where the parent already has them
 * changes nothing. They carry no visibility modifier, which was required while the add-on
 * supported PHP 7.0; it now requires 7.4, so a modifier would be allowed - the form is left as it
 * is because changing it would gain nothing.
 *
 * initialize() is what XF 2.3's AbstractCommand contributes - the six colour styles its commands
 * use in output - copied here so output reads the same on both lines.
 */
abstract class AbstractCommand extends Command
{
	const SUCCESS = 0;
	const FAILURE = 1;
	const INVALID = 2;

	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		foreach (['red', 'green', 'yellow', 'blue', 'magenta', 'cyan'] AS $color)
		{
			$output->getFormatter()->setStyle($color, new OutputFormatterStyle($color));
		}

		parent::initialize($input, $output);
	}
}
