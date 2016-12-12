<?php
namespace Concrete\Core\File\Event;

class DeleteFile extends File
{
    protected $proceed = true;

    public function cancelDelete()
    {
        $this->proceed = false;
    }

    public function proceed()
    {
        return $this->proceed;
    }
}
