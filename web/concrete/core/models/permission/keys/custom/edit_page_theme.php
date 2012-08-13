<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_EditPageThemePagePermissionKey extends PagePermissionKey  {
	
	protected function getAllowedThemeIDs() {

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
		$allptIDs = $db->GetCol('select ptID from PageThemes order by ptID asc');
		$ptIDs = array();
		foreach($list as $l) {
			if ($l->getThemesAllowedPermission() == 'N') {
				$ptIDs = array();
			}
			if ($l->getThemesAllowedPermission() == 'C') {
				if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE) {
					$ptIDs = array_values(array_diff($ptIDs, $l->getThemesAllowedArray()));
				} else { 
					$ptIDs = array_unique(array_merge($ptIDs, $l->getThemesAllowedArray()));
				}
			}
			if ($l->getThemesAllowedPermission() == 'A') {
				$ptIDs = $allptIDs;
			}
		}
		
		return $ptIDs;
	}
	
	public function validate($theme = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$themes = $this->getAllowedThemeIDs();
		if ($theme != false) {
			return in_array($theme->getThemeID(), $themes);
		} else {
			return count($themes) > 0;
		}
	}
	
	
}