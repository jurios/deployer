<?php


namespace Kodilab\Deployer\Changes;


class Delete extends Change
{
    public function __construct(string $path, string $reason = 'unknown')
    {
        parent::__construct($path, $reason);
    }

    /**
     * Returns the status label text (renamed, added...)
     *
     * @return string
     */
    public function getLabeledStatus()
    {
        return 'Deleted';
    }

    /**
     * Returns the output color
     *
     * @return mixed
     */
    public function getColor()
    {
        return 'red';
    }
}
