<?php

namespace Concrete\Core\Package\Offline;

/**
 * Represents the package details extracted from a package controller.php file.
 */
class PackageInfo
{
    /**
     * The package directory (normalized, without trailing slashes).
     *
     * @var string
     */
    private $packageDirectory = '';

    /**
     * The package handle.
     *
     * @var string
     */
    private $handle = '';

    /**
     * The package version.
     *
     * @var string
     */
    private $version = '';

    /**
     * The package name, in English.
     *
     * @var string
     */
    private $name = '';

    /**
     * The package description, in English.
     *
     * @var string
     */
    private $description = '';

    /**
     * The minimum concrete5 version.
     *
     * @var string
     */
    private $minimumCoreVersion = '';

    /**
     * Create a new instance of this class.
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Get the package directory (normalized, without trailing slashes).
     *
     * @return string
     */
    public function getPackageDirectory()
    {
        return $this->packageDirectory;
    }

    /**
     * Set the package directory.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setPackageDirectory($value)
    {
        $this->packageDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', (string) $value), '/');

        return $this;
    }

    /**
     * Get the package handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Set the package handle.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setHandle($value)
    {
        $this->handle = (string) $value;

        return $this;
    }

    /**
     * Get the package version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the package version.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setVersion($value)
    {
        $this->version = (string) $value;

        return $this;
    }

    /**
     * Get the package name, in English.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the package name, in English.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setName($value)
    {
        $this->name = (string) $value;

        return $this;
    }

    /**
     * Get the package description, in English.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the package description, in English.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setDescription($value)
    {
        $this->description = (string) $value;

        return $this;
    }

    /**
     * Get the minimum concrete5 version.
     *
     * @return string
     */
    public function getMinimumCoreVersion()
    {
        return $this->minimumCoreVersion;
    }

    /**
     * Get the mayor minimum concrete5 version.
     *
     * @return string
     *
     * @example '5.0'
     * @example '5.6'
     * @example '5.7'
     * @example '8'
     * @example '9'
     */
    public function getMayorMinimumCoreVersion()
    {
        $chunks = array_merge(explode('.', $this->getMinimumCoreVersion()), ['0', '0']);

        return (int) $chunks[0] === 5 ? "{$chunks[0]}.{$chunks[1]}" : $chunks[0];
    }

    /**
     * Set the minimum concrete5 version.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setMinimumCoreVersion($value)
    {
        $this->minimumCoreVersion = $value;

        return $this;
    }
}
