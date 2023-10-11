<?php namespace Hampel\KnownBots\XF\Admin\Controller;

use Hampel\KnownBots\Exception\KnownBotsException;
use Hampel\KnownBots\SubContainer\Api;
use XF\Mvc\ParameterBag;

class Option extends XFCP_Option
{
    public function actionKnownbotsApiSetup(ParameterBag $params)
    {
        $option = $this->assertKnownbotsApiOption($params->option_id);

        if ($this->isPost())
        {
            $action = $this->filter('action', 'str');

            switch ($action)
            {
                case 'update':
                case 'enabled':
                    $viewParams = [
                        'option' => $option,
                        'action' => $action,
                    ];
                    return $this->view('XF:Option\KnownbotsApiConfigure', 'option_knownbots_api_configure', $viewParams);

                case 'disabled':
                    $optionValue = ['enabled' => false];
                    $this->getOptionRepo()->updateOption($option->option_id, $optionValue);
                // break missing intentionally
                case 'unchanged':
                default:
                    return $this->redirect($this->getDynamicRedirect());
            }
        }

        $viewParams = [
            'option' => $option
        ];

        return $this->view('XF:Option\KnownBotsApiSetup', 'option_knownbots_api_setup', $viewParams);
    }

    public function actionKnownbotsApiConfigure(ParameterBag $params)
    {
        $this->assertPostOnly();

        $option = $this->assertKnownbotsApiOption($params->option_id);

        $optionValue = $this->filter([
            'validation_token' => 'str',
        ]);
        $optionValue['enabled'] = true;

        /** @var Api $api */
        $api = $this->app->container('knownbots.api');

        try
        {
            $optionValue['api_token'] = $api->validate($optionValue['validation_token']);
        }
        catch (KnownBotsException $e)
        {
            throw $this->exception(
                $this->error($e->getMessage())
            );
        }

        $this->getOptionRepo()->updateOption($option->option_id, $optionValue);

        return $this->redirect($this->getDynamicRedirect());
    }

    protected function assertKnownbotsApiOption($optionId)
    {
        $option = $this->assertOptionExists($optionId);

        if (
            $option->edit_format !== 'template'
            || $option->edit_format_params !== 'option_template_knownBotsApiToken'
        )
        {
            throw $this->exception($this->noPermission());
        }

        return $option;
    }
}
