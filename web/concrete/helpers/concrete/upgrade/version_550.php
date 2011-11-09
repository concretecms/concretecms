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
class ConcreteUpgradeVersion550Helper {

	public function run() {
		$db = Loader::db();
		$columns = $db->MetaColumns('Pages');
		if (!isset($columns['CISSYSTEMPAGE'])) {
			$db->Execute('alter table Pages add column cIsSystemPage tinyint(1) not null default 0');
			$db->Execute('alter table Pages add index (cIsSystemPage)');
		}
		$columns = $db->MetaColumns('Pages');
		if (!isset($columns['CISACTIVE'])) {
			$db->Execute('alter table Pages add column cIsActive tinyint(1) not null default 0');
			$db->Execute('alter table Pages add index (cIsActive)');
			$db->Execute('update Pages set cIsActive = 1');
		}
		$columns = $db->MetaColumns('PageSearchIndex');
		if (!isset($columns['CREQUIRESREINDEX'])) {
			$db->Execute('alter table PageSearchIndex add column cRequiresReindex tinyint(1) not null default 0');
			$db->Execute('alter table PageSearchIndex add index (cRequiresReindex)');
		}
		
		// flag system pages appropriately 
		Page::rescanSystemPages();		
		
		// add a newsflow task permission
		$db = Loader::db();
		$cnt = $db->GetOne('select count(*) from TaskPermissions where tpHandle = ?', array('view_newsflow'));
		if ($cnt < 1) {
			$g3 = Group::getByID(ADMIN_GROUP_ID);
			$tip = TaskPermission::addTask('view_newsflow', t('View Newsflow'), false);
			if (is_object($g3)) {
				$tip->addAccess($g3);
			}
		}

		// Install new block types
		
		// install new page types 

		// Migrate the dashboard
		
	}
	
	public function prepare() {
		// we install the updated schema just for tables that matter
		Package::installDB(dirname(__FILE__) . '/db/version_550.xml');
	}

	
}