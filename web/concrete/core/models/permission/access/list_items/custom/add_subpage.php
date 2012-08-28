<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AddSubpagePagePermissionAccessListItem extends PagePermissionAccessListItem {
	
	protected $customPageTypeArray = array();
	protected $pageTypesAllowedPermission = 'N';
	protected $allowExternalLinks = 0;

	public function setPageTypesAllowedPermission($permission) {
		$this->pageTypesAllowedPermission = $permission;
	}
	public function getPageTypesAllowedPermission() {
		return $this->pageTypesAllowedPermission;
	}
	public function setPageTypesAllowedArray($ctIDs) {
		$this->customPageTypeArray = $ctIDs;
	}
	public function getPageTypesAllowedArray() {
		return $this->customPageTypeArray;
	}
	
	public function setAllowExternalLinks($allow) {
		$this->allowExternalLinks = $allow;
	}
	
	public function allowExternalLinks() {
		return $this->allowExternalLinks;
	}
	
	
}