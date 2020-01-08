<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Permission\Access\Entity\GroupSetEntity;

class GroupSet extends AccessEntity
{

    public function deliverEntity()
    {
        $set = \Concrete\Core\User\Group\GroupSet::getByID($this->request->query->get('gsID'));
        if ($set) {
            return GroupSetEntity::getOrCreate($set);
        }
    }
}
