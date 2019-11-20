<?php


namespace Kodilab\Deployer\ComposerLock;


class ComposerLockDiff
{
    /**
     * @var DependencyCollection
     */
    protected $new;

    /**
     * @var DependencyCollection
     */
    protected $updated;

    /**
     * @var DependencyCollection
     */
    protected $removed;

    public function __construct()
    {
        $this->new = new DependencyCollection();
        $this->updated = new DependencyCollection();
        $this->removed = new DependencyCollection();
    }

    public function addAsNew(Dependency $dependency): void
    {
        $this->new->add($dependency);
    }

    public function addAsUpdated(Dependency $dependency): void
    {
        $this->updated->add($dependency);
    }

    public function addAsRemoved(Dependency $dependency): void
    {
        $this->removed->add($dependency);
    }

    /**
     * Returns new dependencies
     *
     * @return DependencyCollection
     */
    public function getNewDependencies(): DependencyCollection
    {
        return $this->new;
    }

    /**
     * Returns updated dependencies
     *
     * @return DependencyCollection
     */
    public function getUpdatedDependencies(): DependencyCollection
    {
        return $this->updated;
    }

    /**
     * Returns removed dependencies
     *
     * @return DependencyCollection
     */
    public function getRemovedDependencies(): DependencyCollection
    {
        return $this->removed;
    }
}
