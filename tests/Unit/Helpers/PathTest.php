<?php


namespace Kodilab\Deployer\Tests\Unit\Helpers;


use Kodilab\Deployer\Helpers\Path;
use Kodilab\Deployer\Tests\TestCase;

class PathTest extends TestCase
{
    public function test_getMatchedRules_should_return_true_if_the_path_is_the_rule()
    {
        $this->assertTrue(Path::match(['a/b/c'], 'a/b/c'));
    }

    public function test_getMatchedRules_should_return_false_if_the_rule_does_not_match_the_path()
    {
        $this->assertFalse(Path::match(['a/b/c'], 'a/b/c/d'));
    }

    public function test_getMatchedRules_should_return_true_if_the_wildcard_is_used_when_rule_is_part_of_the_path()
    {
        $this->assertTrue(Path::match(['a/b/c/*'], 'a/b/c/d/e/f'));
    }

    public function test_getMatchedRules_wildcard_should_work_as_expected()
    {
        $this->assertFalse(Path::match(['a/b/c/*.php'], 'a/b/c/d'));
        $this->assertTrue(Path::match(['a/b/c/*.php'], 'a/b/c/d.php'));
    }

    public function test_build_will_build_a_path()
    {
        $partials = [
            $this->faker->word,
            $this->faker->word,
            $this->faker->word,
            $this->faker->word,
        ];
        $path = $partials[0]
            .DIRECTORY_SEPARATOR
            .$partials[1]
            .DIRECTORY_SEPARATOR
            .$partials[2]
            .DIRECTORY_SEPARATOR
            .$partials[3];
        $this->assertEquals($path, Path::build(
            $partials[0],
            $partials[1],
            $partials[2],
            $partials[3])
        );
    }
    public function test_build_will_ignore_empty_partials()
    {
        $partials = [
            $this->faker->word,
            "",
            $this->faker->word
        ];
        $path = $partials[0]
            .DIRECTORY_SEPARATOR
            .$partials[2];
        $this->assertEquals($path, Path::build(
            $partials[0],
            $partials[1],
            $partials[2])
        );
    }
    public function test_build_will_ignore_last_partials_if_is_empty_and_should_not_add_trailing_slash()
    {
        $partials = [
            $this->faker->word,
            ""
        ];
        $path = $partials[0];
        $this->assertEquals($path, Path::build(
            $partials[0],
            $partials[1])
        );
    }

    public function test_build_will_remove_multiple_slashes_in_case_it_exists()
    {
        $partials = [
            $this->faker->word,
            $this->faker->word
        ];

        $path = $partials[0] . DIRECTORY_SEPARATOR . $partials[1];

        $this->assertEquals($path, Path::build($partials[0] . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . $partials[1]));
    }
}