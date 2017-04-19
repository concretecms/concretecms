<?php
namespace Concrete\Core\Filesystem;

use Concrete\Core\Filesystem\FileLocator\Record;

interface LocatableFileInterface
{
    /**
     * @param $file
     * @return Record
     */
    function getFileLocatorRecord();


}



