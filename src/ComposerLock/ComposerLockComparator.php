<?php


namespace Kodilab\Deployer\ComposerLock;



class ComposerLockComparator
{
    /**
     * @var ComposerLock
     */
    protected $local;

    /**
     * @var ComposerLock
     */
    protected $production;

    public function __construct(ComposerLock $local, ComposerLock $production)
    {
        $this->local = $local;
        $this->production = $production;
    }

    /**
     * Compare the composerLock contents in order to get a dependency changed list
     *
     * @return ComposerLockDiff
     * @throws \Kodilab\Deployer\Exceptions\InvalidComposerLockFileException
     */
    public function compare(): ComposerLockDiff
    {
        $diff = new ComposerLockDiff();

        /** @var Dependency $dependency */
        foreach ($this->local->packages as $dependency)
        {
            if (is_null($this->production->findInPackages($dependency->name))){
                $diff->addAsNew($dependency);
            }

            if (!is_null($production_dependency = $this->production->findInPackages($dependency->name)) &&
                $dependency->getReference() !== $production_dependency->getReference()
            ) {
                $diff->addAsUpdated($dependency);
            }
        }

        //Comparing the other direction in case a package has been removed
        foreach ($this->production->packages as $dependency) {
            if (is_null($this->local->findInPackages($dependency->name))){
                $diff->addAsRemoved($dependency);
            }
        }

        return $diff;
    }
}
