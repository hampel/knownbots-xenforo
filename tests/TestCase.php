<?php namespace Tests;

use Hampel\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /*
     * Set $rootDir to '../../../..' if you use a vendor in your addon id (ie <Vendor/AddonId>)
     * Otherwise, set this to '../../..' for no vendor
     *
     * No trailing slash!
     */
    protected $rootDir = '../../../..';

    /*
     * Load only this add-on. Without this, booting the app registers every active add-on
     * that declares composer_autoload onto XF's class loader, and a sibling's vendor tree
     * can supply PHPUnit itself - which kills the run before the first test.
     */
    protected $addonsToLoad = ['Hampel/KnownBots'];

	protected function getMockData($file)
	{
		return file_get_contents(__DIR__ . '/mock/' . $file);
	}
}
