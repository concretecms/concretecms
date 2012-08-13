<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AddFileFileSetPermissionAccessListItem extends FileSetPermissionAccessListItem {
	
	protected $customFileTypesArray = array();
	protected $fileTypesAllowedPermission = 'N';

	public function setFileTypesAllowedPermission($permission) {
		$this->fileTypesAllowedPermission = $permission;
	}
	public function getFileTypesAllowedPermission() {
		return $this->fileTypesAllowedPermission;
	}
	public function setFileTypesAllowedArray($extensions) {
		$this->customFileTypesArray = $extensions;
	}
	public function getFileTypesAllowedArray() {
		return $this->customFileTypesArray;
	}
	
	
}