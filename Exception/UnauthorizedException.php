<?php namespace Hampel\KnownBots\Exception;

use Hampel\KnownBots\Exception\CustomerException;

class UnauthorizedException extends CustomerException
{
    protected $type = 'Authorization';
}
