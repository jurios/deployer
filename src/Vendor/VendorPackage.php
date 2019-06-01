<?php


namespace Kodilab\Deployer\Vendor;


class VendorPackage
{
    const TYPE_LIBRARY = 0;
    const TYPE_PROJECT = 1;
    const TYPE_METAPACKAGE = 2;
    const TYPE_COMPOSER_PLUGIN = 3;

    /** @var string */
    protected $full_name;

    /** @var string */
    protected $scope;

    /** @var string */
    protected $name;

    /** @var string */
    protected $reference;

    /** @var integer */
    protected $type;

    /**
     * VendorPackage array of dependencies
     * @var array
     */
    protected $require;

    /**
     * VendorPackage array of dev-dependencies
     * @var array
     */
    protected $requireDev;

    public function __construct(string $full_name, string $reference, string $type, array $require = [], array $requireDev = [])
    {
        $this->full_name = $full_name;
        $this->scope = explode('/', $full_name)[0];
        $this->name = explode('/', $full_name)[1];
        $this->reference = $reference;
        $this->type = $this->parseType($type);
        $this->require = [];
        $this->requireDev = [];

        foreach ($require as $dependency => $version) {
            $this->require[] = $dependency;
        }

        // This is not being used
        foreach ($requireDev as $dependency => $version) {
            $this->requireDev[] = $dependency;
        }
    }

    /**
     * Returns the full name of the package ( scope/name )
     *
     * @return mixed
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Returns the scope of the package
     *
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Returns the name of the package
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the package source reference
     *
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Returns require dependencies
     *
     * @return array
     */
    public function getRequire()
    {
        return $this->require;
    }

    /**
     * Return the require-dev dependencies
     *
     * @return array
     */
    public function getRequireDev()
    {
        return $this->requireDev;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isMetapackage()
    {
        return $this->type === self::TYPE_METAPACKAGE;
    }

    private function parseType(string $type)
    {
        if ($type === 'library') {
            return self::TYPE_LIBRARY;
        }

        if ($type === 'project') {
            return self::TYPE_PROJECT;
        }

        if ($type === 'metapackage') {
            return self::TYPE_METAPACKAGE;
        }

        if ($type === 'composer-plugin') {
            return self::TYPE_COMPOSER_PLUGIN;
        }
    }
}