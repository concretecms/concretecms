<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion5622Helper {

	public function run() {
			
		// add user export users task permission
		$pk = PermissionKey::getByHandle('access_user_search_export');
		if (!$pk instanceof PermissionKey) {
			$pk = PermissionKey::add('user', 'access_user_search_export', tc('PermissionKeyName', 'Export Site Users'), tc('PermissionKeyDescription', 'Controls whether a user can export site users or not'), false, false);
			$pa = $pk->getPermissionAccessObject();
			if (!is_object($pa)) {
				$pa = PermissionAccess::create($pk);
			}
			$adminGroup = Group::getByID(ADMIN_GROUP_ID);
			//Make sure "Adminstrators" group still exists
			if ($adminGroup) {
				$adminGroupEntity = GroupPermissionAccessEntity::getOrCreate($adminGroup);
				$pa->addListItem($adminGroupEntity);
				$pt = $pk->getPermissionAssignmentObject();
				$pt->assignPermissionAccess($pa);
			}
		}
		
		
	}

}
