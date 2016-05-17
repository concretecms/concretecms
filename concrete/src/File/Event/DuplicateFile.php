<?php
namespace Concrete\Core\File\Event;

class DuplicateFile extends File
{
    protected $newFile;

    public function setNewFileObject($newFile)
    {
        $this->newFile = $newFile;
    }

    public function getNewFileObject()
    {
        return $this->newFile;
    }
}
