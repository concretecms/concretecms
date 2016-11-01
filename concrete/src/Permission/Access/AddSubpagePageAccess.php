<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Duration as PermissionDuration;
use Database;
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;

class AddSubpagePageAccess extends PageAccess
{
    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Database::connection();
        $r = $db->executeQuery('select * from PagePermissionPageTypeAccessList where paID = ?', [$this->getPermissionAccessID()]);
        while ($row = $r->FetchRow()) {
            $v = [$row['peID'], $newPA->getPermissionAccessID(), $row['permission'], $row['externalLink']];
            $db->executeQuery('insert into PagePermissionPageTypeAccessList (peID, paID, permission, externalLink) values (?, ?, ?, ?)', $v);
        }
        $r = $db->executeQuery('select * from PagePermissionPageTypeAccessListCustom where paID = ?', [$this->getPermissionAccessID()]);
        while ($row = $r->FetchRow()) {
            $v = [$row['peID'], $newPA->getPermissionAccessID(), $row['ptID']];
            $db->executeQuery('insert into PagePermissionPageTypeAccessListCustom  (peID, paID, ptID) values (?, ?, ?)', $v);
        }

        return $newPA;
    }

    public function removeListItem(PermissionAccessEntity $pe)
    {
        parent::removeListItem($pe);
        $db = Database::connection();
        $db->executeQuery('delete from PagePermissionPageTypeAccessList where peID = ? and paID = ?', [$pe->getAccessEntityID(), $this->getPermissionAccessID()]);
        $db->executeQuery('delete from PagePermissionPageTypeAccessListCustom where peID = ? and paID = ?', [$pe->getAccessEntityID(), $this->getPermissionAccessID()]);
    }

    public function save($args = [])
    {
        parent::save();
        $db = Database::connection();
        $db->executeQuery('delete from PagePermissionPageTypeAccessList where paID = ?', [$this->getPermissionAccessID()]);
        $db->executeQuery('delete from PagePermissionPageTypeAccessListCustom where paID = ?', [$this->getPermissionAccessID()]);
        if (is_array($args['pageTypesIncluded'])) {
            foreach ($args['pageTypesIncluded'] as $peID => $permission) {
                $ext = 0;
                if (!empty($args['allowExternalLinksIncluded'][$peID])) {
                    $ext = $args['allowExternalLinksIncluded'][$peID];
                }
                $v = [$this->getPermissionAccessID(), $peID, $permission, $ext];
                $db->executeQuery('insert into PagePermissionPageTypeAccessList (paID, peID, permission, externalLink) values (?, ?, ?, ?)', $v);
            }
        }

        if (is_array($args['pageTypesExcluded'])) {
            foreach ($args['pageTypesExcluded'] as $peID => $permission) {
                $ext = 0;
                if (!empty($args['allowExternalLinksExcluded'][$peID])) {
                    $ext = $args['allowExternalLinksExcluded'][$peID];
                }
                $v = [$this->getPermissionAccessID(), $peID, $permission, $ext];
                $db->executeQuery('insert into PagePermissionPageTypeAccessList (paID, peID, permission, externalLink) values (?, ?, ?, ?)', $v);
            }
        }

        if (is_array($args['ptIDInclude'])) {
            foreach ($args['ptIDInclude'] as $peID => $ptIDs) {
                foreach ($ptIDs as $ptID) {
                    $v = [$this->getPermissionAccessID(), $peID, $ptID];
                    $db->executeQuery('insert into PagePermissionPageTypeAccessListCustom (paID, peID, ptID) values (?, ?, ?)', $v);
                }
            }
        }

        if (is_array($args['ptIDExclude'])) {
            foreach ($args['ptIDExclude'] as $peID => $ptIDs) {
                foreach ($ptIDs as $ptID) {
                    $v = [$this->getPermissionAccessID(), $peID, $ptID];
                    $db->executeQuery('insert into PagePermissionPageTypeAccessListCustom (paID, peID, ptID) values (?, ?, ?)', $v);
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
            $permission = '';
            $pe = $l->getAccessEntityObject();
            $prow = $db->fetchAssoc('select permission, externalLink from PagePermissionPageTypeAccessList where peID = ? and paID = ?', [$pe->getAccessEntityID(), $l->getPermissionAccessID()]);
            if (is_array($prow) && $prow['permission']) {
                $l->setPageTypesAllowedPermission($prow['permission']);
                $l->setAllowExternalLinks($prow['externalLink']);
                $permission = $prow['permission'];
            } elseif ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_INCLUDE) {
                $l->setPageTypesAllowedPermission('A');
                $l->setAllowExternalLinks(1);
            } else {
                $l->setPageTypesAllowedPermission('N');
                $l->setAllowExternalLinks(0);
            }
            if ($permission == 'C') {
                $ptIDs = $db->GetCol('select ptID from PagePermissionPageTypeAccessListCustom where peID = ? and paID = ?', [$pe->getAccessEntityID(), $l->getPermissionAccessID()]);
                $l->setPageTypesAllowedArray($ptIDs);
            }
        }

        return $list;
    }
}
