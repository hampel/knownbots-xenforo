<?php namespace Hampel\KnownBots\Exception;

class KnownBotsException extends \Exception
{
    protected $type = '';

    public function __construct($action, $message, int $code = 0)
    {
        $codeStr = $code > 0 ? " {$code}" : "";

        parent::__construct("{$this->type} error {$action}:{$codeStr} {$message}", $code);
    }
}
