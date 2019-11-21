<?php


namespace Kodilab\Deployer\Exceptions;


use Throwable;

class DiffEntryStatusUnknown extends \Exception
{
    public function __construct(string $status, string $entry, $code = 0, Throwable $previous = null)
    {
        $message = 'Unknown entry status: ' . $status . ' for the entry: ' . $entry;
        parent::__construct($message, $code, $previous);
    }
}
