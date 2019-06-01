<?php


namespace Kodilab\Deployer\Git;


use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Changes\Rename;

class Diff
{
    /** @var string */
    protected $from;

    /** @var string string */
    protected $to;

    /** @var string */
    protected $project_path;

    /** @var bool */
    protected $vendor_changed;

    /** @var array */
    protected $changes;


    public function __construct(string $project_path, string $from, string $to)
    {
        $this->project_path = $project_path;
        $this->from = $from;
        $this->to = $to;

        $this->changes = [];

        $this->vendor_changed = false;

        $this->diff();
    }

    public function isVendorChanged()
    {
        return $this->vendor_changed;
    }

    public function changes()
    {
        return $this->changes;
    }

    private function diff()
    {
        $diff = null;

        exec('git diff --name-status ' . $this->from . " " . $this->to, $diff);

        $this->processDiff($diff);
    }

    private function processDiff(array $diff)
    {
        foreach ($diff as $change) {

            $item = explode("\t", $change);

            if (count($item) < 2 && count($item) > 3) {
                throw new \Exception("Diff entry format not expected: " . $change);
            }

            $status = $item[0][0];
            $file = $item[1];

            if ($file === 'composer.lock') {
                $this->vendor_changed = true;
            }

            //TODO: Check if this is working

            if ($status[0] === "R") {
                $this->changes[] = new Rename($item[1], $item[2]);
            }

            if ($status[0] === 'M') {
                $this->changes[] = new Modify($item[1]);
            }

            if ($status[0] === 'D') {
                $this->changes[] = new Delete($item[1]);
            }

            if ($status[0] === 'A') {
                $this->changes[] = new Add($item[1]);
            }
        }
    }
}