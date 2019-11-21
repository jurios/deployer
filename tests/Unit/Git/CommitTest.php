<?php


namespace Kodilab\Deployer\Tests\Unit\Git;


use Kodilab\Deployer\Exceptions\InvalidCommitSHAReference;
use Kodilab\Deployer\Git\Commit;
use Kodilab\Deployer\Tests\TestCase;

class CommitTest extends TestCase
{
    public function test_commit_can_not_be_constructed_if_the_sha_is_not_valid()
    {
        $this->expectException(InvalidCommitSHAReference::class);

        new Commit("c15a5eec15");
    }

    public function test_commit_is_created_if_the_reference_is_valid()
    {
        $commit = new Commit("c15a5ee0d205ade08ad86174cb9c38aafd2bd226");

        $this->assertEquals(Commit::class, get_class($commit));
    }
}
