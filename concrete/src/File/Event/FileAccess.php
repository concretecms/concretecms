<?php
namespace Concrete\Core\File\Event;

class FileAccess extends FileVersion
{
    protected $u;

    public function setUserObject($u)
    {
        $this->u = $u;
    }

    public function getUserObject()
    {
        return $this->u;
    }
}
