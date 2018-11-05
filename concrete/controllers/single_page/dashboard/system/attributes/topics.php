<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Attributes;

use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Tree;
use Concrete\Core\Utility\Service\Validation\Strings;
use Loader;
use Core;
use Concrete\Core\Tree\Type\Topic as TopicTree;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Permissions;

class Topics extends DashboardPageController
{
    public function view($treeID = false)
    {
        $defaultTree = TopicTree::getDefault();
        $tree = TopicTree::getByID(Loader::helper('security')->sanitizeInt($treeID));
        if (!$tree) {
            $tree = $defaultTree;
        }

        $this->set('tree', $tree);
        $this->requireAsset('core/topics');

        $trees = [];
        if (is_object($defaultTree)) {
            $trees[] = $defaultTree;
            foreach (TopicTree::getList() as $ctree) {
                if ($ctree->getTreeID() != $defaultTree->getTreeID()) {
                    $trees[] = $ctree;
                }
            }
        }
        $this->set('trees', $trees);
    }

    public function tree_added($treeID)
    {
        $this->set('success', t('Tree added successfully.'));
        $this->view($treeID);
    }

    public function tree_deleted()
    {
        $this->set('message', t('Tree deleted successfully.'));
        $this->view();
    }

    public function remove_tree()
    {
        if ($this->token->validate('remove_tree')) {
            $tree = Tree::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeID']));
            $treeType = $tree->getTreeTypeObject();
            if (is_object($treeType)) {
                $treeTypeHandle = $treeType->getTreeTypeHandle();
            }
            if (is_object($tree) && 'topic' == $treeTypeHandle) {
                if (\PermissionKey::getByHandle('remove_topic_tree')->validate()) {
                    $tree->delete();
                    $this->redirect('/dashboard/system/attributes/topics', 'tree_deleted');
                }
            }
        }
    }

    public function remove_tree_node()
    {
        if ($this->token->validate('remove_tree_node')) {
            $node = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_POST['treeNodeID']));
            $tree = $node->getTreeObject();
            $treeNodeID = $node->getTreeNodeID();
            if (!is_object($node)) {
                $this->error->add(t('Invalid node.'));
            }

            if (0 == $node->getTreeNodeParentID()) {
                $this->error->add(t('You may not remove the top level node.'));
            }

            $np = new Permissions($node);
            if (!$np->canDeleteTreeNode()) {
                $this->error->add(t('You may not remove this node.'));
            }

            if ('topic' != $tree->getTreeTypeHandle()) {
                $this->error->add(t('Invalid tree type.'));
            }

            if (!$this->error->has()) {
                $node->delete();
                $r = new \stdClass();
                $r->treeNodeID = $treeNodeID;
                Loader::helper('ajax')->sendResult($r);
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }

        if ($this->error->has()) {
            Loader::helper('ajax')->sendError($this->error);
        }
    }

    public function tree_edit()
    {
        $vs = $this->app->make('helper/validation/strings');
        $request = Request::getInstance();
        $treeId = $request->get('treeID');
        $treeName = $request->get('treeName');

        if (!$vs->notempty($treeName)) {
            $this->error->add(t('You must specify a valid name for your tree.'));
        }

        if (!$this->token->validate('tree_edit')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if ($this->error->has()) {
            return $this->view($treeId);
        }


        $tree = Tree::getByID($this->app->make('helper/security')->sanitizeInt($treeId));

        $treeType = $tree->getTreeTypeObject();
        $treeTypeHandle = '';
        if (is_object($treeType)) {
            $treeTypeHandle = $treeType->getTreeTypeHandle();
        }
        if (is_object($tree) && 'topic' == $treeTypeHandle) {
            if (\PermissionKey::getByHandle('edit_topic_tree')->validate()) {
                $topic = new \Concrete\Core\Tree\Type\Topic();
                $topic->setPropertiesFromArray($tree);
                $topic->setTopicTreeName($treeName);
                $this->redirect('/dashboard/system/attributes/topics/' . $treeId);
            }
        }
    }
}
