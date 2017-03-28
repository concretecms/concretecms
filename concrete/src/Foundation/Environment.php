<?php
namespace Concrete\Core\Foundation;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Support\Facade\Facade;
use Config;

/**
 * Deprecated. Use Concrete\Core\Filesystem\FileLocator instead.
 * @deprecated
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Environment
{

    public static function get()
    {
        $app = Facade::getFacadeApplication();
        return $app->make(Environment::class);
    }

    /**
     * @deprecated
     */
    public function clearOverrideCache()
    {
        $app = Facade::getFacadeApplication();
        $cache = $app->make('cache/overrides');
        $cache->flush();
    }


    /**
     * @deprecated
     */
    public function overrideCoreByPackage($segment, $pkgOrHandle)
    {
        $app = Facade::getFacadeApplication();
        $logger = $app->make('log');
        $logger->warn('overrideCoreByPackage no longer functions in 8.2.');
    }

    public function getRecord($segment, $pkgHandle = false)
    {
        $app = Facade::getFacadeApplication();
        $locator = $app->make(FileLocator::class);
        if ($pkgHandle) {
            $locator->addLocation(new FileLocator\PackageLocation($pkgHandle));
        }
        return $locator->getRecord($segment);
    }

    public function getUncachedRecord($segment, $pkgHandle = false)
    {
        return $this->getRecord($segment, $pkgHandle);
    }

    public function getPath($subpath, $pkgIdentifier = false)
    {
        $r = $this->getRecord($subpath, $pkgIdentifier);

        return $r->getFile();
    }

    public function getURL($subpath, $pkgIdentifier = false)
    {
        $r = $this->getRecord($subpath, $pkgIdentifier);

        return $r->getURL();
    }

}
