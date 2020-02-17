<?php

namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Package\PackageService;

abstract class AbstractRoutine implements RoutineInterface
{
    /**
     * Get a package entity given its handle.
     *
     * @param string|\SimpleXMLElement $pkgHandle the package handle (or an XML attribute/node whose content is the package handle)
     *
     * @return \Concrete\Core\Entity\Package|null
     */
    protected static function getPackageObject($pkgHandle)
    {
        $pkg = null;
        if ($pkgHandle) {
            $pkgHandle = (string) $pkgHandle;
            if ($pkgHandle !== '') {
                $pkg = app(PackageService::class)->getByHandle($pkgHandle);
            }
        }

        return $pkg;
    }
}
