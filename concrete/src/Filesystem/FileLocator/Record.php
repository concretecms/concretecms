<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Illuminate\Filesystem\Filesystem;

class Record
{

    public $file; // should be public for legacy purposes
    public $url; // should be public for legacy purposes
    public $pkgHandle; // should be public for legacy purposes
    public $override; // should be public for legacy purposes
    protected $exists;
    protected $filesystem;

    /**
     * Record constructor.
     * @param $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function exists()
    {
        if (!isset($this->exists)) {
            $this->exists = $this->filesystem->exists($this->file);
        }
        return $this->exists;
    }

    /**
     * @param mixed $exists
     */
    public function setExists($exists)
    {
        $this->exists = $exists;
    }

    /**
     * @return mixed
     */
    public function isOverride()
    {
        return $this->override;
    }

    /**
     * @param mixed $isOverride
     */
    public function setIsOverride($isOverride)
    {
        $this->override = $isOverride;
    }





}
