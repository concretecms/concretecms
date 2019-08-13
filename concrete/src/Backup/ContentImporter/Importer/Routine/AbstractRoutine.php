<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

/**
 * @since 8.0.0
 */
abstract class AbstractRoutine implements RoutineInterface
{

    protected static function getPackageObject($pkgHandle)
    {
        $pkg = null;
        if ($pkgHandle) {
            $pkgHandle = (string) $pkgHandle;
            $pkg = \Package::getByHandle($pkgHandle);
        }

        return $pkg;
    }


}
