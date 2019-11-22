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
    protected $source;

    /**
     * @var string
     */
    protected $destination;

    /**
     * @var string
     */
    protected $reason;

    public function __construct(string $source, string $destination = null, string $reason = 'unknown')
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->reason = $reason;
    }

    /**
     * Returns the source field
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Retruns the destination field
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
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
     * Returns if both entries has the same source
     *
     * @param Change $entry
     * @return bool
     */
    public function hasSameSource(Change $entry)
    {
        return $this->getSource() === $entry->getSource();
    }

    /**
     * Export to array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'source' => $this->source,
            'destination' => $this->destination
        ];
    }

    /**
     * Instance a entry depending on the type
     * @param string $status
     * @param string $source
     * @param string|null $destination
     * @param string $reason
     * @return Change
     * @throws DiffEntryStatusUnknown
     */
    public static function make(string $status, string $source, string $destination = null, string $reason = 'unknown')
    {
        switch ($status) {
            case DiffParser::ADDED:
                return new Add($source, $reason);
            case DiffParser::MODIFIED:
                return new Modify($source, $reason);
            case DiffParser::DELETED:
                return new Delete($source, $reason);
            case DiffParser::RENAMED:
                return new Rename($source, $destination, $reason);
            default:
                throw new DiffEntryStatusUnknown($status, $status . "\t" . $source . "\t" . $destination);
        }
    }
}
