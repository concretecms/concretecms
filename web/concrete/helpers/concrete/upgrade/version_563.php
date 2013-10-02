<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion563Helper {

	public $dbRefreshTables = array(
		'Jobs',
		'JobsLog',
		'Groups',
		'TreeTypes',
		'TreeNodeTypes',
		'TreeCategoryNodes',
		'TreeNodePermissionAssignments',
		'TreeNodes',
		'TreeGroupNodes',
		'Trees'
	);

	public function run() {
			
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

		/* permission categories */
		$groupTreeNodeCategory = PermissionKeyCategory::getByHandle('group_tree_node');
		if (!is_object($groupTreeNodeCategory)) {
			$groupTreeNodeCategory = PermissionKeyCategory::add('group_tree_node', $pkg);
		}

		$entities = array(
			PermissionAccessEntityType::getByHandle('group'),
			PermissionAccessEntityType::getByHandle('user'),
			PermissionAccessEntityType::getByHandle('group_combination'),
			PermissionAccessEntityType::getByHandle('group_set')
		);
		foreach($entities as $ent) {
			$groupTreeNodeCategory->associateAccessEntityType($ent);
		}

		$db = Loader::db();
		$r = $db->Execute('select gID from Groups order by gID asc');
		while ($row = $r->FetchRow()) {
			$g = Group::getByID($row['gID']);
			$g->rescanGroupPath();
		}

		$tt = TreeType::getByHandle('group');
		if (!is_object($tt)) {
			$tt = TreeType::add('group');
		}
		$tnt = TreeNodeType::getByHandle('group');
		if (!is_object($tnt)) {
			$tnt = TreeNodeType::add('group');
		}
		$tree = GroupTree::get();
		if (!is_object($tree)) {
			$tree = GroupTree::add();
		}
		
	}

}