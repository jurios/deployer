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
}