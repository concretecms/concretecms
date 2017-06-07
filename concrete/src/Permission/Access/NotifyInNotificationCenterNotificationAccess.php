<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use Database;

class NotifyInNotificationCenterNotificationAccess extends NotificationAccess
{
    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Database::connection();
        $r = $db->executeQuery('select * from NotificationPermissionSubscriptionList where paID = ?', [$this->getPermissionAccessID()]);
        while ($row = $r->FetchRow()) {
            $v = [$row['peID'], $newPA->getPermissionAccessID(), $row['permission']];
            $db->executeQuery('insert into NotificationPermissionSubscriptionList (peID, paID, permission) values (?, ?, ?)', $v);
        }
        $r = $db->executeQuery('select * from NotificationPermissionSubscriptionListCustom where paID = ?', [$this->getPermissionAccessID()]);
        while ($row = $r->FetchRow()) {
            $v = [$row['peID'], $newPA->getPermissionAccessID(), $row['nSubscriptionIdentifier']];
            $db->executeQuery('insert into NotificationPermissionSubscriptionListCustom  (peID, paID, nSubscriptionIdentifier) values (?, ?, ?)', $v);
        }

        return $newPA;
    }

    public function save($args = [])
    {
        parent::save();
        $db = Database::connection();
        $db->executeQuery('delete from NotificationPermissionSubscriptionList where paID = ?', [$this->getPermissionAccessID()]);
        $db->executeQuery('delete from NotificationPermissionSubscriptionListCustom where paID = ?', [$this->getPermissionAccessID()]);
        if (is_array($args['subscriptionsIncluded'])) {
            foreach ($args['subscriptionsIncluded'] as $peID => $permission) {
                $v = [$this->getPermissionAccessID(), $peID, $permission];
                $db->executeQuery('insert into NotificationPermissionSubscriptionList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (is_array($args['subscriptionsExcluded'])) {
            foreach ($args['subscriptionsExcluded'] as $peID => $permission) {
                $v = [$this->getPermissionAccessID(), $peID, $permission];
                $db->executeQuery('insert into NotificationPermissionSubscriptionList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (is_array($args['subscriptionIdentifierInclude'])) {
            foreach ($args['subscriptionIdentifierInclude'] as $peID => $pThemeIDs) {
                foreach ($pThemeIDs as $pThemeID) {
                    $v = [$this->getPermissionAccessID(), $peID, $pThemeID];
                    $db->executeQuery('insert into NotificationPermissionSubscriptionListCustom (paID, peID, nSubscriptionIdentifier) values (?, ?, ?)', $v);
                }
            }
        }

        if (is_array($args['subscriptionIdentifierExclude'])) {
            foreach ($args['subscriptionIdentifierExclude'] as $peID => $pThemeIDs) {
                foreach ($pThemeIDs as $pThemeID) {
                    $v = [$this->getPermissionAccessID(), $peID, $pThemeID];
                    $db->executeQuery('insert into NotificationPermissionSubscriptionListCustom (paID, peID, nSubscriptionIdentifier) values (?, ?, ?)', $v);
                }
            }
        }
    }

    public function getAccessListItems($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = [], $checkCache = true)
    {
        $db = Database::connection();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        $list = PermissionDuration::filterByActive($list);
        foreach ($list as $l) {
            /**
             * @var \Concrete\Core\Permission\Access\ListItem\NotifyInNotificationCenterNotificationListItem
             */
            $pe = $l->getAccessEntityObject();
            $prow = $db->fetchAssoc('select permission from NotificationPermissionSubscriptionList where peID = ? and paID = ?', [$pe->getAccessEntityID(), $l->getPermissionAccessID()]);
            if (is_array($prow) && $prow['permission']) {
                $l->setSubscriptionsAllowedPermission($prow['permission']);
                $permission = $prow['permission'];
                if ($permission == 'C') {
                    $subscriptions = $db->GetCol('select nSubscriptionIdentifier from NotificationPermissionSubscriptionListCustom where peID = ? and paID = ?', [$pe->getAccessEntityID(), $l->getPermissionAccessID()]);
                    $l->setSubscriptionsAllowedArray($subscriptions);
                }
            } elseif ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_INCLUDE) {
                $l->setSubscriptionsAllowedPermission('A');
            } else {
                $l->setSubscriptionsAllowedPermission('N');
            }
        }

        return $list;
    }
}
