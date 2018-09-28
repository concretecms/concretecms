<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Illuminate\Filesystem\Filesystem;

class CoreLocation implements LocationInterface
{

    protected $filesystem;

    public function getCacheKey()
    {
        return 'core';
    }

    /**
     * @param mixed $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }


    public function contains($file)
    {
        $record = new Record($this->filesystem);
        $record->setFile(DIR_BASE_CORE . '/' . $file);
        $record->setUrl(ASSETS_URL . '/' . $file);
        return $record;
    }

}
