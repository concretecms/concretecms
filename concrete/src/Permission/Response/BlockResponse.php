<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Key\Key;
use Group;

class BlockResponse extends Response
{
    // legacy support
    public function canRead()
    {
        return $this->validate('view_block');
    }
    public function canWrite()
    {
        return $this->validate('edit_block');
    }
    public function canDeleteBlock()
    {
        return $this->validate('delete_block');
    }
    public function canAdmin()
    {
        return $this->validate('edit_block_permissions');
    }
    public function canAdminBlock()
    {
        return $this->validate('edit_block_permissions');
    }

    public function validate($permissionHandle, $args = array())
    {
        $page = $this->object->getBlockCollectionObject();
        if ($page->isMasterCollection()) {
            $key = Key::getByHandle('access_page_defaults');
            return $key->validate();
        } else {
            return parent::validate($permissionHandle, $args);
        }
    }

    public function canViewEditInterface()
    {
        return $this->canEditBlock() ||
            $this->canEditBlockCustomTemplate() ||
            $this->canDeleteBlock() ||
            $this->canEditBlockDesign() ||
            $this->canEditBlockPermissions() ||
            $this->canScheduleGuestAccess()
        ;
    }

    public function canGuestsViewThisBlock()
    {
        $pk = Key::getByHandle('view_block');
        $pk->setPermissionObject($this->getPermissionObject());
        $gg = GroupPermissionAccessEntity::getOrCreate(Group::getByID(GUEST_GROUP_ID));
        $accessEntities = array($gg);
        $valid = false;
        $list = $pk->getAccessListItems(Key::ACCESS_TYPE_ALL, $accessEntities);
        foreach ($list as $l) {
            if ($l->getAccessType() == Key::ACCESS_TYPE_INCLUDE) {
                $valid = true;
            }
            if ($l->getAccessType() == Key::ACCESS_TYPE_EXCLUDE) {
                $valid = false;
            }
        }

        return $valid;
    }
}
