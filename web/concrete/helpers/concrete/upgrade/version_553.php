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
class ConcreteUpgradeVersion553Helper {

	// The upgrade helper will automatically run through these prior to running run()
	// and make sure they are either refreshed from db.xml or that they are created (from db.xml)
	// This gets rid of us having to maintain two lists of database tables AND gets rid of us having to 
	// parse db.xml. Only the new files are affected
	
	public $dbRefreshTables = array(
		'BlockTypes',
		'BlockTypePermissionAssignments',
		'BlockTypePermissionBlockTypeAssignments',
		'BlockTypePermissionBlockTypeAssignmentsCustom',
		'UserPermissionUserSearchAssignments',
		'UserPermissionUserSearchAssignmentsCustom',
		'UserPermissionAssignGroupAssignments',
		'UserPermissionAssignGroupAssignmentsCustom',
		'AreaPermissionBlockTypeAssignments',
		'AreaPermissionBlockTypeAssignmentsCustom',
		'AreaPermissionAssignments',
		'BlockPermissionAssignments',
		'PermissionKeys',
		'PermissionKeyCategories',
		'PermissionAccessEntities',
		'PermissionAccessEntityUsers',
		'PermissionAccessEntityGroups',
		'PermissionDurationObjects',
		'PagePermissionPageTypeAssignments',
		'PagePermissionPageTypeAssignmentsCustom',
		'PagePermissionThemeAssignments',
		'PagePermissionThemeAssignmentsCustom',
		'PagePermissionPropertyAssignments',
		'PagePermissionPropertyAttributeAssignmentsCustom',
		'UserPermissionEditPropertyAssignments',
		'UserPermissionEditPropertyAttributeAssignmentsCustom',
		'UserPermissionViewAttributeAssignments',
		'UserPermissionViewAttributeAssignmentsCustom',
		'PagePermissionAssignments',
		'TaskPermissionAssignments',
		'FileSetPermissionAssignments',
		'FileSetPermissionFileTypeAssignments',
		'FileSetPermissionFileTypeAssignmentsCustom',
		'FilePermissionAssignments'
	);
	
	public function run() {
		Loader::model('single_page');
		$sp = Page::getByPath('/dashboard/system/permissions/users');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/system/permissions/users');
			$d1a->update(array('cName'=>t('User Permissions')));
		}
		$sp = Page::getByPath('/dashboard/system/permissions/advanced');
		if ($sp->isError()) {
			$d1b = SinglePage::add('/dashboard/system/permissions/advanced');
			$d1b->update(array('cName'=>t('Advanced Permissions')));
		}
		
		// install the permissions from permissions.xml
		$this->installPermissions();
		$this->migratePagePermissions();
		$this->migratePagePermissionPageTypes();
		$this->migrateAreaPermissions();
		
	}
	
	protected function migratePagePermissionPageTypes() {
		$db = Loader::db();
		$r = $db->Execute('select distinct cID from PagePermissionPageTypes order by cID asc');	
		$pk = PermissionKey::getByHandle('add_subpage');
		while ($row = $r->FetchRow()) {
			$args = array();
			$entities = array();
			$ro = $db->Execute('select ctID, uID, gID from PagePermissionPageTypes where cID = ?', array($row['cID']));
			while ($row2 = $ro->FetchRow()) { 
				$pe = $this->migrateAccessEntity($row2);			
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
				foreach($entities as $pe) {
					$pk->addAssignment($pe, false, PagePermissionKey::ACCESS_TYPE_INCLUDE);	
				}
				$pk->savePermissionKey($args);
			}
		}
	}
	
	protected function migrateAccessEntity($row) {
		if ($row['uID'] > 0) {
			$ui = UserInfo::getByID($row['uID']);
			$pe = UserPermissionAccessEntity::getOrCreate($ui);
		} else {
			$g = Group::getByID($row['gID']);
			$pe = GroupPermissionAccessEntity::getOrCreate($g);
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
				PermissionKey::getByHandle('delete_area_contents')
			)
		);
		
		$r = $db->Execute('select * from AreaGroups order by cID asc');	
		while ($row = $r->FetchRow()) {
			$pe = $this->migrateAccessEntity($row);
			$permissions = $this->getPermissionsArray($row['agPermissions']);
			$co = Page::getByID($row['cID']);
			$ax = Area::getOrCreate($co, $row['arHandle']);
			foreach($permissions as $p) {
				$permissionsToApply = $permissionMap[$p];
				foreach($permissionsToApply as $pko) {
					$pko->setPermissionObject($ax);
					$pko->addAssignment($pe, false, AreaPermissionKey::ACCESS_TYPE_INCLUDE);	
				}
			}
		}
	}
	
	protected function migratePagePermissions() {
		$db = Loader::db();
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
			$permissions = $this->getPermissionsArray($row['cgPermissions']);
			$co = Page::getByID($row['cID']);
			foreach($permissions as $p) {
				$permissionsToApply = $permissionMap[$p];
				foreach($permissionsToApply as $pko) {
					$pko->setPermissionObject($co);
					$pko->addAssignment($pe, false, PagePermissionKey::ACCESS_TYPE_INCLUDE);	
				}
			}
		}
	}
	
	protected function installPermissions() {
		$sx = simplexml_load_file(DIR_BASE_CORE . '/config/install/base/permissions.xml');
		foreach($sx->permissioncategories->category as $pkc) {
			$handle = (string) $pkc['handle'];
			$pkca = PermissionKeyCategory::getByHandle($handle);
			if (!is_object($pkca)) { 
				$pkx = PermissionKeyCategory::add($pkc['handle']);
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