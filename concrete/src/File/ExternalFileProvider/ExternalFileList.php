<?php

namespace Concrete\Core\File\ExternalFileProvider;

use JsonSerializable;

class ExternalFileList implements JsonSerializable
{
    protected $files;

    /**
     * @param ExternalFileEntry $fileEntry
     */
    public function addFile($fileEntry)
    {
        $this->files[] = $fileEntry;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function jsonSerialize()
    {
        return $this->getFiles();
    }
}