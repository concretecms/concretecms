<?php
namespace Concrete\Core\Filesystem\FileLocator;

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

    public function __construct($pkgHandle, $checkExists = false)
    {
        $this->pkgHandle = $pkgHandle;
        $this->checkExists = $checkExists;
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