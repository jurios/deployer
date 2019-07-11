<?php


namespace Kodilab\Deployer\Changes;


class Change
{
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function path()
    {
        return $this->path;
    }

    public function is(Change $change)
    {
        return get_class($this) === get_class($change) && $this->path === $change->path();
    }
}