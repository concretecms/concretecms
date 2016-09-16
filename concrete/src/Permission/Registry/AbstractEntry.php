<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Access\Entity\Entity;

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
