<?php
namespace Concrete\Core\Permission\Response;
use User;
use Group;
use PermissionKey;
use Permissions;

class UserInfoResponse extends Response {

	public function canViewUser() {
		$ui = $this->getPermissionObject();
		$u = $ui->getUserObject();
		if (!$u->isRegistered()) {
			return true;
		}

		$groups = $u->getUserGroups();

        // note, this will require users to have access to search
        // "registered users" explicitly

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


	public function canEditUser() {
		$ui = $this->getPermissionObject();
		$u = new User();
		if ($ui->getUserID() == USER_SUPER_ID && !$u->isSuperUser()) {
			return false;
		}

		$pk = PermissionKey::getByHandle('edit_user_properties');
		return $pk->validate();
	}


}
