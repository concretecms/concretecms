<?php
namespace Concrete\Core\Filesystem;

use Concrete\Core\Filesystem\FileLocator\Record;

/**
 * @since 8.2.0
 */
interface LocatableFileInterface
{
    /**
     * @param $file
     * @return Record
     */
    function getFileLocatorRecord();


}



