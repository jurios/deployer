<?php


namespace Kodilab\Deployer\Changes;



use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Exceptions\ChangeIncoherenceException;
use Kodilab\Deployer\Helpers\Path;
use Kodilab\Deployer\Support\Collection;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangeList
{
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

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->changes = new Collection();
        $this->ignored = new Collection();
    }

    /**
     * @param Change $change
     * @return ChangeList
     * @throws ChangeIncoherenceException
     */
    public function add(Change $change)
    {
        if ($this->shouldBeIgnored($change)) {
            $this->ignored->put($change->getPath(), $change);
            return $this;
        }

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
     * @param SymfonyStyle $output
     */
    public function outputConfirmedList(SymfonyStyle $output)
    {
        $headers = ['Status', 'Files', 'Type'];
        $rows = [];

        /** @var Change $entry */
        foreach ($this->changes as $entry) {
            $status = $entry->getLabeledStatus();
            $color = $entry->getColor();
            $reason = $entry->getReason();
            $source = $entry->getPath();
            $destination = !is_null($entry->getDestination()) ? ' => '. $entry->getDestination() : '';
            $files = $source . $destination;

            $rows[] = [
                '<fg='. $color .'>' . $status . '</>',
                '<fg='. $color .'>' . $files . '</>',
                '<fg='. $color .'>' . $reason . '</>',
            ];
        }

        $output->table($headers, $rows);
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
     * Returns the ignored change list
     *
     * @return Collection
     */
    public function getIgnored()
    {
        return $this->ignored;
    }

    /**
     * Returns whether a source change should be ignored
     *
     * @param Change $change
     * @return bool
     */
    protected function shouldBeIgnored(Change $change)
    {
        return Path::match($this->getIgnoreRules(), $change->getPath());
    }

    /**
     * Returns the ignore rules
     *
     * @return array
     */
    protected function getIgnoreRules()
    {
        return $this->config->get('ignore', []);
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
            if ($item->hasSamePath($change)) {
                throw new ChangeIncoherenceException($item, $change);
            }
        }
    }
}
