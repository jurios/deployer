<?php


namespace Kodilab\Deployer\ComposerLock;



use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Exceptions\InvalidComposerLockFileException;

class ComposerLockComparator
{
    const REASON = 'vendor diff';

    /**
     * @var ComposerLock
     */
    protected $local;

    /**
     * @var ComposerLock
     */
    protected $production;

    /**
     * ComposerLockComparator constructor.
     * @param ComposerLock $local
     * @param ComposerLock $production
     * @throws InvalidComposerLockFileException
     */
    public function __construct(ComposerLock $local, ComposerLock $production)
    {
        $this->local = $local;
        $this->production = $production;
    }

    /**
     * Compare the composerLock contents in order to get a dependency changed list
     *
     * @return array
     * @throws \Kodilab\Deployer\Exceptions\InvalidComposerLockFileException
     */
    public function compare()
    {
        $diff = [];

        /** @var Dependency $dependency */
        foreach ($this->local->packages as $dependency)
        {
            if (is_null($this->production->findInPackages($dependency->name))){
                $diff[] = new Add($dependency->getPath(), static::REASON);
            }

            if (!is_null($production_dependency = $this->production->findInPackages($dependency->name)) &&
                $dependency->getReference() !== $production_dependency->getReference()
            ) {
                $diff[] = new Modify($dependency->getPath(), static::REASON);
            }
        }

        //Comparing the other direction in case a package has been removed
        foreach ($this->production->packages as $dependency) {
            if (is_null($this->local->findInPackages($dependency->name))){
                $diff[] = new Delete($dependency->getPath(), static::REASON);
            }
        }

        return $this->addAutoloadDirectory($diff);
    }

    /**
     * Adds to the diff the autoload directory only in case there are changes in the vendor
     *
     * @param array $diff
     * @return array
     */
    protected function addAutoloadDirectory(array $diff)
    {
        if (count($diff) > 0) {
            $diff[] = new Modify('vendor/autoload', static::REASON);
        }

        return $diff;
    }
}
