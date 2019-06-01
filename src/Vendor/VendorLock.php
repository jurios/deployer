<?php


namespace Kodilab\Deployer\Vendor;


class VendorLock
{
    /**
     * Composer.lock path
     * @var string
     */
    protected $path;

    /**
     * Composer.lock JSON content
     * @var array
     */
    protected $content;

    /**
     * Composer.lock packages
     * @var array
     */
    protected $packages;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->packages = [];

        if(file_exists($this->path)) {
            $this->content = json_decode(file_get_contents($this->path), true);
        }

        $this->parsePackages();
    }

    /**
     * Look for the package. If it does not exist, then null is returned
     *
     * @param string $fullName
     * @return VendorPackage|mixed|null
     */
    public function find(string $fullName)
    {
        /** @var VendorPackage $package */
        foreach ($this->packages as $package) {
            if ($package->getFullName() === $fullName) {
                return $package;
            }
        }

        return null;
    }

    private function parsePackages()
    {
        if (isset($this->content['packages'])) {
            foreach ($this->content['packages'] as $package) {
                $fullName = $package['name'];
                $reference = $package['dist']['reference'];
                $require = isset($package['require']) ? $package['require'] : [];
                $requireDev = isset($package['require-dev']) ? $package['require-dev'] : [];
                $type = $package['type'];

                $this->packages[] = new VendorPackage($fullName, $reference, $type, $require, $requireDev);
            }
        }
    }
}