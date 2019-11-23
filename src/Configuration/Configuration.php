<?php


namespace Kodilab\Deployer\Configuration;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\Validator;
use Kodilab\Deployer\Validator\Translator;

class Configuration implements Arrayable
{
    /** @var array */
    protected $config;

    /**
     * Configuration constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge(static::getDefualtSettings(), $config);

        $this->validateConfig($this->config);
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

        $pointer = $this->config;

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

        $pointer = &$this->config;

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
        return $this->config;
    }

    /**
     * Validates configuration parameters
     *
     * @param array $config
     * @throws \Exception
     */
    private function validateConfig(array $config)
    {
        $validator = new Validator(new Translator(), $config, [
           'include' => 'array',
           'ignore' => 'array',
           'manager.protocol' => 'required|string',
            'project_path' => 'string'
        ], []);

        if ($validator->fails()) {
            throw new \Exception('Configuration is not valid: ' . $validator->errors() );
        }
    }

    /**
     * Generates a default configuration
     *
     * @return array
     * @throws \Exception
     */
    static public function getDefualtSettings()
    {
        return require __DIR__ . '/../../config/config.php';
    }
}