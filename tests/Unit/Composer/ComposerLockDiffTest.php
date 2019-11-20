<?php


namespace Kodilab\Deployer\Tests\Unit\Composer;


use Kodilab\Deployer\ComposerLock\ComposerLockDiff;
use Kodilab\Deployer\ComposerLock\Dependency;
use Kodilab\Deployer\Tests\TestCase;

class ComposerLockDiffTest extends TestCase
{
    public function test_addDependencyAsAnyList_should_add_a_dependency_in_the_list()
    {
        $dependency = new Dependency($this->loadMockComposerLock()['packages'][0]);

        $diff = new ComposerLockDiff();

        $diff->addAsNew($dependency);

        $this->assertEquals($diff->getNewDependencies()->toArray(), [
            0 => $dependency->toArray()
        ]);

        $diff->addAsUpdated($dependency);

        $this->assertEquals($diff->getUpdatedDependencies()->toArray(), [
            0 => $dependency->toArray()
        ]);

        $diff->addAsRemoved($dependency);

        $this->assertEquals($diff->getRemovedDependencies()->toArray(), [
            0 => $dependency->toArray()
        ]);
    }
}
