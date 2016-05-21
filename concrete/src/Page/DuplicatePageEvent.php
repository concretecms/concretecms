<?php
namespace Concrete\Core\Page;

class DuplicatePageEvent extends Event
{
    protected $newPage;

    public function setNewPageObject($newPage)
    {
        $this->newPage = $newPage;
    }

    public function getNewPageObject()
    {
        return $this->newPage;
    }
}
