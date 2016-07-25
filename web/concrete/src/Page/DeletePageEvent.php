<?php
namespace Concrete\Core\Page;

class DeletePageEvent extends Event
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
