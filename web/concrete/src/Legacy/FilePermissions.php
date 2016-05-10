<?php
namespace Concrete\Core\Legacy;

use Concrete\Core\File\Filesystem;
use FileSet;
use Permissions;

/**
 * @deprecated
 */
final class FilePermissions
{
    public static function getGlobal()
    {
        $filesystem = new Filesystem();
        $fsp = new Permissions($filesystem->getRootFolder());
        return $fsp;
    }
}
