<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion5622Helper {

	public function run() {
			
		// add user export users task permission
		$pk = PermissionKey::getByHandle('access_user_search_export');
		if (!$pk instanceof PermissionKey) {
			$adminGroupEntity = GroupPermissionAccessEntity::getOrCreate(Group::getByID(ADMIN_GROUP_ID));
			$pk = PermissionKey::add('user', 'access_user_search_export', 'Export Site Users', 'Controls whether a user can export site users or not', false, false);
			$pa = $pk->getPermissionAccessObject();
			if (!is_object($pa)) {
				$pa = PermissionAccess::create($pk);
			}
			$pa->addListItem($adminGroupEntity);
			$pt = $pk->getPermissionAssignmentObject();
			$pt->assignPermissionAccess($pa);
		}
		
		
	}

}
