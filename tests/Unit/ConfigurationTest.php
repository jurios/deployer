<?php


namespace Kodilab\Deployer\Tests\Unit;


use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    public function test_protocol_is_accessible()
    {
        $protocol = $this->faker->word;

        $configuration = new Configuration(['manager' => ['protocol' => $protocol]]);

        $this->assertEquals($protocol, $configuration->get('manager.protocol'));
    }

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

        $configuration = Configuration::generateDefaultConfguration();

        $configuration->set($path, $value);

        $this->assertEquals($value, $configuration->get($path));
    }

    public function test_get_default_configuration_returns_the_default_configuration()
    {
        $configuration = Configuration::generateDefaultConfguration();

        $this->assertEquals(Configuration::class, get_class($configuration));
        $this->assertEquals([
            'ignore' => [],
            'include' => [],
            'manager' => [
                'protocol' => 'simulate'
            ]
        ], $configuration->toArray());

    }

    public function test_set_configuration_will_merge_the_new_configuration_in_the_raw_configuration()
    {
        $configuration = Configuration::generateDefaultConfguration();

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

        $configuration->set($index1, $config[$index1]);

        $this->assertEquals($configuration->get($index1), $config[$index1]);
        $this->assertEquals($configuration->get($path), $value);

    }
}