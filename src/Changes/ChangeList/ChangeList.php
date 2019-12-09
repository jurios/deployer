<?php


namespace Kodilab\Deployer\Changes\ChangeList;



use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\ChangeList\Traits\IgnoreFiles;
use Kodilab\Deployer\Changes\ChangeList\Traits\IncludeFiles;
use Kodilab\Deployer\Changes\ChangeList\Traits\OutputList;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Exceptions\ChangeIncoherenceException;
use Kodilab\Deployer\Helpers\Path;
use Kodilab\Deployer\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class ChangeList
{
    use OutputList;
    use IgnoreFiles, IncludeFiles;

    /**
     * Change list
     *
     * @var Collection
     */
    protected $changes;

    /**
     * @var Collection
     */
    protected $ignored;

    /**
     * Deployer configuration
     *
     * @var Configuration
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Project path file list
     *
     * @var string[]
     */
    protected $files;

    /**
     * @var string
     */
    protected $project_path;

    public function __construct(Configuration $config, string $project_path)
    {
        $this->config = $config;
        $this->changes = new Collection();
        $this->ignored = new Collection();
        $this->project_path = $project_path;

        $this->filesystem = new Filesystem();

        $this->files = $this->discoverFiles($this->project_path);

        $this->addIncludedFilesToChanges();
    }

    /**
     * @param Change $change
     * @return ChangeList
     * @throws ChangeIncoherenceException
     */
    public function add(Change $change)
    {
        if ($this->shouldBeIgnored($change->getPath())) {
            $this->ignored->put($change->getPath(), $change);
            return $this;
        }

        //In order to avoid let empty directories in production, we must check if the container directory exists
        // as git doesn't track directories.
        if (get_class($change) === Delete::class && !$change->isDir()) {
            /** @var Delete $change */
            return $this->recursiveDelete($change);
        }

        //When a directory is added, we must generate a change for each file in the directory (recursively)
        if (get_class($change) === Add::class && $change->isDir()) {
            /** @var Add $change */
            return $this->recursiveAdd($change);
        }

        //When a directory is modified, we must generate a change for each file in the directory (recursively)
        if (get_class($change) === Modify::class && $change->isDir()) {
            /** @var Modify $change */
            return $this->recursiveModify($change);
        }

        return $this->addToChanges($change);
    }

    /**
     * @param Add $add
     * @return ChangeList
     * @throws ChangeIncoherenceException
     */
    protected function recursiveAdd(Add $add)
    {
        $files = $this->discoverFiles(Path::build($this->project_path, $add->getPath()));

        foreach ($files as $file) {
            $change = new Add(Str::after($file, $this->project_path . DIRECTORY_SEPARATOR), false, $add->getReason());
            $this->addToChanges($change);
        }

        return $this;
    }

    /**
     * @param Modify $modify
     * @return ChangeList
     * @throws ChangeIncoherenceException
     */
    protected function recursiveModify(Modify $modify)
    {
        $files = $this->discoverFiles(Path::build($this->project_path, $modify->getPath()));

        foreach ($files as $file) {
            $change = new Modify(Str::after($file, $this->project_path . DIRECTORY_SEPARATOR), false, $modify->getReason());
            $this->addToChanges($change);
        }

        return $this;
    }

    /**
     * @param Delete $delete
     * @return $this
     * @throws ChangeIncoherenceException
     */
    protected function recursiveDelete(Delete $delete)
    {
        $parent_directory = Path::build($this->project_path, dirname($delete->getPath()));

        if ($this->filesystem->isDirectory($parent_directory)) {
            $files = $this->discoverFiles($parent_directory, true);
        } else {
            $files = [];
        }

        if (count($files) === 0 && $parent_directory !== $this->project_path) {
            return $this->recursiveDelete(new Delete(dirname($delete->getPath()), true, $delete->getReason()));
        }

        return $this->addToChanges($delete);
    }

    /**
     * Add a Change to the change list
     *
     * @param Change $change
     * @return $this
     * @throws ChangeIncoherenceException
     */
    protected function addToChanges(Change $change)
    {
        $this->validateEntryCoherence($change);

        $this->changes->put($change->getPath(), $change);
        $this->changes = $this->changes->sortKeys();

        return $this;
    }

    /**
     * @param mixed $items
     * @return $this|Collection
     * @throws ChangeIncoherenceException
     */
    public function merge($items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * Returns the change list
     *
     * @return Collection
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * @param Change $change
     * @throws ChangeIncoherenceException
     */
    protected function validateEntryCoherence(Change $change)
    {
        /** @var Change $item */
        foreach ($this->changes as $item)
        {
            if ($item->hasSamePath($change) && get_class($change) !== get_class($item)) {
                throw new ChangeIncoherenceException($item, $change);
            }
        }
    }

    /**
     * Returns the project path file list
     *
     * @param string $path
     * @param bool $hidden
     * @return array
     */
    protected function discoverFiles(string $path, bool $hidden = false)
    {
        $filesystem = new Filesystem();

        return array_map(function (SplFileInfo $file) {
            return $file->getPathname();
        }, $filesystem->allFiles($path, $hidden));
    }
}
