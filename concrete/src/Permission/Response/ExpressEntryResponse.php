<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Tree\Node\Node;
use Permissions;

class ExpressEntryResponse extends Response
{
    protected function getExpressEntryNodePermissions()
    {
        /**
         * @var $entry Entry
         */
        $entry = $this->getPermissionObject();
        if (is_object($entry)) {
            $entity = $entry->getEntity();
            if (is_object($entity)) {
                $node = Node::getByID($entity->getEntityResultsNodeId());
                return new Permissions($node);
            }
        }
    }

    public function canViewExpressEntry()
    {
        $p = $this->getExpressEntryNodePermissions();
        if (is_object($p)) {
            return $p->validate('view_express_entry');
        }
    }

    public function canEditExpressEntry()
    {
        $p = $this->getExpressEntryNodePermissions();
        if (is_object($p)) {
            return $p->validate('edit_express_entry');
        }
    }

    public function canDeleteExpressEntry()
    {
        $p = $this->getExpressEntryNodePermissions();
        if (is_object($p)) {
            return $p->validate('delete_express_entry');
        }
    }



}
