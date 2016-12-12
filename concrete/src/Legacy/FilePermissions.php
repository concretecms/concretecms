<?php
namespace Concrete\Core\Legacy;

use Concrete\Core\File\Filesystem;
use Concrete\Core\Tree\Node\Type\FileFolder;
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
        $folder = $filesystem->getRootFolder();
        if (!is_object($folder)) {
            // We don't have the root folder yet for some reason (maybe this is
            // the beginning of an upgrade?)
            $folder = new FileFolder();
            // Just pass in a stub one, it shouldn't matter
        }
        $fsp = new Permissions($folder);
        return $fsp;
    }
}
