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
class ConcreteUpgradeVersion5411Helper {


	public function run() {
		$db = Loader::db();
		$cnt = $db->GetOne('select count(*) from TaskPermissions where tpHandle = ?', array('install_packages'));
		if ($cnt < 1) {
			$g3 = Group::getByID(ADMIN_GROUP_ID);
			$tip = TaskPermission::addTask('install_packages', t('Install Packages and Connect to the Marketplace'), false);
			if (is_object($g3)) {
				$tip->addAccess($g3);
			}
		}
		
		// ensure we have a proper ocID
		$db->Execute("alter table Files modify column ocID int unsigned not null default 0");	
	}

	
}