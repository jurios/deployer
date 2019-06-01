<?php


namespace Kodilab\Deployer\Tests\Unit;


use Kodilab\Deployer\Project;
use Kodilab\Deployer\Tests\TestCase;

class ProjectTest extends TestCase
{
    public function test_files_with_filter_returns_the_paths_of_match_files()
    {
        $project = new Project(self::LARAVEL_PROJECT);

        $paths = [
            'test/a/b/c/file.php',
            'test/a/b/d/file.php',
            'test/b/file.php',
        ];

        $project->addFiles($paths);

        $filtered_files = $project->files('test/a');

        $this->assertEquals(2, count($filtered_files));
        $this->assertEquals('test/a/b/c/file.php', array_values($filtered_files)[0]);
        $this->assertEquals('test/a/b/d/file.php', array_values($filtered_files)[1]);
    }
}