<?php


namespace Kodilab\Deployer\ComposerLock;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Kodilab\Deployer\ComposerLock\Contracts\AttributeCaster;
use Kodilab\Deployer\Exceptions\InvalidComposerLockFileException;

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

    protected function operatorForWhere($key, $operator = null, $value = null)
    {
        if (func_num_args() === 1) {
            $value = true;

            $operator = '=';
        }
        if (func_num_args() === 2) {
            $value = $operator;

            $operator = '=';
        }

        return function ($item) use ($key, $operator, $value) {

            if ($item instanceof Arrayable) {
                $item = $item->toArray();
            }

            $retrieved = data_get($item, $key);

            $strings = array_filter([$retrieved, $value], function ($value) {
                return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
            });

            if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1) {
                return in_array($operator, ['!=', '<>', '!==']);
            }

            switch ($operator) {
                default:
                case '=':
                case '==':  return $retrieved == $value;
                case '!=':
                case '<>':  return $retrieved != $value;
                case '<':   return $retrieved < $value;
                case '>':   return $retrieved > $value;
                case '<=':  return $retrieved <= $value;
                case '>=':  return $retrieved >= $value;
                case '===': return $retrieved === $value;
                case '!==': return $retrieved !== $value;
            }
        };
    }
}
