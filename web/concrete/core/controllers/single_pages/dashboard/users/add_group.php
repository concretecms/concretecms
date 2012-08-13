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
		
		$g1 = Group::getByName($gName);
		if ($g1 instanceof Group) {
			if ((!is_object($g)) || $g->getGroupID() != $g1->getGroupID()) {
				$this->error->add(t('A group named "%s" already exists', $g1->getGroupName()));
			}
		}

		if (!$this->error->has()) { 	
			$g = Group::add($gName, $_POST['gDescription']);
			$this->checkExpirationOptions($g);
			$this->redirect('/dashboard/users/groups', 'group_added');
		}	
	}

}