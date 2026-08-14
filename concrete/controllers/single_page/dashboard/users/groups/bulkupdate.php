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
        if (!$this->token->validate('bulk_update_groups_confirm')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $this->search();
        $this->prepareMoveRequest();

        if (!$this->error->has()) {
            $selectedGroups = $this->get('selectedGroups');
            $gParentNode = $this->get('gParentNode');

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
        $this->prepareMoveRequest();
    }

    private function prepareMoveRequest(): void
    {
        $gParentNode = $this->getRequestedParentNode();
        if (!$gParentNode instanceof GroupTreeNode) {
            return;
        }

        $this->validateTargetParentNode($gParentNode);
        $selectedGroups = $this->getSelectedGroupsForMove($gParentNode);

        if (!$this->error->has()) {
            $gParent = $gParentNode->getTreeNodeGroupObject();
            $this->set('selectedGroups', $selectedGroups);
            $this->set('gParent', $gParent);
            $this->set('gParentNode', $gParentNode);
        }
    }

    private function getRequestedParentNode(): ?GroupTreeNode
    {
        $gParentNodeID = $this->app->make('helper/security')->sanitizeInt($this->request('gParentNodeID'));
        $gParentNode = $gParentNodeID ? TreeNode::getByID($gParentNodeID) : null;
        if (!($gParentNode instanceof GroupTreeNode)) {
            $this->error->add(t('Invalid target parent group.'));

            return null;
        }

        return $gParentNode;
    }

    private function validateTargetParentNode(GroupTreeNode $gParentNode): void
    {
        $checker = new Checker($gParentNode);
        if (!$checker->canAddTreeSubNode()) {
            $this->error->add(t('You do not have permission to move groups beneath this target location.'));
        }
    }

    private function getSelectedGroupsForMove(GroupTreeNode $gParentNode): array
    {
        $selectedGroups = [];
        if (is_array($this->post('gID'))) {
            foreach ($this->post('gID') as $gID) {
                $group = Group::getByID($gID);
                if ($group !== null) {
                    $groupNode = GroupTreeNode::getTreeNodeByGroupID($group->getGroupID());
                    if ($groupNode !== null) {
                        $this->validateSelectedGroupMove($group, $groupNode, $gParentNode);
                        $selectedGroups[] = $group;
                    }
                }
            }
        }

        if (empty($selectedGroups)) {
            $this->error->add(t('You must select at least one group to move'));
        }

        return $selectedGroups;
    }

    private function validateSelectedGroupMove($group, GroupTreeNode $groupNode, GroupTreeNode $gParentNode): void
    {
        $sourceChecker = new Checker($groupNode);
        if (!$sourceChecker->canEditTreeNode()) {
            $this->error->add(t('You do not have permission to move the group "%s".', $group->getGroupDisplayName(false)));
        }

        $error = $groupNode->checkMove($gParentNode);
        if ($error !== null) {
            $this->error->add($error);
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
