<?php
namespace Concrete\Core\Permission\Registry\Entry\Object;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;

class TaskPermissionsEntry implements EntryInterface
{

    protected $pkHandle;
    protected $accessType;

    public function __construct($pkHandle, $accessType = Key::ACCESS_TYPE_INCLUDE)
    {
        $this->pkHandle = $pkHandle;
        $this->accessType = $accessType;
    }

    public function apply($mixed)
    {
        $key = Key::getByHandle($this->pkHandle);
        $entity = $mixed->getAccessEntity();
        $pa = $key->getPermissionAccessObject();
        if (!is_object($pa)) {
            $pa = Access::create($key);
        }
        $pa->addListItem($entity, false, $this->accessType);
        $pt = $key->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);
    }

    public function remove($mixed)
    {
        $key = Key::getByHandle($this->pkHandle);
        $entity = $mixed->getAccessEntity();
        $pa = $key->getPermissionAccessObject();
        if (is_object($pa)) {
            $listItems = $pa->getAccessListItems($this->accessType);
            foreach($listItems as $item) {
                /**
                 * @var $item ListItem
                 */
                $entity = $item->getAccessEntityObject();
                if ($entity == $mixed->getAccessEntity()) {
                    $pa->removeListItem($entity);
                }
            }
        }
    }


}