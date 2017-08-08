<?php
namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\Page\Page;
use Database;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Config;
use UserInfo;
use Concrete\Core\Permission\Access\PageAccess as PagePermissionAccess;
use Concrete\Core\Permission\Access\AreaAccess as AreaPermissionAccess;
use Concrete\Core\Permission\Access\BlockAccess as BlockPermissionAccess;

class PageOwnerEntity extends Entity
{
    public function getAccessEntityUsers(PermissionAccess $pae)
    {
        $c = null;
        if ($pae instanceof PagePermissionAccess) {
            $c = $pae->getPermissionObject();
        } else {
            if ($pae instanceof AreaPermissionAccess) {
                $c = $pae->getPermissionObject()->getAreaCollectionObject();
            } else {
                if ($pae instanceof BlockPermissionAccess) {
                    $a = $pae->getPermissionObject()->getBlockAreaObject();
                    $c = $a->getAreaCollectionObject();
                }
            }
        }
        if (is_object($c) && ($c instanceof Page)) {
            $ui = UserInfo::getByID($c->getCollectionUserID());
            if ($ui) {
                return [$ui];
            }
        }
    }

    public function validate(PermissionAccess $pae)
    {
        $users = $this->getAccessEntityUsers($pae);
        if (empty($users)) {
            return false;
        } else {
            $u = new \User();

            return $users[0]->getUserID() == $u->getUserID();
        }
    }

    public function getAccessEntityTypeLinkHTML()
    {
        $html = '<a href="javascript:void(0)" onclick="ccm_choosePermissionAccessEntityPageOwner()">' . tc('PermissionAccessEntityTypeName',
                'Page Owner') . '</a>';

        return $html;
    }

    public static function getAccessEntitiesForUser($user)
    {
        $entities = array();
        if ($user->isRegistered()) {
            $db = Database::connection();
            $pae = static::getOrCreate();
            $r = $db->fetchColumn('select cID from Pages where uID = ?', array($user->getUserID()));
            if ($r > 0) {
                $entities[] = $pae;
            }
        }

        return $entities;
    }

    public static function getOrCreate()
    {
        $db = Database::connection();
        $petID = $db->fetchColumn('select petID from PermissionAccessEntityTypes where petHandle = \'page_owner\'');
        $peID = $db->fetchColumn('select peID from PermissionAccessEntities where petID = ?', array($petID));
        if (!$peID) {
            $db->executeQuery("insert into PermissionAccessEntities (petID) values(?)", array($petID));
            $peID = $db->lastInsertId();
            Config::save('concrete.misc.access_entity_updated', time());
        }

        return \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
    }

    public function load()
    {
        $this->label = t('Page Owner');
    }
}
