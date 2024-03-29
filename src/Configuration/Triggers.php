<?php


namespace Kodilab\Deployer\Configuration;


use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\ChangeList;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Project;

class Triggers
{
    /**
     * Configuration instance
     *
     * @var Configuration
     */
    protected $config;

    /**
     * Project instance
     *
     * @var Project
     */
    protected $project;

    /**
     * Triggers configuration
     *
     * @var array
     */
    protected $triggers;

    /**
     * Trigger actions rules
     *
     * @var array
     */
    protected $actions;

    /**
     * Changes added by triggers
     *
     * @var array
     */
    protected $changes;

    public function __construct(Configuration $config, Project $project, ChangeList $changeList)
    {
        $this->config = $config;
        $this->project = $project;
        $this->triggers = $this->config->get('triggers', []);
        $this->actions = array_keys($this->triggers);
        $this->changes = $this->getTriggeredChanges($changeList);
    }

    /**
     * Returns the changes
     *
     * @return array
     */
    public function getChanges()
    {
        return $this->changes;
    }

    private function getTriggeredChanges(ChangeList $changeList)
    {
        $changes = [];

        /** @var Change $change */
        foreach ($changeList->changes() as $change) {
            if (matchPath($this->actions, $change->path(), $matched_actions)) {
                $files = $this->getTriggeredFiles($matched_actions);

                foreach ($files as $file) {
                    $changes[$file] = new Modify($file);
                }
            }
        }

        return $changes;
    }

    /**
     * Return the project files triggered by the acions
     *
     * @param array $actions
     * @return array
     */
    private function getTriggeredFiles(array $actions)
    {
        $files = [];

        foreach ($actions as $action) {
            $rules = $this->triggers[$action];

            foreach ($rules as $rule) {
                $files = array_merge($files, $this->project->files($rule));
            }
        }

        return $files;
    }
}