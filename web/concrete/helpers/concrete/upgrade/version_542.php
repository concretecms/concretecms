<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion542Helper {

	public function run() {
		$db = Loader::db();
		$cnt = $db->GetOne('select count(*) from TaskPermissions where tpHandle = ?', array('delete_user'));
		if ($cnt < 1) {
			$g3 = Group::getByID(ADMIN_GROUP_ID);
			$tip = TaskPermission::addTask('delete_user', t('Delete Users'), false);
			if (is_object($g3)) {
				$tip->addAccess($g3);
			}
		}
	}
	
	public function prepare() {
		// we install the updated schema just for tables that matter
		Package::installDB(dirname(__FILE__) . '/db/version_542.xml');
	}

	
}