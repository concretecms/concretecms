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
class ConcreteUpgradeVersion540Helper {

	public function run() {
		$db = Loader::db();
		Loader::model('single_page');
		
		Cache::disableLocalCache();
		
		//  we backup the custom styles table
		$this->backupCustomStylesTables();
		
		// upgrade blocks that differ between versions
		$this->updateBlocks();
		
		// Migrate data from the custom styles tables to the new approach
		$this->migrateCustomStyleData();		
		
		$this->setupSiteSearchIndexing();
		
		$this->installTaskPermissions();
		
		$this->updateDashboard();
		
		// add the dark chocolate theme 						
		$pt = PageTheme::getByHandle('dark_chocolate');
		if (!is_object($pt)) {
			$chocolate = PageTheme::add('dark_chocolate');
		}
		
		Cache::enableLocalCache();
	}
	
	public function prepare() {
		// we install the updated schema just for tables that matter
		Package::installDB(dirname(__FILE__) . '/db/version_540.xml');
		
		$db = Loader::db();
		$db->Execute('alter table CollectionVersionBlockStyles drop primary key');
		$db->Execute('alter table CollectionVersionBlockStyles add primary key (cID, bID, cvID, arHandle)');
	}

	protected function setupSiteSearchIndexing() {
		Config::save('SEARCH_INDEX_AREA_METHOD', 'blacklist');
		$areas = array('Header', 'Header Nav');
		Config::save('SEARCH_INDEX_AREA_LIST', serialize($areas));
	}
	
	protected function backupCustomStylesTables() {
		// CollectionVersionBlockStyles
		
		$db = Loader::db();
		$dict = NewDataDictionary($db->db, DB_TYPE);
		$tables = $db->MetaTables();
		if (in_array('CollectionVersionBlockStyles', $tables) && (!in_array('_LegacyCollectionVersionBlockStyles', $tables))) {
			$columns = $db->MetaColumns('CollectionVersionBlockStyles');
			if (!isset($columns['CSRID'])) {
				// we have not updated this table yet into the new format
				// so we back this table up
				$dict->ExecuteSQLArray($dict->RenameTableSQL('CollectionVersionBlockStyles', '_LegacyCollectionVersionBlockStyles'));								
			}
		}
	}
	
	protected function updateDashboard() {
		$sp = Page::getByPath('/dashboard/sitemap/full');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/sitemap/full');
			$d1a->update(array('cName'=>t('Full Sitemap')));
		}
		
		$sp = Page::getByPath('/dashboard/sitemap/explore');
		if ($sp->isError()) {
			$d1b = SinglePage::add('/dashboard/sitemap/explore');
			$d1b->update(array('cName'=>t('Flat View')));
		}
		$sp = Page::getByPath('/dashboard/sitemap/search');
		if ($sp->isError()) {
			$d1c = SinglePage::add('/dashboard/sitemap/search');
			$d1c->update(array('cName'=>t('Page Search')));
		}
		
		$sp = Page::getByPath('/dashboard/sitemap/access');
		if ($sp->isError()) {
			$d1d = SinglePage::add('/dashboard/sitemap/access');
		}
		
		// refresh the sitemap page so it points to sitemap/view.php rather than sitemap.php
		$em = Page::getByPath('/dashboard/sitemap');
		if (!$em->isError()) {
			$em = SinglePage::getByID($em->getCollectionID());
			$em->refresh();
		}
		
		// move dashboard attributes
		$sp = Page::getByPath('/dashboard/pages/attributes');
		if ($sp->isError()) { 
			$d7f = SinglePage::add('/dashboard/pages/attributes');
		}
		
		$d7p = Page::getByPath('/dashboard/pages/types/attributes');
		if (is_object($d7p) && !$d7p->isError()) {
			$d7p->delete();
		}

		$sp = Page::getByPath('/dashboard/system');
		if ($sp->isError()) {
			$d9 = SinglePage::add('/dashboard/system');
			$d9->update(array('cName'=>t('System & Maintenance'), 'cDescription'=>t('Backup, cleanup and update.')));
		}
		
		$sp = Page::getByPath('/dashboard/system/jobs');
		if ($sp->isError()) {
			$d9a = SinglePage::add('/dashboard/system/jobs');
		}
		
		$sp = Page::getByPath('/dashboard/system/backup');
		if ($sp->isError()) {
			$d9b = SinglePage::add('/dashboard/system/backup');
			$d9b->update(array('cName'=>t('Backup & Restore')));
		}
		
		$sp = Page::getByPath('/dashboard/system/update');
		if ($sp->isError()) {
			$d9c = SinglePage::add('/dashboard/system/update');
		}
		
		$sp = Page::getByPath('/dashboard/system/notifications');
		if ($sp->isError()) {
			$d9d = SinglePage::add('/dashboard/system/notifications');
		}

		$oldJobsPage = Page::getByPath('/dashboard/jobs');
		if (is_object($oldJobsPage) && !$oldJobsPage->isError()) {
			$oldJobsPage->delete();
		}

		$sp = Page::getByPath('/dashboard/settings/marketplace');
		if ($sp->isError()) {
			$d12 = SinglePage::add('/dashboard/settings/marketplace');
		}
		
	}
	
	protected function installTaskPermissions() {
		$g3 = Group::getByID(ADMIN_GROUP_ID);
		
		$tpo = TaskPermission::getByHandle('access_task_permissions');
		if (!is_object($tpo)) {
			$tp0 = TaskPermission::addTask('access_task_permissions', t('Change Task Permissions'), false);
			$tp1 = TaskPermission::addTask('access_sitemap', t('Access Sitemap and Page Search'), false);
			$tp2 = TaskPermission::addTask('access_user_search', t('Access User Search'), false);
			$tp3 = TaskPermission::addTask('access_group_search', t('Access Group Search'), false);
			$tp4 = TaskPermission::addTask('access_page_defaults', t('Change Content on Page Type Default Pages'), false);
			$tp5 = TaskPermission::addTask('backup', t('Perform Full Database Backups'), false);
			$tp6 = TaskPermission::addTask('sudo', t('Sign in as User'), false);
			$tp7 = TaskPermission::addTask('uninstall_packages', t('Uninstall Packages'), false);
			
			$tp1->addAccess($g3);
			$tp2->addAccess($g3);
			$tp3->addAccess($g3);
			$tp5->addAccess($g3);		
		}
	}
	
	protected function updateBlocks() {
		$b1 = BlockType::getByHandle('form');
		if (is_object($b1)) {
			$b1->refresh();
		}
		$b2 = BlockType::getByHandle('guestbook');
		if (is_object($b2)) {
			$b2->refresh();
		}
		$b3 = BlockType::getByHandle('rss_displayer');
		if (is_object($b3)) {
			$b3->refresh();
		}
	}
	
	protected function migrateCustomStyleData() {
		// if _LegacyCollectionVersionBlockStyles exists, we loop through it and create new csrID values for it and bind them to the new
		// way we're doing this stuff.
		$db = Loader::db();
		$dict = NewDataDictionary($db->db, DB_TYPE);
		$tables = $db->MetaTables();
		if (in_array('_LegacyCollectionVersionBlockStyles', $tables)) {
			$cnt = $db->GetOne('select count(*) from CollectionVersionBlockStyles');
			if ($cnt > 0) {
				return false;
			}
			
			$r = $db->Execute('select * from _LegacyCollectionVersionBlockStyles');
			$argsCustomStyleRules = array();
			$argsCollectionVersionBlockStyles = array();
			while ($row = $r->FetchRow()) {
				$argsCustomStyleRules = array($row['css_id'], $row['css_class'], $row['css_serialized'], $row['css_custom']);
				$db->Execute('insert into CustomStyleRules (css_id, css_class, css_serialized, css_custom) values (?, ?, ?, ?)', $argsCustomStyleRules);
				$csrID = $db->Insert_ID();
				
				// now we insert into the new CollectionVersionBlockStyles
				// since the old table didn't have cvID or arHandle, we need to loop through all versions where this cID/bID combination exists
				// and add a record for ALL of them
				$versions = $db->GetAll('select cID, cvID, bID, arHandle from CollectionVersionBlocks where bID = ? and cID = ?', array($row['bID'], $row['cID']));
				foreach($versions as $versionRow) {
					$argsCollectionVersionBlockStyles = array($versionRow['cID'], $versionRow['cvID'], $versionRow['bID'], $versionRow['arHandle'], $csrID);
					$db->Execute('insert into CollectionVersionBlockStyles (cID, cvID, bID, arHandle, csrID) values (?, ?, ?, ?, ?)', $argsCollectionVersionBlockStyles);
				}
			}
		}
	}		
}