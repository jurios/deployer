<?php


namespace Kodilab\Deployer\Exceptions;


use Kodilab\Deployer\Git\Diff\Entries\Entry;
use Throwable;

class DiffEntryIncoherenceException extends \Exception
{
    public function __construct(Entry $entry1, Entry $entry2, $code = 0, Throwable $previous = null)
    {
        $message = "There are multiple diff entries which refers the same source file:\n" .
            "Entry 1: " . get_class($entry1) . ' refers to ' . $entry1->getSource() . "\n" .
            "Entry 2: " . get_class($entry2) . ' refers to ' . $entry2->getSource();

        parent::__construct($message, $code, $previous);
    }
}
