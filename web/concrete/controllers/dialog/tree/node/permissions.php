<?php
namespace Concrete\Controller\Dialog\Tree\Node;

use Concrete\Controller\Dialog\Tree\Node;

class Permissions extends Node
{
    protected $viewPath = '/dialogs/tree/node/permissions';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canEditTreeNodePermissions();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
    }
}
