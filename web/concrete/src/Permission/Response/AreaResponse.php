<?php
namespace Concrete\Core\Permission\Response;
use User;
class AreaResponse extends Response {

	// legacy support
	public function canRead() { return $this->validate('view_area'); }
	public function canWrite() { return $this->validate('edit_area_contents'); }
	public function canAdmin() { return $this->validate('edit_area_permissions'); }
	public function canAddBlocks() { return $this->validate('add_block_to_area'); }
	public function canAddStacks() { return $this->validate('add_stack_to_area'); }
	public function canAddStack() { return $this->validate('add_stack_to_area'); }
	public function canAddLayout() { return $this->validate('add_layout_to_area'); }
	public function canAddBlock($bt) {
		if ($bt->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
			return $this->canAddLayout();
		}
		$pk = $this->category->getPermissionKeyByHandle('add_block_to_area');
		$pk->setPermissionObject($this->object);
		return $pk->validate($bt);
	}


	// convenience function
	public function canViewAreaControls() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		if (
		$this->canEditAreaContents() ||
		$this->canEditAreaPermissions() ||
		$this->canAddBlockToArea() ||
		$this->canAddStackToArea() ||
		$this->canAddLayoutToArea()) {
			return true;
		} else {
			return false;
		}
	}


}
