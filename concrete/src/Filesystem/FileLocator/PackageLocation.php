<?php
namespace Concrete\Core\Filesystem\FileLocator;

class PackageLocation extends AbstractLocation
{
    /**
     * @var string
     */
    protected $pkgHandle;

    public function __construct($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }

    public function getCacheKey()
    {
        return array('package', $this->pkgHandle);
    }

    public function getPath()
    {
        $pkgHandle = $this->pkgHandle;
        return DIR_PACKAGES . DIRECTORY_SEPARATOR . $pkgHandle;
    }

    public function getURL()
    {
        return DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $this->pkgHandle;
    }
}