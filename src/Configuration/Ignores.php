<?php


namespace Kodilab\Deployer\Configuration;


use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\ChangeList;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Rename;

class Ignores
{
    /**
     * ChangeList instance
     *
     * @var ChangeList
     */
    protected $changeList;

    /**
     * Ignores configuration
     *
     * @var array
     */
    protected $ignores;

    /**
     * Trigger actions rules
     *
     * @var array
     */
    protected $actions;

    /**
     * Changes removed by ignores
     *
     * @var array
     */
    protected $changes;

    public function __construct(Configuration $config, ChangeList $changeList)
    {
        $this->changeList = $changeList;
        $this->ignores = $config->get('ignore', []);
    }

    /**
     * Returns the ChangeList without the removed changes
     *
     * @return ChangeList
     */
    public function getChangeListWithoutIgnores()
    {
        $this->changes = [];

        foreach ($this->changeList->changes() as $change) {
            if ($this->shouldBeIgnored($change)) {
                $this->changes[] = $change;
                $this->changeList->remove($change);
            }
        }

        return $this->changeList;
    }

    /**
     * Returns the changed removed
     *
     * @return array
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Returns if a change should be ignored based on the ignores configuration rules
     *
     * @param Change $change
     * @return bool
     */
    private function shouldBeIgnored(Change $change)
    {

        $ignored = false;

        foreach ($this->ignores as $rule) {

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

                    $new_change = new Delete($change->from());
                    if (!$this->shouldBeIgnored($new_change)) {
                        $this->changeList->add(new Delete($change->from()));
                    }
                    return true;
                }

                /*
                 * If the original path is ignored, then add the new file into the destination path is needed
                 */
                if (fnmatch($rule, $change->from())) {

                    $new_change = new Add($change->to());
                    if (!$this->shouldBeIgnored($new_change)) {
                        $this->changeList->add(new Delete($change->from()));
                    }
                    return true;
                }

            } else {
                if (fnmatch($rule, $change->path())) {
                    return true;
                }
            }

        }

        return $ignored;
    }
}