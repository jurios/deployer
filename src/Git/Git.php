<?php


namespace Kodilab\Deployer\Git;




use Kodilab\Deployer\Git\Diff\DiffParser;

class Git
{
    /** @var string */
    protected $project_path;

    public function __construct(string $project_path)
    {
        $this->project_path = $project_path;
    }

    /**
     * Perform a diff and returns the parser result
     *
     * @param Commit $from
     * @param Commit $to
     * @return DiffParser
     * @throws \Exception
     */
    public function diff(Commit $from, Commit $to)
    {
        $diff = null;

        exec('git diff --name-status ' . $from . " " . $to, $diff);

        return (new DiffParser())->parse($diff);
    }

    /**
     * Save the result of checkout a file
     *
     * @param string $filePath
     * @param Commit $commit
     * @param string|null $savePath
     */
    public function checkoutFileToCommit(string $filePath, Commit $commit, string $savePath = null)
    {
        if (is_null($savePath)) {
            $savePath = $filePath;
        }

        $command = 'git show ' . $commit . ':' . $filePath . ' > ' . $savePath . ' 2> /dev/null';

        exec($command);
    }

    /**
     * Returns the last commit
     *
     * @return Commit
     * @throws \Exception
     */
    public function getLastCommit()
    {
        $commit = null;
        exec('git log --no-decorate -n 1 --format="%H"', $commit);

        $commit = $this->cleanExecOutputCommit($commit);

        return new Commit($commit);
    }

    /**
     * Returns the "empty" commit (that means, when there isn't files. This is not the same one as the first commit
     * of the project where files are added)
     *
     * @return Commit
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

        return new Commit($commit);
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
