<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Tree\Node\Node;

class ExpressEntryAssignment extends Assignment
{

    protected $inheritedPermissions = [
        'view_express_entry' => 'view_express_entries',
        'edit_express_entry' => 'edit_express_entries',
        'delete_express_entry' => 'delete_express_entries',
    ];

    public function getPermissionAccessObject()
    {
        $entry = $this->permissionObject;
        /**
         * @var Entry $entry
         */
        $entity = $entry->getEntity();
        if (is_object($entity)) {
            $node = Node::getByID($entity->getEntityResultsNodeId());
            if ($node) {
                $pk = Key::getByHandle($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]);
                $pk->setPermissionObject($node);
                $access = $pk->getPermissionAccessObject();
                
                // Now that we have the proper access object, let's reset the permission object back to our entyr
                $access->setPermissionKey($this->pk);
                return $access;
            }
        }
        

    }
}
