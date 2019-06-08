<?php


namespace Kodilab\Deployer\Traits;


use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Changes\Rename;

trait FileLists
{
    /**
     * Show the change list
     */
    private function listDeployTasks()
    {
        $this->output->writeln("\n\n");
        $this->output->title('Change list');

        $this->displayFiles($this->changeList->changes());
    }

    /**
     * Show ignored files
     */
    private function listIgnoredFiles()
    {
        $this->output->writeln("\n\n");
        $this->output->title('Ignored files list');

        $this->displayFiles($this->changeList->ignores());
    }

    /**
     * Show included files
     */
    private function listIncludedFiles()
    {
        $this->output->writeln("\n\n");
        $this->output->title('Included files list');

        $this->displayFiles($this->changeList->includes());
    }

    private function displayFiles(array $list)
    {
        /** @var Change $change */
        foreach ($list as $change)
        {
            if (get_class($change) === Add::class) {
                $this->output->writeln('<fg=green>[A] ' . $change->path() . '<fg=default>');
            }

            if (get_class($change) === Modify::class) {
                $this->output->writeln('<fg=blue>[M] ' . $change->path() . '<fg=default>');
            }

            if (get_class($change) === Rename::class) {
                $this->output->writeln('<fg=yellow>[R] ' . $change->from() . ' => ' . $this->changes->to() . '<fg=default>');
            }

            if (get_class($change) === Delete::class) {
                $this->output->writeln('<fg=red>[D] ' . $change->path() . '<fg=default>');
            }
        }
    }
}