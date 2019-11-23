<?php


namespace Kodilab\Deployer\Tests\Unit;


use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_get_method()
    {
        $index1 = $this->faker->word;
        $index2 = $this->faker->word;
        $index3 = $this->faker->word;
        $value  = $this->faker->word;

        $config = [
            $index1 => [
                $index2 => [
                    $index3 => $value
                ]
            ]
        ];

        $path = $index1 . "." . $index2 . "." . $index3;

        $configuration = new Configuration($config);

        $this->assertEquals($value, $configuration->get($path));
    }

    public function test_getDefaultConfiguration_returns_the_default_configuration()
    {
        $this->assertEquals(require __DIR__ . '/../../config/config.php', Configuration::getDefualtSettings());
    }

    public function test_set_configuration_will_merge_the_new_configuration_in_the_raw_configuration()
    {
        $value  = $this->faker->word;
        $settings = [$value => $value];


        $config = new Configuration($settings);

        $this->assertEquals($config->get('manager.protocol'), 'simulate');
        $this->assertEquals($config->get($value), $value);
    }
}