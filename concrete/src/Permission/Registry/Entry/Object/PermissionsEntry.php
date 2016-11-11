<?php
namespace Concrete\Core\Permission\Registry\Entry\Object;

use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;

class PermissionsEntry implements EntryInterface
{

    protected $object;
    protected $permissions;

    public function __construct(ObjectInterface $object, $permissions)
    {
        $this->object = $object;
        $this->permissions = $permissions;
    }

    public function apply($mixed)
    {
        $this->object->getPermissionObject()->assignPermissions($mixed->getAccessEntity(), $this->permissions);
    }


}

