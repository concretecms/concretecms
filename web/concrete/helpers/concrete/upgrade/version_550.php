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
		
		// install version job
		Loader::model("job");
		Job::installByHandle('remove_old_page_versions');		
		
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
		$newDashPage = Page::getByPath('/dashboard/welcome');
		if (!is_object($newDashPage) || $newDashPage->isError()) {
			$dashboard = Page::getByPath('/dashboard');
			$dashboard->moveToTrash();
			
			// install new dashboard + page types		
			$this->installDashboard();
		
			$this->migrateOldDashboard();
		}
		
		Loader::model('system/captcha/library');
		$scl = SystemCaptchaLibrary::getByHandle('securimage');
		if (!is_object($scl)) {
			$scl = SystemCaptchaLibrary::add('securimage', t('SecurImage (Default)'));
			$scl->activate();
		}
		
		Config::save('SEEN_INTRODUCTION', 1);

		
	}
	
	public function installSinglePages() {
		Loader::model('single_page');
		$spl = SinglePage::add(TRASH_PAGE_PATH);
		if (is_object($spl)) {
			$spl->update(array('cName' => t('Trash')));
			$spl->moveToRoot();
		}
		$spl = SinglePage::add(STACKS_PAGE_PATH);
		if (is_object($spl)) {
			$spl->update(array('cName' => t('Stacks')));
			$spl->moveToRoot();
		}
		$spl = SinglePage::add(COMPOSER_DRAFTS_PAGE_PATH);
		if (is_object($spl)) {
			$spl->update(array('cName' => t('Drafts')));
			$spl->moveToRoot();
		}
	}
 
	public function migrateOldDashboard() {
		$pagesToSkip = array(
			'sitemap',
			'sitemap/full',
			'sitemap/explore',
			'sitemap/search',
			'sitemap/access',	
			'files',
			'files/search',
			'files/attributes',
			'files/sets',
			'files/access',
			'reports',
			'reports/forms',
			'reports/surveys',
			'reports/logs',
			'users',
			'users/search',
			'users/add',
			'users/groups',
			'users/attributes',
			'users/registration',
			'pages',
			'pages/themes',
			'pages/themes/add',
			'pages/themes/inspect',
			'pages/themes/customize',
			'pages/types',
			'pages/attributes',
			'pages/single',
			'install',
			'system',
			'system/jobs',
			'system/backup',
			'system/update',
			'system/notifications',
			'settings',
			'settings/mail',
			'settings/marketplace',
			'composer',
			'composer/write',
			'composer/drafts',
			'pages/types/composer',
			'settings/multilingual',
		);
		Loader::model('page_list');
		$oldDashboard = Page::getByPath(TRASH_PAGE_PATH . '/dashboard');
		$children = $oldDashboard->getCollectionChildrenArray();
		$dashboard = Page::getByPath('/dashboard');
		foreach($children as $cID) {
			$c = Page::getByID($cID, 'RECENT');
			if ($c->isInTrash()) { 
				// we do this so that we don't move something that has already been moved out of the trash.
				$path = str_replace(TRASH_PAGE_PATH . '/dashboard/', '', $c->getCollectionPath());
				if (!in_array($path, $pagesToSkip)) {
					$targetPath = substr($path, 0, strrpos($path, '/'));
					if (!$targetPath) { 
						$c->move($dashboard);		
					} else {
						$target = Page::getByPath('/dashboard/' . $targetPath);
						if (is_object($target) && !$target->isError()) {
							$c->move($target);
						} else {
							$c->move($dashboard);
						}
					}
				}				
			}
		}		
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
		$bt = BlockType::getByHandle('dashboard_site_activity');
		if (!is_object($bt)) {
			BlockType::installBlockType('dashboard_site_activity');			
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