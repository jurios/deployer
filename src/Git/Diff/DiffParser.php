<?php


namespace Kodilab\Deployer\Git\Diff;


use Kodilab\Deployer\Git\Diff\Entries\Entry;
use Kodilab\Deployer\Git\Diff\Entries\EntryCollection;
use Kodilab\Deployer\Helpers\Str;

class DiffParser
{
    const ADDED = 'A';
    const MODIFIED = 'M';
    const DELETED = 'D';
    const RENAMED = 'R';

    /**
     * @var bool
     */
    protected $composerlock_changed;

    /**
     * @var EntryCollection
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
        $this->entries = new EntryCollection();

        foreach ($output as $line) {

            $line = Str::removeLastCarriageReturn($line);
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

            $this->entries->add(Entry::make($status, $source, $destination));
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
     * @return EntryCollection|null
     */
    public function getEntries()
    {
        return $this->entries;
    }
}
