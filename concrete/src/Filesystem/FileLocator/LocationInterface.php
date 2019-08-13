<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Illuminate\Filesystem\Filesystem;

/**
 * @since 8.2.0
 */
interface LocationInterface
{

    function getCacheKey();

    function setFilesystem(Filesystem $filesystem);

    /**
     * @param $file
     * @return Record|false
     */
    function contains($file);






}
