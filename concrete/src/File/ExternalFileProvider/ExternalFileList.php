<?php

namespace Concrete\Core\File\ExternalFileProvider;

use JsonSerializable;

class ExternalFileList implements JsonSerializable
{
    protected $files;
    /** @var @var int */
    protected $totalFiles;

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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getFiles();
    }

    /**
     * @return mixed
     */
    public function getTotalFiles()
    {
        return $this->totalFiles;
    }

    /**
     * @param mixed $totalFiles
     * @return ExternalFileList
     */
    public function setTotalFiles($totalFiles)
    {
        $this->totalFiles = $totalFiles;
        return $this;
    }


}