<?php namespace Hampel\KnownBots\Service;

use Hampel\KnownBots\Exception\KnownBotsException;
use Hampel\KnownBots\Exception\UnauthorizedException;
use Hampel\KnownBots\SubContainer\Api;
use Hampel\KnownBots\SubContainer\Log;
use XF\Service\AbstractService;

class ApiTokenChecker extends AbstractService
{
    protected $error = null;

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

	public function checkToken($revalidate = false)
	{
        $api = $this->getApi();
        $log = $this->getLogger();

        try
        {
            $response = $api->checkApiToken($this->api_token);
            return $response['domain'] ?? '';
        }
        catch (UnauthorizedException $e)
        {
            if (!$revalidate)
            {
                // we aren't going to try again, so just return this as an error
                return $this->error($e);
            }

            // if authorisation fails, just log it and try again - no need to report an exception
            $log->notice($e->getMessage());

            // we will continue on from here
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
            $response = $api->checkApiToken($this->api_token);
            return $response['domain'] ?? '';
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

    // -------------------------------------------------------------

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
