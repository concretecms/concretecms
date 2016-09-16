<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\User\Group\Group;

class GroupEntry extends AbstractEntry
{

    public function __construct($groupName, $permissions)
    {
        $group = Group::getByName($groupName);
        $entity = GroupEntity::getOrCreate($group);

        $this->setPermissonKeyHandles($permissions);
        $this->setAccessEntity($entity);
    }

}
