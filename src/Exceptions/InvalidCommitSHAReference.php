<?php


namespace Kodilab\Deployer\Exceptions;


use Throwable;

class InvalidCommitSHAReference extends \Exception
{
    public function __construct($sha, $code = 0, Throwable $previous = null)
    {
        $message = 'Invalid commit SHA reference: ' . $sha;

        parent::__construct($message, $code, $previous);
    }
}
