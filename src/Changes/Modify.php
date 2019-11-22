<?php


namespace Kodilab\Deployer\Changes;


class Modify extends Change
{
    public function __construct(string $source, string $reason = 'unknown')
    {
        parent::__construct($source, null, $reason);
    }

    /**
     * Returns the status label text (renamed, added...)
     *
     * @return string
     */
    public function getLabeledStatus()
    {
        return 'Modified';
    }

    /**
     * Returns the output color
     *
     * @return mixed
     */
    public function getColor()
    {
        return 'blue';
    }
}
