<?php
namespace Concrete\Core\Workflow\Progress;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Workflow\HistoryEntry\HistoryEntry;
use Concrete\Core\Workflow\Request\Request;
use Loader;
use UserInfo;

class History extends ConcreteObject
{
    public function getWorkflowProgressHistoryTimestamp()
    {
        return $this->timestamp;
    }

    public function getWorkflowProgressHistoryID()
    {
        return $this->wphID;
    }

    public function getWorkflowProgressID()
    {
        return $this->wpID;
    }

    public function getWorkflowProgressHistoryInnerObject()
    {
        return $this->object;
    }

    public function getWorkflowProgressHistoryDescription()
    {
        if ($this->object instanceof Request) {
            $d = $this->object->getWorkflowRequestDescriptionObject();
            $ui = UserInfo::getByID($this->object->getRequesterUserID());
            if (is_object($ui)) {
                $userName = $ui->getUserName();
            } else {
                $userName = t('(Deleted User)');
            }

            return $d->getDescription() . ' ' . t('Originally requested by %s.', $userName);
        }
        if ($this->object instanceof HistoryEntry) {
            $d = $this->object->getWorkflowProgressHistoryDescription();

            return $d;
        }
    }

    public static function getLatest(Progress $wp)
    {
        $db = Loader::db();
        $wphID = $db->GetOne('select wphID from WorkflowProgressHistory where wpID = ? order by timestamp desc', [$wp->getWorkflowProgressID()]);
        if ($wphID) {
            return $wp->getWorkflowProgressHistoryObjectByID($wphID);
        }
    }

    public static function getList(Progress $wp)
    {
        $db = Loader::db();
        $r = $db->Execute('select wphID from WorkflowProgressHistory where wpID = ? order by timestamp desc', [$wp->getWorkflowProgressID()]);
        $list = [];
        while ($row = $r->FetchRow()) {
            $obj = $wp->getWorkflowProgressHistoryObjectByID($row['wphID']);
            if (is_object($obj)) {
                $list[] = $obj;
            }
        }

        return $list;
    }
}
