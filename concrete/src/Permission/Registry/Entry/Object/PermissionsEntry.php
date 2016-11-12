<?php
namespace Concrete\Core\Permission\Registry\Entry\Object;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;

class PermissionsEntry implements EntryInterface
{

    protected $object;
    protected $permissions;
    protected $accessType;
    protected $cascadeToChildren = true;

    public function __construct(
        ObjectInterface $object,
        $permissions,
        $cascadeToChildren = true,
        $accessType = Key::ACCESS_TYPE_INCLUDE
    ) {
        $this->object = $object;
        $this->permissions = $permissions;
        $this->accessType = $accessType;
        $this->cascadeToChildren = $cascadeToChildren;
    }

    public function apply($mixed)
    {
        $object = $this->object->getPermissionObject();
        $object->assignPermissions(
            $mixed->getAccessEntity(), $this->permissions, $this->accessType, $this->cascadeToChildren
        );
    }


}

