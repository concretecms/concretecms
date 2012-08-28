<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_EditPageThemePagePermissionAccessListItem extends PagePermissionAccessListItem {
	
	protected $customThemeArray = array();
	protected $themesAllowedPermission = 'N';

	public function setThemesAllowedPermission($permission) {
		$this->themesAllowedPermission = $permission;
	}
	public function getThemesAllowedPermission() {
		return $this->themesAllowedPermission;
	}
	public function setThemesAllowedArray($ptIDs) {
		$this->customThemeArray = $ptIDs;
	}
	public function getThemesAllowedArray() {
		return $this->customThemeArray;
	}
	
}