<?php


namespace Kodilab\Deployer\Tests\Unit\Changes;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\Rename;
use Kodilab\Deployer\Tests\TestCase;

class ChangeTest extends TestCase
{
    use WithFaker;

    public function test_is_returns_true_if_both_changes_are_the_same_one()
    {
        $change = new Change($this->faker->paragraph);

        $cloned_change = clone $change;

        $other_change = new Change($this->faker->paragraph);

        $this->assertTrue($change->is($cloned_change));
        $this->assertFalse($change->is($other_change));
    }

    public function test_is_returns_true_if_rename_change_from_and_to_are_equals()
    {
        $rename = new Rename($this->faker->paragraph, $this->faker->paragraph);

        $cloned_rename = clone $rename;

        $partial_rename = new Rename($rename->from(), $this->faker->paragraph);
        $other_partial_rename = new Rename($this->faker->paragraph, $rename->to());

        $other_rename = new Rename($this->faker->paragraph, $this->faker->paragraph);

        $this->assertTrue($rename->is($cloned_rename));
        $this->assertFalse($rename->is($partial_rename));
        $this->assertFalse($rename->is($other_partial_rename));
        $this->assertFalse($rename->is($other_rename));
    }
}