<?php


namespace Kodilab\Deployer\Tests\Unit;


use Kodilab\Deployer\Tests\TestCase;

class HelperTest extends TestCase
{
    public function test_matchPath_returns_true_when_the_path_is_listed()
    {
        $this->assertTrue(matchPath(['a/b/*'], 'a/b/c'));
        $this->assertTrue(matchPath(['a/b/*'], 'a/b/c'));
        $this->assertFalse(matchPath(['a/b/*'], 'a/c/b'));
    }

    public function test_matchPath_update_the_rules()
    {
        matchPath([
            'a/b/*',
            'a/c/b',
            'a/*'
        ], 'a/b/c', $rules);

        $this->assertEquals(['a/b/*', 'a/*'], $rules);
    }
}