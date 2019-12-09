<?php


namespace Kodilab\Deployer\Changes;


class Add extends Change
{
    public function __construct(string $path, bool $is_dir = false, string $reason = 'unknown')
    {
        parent::__construct($path, $is_dir, $reason);
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
