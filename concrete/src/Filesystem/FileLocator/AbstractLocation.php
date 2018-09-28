<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Illuminate\Filesystem\Filesystem;

abstract class AbstractLocation implements LocationInterface
{

    protected $filesystem;

    abstract public function getPath();
    abstract public function getURL();

    public function getPackageHandle()
    {
        return null;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function isOverride()
    {
        return false;
    }

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getRecord($file)
    {
        $record = new Record($this->filesystem);
        $record->setFile($this->getPath() . '/' . $file);
        $record->setUrl($this->getURL() . '/' . $file);
        $record->setIsOverride($this->isOverride());
        $record->setPackageHandle($this->getPackageHandle());
        return $record;
    }

    public function contains($file)
    {
        if ($this->filesystem->exists(
            $this->getPath() . '/' . $file
        )) {
            $record = $this->getRecord($file);
            $record->setExists(true);
            return $record;
        }
    }

}
