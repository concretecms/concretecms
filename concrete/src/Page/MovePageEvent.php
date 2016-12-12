<?php
namespace Concrete\Core\Page;

class MovePageEvent extends Event
{
    protected $oldParent;
    protected $newParent;

    public function setNewParentPageObject($newParent)
    {
        $this->newParent = $newParent;
    }

    public function setOldParentPageObject($oldParent)
    {
        $this->oldParent = $oldParent;
    }

    public function getNewParentPageObject()
    {
        return $this->newParent;
    }

    public function getOldParentPageObject()
    {
        return $this->oldParent;
    }
}
