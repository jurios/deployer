<?php


namespace Kodilab\Deployer\ComposerLock;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Kodilab\Deployer\ComposerLock\Contracts\AttributeCaster;
use Kodilab\Deployer\Exceptions\InvalidComposerLockFileException;
use Kodilab\Deployer\Validator\Translator;

abstract class JSONComponent implements Arrayable
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $raw;

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * JSONComponent constructor.
     * @param array $attributes
     * @throws InvalidComposerLockFileException
     */
    public function __construct(array $attributes)
    {
        $this->raw = $attributes;

        $this->validateAttributes($attributes);

        $this->attributes = $this->buildAttributes($attributes);
    }

    protected function buildAttributes(array $attributes): array
    {
        $result = [];

        foreach ($attributes as $index => $value) {
            $result[$index] = $value;

            if ($this->shouldBeCasted($index) &&
                in_array(AttributeCaster::class, class_implements($castType = $this->getCastType($index)))) {
                $result[$index] = $castType::input($value);
            }
        }

        return $result;
    }

    /**
     * Returns whether a given attribute should be casted
     *
     * @param string $attribute
     * @return bool
     */
    protected function shouldBeCasted(string $attribute): bool
    {
        return isset($this->casts[$attribute]);
    }

    /**
     * Returns the cast type for a given attribute or null if it should not be casted
     *
     * @param string $attribute
     * @return string
     */
    protected function getCastType(string $attribute): string
    {
        return isset($this->casts[$attribute]) ? $this->casts[$attribute] : null;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
    }

    /**
     * Validates the dependency entry
     *
     * @param array $attributes
     * @throws InvalidComposerLockFileException
     */
    protected function validateAttributes(array $attributes): void
    {
        try {
            (new Validator(new Translator(), $attributes, $this->getValidationRules()))->validate();
        } catch (ValidationException $e) {
            throw new InvalidComposerLockFileException($this, $e->errors());
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->raw;
    }

    /**
     * Returns the validation rules array
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [];
    }
}
