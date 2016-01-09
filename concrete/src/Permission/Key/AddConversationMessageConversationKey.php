<?php
namespace Concrete\Core\Permission\Key;

use Concrete\Core\Permission\Access\ListItem\AddConversationMessageConversationListItem;
use Loader;
use User;
use \Concrete\Core\Permission\Duration as PermissionDuration;

class AddConversationMessageConversationKey extends ConversationKey
{

    public function getMyAssignment()
    {
        $u = new User();
        $asl = new AddConversationMessageConversationListItem();
        if ($u->isSuperUser()) {
            $asl->setNewConversationMessageApprovalStatus('A');
            return $asl;
        }

        $pae = $this->getPermissionAccessObject();
        if (!is_object($pae)) {
            $asl->setNewConversationMessageApprovalStatus('U');
            return $asl;
        }

        $accessEntities = $u->getUserAccessEntityObjects();
        $accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
        $list = $this->getAccessListItems(UserKey::ACCESS_TYPE_ALL, $accessEntities);
        $list = PermissionDuration::filterByActive($list);

        foreach ($list as $l) {
            if ($l->getNewConversationMessageApprovalStatus() == 'U') {
                $asl->setNewConversationMessageApprovalStatus('U');
            }

            if ($l->getNewConversationMessageApprovalStatus() == 'A') {
                $asl->setNewConversationMessageApprovalStatus('A');
            }
        }

        return $asl;
    }


}
