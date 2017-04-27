<?php
namespace Concrete\Core\Filesystem;

class TemplateLocation
{

    protected $location;
    protected $pkgHandle;

    /**
     * TemplateLocation constructor.
     * @param $location
     * @param $pkgHandle
     */
    public function __construct($location, $pkgHandle = null)
    {
        $this->setLocation($location);
        $this->pkgHandle = $pkgHandle;
    }


    /**
     * @return mixed
     */
    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * @param mixed $pkgHandle
     */
    public function setPackageHandle($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }


    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $templateHandle
     */
    public function setLocation($location)
    {
        $this->location = trim($location, DIRECTORY_SEPARATOR);
    }



}
