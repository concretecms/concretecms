<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AddSubpagePagePermissionKey extends PagePermissionKey  {
	
	public function canAddExternalLink() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		
		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return array();
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$canAddLinks = false;
		foreach($list as $l) {
			if (!$l->allowExternalLinks()) {
				$canAddLinks = false;
			} else {
				$canAddLinks = true;
			}
		}
		return $canAddLinks;
	}
	
	protected function getAllowedPageTypeIDs() {

		$u = new User();
		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return array();
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		
		$db = Loader::db();
		$allCTIDs = $db->GetCol('select ctID from PageTypes where ctIsInternal = 0');
		$ctIDs = array();
		foreach($list as $l) {
			if ($l->getPageTypesAllowedPermission() == 'N') {
				$ctIDs = array();
			}
			if ($l->getPageTypesAllowedPermission() == 'C') {
				if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE) {
					$ctIDs = array_values(array_diff($ctIDs, $l->getPageTypesAllowedArray()));
				} else { 
					$ctIDs = array_unique(array_merge($ctIDs, $l->getPageTypesAllowedArray()));
				}
			}
			if ($l->getPageTypesAllowedPermission() == 'A') {
				$ctIDs = $allCTIDs;
			}
		}
		
		return $ctIDs;
	}
	
	public function validate($ct = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$types = $this->getAllowedPageTypeIDs();
		if ($ct != false) {
			return in_array($ct->getCollectionTypeID(), $types);
		} else {
			return count($types) > 0;
		}
	}
	

	
}
