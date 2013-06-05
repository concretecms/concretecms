<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FilePermissionResponse extends PermissionResponse {
	
	public function canRead() { return $this->validate('view_file'); }
	public function canWrite() { return $this->validate('edit_file_properties'); }
	public function canAdmin() { return $this->validate('edit_file_permissions'); }

}