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
			$db->Execute('alter table Pages add column cIsActive tinyint(1) not null default 1');
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
		$this->installBlockTypes();

		// install stacks, trash and drafts
		$this->installSinglePages();
		
		// move the old dashboard
		$dashboard = Page::getByPath('/dashboard');
		$dashboard->moveToTrash();
		
		// install new dashboard + page types		
		$this->installDashboard();
		
		// TODO - migrate non core pages out of the dashboard into where we think they should go.
		
	}
	
	public function installSinglePages() {
		Loader::model('single_page');
		$spl = SinglePage::add('/!trash');
		$spl->update(array('cName' => t('Trash')));
		$spl->moveToRoot();
		$spl = SinglePage::add('/!stacks');
		$spl->update(array('cName' => t('Stacks')));
		$spl->moveToRoot();
		$spl = SinglePage::add('/!drafts');
		$spl->update(array('cName' => t('Drafts')));
		$spl->moveToRoot();
	}
	
	public function installBlockTypes() {
		$bt = BlockType::getByHandle('core_scrapbook_display');
		if (!is_object($bt)) {
			BlockType::installBlockType('core_scrapbook_display');			
		}
		$bt = BlockType::getByHandle('core_stack_display');
		if (!is_object($bt)) {
			BlockType::installBlockType('core_stack_display');			
		}
		$bt = BlockType::getByHandle('dashboard_app_status');
		if (!is_object($bt)) {
			BlockType::installBlockType('dashboard_app_status');			
		}
		$bt = BlockType::getByHandle('dashboard_featured_addon');
		if (!is_object($bt)) {
			BlockType::installBlockType('dashboard_featured_addon');			
		}
		$bt = BlockType::getByHandle('dashboard_featured_theme');
		if (!is_object($bt)) {
			BlockType::installBlockType('dashboard_featured_theme');			
		}
		$bt = BlockType::getByHandle('dashboard_form_summary');
		if (!is_object($bt)) {
			BlockType::installBlockType('dashboard_form_summary');			
		}
		$bt = BlockType::getByHandle('dashboard_newsflow_latest');
		if (!is_object($bt)) {
			BlockType::installBlockType('dashboard_newsflow_latest');			
		}
	}
	
	public function installDashboard() {
		Loader::library('content/importer');
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/dashboard.xml');
	}
	
	public function prepare() {
		// we install the updated schema just for tables that matter
		Package::installDB(dirname(__FILE__) . '/db/version_550.xml');
	}

	
}