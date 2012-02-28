<?
defined('C5_EXECUTE') or die("Access Denied.");
class PagePermissionResponse extends PermissionResponse {
	
	// legacy support
	public function canWrite() { return $this->validate('edit_page_contents'); }
	public function canReadVersions() { return $this->validate('view_page_versions');}
	public function canRead() { return $this->validate('view_page');}
	public function canAddSubContent() { return $this->validate('add_subpage');}
	public function canAddSubpages() { return $this->validate('add_subpage');}
	public function canDeleteCollection() { return $this->validate('delete_page');}
	public function canApproveCollection() { return $this->validate('approve_page_versions');}
	public function canAdminPage() { return $this->validate('edit_page_permissions');}
	public function canAdmin() { return $this->validate('edit_page_permissions');}
	public function canAddSubCollection($ct) {
		$pk = $this->category->getPermissionKeyByHandle('add_subpage');
		$pk->setPermissionObject($this->object);
		return $pk->validate($ct);
	}
	// end legacy
	
	// convenience function
	public function canViewToolbar() {
		$dh = Loader::helper('concrete/dashboard');
		if ($dh->canRead() ||
		$this->canViewPageVersions() ||
		$this->canEditPageContents() || 
		$this->canAddSubpage() ||
		$this->canDeletePage() ||
		$this->canApprovePageVersions() ||
		$this->canEditPagePermissions() ||
		$this->canMoveOrCopyPage()) {
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

	
	
}