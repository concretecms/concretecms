<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Entity\User\User;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Access\Entity\GroupCombinationEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity;
use Concrete\Core\Permission\Key\Key;

trait AssignableObjectTrait
{
    public function assignPermissions(
        $userOrGroup,
        $permissions = [],
        $accessType = Key::ACCESS_TYPE_INCLUDE,
        $cascadeToChildren = true
    ) {
        if (!$cascadeToChildren) {
            $this->setChildPermissionsToOverride();
        }

        $this->setPermissionsToOverride();

        if (is_array($userOrGroup)) {
            $pe = GroupCombinationEntity::getOrCreate($userOrGroup);
            // group combination
        } elseif ($userOrGroup instanceof User || $userOrGroup instanceof \Concrete\Core\User\UserInfo || $userOrGroup instanceof \Concrete\Core\User\User) {
            $pe = UserEntity::getOrCreate($userOrGroup);
        } elseif ($userOrGroup instanceof Entity) {
            $pe = $userOrGroup;
        } else {
            // group;
            $pe = GroupEntity::getOrCreate($userOrGroup);
        }

        foreach ($permissions as $pkHandle) {
            $pk = Key::getByHandle($pkHandle);
            $pk->setPermissionObject($this);
            $pa = $pk->getPermissionAccessObject();
            if (!is_object($pa)) {
                $pa = Access::create($pk);
            } elseif ($pa->isPermissionAccessInUse()) {
                $pa = $pa->duplicate();
            }
            $pa->addListItem($pe, false, $accessType);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }
    }
}