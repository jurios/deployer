<?php


namespace Kodilab\Deployer\Exceptions;


use Kodilab\Deployer\Changes\Change;
use Throwable;

class ChangeIncoherenceException extends \Exception
{
    public function __construct(Change $change1, Change $change2, $code = 0, Throwable $previous = null)
    {
        $message = "There are multiple diff entries which refers the same source file:\n" .
            "Change 1: " . get_class($change1) . ' refers to ' . $change1->getPath() . "\n" .
            "Change 2: " . get_class($change2) . ' refers to ' . $change2->getPath();

        parent::__construct($message, $code, $previous);
    }
}
