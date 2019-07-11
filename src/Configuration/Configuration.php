<?php


namespace Kodilab\Deployer\Configuration;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Validator;

class Configuration implements Arrayable
{
    /** @var array */
    protected $raw_config;

    /** @var array  */
    protected $default_config;

    public function __construct(array $config)
    {
        $this->raw_config = $config;
        $this->validateRawConfig();
    }

    /**
     * Returns the configuration value based on the path
     *
     * @param string $path
     * @return array|mixed|null
     */
    public function get(string $path, $default = null)
    {
        $indexes = explode(".", $path);

        $pointer = $this->raw_config;

        foreach ($indexes as $index) {

            if (!isset($pointer[$index])) {
                return $default;
            }

            $pointer = $pointer[$index];
        }

        return !is_null($pointer) ? $pointer : $default;
    }

    /**
     * Set a configuration parameter
     *
     * @param string $path
     * @param $value
     */
    public function set(string $path, $value)
    {
        $indexes = explode(".", $path);

        $pointer = &$this->raw_config;

        foreach ($indexes as $i => $index) {

            if (!isset($pointer[$index])) {
                $pointer[$index] = [];
            }

            $pointer = &$pointer[$index];

            if ($i === count($indexes) - 1) {
                $pointer = $value;
            }
        }
    }

    /**
     * Export to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->raw_config;
    }

    /**
     * Validates configuration parameters
     *
     * @throws \Exception
     */
    private function validateRawConfig()
    {
        $validator = new Validator(new Translator(new ArrayLoader(), 'en'), $this->raw_config, [
           'include' => 'array',
           'ignore' => 'array',
           'manager.protocol' => 'required|string'
        ], []);

        if ($validator->fails()) {
            throw new \Exception('Configuration is not valid: ' . $validator->errors() );
        }
    }

    /**
     * Generates a default configuration
     *
     * @return Configuration
     */
    static public function generateDefaultConfguration()
    {
        $default_configuration = [
            'ignore' => [],
            'include' => [],
            'manager' => [
                'protocol' => 'simulate'
            ]
        ];

        return new self($default_configuration);
    }
}