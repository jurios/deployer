<?php


namespace Kodilab\Deployer\Changes;


use Kodilab\Deployer\Configuration;

class ChangeList
{
    /**
     * Change list
     *
     * @var array
     */
    protected $changes;

    /**
     * List of changes which are being included by configuration
     *
     * @var array
     */
    protected $includes;

    /**
     * List of changes which are being ignored by configuration
     *
     * @var array
     */
    protected $ignores;

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
        $this->ignores = [];
        $this->includes = [];
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
     * Returns the changes ignored
     *
     * @return array
     */
    public function ignores()
    {
        return $this->ignores;
    }

    /**
     * Returns the changes included
     *
     * @return array
     */
    public function includes()
    {
        return $this->includes;
    }

    /**
     * Add the included files from the $paths given
     *
     * @param array $paths
     */
    public function addIncludedFiles(array $paths)
    {
        $rules = $this->config->get('include');

        /** @var string $path */
        foreach ($paths as $path) {
            $match = false;

            foreach ($rules as $rule){
                if (fnmatch($rule, $path)) {
                    $match = true;
                }
            }

            if ($match) {
                $change = new Add($path);
                $this->addIncluded($change);
                $this->add($change);
            }
        }
    }

    /**
     * Add a change into the change list
     *
     * @param Change $change
     */
    public function add(Change $change)
    {
        if ($this->shouldBeIgnored($change)) {
            return;
        }

        $this->changes[] = $change;
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
     * Add a change into the include list
     *
     * @param Change $change
     */
    private function addIncluded(Change $change)
    {
        if ($this->shouldBeIgnored($change)) {
            return;
        }

        $this->includes[] = $change;
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


    /**
     * Returns if a change should be ignored based on the ignores configuration rules
     *
     * @param Change $change
     * @return bool
     */
    private function shouldBeIgnored(Change $change)
    {
        $rules = $this->config->get('ignore');

        $ignored = false;

        foreach ($rules as $rule) {

            /*
             * Rename changes are a bit different as they contain two actions: Remove the file in the "from" path and
             * add it into the "to" path therefore we should check both paths. If one of them should be ignored, then
             * the Rename change will be transformed into a simple change (remove or add)
             *
             * If the change is just a simple path change (add, modify, or delete), then just compare that path in order
             * to know if it should be ignored
             */
            if (get_class($change) === Rename::class) {

                /*
                 * If the destination path should be ignored, then the Rename will be transformed into a Remove change
                 * of the from path
                 */
                if (fnmatch($rule, $change->to())) {
                    $this->add(new Delete($change->from()));

                    $this->ignores[] = new Add($change->to());

                    return true;
                }

                /*
                 * If the original path is ignored, then add the new file into the destination path is needed
                 */
                if (fnmatch($rule, $change->from())) {
                    $this->add(new Add($change->to()));

                    $this->ignores[] = new Delete($change->from());

                    return true;
                }

            } else {
                if (fnmatch($rule, $change->path())) {

                    $this->ignores[] = $change;

                    return true;
                }
            }

        }

        return $ignored;
    }
}