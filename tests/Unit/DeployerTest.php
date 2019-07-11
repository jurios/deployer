<?php


namespace Kodilab\Deployer\Tests\Unit;


use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Deployer;
use Kodilab\Deployer\Managers\Protocols\SimulateManager;
use Kodilab\Deployer\Tests\TestCase;

class DeployerTest extends TestCase
{
    public function test_protocol_config_will_load_the_suitable_manager()
    {
        $deployer = new Deployer(self::LARAVEL_PROJECT, Configuration::generateDefaultConfguration()->toArray());

        $this->assertEquals(SimulateManager::class, get_class($deployer->manager));
    }
}