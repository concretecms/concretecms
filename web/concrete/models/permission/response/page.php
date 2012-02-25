<?
defined('C5_EXECUTE') or die("Access Denied.");
class PagePermissionResponse extends PermissionResponse {
	
	// legacy support
	public function canWrite() { return in_array('edit_page_contents', $this->allowedPermissions); }
	public function canReadVersions() { return in_array('view_page_versions', $this->allowedPermissions);}
	public function canRead() { return in_array('view_page', $this->allowedPermissions);}
	public function canAddSubContent() { return in_array('add_subpage', $this->allowedPermissions);}
	public function canDeleteCollection() { return in_array('delete_page', $this->allowedPermissions);}
	public function canApproveCollection() { return in_array('approve_page_versions', $this->allowedPermissions);}
	public function canAdminPage() { return in_array('edit_page_permissions', $this->allowedPermissions);}
	public function canAdmin() { return in_array('edit_page_permissions', $this->allowedPermissions);}
	public function canAddSubCollection($ct) {
		$pk = $this->category->getPermissionKeyByHandle('add_subpage');
		$pk->setPermissionObject($this->object);
		return $pk->validate($ct);
	}
	// end legacy
	
	// convenience function
	public function canViewToolbar() {
		$dh = Loader::helper('concrete/dashboard');
		if ($dh->canRead() || in_array(
			array('view_page_versions', 
			'edit_page_contents', 
			'add_subpage', 
			'delete_page', 
			'approve_page_versions', 
			'edit_page_permissions',
			'move_or_copy_page'), $this->allowedPermissions)) {
				return true;
		} else { 
			return false;
		}
	}

	public function testForErrors() { 
		if ((!$this->canViewPage()) && (!$this->object->getCollectionPointerExternalLink() != '')) {
			return COLLECTION_FORBIDDEN;
		}
		if ($this->object->isMasterCollection()) {
			$canEditMaster = TaskPermission::getByHandle('access_page_defaults')->can();
			if (!($canEditMaster && $_SESSION['mcEditID'] == $this->object->getCollectionID())) {
				return COLLECTION_FORBIDDEN;
			}
		}

	}

	public function loadPermissions() {
		$u = new User();
		if ($u->isSuperUser()) {
			$this->loadSuperUserPermissions();
		} else {
			$accessEntities = $u->getUserAccessEntityObjects();
			$peIDs = array('-1');
			foreach($accessEntities as $pe) {
				$peIDs[] = $pe->getAccessEntityID();
			}
			$db = Loader::db();
			$this->allowedPermissions = $db->GetCol('select pkHandle from PermissionKeys pk inner join PagePermissionAssignments ppa where pk.pkID = ppa.pkID and ppa.cID = ? and ppa.peID in (' . implode(',', $peIDs) . ')', array(
				$this->object->getPermissionsCollectionID()
			)); 
		}
	}
	
	
}