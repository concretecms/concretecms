<?php
namespace Concrete\Core\Filesystem;

use Concrete\Core\Application\Application;
use Concrete\Core\Filesystem\FileLocator\ApplicationLocation;
use Concrete\Core\Filesystem\FileLocator\CoreLocation;
use Concrete\Core\Filesystem\FileLocator\LocationInterface;
use Concrete\Core\Filesystem\FileLocator\PackageLocation;
use Concrete\Core\Filesystem\FileLocator\Record;
use Illuminate\Filesystem\Filesystem;

class FileLocator
{

    protected $filesystem;
    protected $app;
    protected $locations = [];

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __construct(Filesystem $filesystem, Application $app)
    {
        $this->filesystem = $filesystem;
        $this->app = $app;
        $this->cache = $app->make('cache/overrides');
    }

    public function addDefaultLocations()
    {
        array_unshift($this->locations, new ApplicationLocation($this->filesystem));
        $this->locations[] = new CoreLocation($this->filesystem);
    }
    public function addLocation(LocationInterface $location)
    {
        $this->locations[] = $location;
    }

    public function addPackageLocation($pkgHandle)
    {
        $this->locations[] = new PackageLocation($pkgHandle);
    }

    protected function getCacheKey($file)
    {
        $keys = [];
        $file = trim(str_replace('/', DIRECTORY_SEPARATOR, $file), DIRECTORY_SEPARATOR);
        foreach($this->locations as $location) {
            $cacheKey = $location->getCacheKey();
            if (is_array($cacheKey)) {
                $keys = array_merge($keys, $cacheKey);
            } else {
                $keys[] = $cacheKey;
            }
        }
        $keys = array_merge($keys, explode(DIRECTORY_SEPARATOR, $file));
        return 'overrides.' . md5(implode('.', $keys));
    }

    public function getAllRecords($file)
    {
        $this->addDefaultLocations();
        $records = array();
        foreach($this->locations as $location) {
            $location->setFilesystem($this->filesystem);
            if ($record = $location->contains($file)) {
                $records[] = $record;
            }
        }
        return $records;
    }

    /**
     * @param $file
     * @return Record
     */
    public function getRecord($file)
    {
        $this->addDefaultLocations();
        $key = $this->getCacheKey($file);
        $item = $this->cache->getItem($key);
        $record = null;
        if ($item->isMiss()) {
            $item->lock();
            foreach($this->locations as $location) {
                $location->setFilesystem($this->filesystem);
                if ($record = $location->contains($file)) {
                    break;
                }
            }
            if (isset($record)) {
                $this->cache->save($item->set($record));
            }
        } else {
            $record = $item->get();
        }
        return $record;
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }


}



