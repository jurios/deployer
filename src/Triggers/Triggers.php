<?php


namespace Kodilab\Deployer\Triggers;


use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\ChangeList;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Configuration;
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

    public function __construct(Configuration $config, Project $project)
    {
        $this->triggers = $this->config->get('triggers', []);
        $this->actions = array_keys($this->triggers);
    }

    public function getTriggeredChanges(ChangeList $changeList)
    {
        $changes = [];

        /** @var Change $change */
        foreach ($changeList as $change) {

            if (matchPath($this->actions, $change->path(), $matched_actions)) {

                $files = $this->getTriggeredFiles($matched_actions);

                foreach ($files as $file) {
                    $changes[] = new Modify($file);
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
                array_merge($files, $this->project->files($rule));
            }
        }

        return $files;
    }
}