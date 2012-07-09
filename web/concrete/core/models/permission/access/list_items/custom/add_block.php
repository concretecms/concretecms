<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AddBlockBlockTypePermissionAccessListItem extends BlockTypePermissionAccessListItem {
	
	protected $customBlockTypeArray = array();
	protected $blockTypesAllowedPermission = 'N';

	public function setBlockTypesAllowedPermission($permission) {
		$this->blockTypesAllowedPermission = $permission;
	}
	public function getBlockTypesAllowedPermission() {
		return $this->blockTypesAllowedPermission;
	}
	public function setBlockTypesAllowedArray($btIDs) {
		$this->customBlockTypeArray = $btIDs;
	}
	public function getBlockTypesAllowedArray() {
		return $this->customBlockTypeArray;
	}
	
	
}