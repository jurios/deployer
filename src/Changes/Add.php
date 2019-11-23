<?php


namespace Kodilab\Deployer\Changes;


class Add extends Change
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
        return 'Added';
    }

    /**
     * Returns the output color
     *
     * @return mixed
     */
    public function getColor()
    {
        return 'green';
    }
}
