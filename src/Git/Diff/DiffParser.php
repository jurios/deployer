<?php


namespace Kodilab\Deployer\Git\Diff;


use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Helpers\Str;

class DiffParser
{
    const ADDED = 'A';
    const MODIFIED = 'M';
    const DELETED = 'D';
    const RENAMED = 'R';

    const REASON = 'git diff';

    /**
     * @var bool
     */
    protected $composerlock_changed;

    /**
     * @var array
     */
    protected $entries;

    public function __construct()
    {
        $this->composerlock_changed = false;
        $this->entries = null;
    }

    /**
     * @param array $output
     * @return DiffParser
     * @throws \Exception
     */
    public function parse(array $output)
    {
        $this->entries = [];

        foreach ($output as $line) {

            $line = trim($line);
            $fields = explode("\t", $line);

            if (count($fields) < 2 && count($fields) > 3) {
                throw new \Exception("Diff entry format not expected: " . $line);
            }

            $status = $fields[0][0]; //We get only the first character in order to avoid the numbers in case as R000
            $source = $fields[1];
            $destination = isset($fields[2]) ? $fields[2] : null;

            if ($source === 'composer.lock') {
                $this->composerlock_changed = true;
            }

            $this->entries[] = Change::make($status, $source, $destination, static::REASON);
        }

        return $this;
    }

    public function hasDependenciesChanged()
    {
        return $this->composerlock_changed;
    }

    /**
     * Returns the entries generated after parse the diff output
     *
     * @return array|null
     */
    public function getEntries()
    {
        return $this->entries;
    }
}
