<?php namespace Hampel\KnownBots\Service;

use Hampel\KnownBots\Exception\KnownBotsException;
use Hampel\KnownBots\Exception\UnauthorizedException;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Log;
use XF\Service\AbstractService;

class UserAgentSender extends AbstractService
{
	protected $agents = [];

    protected $error = '';

    protected $api_token = null;

    protected $validation_token = null;

    /**
     * @param string $api_token
     */
    public function setApiToken($api_token)
    {
        $this->api_token = $api_token;
    }

    /**
     * @param string $validation_token
     */
    public function setValidationToken($validation_token)
    {
        $this->validation_token = $validation_token;
    }

	public function setUserAgents(array $agents)
	{
		$this->agents = $agents;
	}

	public function sendUserAgents()
	{
        $api = $this->getApi();
        $log = $this->getLogger();

        try
        {
            return $api->sendUserAgents($this->api_token, $this->agents);
        }
        catch (UnauthorizedException $e)
        {
            // if authorisation fails, just log it and try again - no need to report an exception
            $log->notice($e->getMessage());

            // we'll continue on from here and attempt to revalidate
        }
        catch (KnownBotsException $e)
        {
            // some other error - abort
            return $this->error($e);
        }

        try
        {
            $this->api_token = $api->validate($this->validation_token);
        }
        catch (KnownBotsException $e)
        {
            // validation failed - abort
            return $this->error($e);
        }

        $api->updateApiToken($this->api_token);

        try
        {
            return $api->sendUserAgents($this->api_token, $this->agents);
        }
        catch (KnownBotsException $e)
        {
            // second time around, all errors are failures, including unauthorized
            return $this->error($e);
        }
	}

    public function getError()
    {
        return $this->error;
    }

    // -----------------------------------------------------------------

    protected function error(\Throwable $e)
    {
        \XF::logException($e);
        $this->error = $e->getMessage();
        $this->getLogger()->error($this->error);
        return false;
    }

    /**
     * @return Log
     */
    protected function getLogger()
    {
        return $this->app['knownbots.log'];
    }

    /**
     * @return Api
     */
    protected function getApi()
    {
        return $this->app['knownbots.api'];
    }
}
