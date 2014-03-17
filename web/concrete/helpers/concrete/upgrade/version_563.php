<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion563Helper {

	public $dbRefreshTables = array(
		'Jobs',
		'JobsLog',
		'PageSearchIndex'
	);

	public function run() {
			

		$bt = BlockType::getByHandle('guestbook');
		if (is_object($bt)) {
			$bt->refresh();
		}

		// add user export users task permission
		$pk = PermissionKey::getByHandle('access_user_search_export');
		if (!$pk instanceof PermissionKey) {
			$pk = PermissionKey::add('user', 'access_user_search_export', 'Export Site Users', 'Controls whether a user can export site users or not', false, false);
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

		if (!Config::get('SECURITY_TOKEN_JOBS')) {
			Config::save('SECURITY_TOKEN_JOBS', Loader::helper('validation/identifier')->getString(64));
		}
		if (!Config::get('SECURITY_TOKEN_ENCRYPTION')) {
			Config::save('SECURITY_TOKEN_ENCRYPTION', Loader::helper('validation/identifier')->getString(64));
		}
		if (!Config::get('SECURITY_TOKEN_VALIDATION')) {
			Config::save('SECURITY_TOKEN_VALIDATION', Loader::helper('validation/identifier')->getString(64));
		}

		$sp = Page::getByPath('/dashboard/system/mail/method/test_settings');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/system/mail/method/test_settings');
			$sp->update(array('cName'=>t('Test Mail Settings')));
			$sp->setAttribute('meta_keywords', 'test smtp, test mail');
		}

	}

}
