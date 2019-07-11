<?php


namespace Kodilab\Deployer\Configuration;


use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Project;

class Includes
{
    /**
     * Project instance
     *
     * @var Project
     */
    protected $project;

    /**
     * Includes configuration
     *
     * @var array
     */
    protected $includes;

    /**
     * Trigger actions rules
     *
     * @var array
     */
    protected $actions;

    /**
     * Changes applied by includes
     *
     * @var array
     */
    protected $changes;

    public function __construct(Configuration $config, Project $project)
    {
        $this->project = $project;
        $this->includes = $config->get('include', []);

        $this->changes = $this->getIncludesChanges();
    }

    /**
     * @return array
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Generate the changes
     * @return array
     */
    private function getIncludesChanges()
    {
        $changes = [];

        /** @var string $path */
        foreach ($this->project->files() as $path) {
            if (matchPath($this->includes, $path)) {
                $change = new Modify($path);
                $changes[] = $change;
            }
        }

        return $changes;
    }
}