<?php
namespace Concrete\Core\Workflow\HistoryEntry;

use UserInfo;

class BasicHistoryEntry extends HistoryEntry
{
    public function getWorkflowProgressHistoryDescription()
    {
        $uID = $this->getRequesterUserID();
        $ux = UserInfo::getByID($uID);
        if (is_object($ux)) {
            $userName = $ux->getUserName();
        } else {
            $userName = t('(Deleted User)');
        }
        switch ($this->getAction()) {
            case 'approve':
                $d = t('Approved by %s', $userName);
                break;
            case 'cancel':
                $d = t('Denied by %s', $userName);
                break;
        }

        return $d;
    }
}
