<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Permission\Access\Entity\GroupEntity;

class Group extends AccessEntity
{

    public function deliverEntity()
    {
        $group = \Concrete\Core\User\Group\Group::getByID($this->request->query->get('gID'));
        if ($group) {
            return GroupEntity::getOrCreate($group);
        }
    }
}
