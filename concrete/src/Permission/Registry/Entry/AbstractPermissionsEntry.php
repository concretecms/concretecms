<?php
namespace Concrete\Core\Permission\Registry\Entry;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\AssignableObjectInterface;

abstract class AbstractPermissionsEntry implements EntryInterface
{

    public function apply(AssignableObjectInterface $object)
    {
        $entity = $this->getAccessEntity();
        if (is_object($entity)) {
            $object->assignPermissions($entity, $this->getPermissionKeyHandles());
        }
    }

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
