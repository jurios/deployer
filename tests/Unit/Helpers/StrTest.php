<?php


namespace Kodilab\Deployer\Tests\Unit\Helpers;


use Kodilab\Deployer\Helpers\Str;
use Kodilab\Deployer\Tests\TestCase;

class StrTest extends TestCase
{
    public function test_removeLastCarriageReturn_should_remove_the_last_carraige_return()
    {
        $text = $this->faker->parse(1) . "\n";

        $this->assertEquals(str_replace("\n", "", $text), Str::removeLastCarriageReturn($text));
    }
}
