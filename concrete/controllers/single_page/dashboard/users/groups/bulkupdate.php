<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users\Groups;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Group;
use GroupList;
use Concrete\Core\Tree\Node\Node as TreeNode;

class Bulkupdate extends DashboardPageController
{
    public function confirm()
    {
        $this->move();

        $gParentNode = $this->get('gParentNode');
        if ($gParentNode) {
            $checker = new Checker($gParentNode);
            if (!$checker->canAddTreeSubNode()) {
                $this->error->add(t('You do not have permission to move groups beneath this target location.'));
            }
        }

        if (!$this->error->has()) {
            $selectedGroups = $this->get('selectedGroups');


            foreach ($selectedGroups as $g) {
                $node = GroupTreeNode::getTreeNodeByGroupID($g->getGroupID());
                if (is_object($node)) {
                    $node->move($gParentNode);
                }
            }

            $this->redirect('/dashboard/users/groups', 'bulk_update_complete');

        }
    }

    public function move()
    {
        $this->search();
        $gParentNodeID = $this->app->make('helper/security')->sanitizeInt($this->request('gParentNodeID'));
        $gParentNode = $gParentNodeID ? TreeNode::getByID($gParentNodeID) : null;
        if (!($gParentNode instanceof GroupTreeNode)) {
            $gParentNode = null;
            $this->error->add(t("Invalid target parent group."));
        }
        $selectedGroups = [];
        if (is_array($this->post('gID'))) {
            foreach ($this->post('gID') as $gID) {
                $group = Group::getByID($gID);
                if ($group !== null) {
                    $groupNode = GroupTreeNode::getTreeNodeByGroupID($group->getGroupID());
                    if ($groupNode !== null) {
                        if ($gParentNode !== null) {
                            $error = $groupNode->checkMove($gParentNode);
                            if ($error !== null) {
                                $this->error->add($error);
                            }
                        }
                        $selectedGroups[] = $group;
                    }
                }
            }
        }

        if (empty($selectedGroups)) {
            $this->error->add(t("You must select at least one group to move"));
        }
        if (!$this->error->has()) {
            $gParent = $gParentNode->getTreeNodeGroupObject();
            $this->set('selectedGroups', $selectedGroups);
            $this->set('gParent', $gParent);
            $this->set('gParentNode', $gParentNode);
        }
    }

    public function search()
    {
        $tree = GroupTree::get();
        $this->set("tree", $tree);
        $gName = (string) $this->app->make('helper/security')->sanitizeString($this->request('gName'));
        if ($gName === '') {
            $this->error->add(t('You must specify a search string.'));
        }
        if (!$this->error->has()) {
            $gl = $this->app->make(GroupList::class);
            $gl->filterByKeywords($gName);
            $this->set('groups', $gl->getResults());
        }
    }
}
