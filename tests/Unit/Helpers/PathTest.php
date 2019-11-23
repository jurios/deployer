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

    public function test_getMatchedRules_should_return_true_if_the_rule_is_included_in_the_path()
    {
        $this->assertTrue(Path::match(['a/b/c'], 'a/b/c/d'));
    }

    public function test_getMatchedRules_wildcard_should_work_as_expected()
    {
        $this->assertFalse(Path::match(['a/b/c/*.php'], 'a/b/c/d'));
        $this->assertTrue(Path::match(['a/b/c/*.php'], 'a/b/c/d.php'));
    }
}