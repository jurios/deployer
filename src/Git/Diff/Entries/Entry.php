<?php


namespace Kodilab\Deployer\Git\Diff\Entries;


use Illuminate\Contracts\Support\Arrayable;
use Kodilab\Deployer\Exceptions\DiffEntryStatusUnknown;
use Kodilab\Deployer\Git\Diff\DiffParser;

abstract class Entry implements Arrayable
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $destination;

    public function __construct(string $source, string $destination = null)
    {
        $this->source = $source;
        $this->destination = $destination;
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
     * Returns if both entries has the same source
     *
     * @param Entry $entry
     * @return bool
     */
    public function hasSameSource(Entry $entry)
    {
        return $this->getSource() === $entry->getSource();
    }

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
     * @return Entry
     * @throws DiffEntryStatusUnknown
     */
    public static function make(string $status, string $source, string $destination = null)
    {
        switch ($status) {
            case DiffParser::ADDED:
                return new Add($source);
            case DiffParser::MODIFIED:
                return new Modify($source);
            case DiffParser::DELETED:
                return new Delete($source);
            case DiffParser::RENAMED:
                return new Rename($source, $destination);
            default:
                throw new DiffEntryStatusUnknown($status, $status . "\t" . $source . "\t" . $destination);
        }
    }
}
