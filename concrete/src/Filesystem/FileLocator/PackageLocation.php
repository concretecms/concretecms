<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Concrete\Core\Entity\Package;

class PackageLocation extends AbstractLocation
{
    /**
     * @var string
     */
    protected $pkgHandle;

    protected $checkExists;

    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    public function __construct($pkgOrHandle, $checkExists = false)
    {
        if ($pkgOrHandle instanceof Package) {
            $pkgHandle = $pkgOrHandle->getPackageHandle();
        } else {
            $pkgHandle = $pkgOrHandle;
        }
        $this->pkgHandle = $pkgHandle;
        $this->checkExists = $checkExists;
    }

    public function getCacheKey()
    {
        return array('package', $this->pkgHandle);
    }

    public function getPath()
    {
        return DIR_PACKAGES . '/' . $this->pkgHandle;
    }

    public function getURL()
    {
        return REL_DIR_PACKAGES . '/' . $this->pkgHandle;
    }

    public function contains($file)
    {
        if ($this->checkExists) {
            return parent::contains($file);
        } else {
            $record = $this->getRecord($file);
            return $record;
        }
    }

}