<?php
namespace Concrete\Controller\Dialog\Tree;

use Concrete\Controller\Backend\UserInterface;

abstract class Node extends UserInterface
{
    protected $node;

    /**
     * @return \Concrete\Core\Tree\Node\Node
     */
    protected function getNode()
    {
        if (!isset($this->node)) {
            $this->node = \Concrete\Core\Tree\Node\Node::getByID(\Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
        }
        return $this->node;
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
    }

    public function action()
    {
        $url = call_user_func_array('parent::action', func_get_args());
        if (isset($this->node)) {
            $url .= '&treeNodeID=' . $this->node->getTreeNodeID();
        }
        return $url;
    }


}
