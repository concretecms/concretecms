<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Group as ConcreteGroup;

class AddGroup extends DashboardPageController
{
    public function checkExpirationOptions($g)
    {
        if ($this->request->post('gUserExpirationIsEnabled')) {
            $date = $this->app->make('helper/form/date_time');
            switch ($this->request->post('gUserExpirationMethod')) {
                case 'SET_TIME':
                    $g->setGroupExpirationByDateTime($date->translate('gUserExpirationSetDateTime'), $this->request->post('gUserExpirationAction'));
                    break;
                case 'INTERVAL':
                    $g->setGroupExpirationByInterval($this->request->post('gUserExpirationIntervalDays'), $this->request->post('gUserExpirationIntervalHours'), $this->request->post('gUserExpirationIntervalMinutes'), $this->request->post('gUserExpirationAction'));
                    break;
            }
        } else {
            $g->removeGroupExpiration();
        }
    }

    public function checkBadgeOptions($g)
    {
        if ($this->request->post('gIsBadge')) {
            $g->setBadgeOptions($this->post('gBadgeFID'), $this->post('gBadgeDescription'), $this->post('gBadgeCommunityPointValue'));
        } else {
            $g->clearBadgeOptions();
        }
    }

    public function checkAutomationOptions($g)
    {
        if ($this->request->post('gIsAutomated')) {
            $g->setAutomationOptions($this->post('gCheckAutomationOnRegister'), $this->post('gCheckAutomationOnLogin'), $this->post('gCheckAutomationOnJobRun'));
        } else {
            $g->clearAutomationOptions();
        }
    }

    public function view()
    {
        $tree = GroupTree::get();
        $this->set('tree', $tree);
        $this->requireAsset('core/groups');
    }

    public function do_add()
    {
        $txt = $this->app->make('helper/text');
        $valt = $this->app->make('helper/validation/token');
        $gName = $txt->sanitize($this->request->post('gName'));
        $gDescription = $this->request->post('gDescription');

        if (!$gName) {
            $this->error->add(t("Name required."));
        }

        if (!$valt->validate('add_or_update_group')) {
            $this->error->add($valt->getErrorMessage());
        }

        if ($this->request->post('gIsBadge')) {
            if (!$this->post('gBadgeDescription')) {
                $this->error->add(t('You must specify a description for this badge. It will be displayed publicly.'));
            }
        }

        $parentGroup = null;
        if ($this->request->post('gParentNodeID')) {
            $parentGroupNode = TreeNode::getByID($this->request->post('gParentNodeID'));
            if ($parentGroupNode instanceof GroupTreeNode) {
                $parentGroup = $parentGroupNode->getTreeNodeGroupObject();
            }
        }

        if (is_object($parentGroup)) {
            $pp = new \Permissions($parentGroup);
            if (!$pp->canAddSubGroup()) {
                $this->error->add(t('You do not have permission to add a group beneath %s', $parentGroup->getGroupDisplayName()));
            }
        }

        if (!$this->error->has()) {
            $g = ConcreteGroup::add($gName, $this->request->post('gDescription'), $parentGroup);
            $this->checkExpirationOptions($g);
            $this->checkBadgeOptions($g);
            $this->checkAutomationOptions($g);
            $this->redirect('/dashboard/users/groups', 'group_added');
        }
        $this->view();
    }
}
