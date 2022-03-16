<?php
namespace Concrete\Core\File\Event;

use Concrete\Core\File\Set\File as ConcreteFileSetFile;

class FileSetFile
{
    protected $fs;

    public function getFileSetFileObject()
    {
        return $this->fs;
    }

    public function __construct(ConcreteFileSetFile $fs)
    {
        $this->fs = $fs;
    }
}
