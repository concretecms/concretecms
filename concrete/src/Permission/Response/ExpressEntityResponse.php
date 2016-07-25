<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Tree\Node\Node;
use Permissions;

class ExpressEntityResponse extends Response
{
    protected function getExpressNodePermissions()
    {
        /**
         * @var $entry Entry
         */
        $entity = $this->getPermissionObject();
        if (is_object($entity)) {
            $node = Node::getByID($entity->getEntityResultsNodeId());
            return new Permissions($node);
        }
    }

    public function __call($nm, $arguments)
    {
        $p = $this->getExpressNodePermissions();
        return call_user_func_array(array($p, $nm), $arguments);
    }

}
