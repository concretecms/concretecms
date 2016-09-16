<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Access\Entity\Entity;

class Entry extends AbstractEntry
{

    public function __construct(Entity $entity, $permissions)
    {
        $this->permissions = $permissions;
        $this->entity = $entity;

        $this->setAccessEntity($entity);
        $this->setPermissonKeyHandles($permissions);
    }

}
