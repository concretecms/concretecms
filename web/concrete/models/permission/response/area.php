<?
defined('C5_EXECUTE') or die("Access Denied.");
class AreaPermissionResponse extends PermissionResponse {
	
	// legacy support
	public function canRead() { return $this->validate('view_area'); }
	public function canWrite() { return $this->validate('edit_area_contents'); }
	public function canAdmin() { return $this->validate('edit_area_permissions'); }
	public function canAddBlocks() { return $this->validate('add_block'); }
	public function canAddStacks() { return $this->validate('add_stack'); }
	public function canAddBlock($bt) {
		$pk = $this->category->getPermissionKeyByHandle('add_block');
		$pk->setPermissionObject($this->object);
		return $pk->validate($bt);
	}

	
	
}