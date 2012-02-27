<?
defined('C5_EXECUTE') or die("Access Denied.");
class BlockPermissionResponse extends PermissionResponse {
	
	// legacy support
	public function canRead() { return $this->validate('view_block'); }
	public function canWrite() { return $this->validate('edit_block'); }
	public function canDeleteBlock() { return $this->validate('delete_block'); }
	public function canAdmin() { return $this->validate('edit_block_permissions'); }
	
}