<?php
namespace Concrete\Core\File;

interface FileProviderInterface
{
    /**
     * @return File[]
     */
    function getFileObjects();

}