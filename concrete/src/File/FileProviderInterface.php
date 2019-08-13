<?php
namespace Concrete\Core\File;

/**
 * @since 8.0.0
 */
interface FileProviderInterface
{
    /**
     * @return File[]
     */
    function getFileObjects();

}