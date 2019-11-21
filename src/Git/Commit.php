<?php


namespace Kodilab\Deployer\Git;


use Kodilab\Deployer\Exceptions\InvalidCommitSHAReference;

class Commit
{
    protected $reference;

    /**
     * Commit constructor.
     * @param string $sha1
     * @throws InvalidCommitSHAReference
     */
    public function __construct(string $sha1)
    {
        $this->validateCommitId($sha1);

        $this->reference = $sha1;
    }

    public function __toString()
    {
        return $this->reference;
    }

    /**
     * Returns the commit SHA-1 reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Validates is a valid SHA-1 commit reference
     *
     * @param string $sha1
     * @throws InvalidCommitSHAReference
     */
    protected function validateCommitId(string $sha1)
    {
        if (! preg_match("/[a-z0-9]{40}/", $sha1)) {
            throw new InvalidCommitSHAReference($sha1);
        }
    }
}
