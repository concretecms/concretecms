<?php
namespace Concrete\Core\File\Event;

use Concrete\Core\File\File as ConcreteFile;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;

class File extends AbstractEvent
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
