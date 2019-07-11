<?php


namespace Kodilab\Deployer\Git;




class Git
{
    /** @var string */
    protected $project_path;

    public function __construct(string $project_path)
    {
        $this->project_path = $project_path;
    }

    public function diff(string $from, string $to)
    {
        return new Diff($this->project_path, $from, $to);
    }

    public function checkout(string $path, string $commit, string $dest = null)
    {
        if (is_null($dest)) {
            $dest = $path;
        }

        $command = 'git show ' . $commit . ':' . $path . ' > ' . $dest . ' 2> /dev/null';

        exec($command);
    }

    /**
     * Returns the last commit
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function getLastCommit()
    {
        $commit = null;
        exec('git log --no-decorate -n 1 --format="%H"', $commit);

        $commit = $this->cleanExecOutputCommit($commit);

        if (!isCommitValid($commit)) {
            new \Exception('The last commit is not valid');
        }

        return $commit;
    }

    /**
     * Returns the "nothing" commit in order to list all tracked files
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function getEmptyCommit()
    {
        $commit = null;

        //That's the way to get the first commit. Unfortunately, we need the diff from "nothing"
        //exec('git log --no-decorate -n 1 --format="%h" --max-parents=0 HEAD', $commit);
        //Here the way to get the "commit" of nothing.
        exec('git hash-object -t tree /dev/null', $commit);

        $commit = $this->cleanExecOutputCommit($commit);

        if (!isCommitValid($commit)) {
            new \Exception('Empty commit is not valid');
        }

        return $commit;
    }

    /**
     * Cleans the git output in order to get the commit
     *
     * @param $output
     * @return mixed
     * @throws \Exception
     */
    private function cleanExecOutputCommit($output)
    {
        if (!is_array($output) || count($output) !== 1) {
            throw new \Exception("Unexpected exec output for commit");
        }

        $output = $output[0];

        return $output;
    }
}