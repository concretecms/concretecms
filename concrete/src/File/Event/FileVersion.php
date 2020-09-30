<?php
namespace Concrete\Core\File\Event;

use Concrete\Core\Entity\File\Version;

class FileVersion
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
