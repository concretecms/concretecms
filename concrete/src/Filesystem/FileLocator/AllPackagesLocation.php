<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Concrete\Core\Package\PackageList;
use Illuminate\Filesystem\Filesystem;

class AllPackagesLocation implements LocationInterface
{

    protected $filesystem;
    protected $packageList;

    public function getCacheKey()
    {
        return 'all_packages';
    }

    public function __construct(PackageList $packageList)
    {
        $this->packageList = $packageList;
    }

    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function contains($file)
    {
        foreach($this->packageList->getPackages() as $pkg) {
            $location = new PackageLocation($pkg->getPackageHandle());
            $location->setFilesystem($this->filesystem);
            $record = $location->contains($file);
            if ($record->exists()) {
                return $record;
            }
        }
    }

}
