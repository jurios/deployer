<?php


namespace Kodilab\Deployer\Changes\ChangeList\Traits;


use Kodilab\Deployer\Changes\Change;
use Symfony\Component\Console\Style\SymfonyStyle;

trait OutputList
{
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
}