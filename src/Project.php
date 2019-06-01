<?php


namespace Kodilab\Deployer;


use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class Project
{
    /** @var string */
    protected $path;

    /** @var Filesystem */
    protected $filesystem;

    /** @var array */
    protected $files;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->filesystem = new Filesystem();

        $this->files = $this->fileDiscover();
    }

    /**
     * Returns the files of the project after apply the filter
     *
     * @param string|null $filter
     * @return array
     */
    public function files(string $filter = null)
    {
        if (is_null($filter)) {
            return $this->files;
        }

        return array_filter($this->files, function ($item) use ($filter) {
            return fnmatch($filter . DIRECTORY_SEPARATOR . '*/*', $item);
        });
    }

    /**
     * Add files to the project
     *
     * @param array $paths
     */
    public function addFiles(array $paths)
    {
        foreach ($paths as $path) {
            $this->add($path);
        }
    }

    /**
     * Add a file to the project
     *
     * @param string $path
     */
    public function add(string $path)
    {
        $this->files[] = $path;
    }

    /**
     * List all the project files
     *
     * @return array
     */
    private function fileDiscover()
    {
        $files = [];

        $splFileInfos = $this->filesystem->allFiles($this->path, false);

        /** @var SplFileInfo $splFileInfo */
        foreach ($splFileInfos as $splFileInfo){
            $files[] = $splFileInfo->getRelativePathname();
        }

        return $files;
    }
}