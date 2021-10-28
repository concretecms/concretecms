<?php
namespace Concrete\Core\File\Event;

use Concrete\Core\Entity\File\File as ConcreteFile;

class File
{
    protected $f;

    public function __construct(ConcreteFile $f)
    {
        $this->f = $f;
    }

    public function getFileObject()
    {
        return $this->f;
    }
}
