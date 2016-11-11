<?php
namespace Concrete\Core\Permission\Registry\Entry\Access\Entity;

use Concrete\Core\User\Group\Group;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupAccessEntity;

class GroupEntity implements EntityInterface
{

    protected $groupName;

    public function __construct($groupName)
    {
        $this->groupName = $groupName;
    }

    public function getAccessEntity()
    {
        $group = Group::getByName($this->groupName);
        $entity = GroupAccessEntity::getOrCreate($group);
        return $entity;
    }

}
