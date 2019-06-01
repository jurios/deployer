<?php


namespace Kodilab\Deployer\Vendor;


use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Deployer;
use Kodilab\Deployer\Project;

class VendorDiff
{
    /** @var string */
    protected $project_path;

    /** @var Vendor */
    protected $local_vendor;

    /** @var Vendor */
    protected $production_vendor;

    /** @var array */
    protected $changes;

    /** @var Project */
    protected $project;

    public function __construct(string $project_path, Project $project)
    {
        $this->project_path = $project_path;
        $this->project = $project;

        $this->local_vendor = new Vendor(
            $this->project_path . DIRECTORY_SEPARATOR . 'composer.json',
            $this->project_path . DIRECTORY_SEPARATOR . 'composer.lock'
        );

        $this->production_vendor = new Vendor(
            $this->project_path . DIRECTORY_SEPARATOR . Deployer::COMPOSER_JSON_PRODUCTION_FILENAME,
            $this->project_path . DIRECTORY_SEPARATOR . Deployer::COMPOSER_LOCK_PRODUCTION_FILENAME
        );
    }

    /**
     * Compare dependencies in production and in local in order to get the changes needed for deployment
     *
     * @param Vendor $production
     * @return array
     */
    public function diff()
    {

        $this->changes = [];

        /** @var VendorPackage $package */
        foreach ($this->local_vendor->dependencies() as $package) {

            /** @var VendorPackage $production_package */
            $production_package = $this->production_vendor->findDependency($package);

            if (is_null($production_package)) {
                $this->addPackage($package);
            }

            if (!is_null($production_package) && $package->getReference() !== $production_package->getReference()) {
                $this->updatePackage($package);
            }
        }

        //Now we look for removed packages. Removed packages must be present in production Vendor but not present in
        //local Vendor:
        foreach ($this->production_vendor->dependencies() as $package) {
            $local_package = $this->local_vendor->findDependency($package);

            if (is_null($local_package)) {
                $this->deletePackage($package);
            }
        }

        //TODO: If changes isn't empty, then 'bin' folder and autoload folder should be added

        return $this->changes;
    }

    private function addPackage(VendorPackage $package)
    {
        //Metapackages contains no files and will not write anything to the filesystem
        if ($package->isMetapackage()) {
            return;
        }

        $files = $this->project->files(
            'vendor' . DIRECTORY_SEPARATOR . $package->getScope() . DIRECTORY_SEPARATOR . $package->getName()
        );

        foreach ($files as $file)
        {
            $this->changes[] = new Add($file);
        }
    }

    private function updatePackage(VendorPackage $package)
    {
        //Metapackages contains no files and will not write anything to the filesystem
        if ($package->isMetapackage()) {
            return;
        }

        $files = $this->project->files(
            'vendor' . DIRECTORY_SEPARATOR . $package->getScope() . DIRECTORY_SEPARATOR . $package->getName()
        );

        foreach ($files as $file)
        {
            $this->changes[] = new Modify($file);
        }
    }

    private function deletePackage(VendorPackage $package)
    {
        //Metapackages contains no files and will not write anything to the filesystem
        if ($package->isMetapackage()) {
            return;
        }

        $files = $this->project->files(
            'vendor' . DIRECTORY_SEPARATOR . $package->getScope() . DIRECTORY_SEPARATOR . $package->getName()
        );

        foreach ($files as $file)
        {
            $this->changes[] = new Delete($file);
        }
    }
}