<?php
namespace Concrete\Core\Permission\Registry\Entry\Access\Entity;

use Concrete\Core\User\Group\Group;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupAccessEntity;

class GroupEntity implements EntityInterface
{

    protected $group;

    public function __construct($group)
    {
        $this->group = $group;
    }

    public function getAccessEntity()
    {
        $group = $this->group;
        if (!is_object($group)) {
            $group = Group::getByName($this->group);
            if (!is_object($group)) {
                $group = Group::getByPath($this->group);
            }
        }
        if (is_object($group)) {
            $entity = GroupAccessEntity::getOrCreate($group);
            return $entity;
        }
    }

}
