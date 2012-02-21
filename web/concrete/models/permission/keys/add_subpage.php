<?
defined('C5_EXECUTE') or die("Access Denied.");

class AddSubpagePagePermissionKey extends PagePermissionKey  {

	public function savePermissionKey($args) {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionPageTypeAssignments where cID = ?', array($this->page->getCollectionID()));
		$db->Execute('delete from PagePermissionPageTypeAssignmentsCustom where cID = ?', array($this->page->getCollectionID()));
		if (is_array($args['pageTypesIncluded'])) { 
			foreach($args['pageTypesIncluded'] as $peID => $permission) {
				$v = array($this->page->getCollectionID(), $peID, $permission);
				$db->Execute('insert into PagePermissionPageTypeAssignments (cID, peID, permission) values (?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['pageTypesExcluded'])) { 
			foreach($args['pageTypesExcluded'] as $peID => $permission) {
				$v = array($this->page->getCollectionID(), $peID, $permission);
				$db->Execute('insert into PagePermissionPageTypeAssignments (cID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['ctIDInclude'])) { 
			foreach($args['ctIDInclude'] as $peID => $ctIDs) {
				foreach($ctIDs as $ctID) { 
					$v = array($this->page->getCollectionID(), $peID, $ctID);
					$db->Execute('insert into PagePermissionPageTypeAssignmentsCustom (cID, peID, ctID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['ctIDExclude'])) { 
			foreach($args['ctIDExclude'] as $peID => $ctIDs) {
				foreach($ctIDs as $ctID) { 
					$v = array($this->page->getCollectionID(), $peID, $ctID);
					$db->Execute('insert into PagePermissionPageTypeAssignmentsCustom (cID, peID, ctID) values (?, ?, ?)', $v);
				}
			}
		}

	}


	public function getAssignmentList($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$list = parent::getAssignmentList($accessType);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			$permission = $db->GetOne('select permission from PagePermissionPageTypeAssignments where peID = ?', array($pe->getAccessEntityID()));
			$l->setPageTypesAllowedPermission($permission);
			if ($permission == 'C') { 
				$ctIDs = $db->GetCol('select ctID from PagePermissionPageTypeAssignmentsCustom where peID = ?', array($pe->getAccessEntityID()));
				$l->setPageTypesAllowedArray($ctIDs);
			}
		}
		return $list;
	}
	
}

class AddSubpagePagePermissionAssignment extends PagePermissionAssignment {
	
	protected $customPageTypeArray = array();
	protected $pageTypesAllowedPermission = 0;

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
	
	
}