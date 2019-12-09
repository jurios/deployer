<?php


namespace Kodilab\Deployer\Changes;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\Filesystem;
use Kodilab\Deployer\Exceptions\DiffEntryStatusUnknown;
use Kodilab\Deployer\Git\Diff\DiffParser;

abstract class Change implements Arrayable
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @var bool
     */
    protected $is_dir;

    public function __construct(string $path, bool $is_dir = false, string $reason = 'unknown')
    {
        $this->path = $path;
        $this->reason = $reason;
        $this->is_dir = $is_dir;
    }

    /**
     * Returns the source field
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the change reason
     *
     * @return bool
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Returns the status label text (renamed, added...)
     *
     * @return string
     */
    abstract public function getLabeledStatus();

    /**
     * Returns the output color
     *
     * @return mixed
     */
    abstract public function getColor();

    /**
     * Returns if both entries has the same path
     *
     * @param Change $entry
     * @return bool
     */
    public function hasSamePath(Change $entry)
    {
        return $this->getPath() === $entry->getPath();
    }

    /**
     * Returns whether the changed item is a file or a directory
     *
     * @return bool
     */
    public function isDir()
    {
        return $this->is_dir;
    }

    /**
     * Export to array
     *
     * @return array
     */
    public function toArray()
    {
        return ['path' => $this->path, 'reason' => $this->reason];
    }

    /**
     * Instance a entry depending on the type
     * @param string $status
     * @param string $path
     * @param string|null $destination
     * @param bool $is_dir
     * @param string $reason
     * @return Change[]
     * @throws DiffEntryStatusUnknown
     */
    public static function buildChanges(string $status, string $path, string $destination = null, bool $is_dir = false, string $reason = 'unknown')
    {
        switch ($status) {
            case DiffParser::ADDED:
                return [new Add($path, $is_dir, $reason)];
            case DiffParser::MODIFIED:
                return [new Modify($path, $is_dir, $reason)];
            case DiffParser::DELETED:
                return [new Delete($path, $is_dir, $reason)];
            case DiffParser::RENAMED:
                return [
                    new Delete($path, $is_dir, $reason . '(rename)'),
                    new Add($destination, $is_dir, $reason . '(rename)')
                ];
            default:
                throw new DiffEntryStatusUnknown($status, $status . "\t" . $path . "\t");
        }
    }
}
