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
class ConcreteUpgradeVersion560Helper {

	// The upgrade helper will automatically run through these prior to running run()
	// and make sure they are either refreshed from db.xml or that they are created (from db.xml)
	// This gets rid of us having to maintain two lists of database tables AND gets rid of us having to 
	// parse db.xml. Only the new files are affected
	
	public $dbRefreshTables = array(
		'AttributeKeys',
		'CollectionVersions',
		'BlockTypes',
		'BlockTypePermissionBlockTypeAccessList',
		'BlockTypePermissionBlockTypeAccessListCustom',
		'UserPermissionUserSearchAccessList',
		'UserPermissionUserSearchAccessListCustom',
		'UserPermissionAssignGroupAccessList',
		'UserPermissionAssignGroupAccessListCustom',
		'AreaPermissionBlockTypeAccessList',
		'AreaPermissionBlockTypeAccessListCustom',
		'AreaPermissionAssignments',
		'BlockPermissionAssignments',
		'PermissionKeys',
		'PermissionAssignments',
		'PermissionAccess',
		'PermissionAccessList',
		'PermissionKeyCategories',
		'PermissionAccessEntities',
		'PermissionAccessEntityTypes',
		'PermissionAccessEntityTypeCategories',
		'PermissionAccessEntityUsers',
		'PermissionAccessEntityGroupSets',
		'PermissionAccessEntityGroups',
		'PermissionDurationObjects',
		'PagePermissionPageTypeAccessList',
		'PagePermissionPageTypeAccessListCustom',
		'PagePermissionThemeAccessList',
		'PagePermissionThemeAccessListCustom',
		'PagePermissionPropertyAccessList',
		'PagePermissionPropertyAttributeAccessListCustom',
		'UserPermissionEditPropertyAccessList',
		'UserPermissionEditPropertyAttributeAccessListCustom',
		'UserPermissionViewAttributeAccessList',
		'UserPermissionViewAttributeAccessListCustom',
		'PagePermissionAssignments',
		'FileSetPermissionAssignments',
		'FileSetPermissionFileTypeAccessList',
		'FileSetPermissionFileTypeAccessListCustom',
		'FilePermissionAssignments', 
		'Workflows',
		'WorkflowTypes',
		'WorkflowProgress', 
		'WorkflowProgressHistory',
		'WorkflowProgressCategories', 
		'WorkflowRequestObjects',
		'PageWorkflowProgress',
		'PermissionAccessWorkflows',
		'BasicWorkflowPermissionAssignments',
		'BasicWorkflowProgressData',
		'GroupSets',
		'GroupSetGroups',
		'CollectionVersionAreaLayouts',
		'Logs',
		'Users'
	);
	
	
	public function run() {
		if (!Config::get('SITE_INSTALLED_APP_VERSION')) {
			Config::save('SITE_INSTALLED_APP_VERSION', Config::get('SITE_APP_VERSION'));
 	 	}

		BlockTypeList::resetBlockTypeDisplayOrder();

 	 	$th = PageTheme::getByHandle('greek_yogurt');
 	 	if(!is_object($th)) {
 	 		PageTheme::add('greek_yogurt');
 	 	}
 	 		
		$bt = BlockType::getByHandle('core_scrapbook_display');
		if (is_object($bt)) {
			$bt->refresh();
		}

		$bt = BlockType::getByHandle('search');
		if (is_object($bt)) {
			$bt->refresh();
		}

		$sp = Page::getByPath('/dashboard/users/group_sets');
		if ($sp->isError()) {
			$d11 = SinglePage::add('/dashboard/users/group_sets');
			$d11->update(array('cName'=>t('Group Sets')));
		}

		$sp = Page::getByPath('/dashboard/system/seo/bulk_seo_tool');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/system/seo/bulk_seo_tool');
			$d1a->update(array('cName'=>t('Bulk SEO Updater')));
		}
		
		$sp = Page::getByPath('/dashboard/system/permissions/users');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/system/permissions/users');
			$d1a->update(array('cName'=>t('User Permissions')));
		}
		$sp = Page::getByPath('/dashboard/blocks/permissions');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/blocks/permissions');
			$d1a->update(array('cName'=>t('Block &amp; Stack Permissions')));
		}

		$sp = Page::getByPath('/dashboard/system/permissions/advanced');
		if ($sp->isError()) {
			$d1b = SinglePage::add('/dashboard/system/permissions/advanced');
			$d1b->update(array('cName'=>t('Advanced Permissions')));
		}
		$sp = Page::getByPath('/dashboard/workflow');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/workflow');
			$d1a->update(array('cName'=>t('Workflow')));
		}
		$sp = Page::getByPath('/dashboard/workflow/list');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/workflow/list');
		}
		$sp = Page::getByPath('/dashboard/workflow/me');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/workflow/me');
			$d1a->update(array('cName'=>t('Waiting for Me')));
		}
		$sp = Page::getByPath('/dashboard/system/environment/proxy');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/system/environment/proxy');
			$d1a->update(array('cName'=>t('Proxy Server')));
		}
		// update meta keywords
		$pageKeywords = array(
			'/dashboard/composer' => t('blog, blogging'),
			'/dashboard/composer/write' => t('new blog, write blog, blogging'),
			'/dashboard/composer/drafts' => t('blog drafts, composer'),
			'/dashboard/sitemap' => t('pages, add page, delete page, copy, move, alias'),
			'/dashboard/sitemap/full' => t('pages, add page, delete page, copy, move, alias'),
			'/dashboard/sitemap/explore' => t('pages, add page, delete page, copy, move, alias, bulk'),
			'/dashboard/sitemap/search' => t('find page, search page, search, find, pages, sitemap'),
			'/dashboard/files/search' => t('add file, delete file, copy, move, alias, resize, crop, rename, images, title, attribute'),
			'/dashboard/files/attributes' => t('file, file attributes, title, attribute, description, rename'),
			'/dashboard/files/sets' => t('files, category, categories'),
			'/dashboard/files/add_set' => t('new file set'),
			'/dashboard/users' => t('users, groups, people, find, delete user, remove user, change password, password'),
			'/dashboard/users/search' => t('find, search, people, delete user, remove user, change password, password'),
			'/dashboard/users/groups' => t('user, group, people, permissions, access, expire'),
			'/dashboard/users/attributes' => t('user attributes, user data, gather data, registration data'),
			'/dashboard/users/add' => t('new user, create'),
			'/dashboard/users/add_group' => t('new user group, new group, group, create'),
			'/dashboard/users/group_sets' => t('group set'),
			'/dashboard/reports' => t('forms, log, error, email, mysql, exception, survey'),
			'/dashboard/reports/statistics' => t('hits, pageviews, visitors, activity'),
			'/dashboard/reports/forms' => t('forms, questions, response, data'),
			'/dashboard/reports/surveys' => t('questions, quiz, response'),
			'/dashboard/reports/logs' => t('forms, log, error, email, mysql, exception, survey, history'),
			'/dashboard/pages/themes' => t('new theme, theme, active theme, change theme, template, css'),
			'/dashboard/pages/themes/add' => t('theme'),
			'/dashboard/pages/themes/inspect' => t('page types'),
			'/dashboard/pages/themes/customize' => t('custom theme, change theme, custom css, css'),
			'/dashboard/pages/types' => t('page type defaults, global block, global area, starter, template'),
			'/dashboard/pages/attributes' => t('page attributes, custom'),
			'/dashboard/pages/single' => t('single, page, custom, application'),
			'/dashboard/workflow' => t('add workflow, remove workflow'),
			'/dashboard/blocks/stacks' => t('stacks, reusable content, scrapbook, copy, paste, paste block, copy block, site name, logo'),
			'/dashboard/blocks/stacks/list' => t('edit stacks, view stacks, all stacks'),
			'/dashboard/blocks/types' => t('block, refresh, custom'),
			'/dashboard/extend' => t('add-on, addon, add on, package, applications, ecommerce, discussions, forums, themes, templates, blocks'),
			'/dashboard/extend/install' => t('add-on, addon, ecommerce, install, discussions, forums, themes, templates, blocks'),
			'/dashboard/extend/update' => t('update, upgrade'),
			'/dashboard/extend/connect' => t('concrete5.org, my account, marketplace'),
			'/dashboard/extend/themes' => t('buy theme, new theme, marketplace, template'),
			'/dashboard/extend/add-ons' => t('buy addon, buy add on, buy add-on, purchase addon, purchase add on, purchase add-on, find addon, new addon, marketplace'),
			'/dashboard/system' => t('dashboard, configuration'),
			'/dashboard/system/basics/site_name' => t('website name, title'),
			'/dashboard/system/basics/icons' => t('logo, favicon, iphone, icon, bookmark'),
			'/dashboard/system/basics/editor' => t('tinymce, content block, fonts, editor, content, overlay'),
			'/dashboard/system/basics/multilingual' => t('translate, translation, internationalization, multilingual'),
			'/dashboard/system/basics/timezone' => t('timezone, profile, locale'),
			'/dashboard/system/basics/interface' => t('interface, quick nav, dashboard background, background image'),
			'/dashboard/system/seo/urls' => t('vanity, pretty url, seo, pageview, view'),
			'/dashboard/system/seo/bulk_seo_tool' => t('bulk, seo, change keywords, engine, optimization, search'),
			'/dashboard/system/seo/tracking_codes' => t('traffic, statistics, google analytics, quant, pageviews, hits'),
			'/dashboard/system/seo/statistics' => t('turn off statistics, tracking, statistics, pageviews, hits'),
			'/dashboard/system/seo/search_index' => t('configure search, site search, search option'),
			'/dashboard/system/optimization/cache' => t('cache option, change cache, override, turn on cache, turn off cache, no cache, page cache, caching'),
			'/dashboard/system/optimization/clear_cache' => t('cache option, turn off cache, no cache, page cache, caching'),
			'/dashboard/system/optimization/jobs' => t('index search, reindex search, build sitemap, sitemap.xml, clear old versions, page versions, remove old'),
			'/dashboard/system/permissions/site' => t('editors, hide site, offline, private, public, access'),
			'/dashboard/system/permissions/files' => t('file options, file manager, upload, modify'),
			'/dashboard/system/permissions/file_types' => t('security, files, media, extension, manager, upload'),
			'/dashboard/system/permissions/tasks' => t('security, actions, administrator, admin, package, marketplace, search'),
			'/dashboard/system/permissions/ip_blacklist' => t('security, lock ip, lock out, block ip, address, restrict, access'),
			'/dashboard/system/permissions/captcha' => t('security, registration'),
			'/dashboard/system/permissions/antispam' => t('antispam, block spam, security'),
			'/dashboard/system/permissions/maintenance_mode' => t('lock site, under construction, hide, hidden'),
			'/dashboard/system/registration/postlogin' => t('profile, login, redirect, specific, dashboard, administrators'),
			'/dashboard/system/registration/profiles' => t('member profile, member page, community, forums, social, avatar'),
			'/dashboard/system/registration/public_registration' => t('signup, new user, community'),
			'/dashboard/system/mail' => t('smtp, mail settings'),
			'/dashboard/system/mail/method' => t('email server, mail settings, mail configuration, external, internal'),
			'/dashboard/system/mail/importers' => t('email server, mail settings, mail configuration, private message, message system, import, email, message'),
			'/dashboard/system/attributes' => t('attribute configuration'),
			'/dashboard/system/attributes/sets' => t('attributes, sets'),
			'/dashboard/system/attributes/types' => t('attributes, types'),
			'/dashboard/system/environment/info' => t('overrides, system info, debug, support, help'),
			'/dashboard/system/environment/debug' => t('errors, exceptions, develop, support, help'),
			'/dashboard/system/environment/logging' => t('email, logging, logs, smtp, pop, errors, mysql, log'),
			'/dashboard/system/environment/file_storage_locations' => t('security, alternate storage, hide files'),
			'/dashboard/system/environment/proxy' => t('network, proxy server'),
			'/dashboard/system/backup_restore' => t('export, backup, database, sql, mysql, encryption, restore'),
			'/dashboard/system/backup_restore/update' => t('upgrade, new version, update'),
			'/dashboard/system/backup_restore/database' => t('export, database, xml, starting, points, schema, refresh, custom, tables'),
			'/dashboard/system/seo/search_index' => t('configure search, site search, search option'),
			'/dashboard/system/optimization/cache' => t('cache option, change cache, override, turn on cache, turn off cache, no cache, page cache, caching')
			);
		foreach ($pageKeywords as $page => $keywords) {
			$p = Page::getByPath($page, 'ACTIVE');
			if (is_object($p) && !$p->isError()) {
			$p->setAttribute('meta_keywords', $keywords);
			}
		}	
		// install the permissions from permissions.xml
		$this->installPermissionsAndWorkflow();
		$this->addGlobalBlockPermissions();
		$this->migratePagePermissions();
		$this->migratePagePermissionPageTypes();
		$this->migrateAreaPermissions();
		$this->migrateAreaPermissionBlockTypes();
		$this->migrateBlockPermissions();
		$this->migrateFileSetPermissions();
		$this->migrateAddFilePermissions();
		$this->migrateFilePermissions();
		$this->migrateTaskPermissions();		
		$this->migrateThemes();		
		$this->migratePageTypes();
		$this->setupDashboardIcons();		
	}
	
	protected function addGlobalBlockPermissions() {
		if (PERMISSIONS_MODEL == 'simple') {
			// permissions
			$db = Loader::db();
			$permissionMap = array(
				'wa' => array(
					PermissionKey::getByHandle('add_block'),
					PermissionKey::getByHandle('add_stack')
				)
			);

			$r = $db->Execute('select * from PagePermissions where cID = 1');	
			while ($row = $r->FetchRow()) {
				$pe = $this->migrateAccessEntity($row);
				if (!$pe) {
					continue;
				}
				$permissions = $this->getPermissionsArray($row['cgPermissions']);
				foreach($permissions as $p) {
					$permissionsToApply = $permissionMap[$p];
					if (is_array($permissionsToApply)) {
						foreach($permissionsToApply as $pko) {
							$pt = $pko->getPermissionAssignmentObject();
							$pa = $pko->getPermissionAccessObject();
							if (!is_object($pa)) {
								$pa = PermissionAccess::create($pko);
							} else if ($pa->isPermissionAccessInUse()) {
								$pa = $pa->duplicate();
							}
							$pa->addListItem($pe, false, PermissionKey::ACCESS_TYPE_INCLUDE);	
							$pt->assignPermissionAccess($pa);
						}
					}
				}
			}
			
		} else {
			$adminGroup = Group::getByID(ADMIN_GROUP_ID);
			if ($adminGroup) {
				$pae = GroupPermissionAccessEntity::getOrCreate($adminGroup);
				$pk = PermissionKey::getByHandle("add_block");
				$pt = $pk->getPermissionAssignmentObject();
				$pt->clearPermissionAssignment();
				$pa = PermissionAccess::create($pk);
				$pa->addListItem($pae);
				$pt->assignPermissionAccess($pa);

				$pk = PermissionKey::getByHandle("add_stack");
				$pt = $pk->getPermissionAssignmentObject();
				$pt->clearPermissionAssignment();
				$pa = PermissionAccess::create($pk);
				$pa->addListItem($pae);
				$pt->assignPermissionAccess($pa);
			}
		}
	}

	protected function setupDashboardIcons() {
		$cak = CollectionAttributeKey::getByHandle('icon_dashboard');
		if (!is_object($cak)) {
			$textt = AttributeType::getByHandle('text');
			$cab4b = CollectionAttributeKey::add($textt, array('akHandle' => 'icon_dashboard', 'akName' => t('Dashboard Icon'), 'akIsInternal' => true));
		}
		
		$iconArray = array(
			'/dashboard/composer/write' => 'icon-pencil',
			'/dashboard/composer/drafts' => 'icon-book',	
			'/dashboard/sitemap/full' => 'icon-home',
			'/dashboard/sitemap/explore' => 'icon-road',
			'/dashboard/sitemap/search' => 'icon-search',
			'/dashboard/files/search' => 'icon-picture',
			'/dashboard/files/attributes' => 'icon-cog',
			'/dashboard/files/sets' => 'icon-list-alt',
			'/dashboard/files/add_set' => 'icon-plus-sign',
			'/dashboard/users/search' => 'icon-user',
			'/dashboard/users/groups' => 'icon-globe',
			'/dashboard/users/attributes' => 'icon-cog',
			'/dashboard/users/add' => 'icon-plus-sign',
			'/dashboard/users/add_group' => 'icon-plus',
			'/dashboard/users/group_sets' => 'icon-list',
			'/dashboard/reports/statistics' => 'icon-signal',
			'/dashboard/reports/forms' => 'icon-briefcase',
			'/dashboard/reports/surveys' => 'icon-tasks',
			'/dashboard/reports/logs' => 'icon-time',
			'/dashboard/pages/themes' => 'icon-font',
			'/dashboard/pages/types' => 'icon-file',
			'/dashboard/pages/attributes' => 'icon-cog',
			'/dashboard/pages/single' => 'icon-wrench',
			'/dashboard/workflow/list' => 'icon-list',
			'/dashboard/workflow/me' => 'icon-user',
			'/dashboard/blocks/stacks' => 'icon-th',
			'/dashboard/blocks/permissions' => 'icon-lock',
			'/dashboard/blocks/types' => 'icon-wrench'
		);
		foreach($iconArray as $path => $icon) {
			$sp = Page::getByPath($path);
			if (is_object($sp) && (!$sp->isError())) {
				$sp->setAttribute('icon_dashboard', $icon);
			}
		}
	}
	protected function migrateThemes() {
		try {
			$db = Loader::db();
			$r = $db->Execute('select cID, ptID from Pages where ptID > 0');
			while ($row = $r->FetchRow()) {
				$db->Execute('update CollectionVersions set ptID = ? where cID = ?', array($row['ptID'], $row['cID']));
			}		
		} catch(Exception $e) {}
		
	}
	
	protected function migratePageTypes() {
		try {
			$db = Loader::db();
			$r = $db->Execute('select cID, ctID from Pages where ctID > 0');
			while ($row = $r->FetchRow()) {
				$db->Execute('update CollectionVersions set ctID = ? where cID = ?', array($row['ctID'], $row['cID']));
			}		
		} catch(Exception $e) {}
		
	}
	
	protected function migratePagePermissionPageTypes() {
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('PagePermissionPageTypes', $tables)) {
			return false;
		}
		
		$r = $db->Execute('select distinct cID from PagePermissionPageTypes order by cID asc');	
		$pk = PermissionKey::getByHandle('add_subpage');
		while ($row = $r->FetchRow()) {
			$args = array();
			$entities = array();
			$ro = $db->Execute('select ctID, uID, gID from PagePermissionPageTypes where cID = ?', array($row['cID']));
			while ($row2 = $ro->FetchRow()) { 
				$pe = $this->migrateAccessEntity($row2);			
				if (!$pe) {
					continue;
				}
				if (!in_array($pe, $entities)) {
					$entities[] = $pe;				
				}
				$args['allowExternalLinksIncluded'][$pe->getAccessEntityID()] = 1;
				$args['pageTypesIncluded'][$pe->getAccessEntityID()] = 'C';
				$args['ctIDInclude'][$pe->getAccessEntityID()][] = $row2['ctID'];
			}
			$co = Page::getByID($row['cID']);
			if (is_object($co) && (!$co->isError())) { 
				$pk->setPermissionObject($co);
				$pt = $pk->getPermissionAssignmentObject();
				$pa = $pk->getPermissionAccessObject();
				if (!is_object($pa)) {
					$pa = PermissionAccess::create($pk);
				} else if ($pa->isPermissionAccessInUse()) {
					$pa = $pa->duplicate();
				}
				foreach($entities as $pe) {
					$pa->addListItem($pe, false, PagePermissionKey::ACCESS_TYPE_INCLUDE);	
				}
				$pa->save($args);
				$pt->assignPermissionAccess($pa);
			}
		}
	}
	
	protected function migrateTaskPermissions() {
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('TaskPermissions', $tables)) {
			return false;
		}
		$r = $db->Execute('select tp.tpHandle, tpug.* from TaskPermissions tp inner join TaskPermissionUserGroups tpug on tp.tpID = tpug.tpID order by tpID asc');
		while ($row = $r->FetchRow()) {
			$pk = PermissionKey::getByHandle($row['tpHandle']);
			if (is_object($pk)) {
				$pa = $pk->getPermissionAccessObject();
				if (!is_object($pa)) {
					$pa = PermissionAccess::create($pk);
				} else if ($pa->isPermissionAccessInUse()) {
					$pa = $pa->duplicate();
				}
				$pe = $this->migrateAccessEntity($row);
				if (!$pe) {
					continue;
				}
				$pt = $pk->getPermissionAssignmentObject();
				$pa->addListItem($pe, false, FileSetPermissionKey::ACCESS_TYPE_INCLUDE);	
				$pt->assignPermissionAccess($pa);
			}			
		}
	}

	protected function migrateAddFilePermissions() {
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('FileSetPermissions', $tables)) {
			return false;
		}
		$r = $db->Execute('select canAdd, gID, uID, fsID from FileSetPermissions where canAdd > 0 order by fsID asc');	
		$pko = FileSetPermissionKey::getByHandle('add_file');
		while ($row = $r->FetchRow()) {
			$pe = $this->migrateAccessEntity($row);
			if (!$pe) {
				continue;
			}
			
			if ($row['fsID'] > 0) {
				$fs = FileSet::getByID($row['fsID']);
			} else {
				$fs = FileSet::getGlobal();
			}
			if (is_object($fs)) { 
				$pko->setPermissionObject($fs);
				$pt = $pko->getPermissionAssignmentObject();
				$pa = $pko->getPermissionAccessObject();
				if (!is_object($pa)) {
					$pa = PermissionAccess::create($pko);
				} else if ($pa->isPermissionAccessInUse()) {
					$pa = $pa->duplicate();
				}
				$pa->addListItem($pe, false, FileSetPermissionKey::ACCESS_TYPE_INCLUDE);	
				$args = array();
				if ($row['canAdd'] == 10) {
					$args['fileTypesIncluded'][$pe->getAccessEntityID()] = 'A';
				} else {
					$args['fileTypesIncluded'][$pe->getAccessEntityID()] = 'C';
					$extensions = $db->GetCol('select extension from FilePermissionFileTypes where
						fsID = ? and gID = ? and uID = ?', array($row['fsID'], $row['gID'], $row['uID']));
					foreach($extensions as $ext) {
						$args['extensionInclude'][$pe->getAccessEntityID()][] = $ext;
					}
				}
				$pa->save($args);
				$pt->assignPermissionAccess($pa);
			}
		}
	}


	protected function migrateAreaPermissionBlockTypes() {
		if (PERMISSIONS_MODEL == 'simple') {
			return;
		}
		
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('AreaGroupBlockTypes', $tables)) {
			return false;
		}
		$r = $db->Execute('select distinct cID, arHandle from AreaGroupBlockTypes order by cID asc');	
		$pk = PermissionKey::getByHandle('add_block_to_area');
		$spk = PermissionKey::getByHandle('add_stack_to_area');
		while ($row = $r->FetchRow()) {
			$args = array();
			$entities = array();
			$ro = $db->Execute('select btID, uID, gID from AreaGroupBlockTypes where cID = ? and arHandle = ?', array($row['cID'], $row['arHandle']));
			while ($row2 = $ro->FetchRow()) { 
				$pe = $this->migrateAccessEntity($row2);			
				if (!$pe) {
					continue;
				}
				if (!in_array($pe, $entities)) {
					$entities[] = $pe;				
				}
				$args['blockTypesIncluded'][$pe->getAccessEntityID()] = 'C';
				$args['btIDInclude'][$pe->getAccessEntityID()][] = $row2['btID'];
			}
			$co = Page::getByID($row['cID']);
			if (is_object($co) && (!$co->isError())) { 
				$ax = Area::getOrCreate($co, $row['arHandle']);
				if (is_object($ax)) { 
					$pk->setPermissionObject($ax);
					$pt = $pk->getPermissionAssignmentObject();
					$pa = $pk->getPermissionAccessObject();
					if (!is_object($pa)) {
						$pa = PermissionAccess::create($pk);
					} else if ($pa->isPermissionAccessInUse()) {
						$pa = $pa->duplicate();
					}
					$spk->setPermissionObject($ax);
					$spt = $pk->getPermissionAssignmentObject();
					$spa = $spk->getPermissionAccessObject();
					if (!is_object($spa)) {
						$spa = PermissionAccess::create($spk);
					} else if ($spa->isPermissionAccessInUse()) {
						$spa = $spa->duplicate();
					}

					foreach($entities as $pe) {
						$pa->addListItem($pe, false, AreaPermissionKey::ACCESS_TYPE_INCLUDE);	
						$spa->addListItem($pe, false, AreaPermissionKey::ACCESS_TYPE_INCLUDE);
					}
					$pa->save($args);
					$spa->save($args);
					$pt->assignPermissionAccess($pa);
					$spt->assignPermissionAccess($spa);
				}
			}
		}
	}
	
	protected function migrateAccessEntity($row) {
		if ($row['uID'] > 0) {
			$ui = UserInfo::getByID($row['uID']);
			if ($ui) { 
				$pe = UserPermissionAccessEntity::getOrCreate($ui);
			}
		} else {
			$g = Group::getByID($row['gID']);
			if ($g) { 
				$pe = GroupPermissionAccessEntity::getOrCreate($g);
			}
		}
		return $pe;		
	}
	
	protected function getPermissionsArray($permissions) {
		$tmp = explode(':', $permissions);
		$permissions = array();
		if (is_array($tmp)) {
			foreach($tmp as $i) {
				$i = trim($i);
				if ($i) {
					$permissions[] = $i;
				}
			}
		}
		return $permissions;
	}

	protected function migrateAreaPermissions() {
		if (PERMISSIONS_MODEL == 'simple') {
			return;
		}
		
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('AreaGroups', $tables)) {
			return false;
		}
		// permissions
		$permissionMap = array(
			'r' => array(PermissionKey::getByHandle('view_area')),
			'wa' => array(
				PermissionKey::getByHandle('edit_area_contents'),
				PermissionKey::getByHandle('add_layout_to_area'),
				PermissionKey::getByHandle('edit_area_design'),
				PermissionKey::getByHandle('edit_area_contents')
			),
			'db' => array(
				PermissionKey::getByHandle('edit_area_permissions'),
				PermissionKey::getByHandle('schedule_area_contents_guest_access'),
				PermissionKey::getByHandle('delete_area_contents')
			)
		);
		
		$r = $db->Execute('select * from AreaGroups order by cID asc');	
		while ($row = $r->FetchRow()) {
			$pe = $this->migrateAccessEntity($row);
			if (!$pe) {
				continue;
			}
			$permissions = $this->getPermissionsArray($row['agPermissions']);
			$co = Page::getByID($row['cID']);
			if(!is_object($co) || $co->getCollectionID()<=0) { continue; }
			$ax = Area::getOrCreate($co, $row['arHandle']);
			foreach($permissions as $p) {
				$permissionsToApply = $permissionMap[$p];
				foreach($permissionsToApply as $pko) {
					$pko->setPermissionObject($ax);
					$pt = $pko->getPermissionAssignmentObject();
					$pa = $pko->getPermissionAccessObject();
					if (!is_object($pa)) {
						$pa = PermissionAccess::create($pko);
					} else if ($pa->isPermissionAccessInUse()) {
						$pa = $pa->duplicate();
					}
					$pa->addListItem($pe, false, AreaPermissionKey::ACCESS_TYPE_INCLUDE);	
					$pt->assignPermissionAccess($pa);
				}
			}
		}
	}
	
	protected function migratePagePermissions() {
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('PagePermissions', $tables)) {
			return false;
		}
		// first, we fix permissions that are set to override but are pointing to another page. They shouldn't do that.
		$db->Execute('update Pages set cInheritPermissionsFromCID = cID where cInheritPermissionsFrom = "OVERRIDE"');
		// permissions
		$waSet = array(
			PermissionKey::getByHandle('preview_page_as_user'),
			PermissionKey::getByHandle('edit_page_properties'),
			PermissionKey::getByHandle('edit_page_contents'),
			PermissionKey::getByHandle('move_or_copy_page'),
			PermissionKey::getByHandle('add_block_to_area'),
			PermissionKey::getByHandle('add_stack_to_area'),
		);
		if (PERMISSIONS_MODEL == 'simple') {
			$waSet[] = PermissionKey::getByHandle('approve_page_versions');
			$waSet[] = PermissionKey::getByHandle('delete_page_versions');
			$waSet[] = PermissionKey::getByHandle('add_subpage');
		}		
		$permissionMap = array(
			'r' => array(PermissionKey::getByHandle('view_page')),
			'rv' => array(PermissionKey::getByHandle('view_page_versions')),
			'wa' => $waSet,
			'adm' => array(
				PermissionKey::getByHandle('edit_page_speed_settings'),
				PermissionKey::getByHandle('edit_page_theme'),
				PermissionKey::getByHandle('edit_page_type'),
				PermissionKey::getByHandle('schedule_page_contents_guest_access'),
				PermissionKey::getByHandle('edit_page_permissions')
			),
			'dc' => array(
				PermissionKey::getByHandle('delete_page')
			),
			'av' => array(
				PermissionKey::getByHandle('approve_page_versions'),
				PermissionKey::getByHandle('delete_page_versions')
			),
			'db' => array(PermissionKey::getByHandle('edit_page_contents'))	
		);
		
		
		$r = $db->Execute('select * from PagePermissions order by cID asc');	
		while ($row = $r->FetchRow()) {
			$pe = $this->migrateAccessEntity($row);
			if (!$pe) {
				continue;
			}
			$permissions = $this->getPermissionsArray($row['cgPermissions']);
			$co = Page::getByID($row['cID']);
			foreach($permissions as $p) {
				$permissionsToApply = $permissionMap[$p];
				foreach($permissionsToApply as $pko) {
					$pko->setPermissionObject($co);
					$pt = $pko->getPermissionAssignmentObject();
					$pa = $pko->getPermissionAccessObject();
					if (!is_object($pa)) {
						$pa = PermissionAccess::create($pko);
					} else if ($pa->isPermissionAccessInUse()) {
						$pa = $pa->duplicate();
					}
					$pa->addListItem($pe, false, PagePermissionKey::ACCESS_TYPE_INCLUDE);	
					$pt->assignPermissionAccess($pa);
				}
			}
		}
	}
	
	const ACCESS_TYPE_MINE = 3;
	
	protected function getFileSetPermissionsArray($row) {
		$check = array('canRead', 'canWrite', 'canAdmin', 'canSearch');
		$permissions = array();
		foreach($check as $v) {
			if ($row[$v] == 3) {
				$permissions[$v] = self::ACCESS_TYPE_MINE;
			}
			if ($row[$v] == 10) {
				$permissions[$v] = FileSetPermissionKey::ACCESS_TYPE_INCLUDE;
			}
		}
		return $permissions;
	}
	
	protected function migrateFileSetPermissions() {
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('FileSetPermissions', $tables)) {
			return false;
		}
		// permissions
		$fpe = FileUploaderPermissionAccessEntity::getOrCreate();
		$permissionMap = array(
			'canRead' => array(PermissionKey::getByHandle('view_file_set_file')),
			'canSearch' => array(PermissionKey::getByHandle('search_file_set')),
			'canWrite' => array(
				PermissionKey::getByHandle('edit_file_set_file_properties'),
				PermissionKey::getByHandle('edit_file_set_file_contents'),
				PermissionKey::getByHandle('copy_file_set_files'),
				PermissionKey::getByHandle('delete_file_set_files')
			),
			'canAdmin' => array(
				PermissionKey::getByHandle('edit_file_set_permissions'),
				PermissionKey::getByHandle('delete_file_set')
			)
		);		
		$r = $db->Execute('select * from FileSetPermissions order by fsID asc');	
		while ($row = $r->FetchRow()) {
			$pe = $this->migrateAccessEntity($row);
			if (!$pe) {
				continue;
			}
			if ($row['fsID'] > 0) {
				$fs = FileSet::getByID($row['fsID']);
			} else {
				$fs = FileSet::getGlobal();
			}
			$permissions = $this->getFileSetPermissionsArray($row);
			if (is_object($fs)) { 
				foreach($permissions as $p => $accessType) {
					if ($accessType == self::ACCESS_TYPE_MINE) {
						$_pe = $fpe;
					} else { 
						$_pe = $pe;
					}
					$permissionsToApply = $permissionMap[$p];
					foreach($permissionsToApply as $pko) {
						$pko->setPermissionObject($fs);
						$pt = $pko->getPermissionAssignmentObject();
						$pa = $pko->getPermissionAccessObject();
						if (!is_object($pa)) {
							$pa = PermissionAccess::create($pko);
						} else if ($pa->isPermissionAccessInUse()) {
							$pa = $pa->duplicate();
						}
						$pa->addListItem($_pe, false, FileSetPermissionKey::ACCESS_TYPE_INCLUDE);	
						$pt->assignPermissionAccess($pa);
					}
				}
			}
		}
	}

	protected function migrateFilePermissions() {
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('FilePermissions', $tables)) {
			return false;
		}
		
		$permissionMap = array(
			'canRead' => array(PermissionKey::getByHandle('view_file')),
			'canSearch' => array(PermissionKey::getByHandle('view_file_in_file_manager')),
			'canWrite' => array(
				PermissionKey::getByHandle('edit_file_properties'),
				PermissionKey::getByHandle('edit_file_contents'),
				PermissionKey::getByHandle('copy_file'),
				PermissionKey::getByHandle('delete_file')
			),
			'canAdmin' => array(
				PermissionKey::getByHandle('edit_file_permissions')
			)
		);		
		$r = $db->Execute('select * from FilePermissions order by fID asc');	
		while ($row = $r->FetchRow()) {
			$pe = $this->migrateAccessEntity($row);
			if (!$pe) {
				continue;
			}
			$f = File::getByID($row['fID']);
			$permissions = $this->getFileSetPermissionsArray($row);
			if (is_object($f) && !$f->isError()) { 
				foreach($permissions as $p => $accessType) {
					$permissionsToApply = $permissionMap[$p];
					foreach($permissionsToApply as $pko) {
						$pko->setPermissionObject($f);
						$pt = $pko->getPermissionAssignmentObject();
						$pa = $pko->getPermissionAccessObject();
						if (!is_object($pa)) {
							$pa = PermissionAccess::create($pko);
						} else if ($pa->isPermissionAccessInUse()) {
							$pa = $pa->duplicate();
						}
						$pa->addListItem($pe, false, $accessType);	
						$pt->assignPermissionAccess($pa);
					}
				}
			}
		}
	}	
	protected function migrateBlockPermissions() {
		if (PERMISSIONS_MODEL == 'simple') {
			return;
		}
		
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (!in_array('CollectionVersionBlockPermissions', $tables)) {
			return false;
		}
		// permissions
		$permissionMap = array(
			'r' => array(PermissionKey::getByHandle('view_block')),
			'wa' => array(
				PermissionKey::getByHandle('edit_block'),
				PermissionKey::getByHandle('edit_block_custom_template'),
				PermissionKey::getByHandle('edit_block_design')
			),
			'db' => array(
				PermissionKey::getByHandle('delete_block'),
				PermissionKey::getByHandle('schedule_guest_access'),
				PermissionKey::getByHandle('edit_block_permissions')
			)
		);
		
		$r = $db->Execute('select * from CollectionVersionBlockPermissions order by cID asc');	
		while ($row = $r->FetchRow()) {
			$pe = $this->migrateAccessEntity($row);
			if (!$pe) {
				continue;
			}
			$permissions = $this->getPermissionsArray($row['cbgPermissions']);
			$co = Page::getByID($row['cID'], $row['cvID']);
			if (!is_object($co) || $co->isError()) {
				continue;
			}
			$arHandle = $db->GetOne('select arHandle from CollectionVersionBlocks cvb where cvb.cID = ? and 
				cvb.cvID = ? and cvb.bID = ?', array($row['cID'], $row['cvID'], $row['bID']));
			$a = Area::get($co, $arHandle);
			$bo = Block::getByID($row['bID'], $co, $a);
			if (is_object($bo)) { 
				foreach($permissions as $p) {
					$permissionsToApply = $permissionMap[$p];
					foreach($permissionsToApply as $pko) {
						$pko->setPermissionObject($bo);
						$pt = $pko->getPermissionAssignmentObject();
						$pa = $pko->getPermissionAccessObject();
						if (!is_object($pa)) {
							$pa = PermissionAccess::create($pko);
						} else if ($pa->isPermissionAccessInUse()) {
							$pa = $pa->duplicate();
						}
						$pa->addListItem($pe, false, BlockPermissionKey::ACCESS_TYPE_INCLUDE);	
						$pt->assignPermissionAccess($pa);
					}
				}
			}
		}
	}	
	protected function installPermissionsAndWorkflow() {
		$sx = simplexml_load_file(DIR_BASE_CORE . '/config/install/base/permissions.xml');
		foreach($sx->permissioncategories->category as $pkc) {
			$handle = (string) $pkc['handle'];
			$pkca = PermissionKeyCategory::getByHandle($handle);
			if (!is_object($pkca)) { 
				$pkx = PermissionKeyCategory::add((string) $pkc['handle']);
			}
		}
		foreach($sx->workflowprogresscategories->category as $pkc) {
			$handle = (string) $pkc['handle'];
			$pkca = WorkflowProgressCategory::getByHandle($handle);
			if (!is_object($pkca)) { 
				$pkx = WorkflowProgressCategory::add((string) $pkc['handle']);
			}
		}
		foreach($sx->workflowtypes->workflowtype as $wt) {
			$handle = (string) $wt['handle'];
			$name = (string) $wt['name'];
			$wtt = WorkflowType::getByHandle($handle);
			if (!is_object($wtt)) { 
				$pkx = WorkflowType::add($handle, $name);
			}
		}
		if (isset($sx->permissionaccessentitytypes)) {
			foreach($sx->permissionaccessentitytypes->permissionaccessentitytype as $pt) {
				$name = $pt['name'];
				if (!$name) {
					$name = Loader::helper('text')->unhandle($pt['handle']);
				}
				$handle = (string) $pt['handle'];
				$patt = PermissionAccessEntityType::getByHandle($handle);
				if (!is_object($patt)) {
					$type = PermissionAccessEntityType::add((string) $pt['handle'], $name);
					if (isset($pt->categories)) {
						foreach($pt->categories->children() as $cat) {
							$catobj = PermissionKeyCategory::getByHandle((string) $cat['handle']);
							$catobj->associateAccessEntityType($type);
						}
					}
				}
			}
		}

		$txt = Loader::helper('text');
		foreach($sx->permissionkeys->permissionkey as $pk) {
			$pkc = PermissionKeyCategory::getByHandle((string) $pk['category']);
			$className = $txt->camelcase($pkc->getPermissionKeyCategoryHandle());
			$c1 = $className . 'PermissionKey';
			$handle = (string) $pk['handle'];
			$pka = PermissionKey::getByHandle($handle);
			if (!is_object($pka)) { 
				$pkx = call_user_func(array($c1, 'import'), $pk);	
			}
		}
	}
	
}
