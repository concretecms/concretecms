<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Users_AddGroup extends DashboardBaseController {

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

	public function view() {
		$tree = GroupTree::get();
		$this->set('tree', $tree);

		$this->addHeaderItem(Loader::helper('html')->css('dynatree/dynatree.css'));
		$this->addFooterItem(Loader::helper('html')->javascript('dynatree/dynatree.js'));
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

		if (isset($_POST['gParentNodeID'])) {
			$parentGroupNode = TreeNode::getByID($_POST['gParentNodeID']);
			if (is_object($parentGroupNode) && $parentGroupNode instanceof GroupTreeNode) {
				$parentGroup = $parentGroupNode->getTreeNodeGroupObject();
			}
		}

		if (is_object($parentGroup)) {
			$pp = new Permissions($parentGroup);
			if (!$pp->canAddSubGroup()) {
				$this->error->add(t('You do not have permission to add a group beneath %s', $parentGroup->getGroupDisplayName()));
			}
		}
		
		if (!$this->error->has()) {
			$g = Group::add($gName, $_POST['gDescription'], $parentGroup);
			$this->checkExpirationOptions($g);
			$this->redirect('/dashboard/users/groups', 'group_added');
		}	
		$this->view();
	}

}