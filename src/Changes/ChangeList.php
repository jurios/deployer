<?php


namespace Kodilab\Deployer\Changes;



use Kodilab\Deployer\Configuration\Configuration;

class ChangeList
{
    /**
     * Change list
     *
     * @var array
     */
    protected $changes;

    /**
     * Deployer configuration
     *
     * @var Configuration
     */
    protected $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->changes = [];
    }

    /**
     * Returns the change list
     *
     * @return array
     */
    public function changes()
    {
        $this->sortChanges();
        return $this->changes;
    }

    /**
     * Add a change into the change list
     *
     * @param Change $change
     */
    public function add(Change $change)
    {
        $this->changes[] = $change;
    }

    /**
     * Remove a change from the change list
     *
     * @param Change $change
     */
    public function remove(Change $change)
    {
        for ($i = 0; $i < count($this->changes); $i++) {
            if ($this->changes[$i]->is($change)) {
                unset($this->changes[$i]);
            }
        }
    }

    /**
     * Add a list of changes into the change list
     *
     * @param array $changes
     */
    public function merge(array $changes)
    {
        /** @var Change $change */
        foreach ($changes as $change) {
            $this->add($change);
        }
    }

    /**
     * Sort the change array alphabetically by path
     */
    private function sortChanges()
    {
        usort($this->changes, function (Change $a, Change $b) {
            return $a->path() > $b->path() ? 1 : -1;
        });
    }
}