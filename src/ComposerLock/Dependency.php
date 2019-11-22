<?php


namespace Kodilab\Deployer\ComposerLock;


class Dependency extends JSONComponent
{
    /**
     * Returns the package's publisher
     *
     * @return string
     */
    public function getPublisher(): string
    {
        return explode('/', $this->attributes['name'])[0];
    }

    /**
     * Returns the validation rules
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'name' => ['required', 'string', function($attribute, $value, $fail) {
                if (!preg_match("/^[a-zA-Z0-9-_]*\/[a-zA-Z0-9-_]*$/", $value)) {
                    $fail('Package does not follow the format publisher/package');
                }
            }],
            'dist.reference' => ['required', 'string'],
            'type' => ['required', 'string'],
            'bin'  => ['array']
        ];
    }

    /**
     * Returns the dependency path
     */
    public function getPath()
    {
        return 'vendor/' . $this->attributes['name'];
    }

    /**
     * Returns the dependency reference build
     *
     * @return string
     */
    public function getReference(): string
    {
        return $this->attributes['dist']['reference'];
    }
}
