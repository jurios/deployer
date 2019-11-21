<?php


namespace Kodilab\Deployer\Tests\Unit\Git\Diff\Entries;


use Kodilab\Deployer\Git\Diff\Entries\Delete;
use Kodilab\Deployer\Git\Diff\Entries\Modify;
use Kodilab\Deployer\Git\Diff\Entries\Rename;
use Kodilab\Deployer\Tests\TestCase;

class EntryTest extends TestCase
{
    public function test_hasSameSource_should_return_true_if_two_entries_has_the_same_source()
    {
        $entry = new Delete($this->faker->name);

        $entry2 = new Rename($entry->getSource(), $this->faker->name);

        $this->assertTrue($entry->hasSameSource($entry2));
    }

    public function test_hasSameSource_should_return_false_if_two_entries_has_not_the_same_source()
    {
        $entry = new Delete($this->faker->name);

        $entry2 = new Delete($this->faker->name);

        $this->assertFalse($entry->hasSameSource($entry2));
    }
}
