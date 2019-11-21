<?php


namespace Kodilab\Deployer\ComposerLock;


use Kodilab\Deployer\ComposerLock\Contracts\AttributeCaster;
use Kodilab\Deployer\Exceptions\InvalidComposerLockFileException;
use Kodilab\Deployer\Support\Collection;

class DependencyCollection extends Collection implements AttributeCaster
{
    /**
     * @param $data
     * @return static
     * @throws InvalidComposerLockFileException
     */
    public static function input($data)
    {
        $collection = new static();

        if (!is_array($data)) {
            throw new \InvalidArgumentException(static::class . ' caster expects an array');
        }

        /** @var array $attributes */
        foreach ($data as $attributes) {
            $collection->add(new Dependency($attributes));
        }

        return $collection;
    }
}
