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

    public function contains($file)
    {
        if ($this->filesystem->exists(
            $this->getPath() . DIRECTORY_SEPARATOR . $file
        )) {
            $record = new Record($this->filesystem);
            $record->setFile($this->getPath() . DIRECTORY_SEPARATOR . $file);
            $record->setUrl($this->getURL() . '/' . $file);
            $record->setIsOverride($this->isOverride());
            $record->setExists(true);
            $record->setPackageHandle($this->getPackageHandle());
            return $record;
        }
    }

}
