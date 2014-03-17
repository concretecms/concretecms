<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion564Helper {

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

	protected function addGroupPermission($pkHandle, $upgradeFromPermissionHandle = false) {
		$tree = GroupTree::get();
		$rootNode = $tree->getRootTreeNodeObject();
		$adminGroup = Group::getByID(ADMIN_GROUP_ID);
		if ($adminGroup) {
			$adminGroupEntity = GroupPermissionAccessEntity::getOrCreate($adminGroup);
		}

		$pk = PermissionKey::getByHandle($pkHandle);
		if (!$pk) {
			$pk = PermissionKey::add('group_tree_node', $pkHandle, Loader::helper('text')->unhandle($pkHandle), '', false, false);
		}

		if ($upgradeFromPermissionHandle) {
			$upk = PermissionKey::getByHandle($upgradeFromPermissionHandle);
			if (!$upk) {
				return false;
			}
			$pau = $upk->getPermissionAccessObject();
			$groupItems = array();
			$rootItems = array();
			if (is_object($pau)) {
				$listItems = $pau->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL);
				foreach($listItems as $listItem) {
					// if the list item is bound to a specific group in the old permission
					// way, then we set the permission to that particular group in the new tree way.
					// if it's NOT, then we set it to the root node

					// not all upgraded permissions have this stuff.

					if (method_exists($listItem, 'getGroupsAllowedPermission') && $listItem->getGroupsAllowedPermission() == 'C') {
						// it's custom, so we have to add this entity to particular group nodes.
						foreach($listItem->getGroupsAllowedArray() as $gID) {
							$groupItems[$gID] = $listItem;
						}
					} else {
						// it's either ALL groups (if it's inclusive) or no groups (if it's exclusive)
						// either way we add it to root node.
						$rootItems[] = $listItem;
					}
				}
			}

			$pk->setPermissionObject($rootNode);
			$pa = PermissionAccess::create($pk);
			foreach($rootItems as $listItem) {
				$entity = $listItem->getAccessEntityObject();
				$duration = $listItem->getPermissionDurationObject();
				$pa->addListItem($entity, $duration, $listItem->getAccessType());
			}
			$pt = $pk->getPermissionAssignmentObject();
			$pt->assignPermissionAccess($pa);

			foreach($groupItems as $gID => $listItem) {
				$node = GroupTreeNode::getTreeNodeByGroupID($gID);
				if ($node->overrideParentTreeNodePermissions()) {
					$node->setTreeNodePermissionsToGlobal(); // we do this first in case we're running this twice and we need to recopy root permissions
				}
				$node->setTreeNodePermissionsToOverride();				
				$pk->setPermissionObject($node);
				$pa = $pk->getPermissionAccessObject();
				if (!is_object($pa)) {
					$pa = PermissionAccess::create($pk);
				} else if ($pa->isPermissionAccessInUse()) {
					$pa = $pa->duplicate();
				}
				$entity = $listItem->getAccessEntityObject();
				$duration = $listItem->getPermissionDurationObject();
				$pa->addListItem($entity, $duration, $listItem->getAccessType());
				$pt = $pk->getPermissionAssignmentObject();
				$pt->assignPermissionAccess($pa);
			}
			
		} else if ($adminGroupEntity) {
			// there's no upgrade so we set the permission to administrator group
			$pk->setPermissionObject($rootNode);
			$pa = PermissionAccess::create($pk);
			$pa->addListItem($adminGroupEntity);
			$pt = $pk->getPermissionAssignmentObject();
			$pt->assignPermissionAccess($pa);
		}
	}

	public function run() {
			
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

		GroupTree::ensureGroupNodes();

		$sp = Page::getByPath('/dashboard/users/groups/bulk_update');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/users/groups/bulk_update');
			$sp->update(array('cName'=>'Move Multiple Groups'));
		}

	
		$this->addGroupPermission('search_users_in_group', 'access_user_search');
		$this->addGroupPermission('edit_group', 'edit_groups');
		$this->addGroupPermission('assign_group', 'assign_user_groups');
		$this->addGroupPermission('add_sub_group');
		$this->addGroupPermission('edit_group_permissions');

		$pk = PermissionKey::getByHandle('access_user_search');	
		if (is_object($pk)) {
			$db = Loader::db();
			$db->Execute('update PermissionKeys set pkHasCustomClass = 0 where pkID = ?', array($pk->getPermissionKeyID()));
		} else {
			$vpk = PermissionKey::add('user', 'access_user_search', 'Access User Search', 'Controls whether a user can view user search results in the dashboard.', false, false);

		}
	}

}