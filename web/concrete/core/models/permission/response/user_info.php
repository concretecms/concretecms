<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_UserInfoPermissionResponse extends PermissionResponse {

	public function canViewUser() {
		$ui = $this->getPermissionObject();
		$u = $ui->getUserObject();
		if (!$u->isRegistered()) {
			return true;
		}

		$groups = $u->getUserGroups();
		if (count($groups) == 2) {
			// guest and registered
			return true;
		}
		
		foreach($groups as $gID => $gName) {
			$g = Group::getByID($gID);
			if (is_object($g)) {
				$gp = new Permissions($g);
				if ($gp->canSearchUsersInGroup()) {
					return true;
				}
			}
		}
		return false;
	}


}