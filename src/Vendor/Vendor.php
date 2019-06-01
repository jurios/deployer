<?php


namespace Kodilab\Deployer\Vendor;


class Vendor
{
    /** @var string */
    protected $json_path;

    /** @var string */
    protected $lock_path;

    /** @var array */
    protected $content;

    /** @var VendorLock */
    protected $lock;

    /** @var array */
    protected $dependencies;

    public function __construct(string $json_path, string $lock_path)
    {
        $this->json_path = $json_path;
        $this->lock_path = $lock_path;

        $this->content = null;
        $this->dependencies = [];
        $this->changes = [];

        $this->content = null;

        if (file_exists($this->json_path)) {
            $this->content = json_decode(file_get_contents($this->json_path), true);
        }

        $this->lock = new VendorLock($this->lock_path);

        $this->getRequiredDependencies();
    }

    /**
     * Returns dependencies list
     *
     * @return array
     */
    public function dependencies()
    {
        return $this->dependencies;
    }

    /**
     * Return an specific dependency. Null is returned if the dependency does not exist
     *
     * @param VendorPackage $dependency
     * @return VendorPackage|null
     */
    public function findDependency(VendorPackage $dependency)
    {
        /** @var VendorPackage $own_package */
        foreach ($this->dependencies as $own_package) {
            if ($own_package->getScope() === $dependency->getScope() && $own_package->getName() === $dependency->getName()) {
                return $own_package;
            }
        }
        return null;
    }

    /**
     * Recursively get the require dependencies in $this->dependencies. require-dev will be ignored
     */
    public function getRequiredDependencies()
    {
        if (is_null($this->content)) {
            // No composer content. No dependencies
            return;
        }

        // We only look at require packages (require-dev is only for dev)
        if (isset($this->content['require'])) {
            foreach ($this->content['require'] as $dependency => $version) {
                if ($this->isAPackage($dependency)) {
                    $this->addPackage($dependency);
                }
            }
        }
    }

    /**
     * Add a dependency to the $this->dependencies list and its dependencies. require-dev is ignored
     *
     * @param $dependency
     */
    private function addPackage($dependency)
    {
        //Check if $dependency is already listed in order to avoid a loop
        if ($this->isPackageAdded($dependency)) {
            return;
        }

        /** @var VendorPackage $lockPackage */
        $lockPackage = $this->lock->find($dependency);

        if (!is_null($lockPackage)) {
            $this->dependencies[] = $lockPackage;
            foreach ($lockPackage->getRequire() as $require) {
                if ($this->isAPackage($require)) {
                    $this->addPackage($require);
                }
            }
        }
    }

    /**
     * Check if package $fullName is already present in $this->dependencies
     * @param $fullName
     * @return bool
     */
    private function isPackageAdded(string $fullName)
    {
        /** @var VendorPackage $dependency */
        foreach ($this->dependencies as $dependency) {
            if ($dependency->getFullName() === $fullName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the package is not php & extensions
     * @param $fullName
     * @return bool
     */
    private function isAPackage($fullName)
    {
        return count(explode('/', $fullName)) === 2;
    }


}