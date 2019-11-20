<?php


namespace Kodilab\Deployer\Tests\Unit\Composer;


use Kodilab\Deployer\ComposerLock\ComposerLock;
use Kodilab\Deployer\ComposerLock\ComposerLockComparator;
use Kodilab\Deployer\Tests\TestCase;

class ComposerLockComparatorTest extends TestCase
{
    public function test_a_new_package_should_be_present_in_the_diff_after_compare()
    {
        $local = new ComposerLock($this->loadMockComposerLock());

        $raw_production = $this->loadMockComposerLock();
        unset($raw_production['packages'][0]);
        $production = new ComposerLock($raw_production);

        $comparator = new ComposerLockComparator($local, $production);

        $this->assertEquals(
            $local->packages[0], $comparator->compare()->getNewDependencies()->first()
        );
    }

    public function test_an_updated_package_should_be_present_in_the_diff_after_compare()
    {
        $local = new ComposerLock($this->loadMockComposerLock());

        $raw_production = $this->loadMockComposerLock();
        $raw_production['packages'][0]['dist']['reference'] = $this->faker->md5;
        $production = new ComposerLock($raw_production);

        $comparator = new ComposerLockComparator($local, $production);

        $this->assertEquals(
            $local->packages[0], $comparator->compare()->getUpdatedDependencies()->first()
        );
    }

    public function test_a_removed_package_should_be_present_in_the_diff_after_compare()
    {
        $raw_local = $this->loadMockComposerLock();
        unset($raw_local['packages'][0]);
        $local = new ComposerLock($raw_local);

        $production = new ComposerLock($this->loadMockComposerLock());

        $comparator = new ComposerLockComparator($local, $production);

        $this->assertEquals(
            $production->packages[0], $comparator->compare()->getRemovedDependencies()->first()
        );
    }
}
