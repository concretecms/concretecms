<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Tree\Type\Group as GroupTree;
use \Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Loader;
use \Concrete\Core\Tree\Node\Node as TreeNode;
use Group as ConcreteGroup;

class AddGroup extends DashboardPageController {

	public function checkExpirationOptions($g) {
		if ($_POST['gUserExpirationIsEnabled']) {
			$date = Loader::helper('form/date_time');
			switch($_POST['gUserExpirationMethod']) {
				case 'SET_TIME':
					$g->setGroupExpirationByDateTime($date->translate('gUserExpirationSetDateTime'), $_POST['gUserExpirationAction']);
					break;
				case 'INTERVAL':
					$g->setGroupExpirationByInterval($_POST['gUserExpirationIntervalDays'], $_POST['gUserExpirationIntervalHours'], $_POST['gUserExpirationIntervalMinutes'], $_POST['gUserExpirationAction']);
					break;
			}
		} else {
			$g->removeGroupExpiration();
		}
	}

	public function checkBadgeOptions($g) {
		if ($_POST['gIsBadge']) {
			$g->setBadgeOptions($this->post('gBadgeFID'), $this->post('gBadgeDescription'), $this->post('gBadgeCommunityPointValue'));
		} else {
			$g->clearBadgeOptions();
		}
	}

	public function checkAutomationOptions($g) {
		if ($_POST['gIsAutomated']) {
			$g->setAutomationOptions($this->post('gCheckAutomationOnRegister'), $this->post('gCheckAutomationOnLogin'), $this->post('gCheckAutomationOnJobRun'));
		} else {
			$g->clearAutomationOptions();
		}
	}


	public function view() {
		$tree = GroupTree::get();
		$this->set('tree', $tree);
		$this->requireAsset('core/groups');
	}

	public function do_add() {
		$txt = Loader::helper('text');
		$valt = Loader::helper('validation/token');
		$gName = $txt->sanitize($_POST['gName']);
		$gDescription = $_POST['gDescription'];
		
		if (!$gName) {
			$this->error->add(t("Name required."));
		}
		
		if (!$valt->validate('add_or_update_group')) {
			$this->error->add($valt->getErrorMessage());
		}

		if ($_POST['gIsBadge']) {
			if (!$this->post('gBadgeDescription')) {
				$this->error->add(t('You must specify a description for this badge. It will be displayed publicly.'));
			}
		}
		
		if (isset($_POST['gParentNodeID'])) {
			$parentGroupNode = TreeNode::getByID($_POST['gParentNodeID']);
			if (is_object($parentGroupNode) && $parentGroupNode instanceof GroupTreeNode) {
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
			$g = ConcreteGroup::add($gName, $_POST['gDescription'], $parentGroup);
			$this->checkExpirationOptions($g);
			$this->checkBadgeOptions($g);
			$this->checkAutomationOptions($g);
			$this->redirect('/dashboard/users/groups', 'group_added');
		}	
		$this->view();
	}

}