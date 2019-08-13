<?php
namespace Concrete\Core\Permission\Access\ListItem;

/**
 * @since 8.0.0
 */
class NotifyInNotificationCenterNotificationListItem extends AdminListItem
{
    protected $customSubscriptionsArray = array();
    protected $subscriptionsAllowedPermission = 'N';

    public function setSubscriptionsAllowedPermission($permission)
    {
        $this->subscriptionsAllowedPermission = $permission;
    }
    public function getSubscriptionsAllowedPermission()
    {
        return $this->subscriptionsAllowedPermission;
    }
    public function setSubscriptionsAllowedArray($subscriptions)
    {
        $this->customSubscriptionsArray = $subscriptions;
    }
    public function getSubscriptionsAllowedArray()
    {
        return $this->customSubscriptionsArray;
    }
}
