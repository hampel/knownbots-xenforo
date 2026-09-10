<?php namespace Tests\Unit;

use Hampel\KnownBots\Exception\UnauthorizedException;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Log;
use Tests\TestCase;

/**
 * Checking the API token, with an optional revalidation when the token is refused.
 */
class ApiTokenCheckerTest extends TestCase
{
    private $api;

    protected function setUp() : void
    {
        parent::setUp();

        $this->fakesErrors();

        $this->mock('knownbots.log', Log::class, function ($mock)
        {
            $mock->shouldIgnoreMissing();
        });

        $this->api = $this->mock('knownbots.api', Api::class);
    }

    public function test_returns_the_domain_the_token_belongs_to()
    {
        $this->api->expects('checkApiToken')->with('old-token')->once()->andReturns(['domain' => 'www.example.com']);
        $this->api->expects('validate')->never();

        $this->assertSame('www.example.com', $this->checker()->checkToken());
    }

    public function test_returns_an_empty_domain_when_none_is_reported()
    {
        $this->api->expects('checkApiToken')->once()->andReturns([]);

        $this->assertSame('', $this->checker()->checkToken());
    }

    public function test_a_401_is_final_unless_asked_to_revalidate()
    {
        $this->api->expects('checkApiToken')->once()->andThrow($this->unauthorized());
        $this->api->expects('validate')->never();
        $this->api->expects('updateApiToken')->never();

        $this->assertFalse($this->checker()->checkToken(false));
        $this->assertExceptionLogged(UnauthorizedException::class, 1);
    }

    public function test_revalidates_stores_the_new_token_and_rechecks_when_asked()
    {
        $this->api->expects('checkApiToken')->with('old-token')->once()->andThrow($this->unauthorized());
        $this->api->expects('validate')->with('licence-token')->once()->andReturns('new-token');
        $this->api->expects('updateApiToken')->with('new-token')->once();
        $this->api->expects('checkApiToken')->with('new-token')->once()->andReturns(['domain' => 'www.example.com']);

        $this->assertSame('www.example.com', $this->checker()->checkToken(true));
        $this->assertNoExceptionsLogged();
    }

    // ------------------------------------------------------------------

    private function checker()
    {
        $checker = $this->app()->service('Hampel\KnownBots:ApiTokenChecker');
        $checker->setApiToken('old-token');
        $checker->setValidationToken('licence-token');

        return $checker;
    }

    private function unauthorized() : UnauthorizedException
    {
        return new UnauthorizedException('checking API token', 'Unauthorized', 401);
    }
}
