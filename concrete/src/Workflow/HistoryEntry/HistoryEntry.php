<?php
namespace Concrete\Core\Workflow\HistoryEntry;

abstract class HistoryEntry
{
    abstract public function getWorkflowProgressHistoryDescription();

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setRequesterUserID($uID)
    {
        $this->uID = $uID;
    }

    public function getRequesterUserID()
    {
        return $this->uID;
    }
}
