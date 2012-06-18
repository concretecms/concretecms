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
		'BasicWorkflowProgressData'
	);
	
	
	public function run() {
		$bt = BlockType::getByHandle('core_scrapbook_display');
		if (is_object($bt)) {
			$bt->refresh();
		}
		
		Loader::model('single_page');
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
		
		// install the permissions from permissions.xml
		$this->installPermissionsAndWorkflow();
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
					}
					$spk->setPermissionObject($ax);
					$spt = $pk->getPermissionAssignmentObject();
					$spa = $spk->getPermissionAccessObject();
					if (!is_object($spa)) {
						$spa = PermissionAccess::create($spk);
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
		// permissions
		$permissionMap = array(
			'r' => array(PermissionKey::getByHandle('view_page')),
			'rv' => array(PermissionKey::getByHandle('view_page_versions')),
			'wa' => array(
				PermissionKey::getByHandle('view_page'),
				PermissionKey::getByHandle('preview_page_as_user'),
				PermissionKey::getByHandle('edit_page_properties'),
				PermissionKey::getByHandle('edit_page_contents'),
				PermissionKey::getByHandle('move_or_copy_page')
			),
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
					}
					$pa->addListItem($pe, false, PagePermissionKey::ACCESS_TYPE_INCLUDE);	
					$pt->assignPermissionAccess($pa);
				}
			}
		}
	}
	
	protected function getFileSetPermissionsArray($row) {
		$check = array('canRead', 'canWrite', 'canAdmin', 'canSearch');
		$permissions = array();
		foreach($check as $v) {
			if ($row[$v] == 3) {
				$permissions[$v] = FileSetPermissionKey::ACCESS_TYPE_MINE;
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
		Loader::model("file_set");
		// permissions
		
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
					$permissionsToApply = $permissionMap[$p];
					foreach($permissionsToApply as $pko) {
						$pko->setPermissionObject($fs);
						$pt = $pko->getPermissionAssignmentObject();
						$pa = $pko->getPermissionAccessObject();
						if (!is_object($pa)) {
							$pa = PermissionAccess::create($pko);
						}
						$pa->addListItem($pe, false, $accessType);	
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
						}
						$pa->addListItem($pe, false, $accessType);	
						$pt->assignPermissionAccess($pa);
					}
				}
			}
		}
	}	
	protected function migrateBlockPermissions() {
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
				$pkx = PermissionKeyCategory::add($pkc['handle']);
			}
		}
		foreach($sx->workflowprogresscategories->category as $pkc) {
			$handle = (string) $pkc['handle'];
			$pkca = WorkflowProgressCategory::getByHandle($handle);
			if (!is_object($pkca)) { 
				$pkx = WorkflowProgressCategory::add($pkc['handle']);
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
		$txt = Loader::helper('text');
		foreach($sx->permissionkeys->permissionkey as $pk) {
			$pkc = PermissionKeyCategory::getByHandle($pk['category']);
			Loader::model('permission/categories/' . $pkc->getPermissionKeyCategoryHandle());
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