<?php
namespace Concrete\Core\Permission\Access\ListItem;

/**
 * @since 5.7.4
 */
class AddConversationMessageConversationListItem extends ListItem
{
    protected $cnvNewMessageApprovalStatus = 'A'; // approved by default. Could also be U

    public function setNewConversationMessageApprovalStatus($cnvNewMessageApprovalStatus)
    {
        $this->cnvNewMessageApprovalStatus = $cnvNewMessageApprovalStatus;
    }

    public function getNewConversationMessageApprovalStatus()
    {
        return $this->cnvNewMessageApprovalStatus;
    }

    public function approveNewConversationMessages()
    {
        return $this->cnvNewMessageApprovalStatus == 'A';
    }
}
