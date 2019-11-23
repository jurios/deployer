<?php


namespace Kodilab\Deployer\Tests\Unit\Composer;


use Kodilab\Deployer\ComposerLock\ComposerLock;
use Kodilab\Deployer\ComposerLock\Dependency;
use Kodilab\Deployer\ComposerLock\DependencyCollection;
use Kodilab\Deployer\Tests\TestCase;

class ComposerLockTest extends TestCase
{
    /**
     * @var ComposerLock
     */
    protected $composerLock;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->composerLock = new ComposerLock($this->loadMockComposerLock());
    }

    public function test_casted_attributes_are_casted_when_is_initialized()
    {
        $this->assertEquals(DependencyCollection::class, get_class($this->composerLock->packages));
    }

    public function test_findInPackages_should_return_the_dependency_if_exists()
    {
        $this->assertEquals(
            new Dependency($this->loadMockComposerLock()['packages'][0]),
            $this->composerLock->findInPackages($this->loadMockComposerLock()['packages'][0]['name'])
        );
    }
}