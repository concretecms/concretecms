<?php
namespace Concrete\Core\Permission\Registry\Entry\Access;

use Concrete\Core\Permission\Access\ListItem\ListItem;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;

class PermissionsEntry implements EntryInterface
{

    protected $accessEntry;
    protected $permissions;
    protected $accessType;
    protected $cascadeToChildren = true;

    public function __construct(
        EntityInterface $accessEntry,
        $permissions,
        $cascadeToChildren = true,
        $accessType = Key::ACCESS_TYPE_INCLUDE
    ) {
        $this->accessEntry = $accessEntry;
        $this->permissions = $permissions;
        $this->accessType = $accessType;
        $this->cascadeToChildren = $cascadeToChildren;
    }

    public function apply($mixed)
    {
        $entity = $this->accessEntry->getAccessEntity();
        $mixed->getPermissionObject()->assignPermissions($entity, $this->permissions,
            $this->accessType,
            $this->cascadeToChildren
        );
    }

    public function remove($mixed)
    {
        $accessEntity = $this->accessEntry->getAccessEntity();
        foreach($this->permissions as $pkHandle) {
            $pk = Key::getByHandle($pkHandle);
            $pk->setPermissionObject($mixed->getPermissionObject());
            $pa = $pk->getPermissionAccessObject();
            $listItems = $pa->getAccessListItems($this->accessType);
            foreach($listItems as $item) {
                /**
                 * @var $item ListItem
                 */
                $entity = $item->getAccessEntityObject();
                if ($entity == $accessEntity) {
                    $pa->removeListItem($entity);
                }
            }
        }
    }


}

