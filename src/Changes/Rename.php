<?php


namespace Kodilab\Deployer\Changes;


class Rename extends Change
{
    public function __construct(string $source, string $destination, string $reason = 'unknown')
    {
        parent::__construct($source, $destination, $reason);
    }

    /**
     * Returns the status label text (renamed, added...)
     *
     * @return string
     */
    public function getLabeledStatus()
    {
        return 'Renamed';
    }


    /**
     * Returns the output color
     *
     * @return mixed
     */
    public function getColor()
    {
        return 'cyan';
    }
}
