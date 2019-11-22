<?php


namespace Kodilab\Deployer\Tests\Unit\Composer;


use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\ComposerLock\ComposerLock;
use Kodilab\Deployer\ComposerLock\ComposerLockComparator;
use Kodilab\Deployer\Exceptions\InvalidComposerLockFileException;
use Kodilab\Deployer\Tests\TestCase;

class ComposerLockComparatorTest extends TestCase
{
    /**
     * @throws InvalidComposerLockFileException
     */
    public function test_a_new_package_should_be_present_in_the_diff_after_compare()
    {
        $local = new ComposerLock($this->loadMockComposerLock());

        $raw_production = $this->loadMockComposerLock();
        unset($raw_production['packages'][0]);
        $production = new ComposerLock($raw_production);

        $comparator = new ComposerLockComparator($local, $production);
        /** @var Change $change */
        $change = $comparator->compare()[0];

        $this->assertEquals('vendor/' . $local->packages[0]->name, $change->getSource());
        $this->assertEquals(Add::class, get_class($change));
    }

    /**
     * @throws InvalidComposerLockFileException
     */
    public function test_an_updated_package_should_be_present_in_the_diff_after_compare()
    {
        $local = new ComposerLock($this->loadMockComposerLock());

        $raw_production = $this->loadMockComposerLock();
        $raw_production['packages'][0]['dist']['reference'] = $this->faker->md5;
        $production = new ComposerLock($raw_production);

        $comparator = new ComposerLockComparator($local, $production);

        /** @var Change $change */
        $change = $comparator->compare()[0];

        $this->assertEquals('vendor/' . $local->packages[0]->name, $change->getSource());
        $this->assertEquals(Modify::class, get_class($change));
    }

    /**
     * @throws InvalidComposerLockFileException
     */
    public function test_a_removed_package_should_be_present_in_the_diff_after_compare()
    {
        $raw_local = $this->loadMockComposerLock();
        unset($raw_local['packages'][0]);
        $local = new ComposerLock($raw_local);

        $production = new ComposerLock($this->loadMockComposerLock());

        $comparator = new ComposerLockComparator($local, $production);

        /** @var Change $change */
        $change = $comparator->compare()[0];

        $this->assertEquals('vendor/' . $production->packages[0]->name, $change->getSource());
        $this->assertEquals(Delete::class, get_class($change));

    }
}
