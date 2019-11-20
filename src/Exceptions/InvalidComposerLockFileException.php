<?php


namespace Kodilab\Deployer\Exceptions;


use Kodilab\Deployer\ComposerLock\JSONComponent;
use Throwable;

class InvalidComposerLockFileException extends \Exception
{
    /**
     * Error list
     *
     * @var array
     */
    protected $errors;

    public function __construct(JSONComponent $component, $errors = [], $code = 0, Throwable $previous = null)
    {
        $message = "Invalid attributes for " . get_class($component) . ":\n" .
            "Attributes: \n" .
            print_r($component->toArray(), true) . "\n\n" .
            "Errors: \n" .
            print_r($errors, true);

        parent::__construct($message, $code, $previous);
    }
}
