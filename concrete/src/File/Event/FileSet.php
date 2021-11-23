<?php
namespace Concrete\Core\File\Event;

use Concrete\Core\File\Set\Set;

class FileSet
{
    protected $fs;

    public function __construct(Set $fs)
    {
        $this->fs = $fs;
    }

    public function getFileSetObject()
    {
        return $this->fs;
    }
}
