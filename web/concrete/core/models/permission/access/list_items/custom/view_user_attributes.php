<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_ViewUserAttributesUserPermissionAccessListItem extends UserPermissionAccessListItem {
	
	protected $customAttributeArray = array();
	protected $attributesAllowedPermission = 'N';

	public function setAttributesAllowedPermission($permission) {
		$this->attributesAllowedPermission = $permission;
	}
	public function getAttributesAllowedPermission() {
		return $this->attributesAllowedPermission;
	}
	public function setAttributesAllowedArray($akIDs) {
		$this->customAttributeArray = $akIDs;
	}
	public function getAttributesAllowedArray() {
		return $this->customAttributeArray;
	}
	
	
}