<?php


namespace Kodilab\Deployer\Changes;



use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Exceptions\ChangeIncoherenceException;
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
     * Deployer configuration
     *
     * @var Configuration
     */
    protected $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->changes = new Collection();
    }

    /**
     * @param Change $change
     * @return ChangeList
     * @throws ChangeIncoherenceException
     */
    public function add(Change $change)
    {
        $this->validateEntryCoherence($change);

        $this->changes->put($change->getSource(), $change);

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
     * @param Change $change
     * @throws ChangeIncoherenceException
     */
    protected function validateEntryCoherence(Change $change)
    {
        /** @var Change $item */
        foreach ($this->changes as $item)
        {
            if ($item->hasSameSource($change)) {
                throw new ChangeIncoherenceException($item, $change);
            }
        }
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
            $source = $entry->getSource();
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
}
