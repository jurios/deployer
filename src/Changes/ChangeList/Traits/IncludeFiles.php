<?php


namespace Kodilab\Deployer\Changes\ChangeList\Traits;


use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Helpers\Path;

trait IncludeFiles
{
    /**
     * Returns the include rules
     *
     * @return array
     */
    protected function getIncludedRules()
    {
        return $this->config->get('include', []);
    }

    /**
     * Add the project files that must be included
     *
     */
    protected function addIncludedFilesToChanges()
    {
        $shouldBeIncludedFiles = array_filter($this->files, function (string $path) {
            return $this->shouldBeIncluded($path);
        });

        $changes = array_map(function (string $path) {
            return new Add($path, 'included');
        }, $shouldBeIncludedFiles);

        $this->merge($changes);
    }

    /**
     * Returns whether a path should be included
     *
     * @param string $path
     * @return bool
     */
    protected function shouldBeIncluded(string $path)
    {
        return Path::match($this->getIncludedRules(), $path);
    }
}