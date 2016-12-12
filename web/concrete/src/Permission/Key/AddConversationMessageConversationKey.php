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

        $foundUnapproved = false;
        $foundApproved = false;
        foreach ($list as $l) {
            if ($l->getNewConversationMessageApprovalStatus() == 'A') {
                $foundApproved = true;
            }

            if ($l->getNewConversationMessageApprovalStatus() == 'U') {
                $foundUnapproved = true;
            }
        }

        if ($foundApproved) {
            $asl->setNewConversationMessageApprovalStatus('A');
        } else if ($foundUnapproved) {
            $asl->setNewConversationMessageApprovalStatus('U');
        }

        return $asl;
    }


}
