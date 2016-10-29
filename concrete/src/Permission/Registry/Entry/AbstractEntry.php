<?php
namespace Concrete\Core\Permission\Registry\Entry;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\AssignableObjectInterface;

abstract class AbstractEntry implements EntryInterface
{

    protected $permissions = [];
    protected $entity;

    /**
     * @return array
     */
    public function getPermissionKeyHandles()
    {
        return $this->permissions;
    }

    /**
     * @param array $permissions
     */
    public function setPermissonKeyHandles($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return mixed
     */
    public function getAccessEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setAccessEntity($entity)
    {
        $this->entity = $entity;
    }


}
