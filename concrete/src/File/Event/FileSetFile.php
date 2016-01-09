<?php
namespace Concrete\Core\File\Event;

use Concrete\Core\File\Set\File as ConcreteFileSetFile;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;

class FileSetFile extends AbstractEvent
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
