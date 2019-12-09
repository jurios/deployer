<?php


namespace Kodilab\Deployer\Changes\ChangeList\Traits;


use Kodilab\Deployer\Helpers\Path;
use Kodilab\Deployer\Support\Collection;

trait IgnoreFiles
{
    /**
     * Returns the ignored change list
     *
     * @return Collection
     */
    public function getIgnored()
    {
        return $this->ignored;
    }

    /**
     * Returns whether a path should be ignored
     *
     * @param string $path
     * @return bool
     */
    protected function shouldBeIgnored(string $path)
    {
        return Path::match($this->getIgnoreRules(), $path);
    }

    /**
     * Returns the ignore rules
     *
     * @return array
     */
    protected function getIgnoreRules()
    {
        return $this->config->get('ignore', []);
    }
}