<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Illuminate\Filesystem\Filesystem;

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
