<?php
namespace Concrete\Core\File\Event;

use Concrete\Core\File\Version;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;

class FileVersion extends AbstractEvent
{

    protected $fv;

    public function __construct(Version $fv)
    {
        $this->fv = $fv;
    }

    public function getFileVersionObject()
    {
        return $this->fv;
    }

}
