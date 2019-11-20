<?php


namespace Kodilab\Deployer\ComposerLock;


use Kodilab\Deployer\Exceptions\InvalidComposerLockFileException;

/**
 * Class ComposerLock
 * @package Kodilab\Deployer\ComposerLock
 *
 * @property $packages DependencyCollection::class
 */
class ComposerLock extends JSONComponent
{
    protected $casts = [
        'packages' => DependencyCollection::class,
        'packages-dev' => DependencyCollection::class
    ];

    /**
     * Returns the dependency if it exists in the packages list
     *
     * @param string $packageFullName
     * @return Dependency
     * @throws InvalidComposerLockFileException
     */
    public function findInPackages(string $packageFullName)
    {
        if (!is_null($dependency = $this->attributes['packages']->where('name', $packageFullName)->first())) {
            return $dependency;
        };

        return null;
    }
}
