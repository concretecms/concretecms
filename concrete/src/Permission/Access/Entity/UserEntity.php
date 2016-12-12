<?php
namespace Concrete\Core\Permission\Access\Entity;

use Config;
use Loader;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use URL;
use Concrete\Core\User\UserInfo;

class UserEntity extends Entity
{

    protected $uID;

    public static function getAccessEntitiesForUser($user)
    {
        $entities = array();
        $db = Loader::db();
        if ($user->isRegistered()) {
            // we find the peID for the current user, if one exists. This means that the user has special permissions set just for them.
            $peID = $db->GetOne(
                'SELECT peID FROM PermissionAccessEntityUsers WHERE uID = ?',
                array($user->getUserID()));
            if ($peID > 0) {
                $entity = \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
                if (is_object($entity)) {
                    $entities[] = $entity;
                }
            }
        }

        return $entities;
    }

    public static function getOrCreate(UserInfo $ui)
    {
        $db = Loader::db();
        $petID = $db->GetOne('SELECT petID FROM PermissionAccessEntityTypes WHERE petHandle = \'user\'');
        $peID = $db->GetOne(
            'SELECT pae.peID FROM PermissionAccessEntities pae INNER JOIN PermissionAccessEntityUsers paeg ON pae.peID = paeg.peID WHERE petID = ? AND paeg.uID = ?',
            array($petID, $ui->getUserID()));
        if (!$peID) {
            $db->Execute("INSERT INTO PermissionAccessEntities (petID) VALUES(?)", array($petID));
            $peID = $db->Insert_ID();
            Config::save('concrete.misc.access_entity_updated', time());
            $db->Execute(
                'INSERT INTO PermissionAccessEntityUsers (peID, uID) VALUES (?, ?)',
                array($peID, $ui->getUserID()));
        }

        return \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
    }

    public function getAccessEntityUsers(PermissionAccess $pa)
    {
        return array($this->getUserObject());
    }

    public function getUserObject()
    {
        return \UserInfo::getByID($this->uID);
    }

    public function getAccessEntityTypeLinkHTML()
    {
        $html = '<a href="' . URL::to(
                '/ccm/system/dialogs/user/search') . '" dialog-modal="false" dialog-width="90%" dialog-title="' . t(
                'Add User') . '" class="dialog-launch" dialog-height="70%">' . tc(
                'PermissionAccessEntityTypeName',
                'User') . '</a>';

        return $html;
    }

    public function load()
    {
        $db = Loader::db();
        $uID = $db->GetOne('SELECT uID FROM PermissionAccessEntityUsers WHERE peID = ?', array($this->peID));
        if ($uID) {
            $ui = \UserInfo::getByID($uID);
            if (is_object($ui)) {
                $this->uID = $uID;
                $this->label = $ui->getUserName();
            } else {
                $this->label = t('(Deleted User)');
            }
        }
    }
}
