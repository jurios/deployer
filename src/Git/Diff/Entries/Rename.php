<?php


namespace Kodilab\Deployer\Git\Diff\Entries;


class Rename extends Entry
{
    public function __construct(string $source, string $destination)
    {
        parent::__construct($source, $destination);
    }
}
