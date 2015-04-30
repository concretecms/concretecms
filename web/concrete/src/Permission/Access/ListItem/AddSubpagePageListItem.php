<?php
namespace Concrete\Core\Permission\Access\ListItem;
class AddSubpagePageListItem extends PageListItem {

	protected $customPageTypeArray = array();
	protected $pageTypesAllowedPermission = 'N';
	protected $allowExternalLinks = 0;

	public function setPageTypesAllowedPermission($permission) {
		$this->pageTypesAllowedPermission = $permission;
	}
	public function getPageTypesAllowedPermission() {
		return $this->pageTypesAllowedPermission;
	}
	public function setPageTypesAllowedArray($ptIDs) {
		$this->customPageTypeArray = $ptIDs;
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
