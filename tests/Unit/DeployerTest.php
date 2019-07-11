<?php


namespace Kodilab\Deployer\Tests\Unit;


use Illuminate\Console\OutputStyle;
use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Deployer;
use Kodilab\Deployer\Managers\Protocols\SimulateManager;
use Kodilab\Deployer\Tests\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeployerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_protocol_config_will_load_the_suitable_manager()
    {
        $filename = 'php://memory';
        $stream_output = new StreamOutput(fopen($filename, 'w'), OutputInterface::VERBOSITY_NORMAL);
        $output = new OutputStyle(new StringInput(''), $stream_output);

        $deployer = new Deployer(
            self::LARAVEL_PROJECT,
            Configuration::generateDefaultConfguration()->toArray(),
            null,
            $output
        );

        $this->assertEquals(SimulateManager::class, get_class($deployer->manager));
    }
}