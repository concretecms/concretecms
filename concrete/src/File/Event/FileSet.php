<?php
namespace Concrete\Core\File\Event;

use Concrete\Core\File\Set\Set;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;

class FileSet extends AbstractEvent
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
