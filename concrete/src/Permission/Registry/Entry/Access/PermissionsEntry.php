<?php
namespace Concrete\Core\Permission\Registry\Entry\Access;

use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;

class PermissionsEntry implements EntryInterface
{

    protected $accessEntry;
    protected $permissions;

    public function __construct(EntityInterface $accessEntry, $permissions)
    {
        $this->accessEntry = $accessEntry;
        $this->permissions = $permissions;
    }

    public function apply($mixed)
    {
        $entity = $this->accessEntry->getAccessEntity();
        if (is_object($entity)) {
            $mixed->getPermissionObject()->assignPermissions($entity, $this->permissions);
        }
    }


}

