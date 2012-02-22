<?
defined('C5_EXECUTE') or die("Access Denied.");
class AreaPermissionKey extends PermissionKey {
	
	protected $area;
	protected $permissionsObject;
	protected $inheritedPermissions = array(
		'view_area' => 'view_page',
		'edit_area_contents' => 'edit_page_contents',
		'add_block' => 'edit_page_contents',
		'add_layout' => 'edit_page_contents',
		'edit_area_design' => 'edit_page_design',
		'edit_area_permissions' => 'edit_page_permissions',
		'delete_area_contents' => 'edit_page_contents'		
	);
	
	public function getAreaObject() {
		return $this->area;
	}

	public function setAreaObject(Area $a) {
		$ax = $a;
		if ($a->isGlobalArea()) {
			$cx = Stack::getByName($a->getAreaHandle());
			$a = Area::get($cx, STACKS_AREA_NAME);
		}

		$this->area = $a;
		
		// if the area overrides the collection permissions explicitly (with a one on the override column) we check
		if ($a->overrideCollectionPermissions()) {
			$this->permissionsObject = $a;
		} else {
			if ($a->getAreaCollectionInheritID() > 0) {
				// in theory we're supposed to be inheriting some permissions from an area with the same handle,
				// set on the collection id specified above (inheritid). however, if someone's come along and
				// reverted that area to the page's permissions, there won't be any permissions, and we 
				// won't see anything. so we have to check
				$areac = Page::getByID($a->getAreaCollectionInheritID());
				$inheritArea = Area::get($areac, $a->getAreaHandlE());
				if ($inheritArea->overrideCollectionPermissions()) {
					// okay, so that area is still around, still has set permissions on it. So we
					// pass our current area to our grouplist, userinfolist objects, knowing that they will 
					// smartly inherit the correct items.
					$this->permissionsObject = $inheritArea;
				}
			}
			
			if (!$this->permissionsObject) { 
				$this->permissionsObject = $a->getAreaCollectionObject();
			}
		}
	}
	
	public function copyFromPageToArea() {
		$db = Loader::db();

		if (isset($this->inheritedPermissions[$this->getPermissionKeyHandle()])) {
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select peID, accessType from PagePermissionAssignments where cID = ? and pkID = ?', array(
				$this->permissionsObject->getCollectionID(), $inheritedPKID
			));
			while ($row = $r->FetchRow()) {
				$db->Replace('AreaPermissionAssignments', array(
					'cID' => $this->area->getCollectionID(), 
					'arHandle' => $this->area->getAreaHandle(), 
					'pkID' => $this->getPermissionKeyID(),
					'accessType' => $row['accessType'],
					'peID' => $row['peID']), array('cID', 'arHandle', 'peID', 'pkID'), true);				
			}
		}
	}
	
	public static function getByID($pkID, Area $area) {
		$pk = self::load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			$pk->setAreaObject($area);
			return $pk;
		}
	}
	
	public function getAssignmentList($accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		if ($this->permissionsObject instanceof Area) { 
			$r = $db->Execute('select peID, pdID from AreaPermissionAssignments where cID = ? and arHandle = ? and accessType = ? and pkID = ?', array(
				$this->permissionsObject->getCollectionID(), $this->permissionsObject->getAreaHandle(), $accessType, $this->getPermissionKeyID()
			));
		} else if (isset($this->inheritedPermissions[$this->getPermissionKeyHandle()])) { 
			// this is a page
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select peID, 0 as pdID from PagePermissionAssignments where cID = ? and accessType = ? and pkID = ?', array(
				$this->permissionsObject->getCollectionID(), $accessType, $inheritedPKID
			));
		} else {
			return array();
		}

 		$list = array();
 		$class = str_replace('AreaPermissionKey', 'AreaPermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'AreaPermissionAssignment';
 		}
 		while ($row = $r->FetchRow()) {
 			$ppa = new $class();
 			$ppa->setAccessType($accessType);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setAreaObject($this->area);
			$list[] = $ppa;
 		}
 		
 		return $list;
	}
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('AreaPermissionAssignments', array(
			'cID' => $this->area->getCollectionID(),
			'arHandle' => $this->area->getAreaHandle(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'arHandle', 'peID', 'pkID'), true);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from AreaPermissionAssignments where cID = ? and arHandle = ? and peID = ?', array($this->area->getCollectionID(), $this->area->getAreaHandle(), $pe->getAccessEntityID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		$area = $this->getAreaObject();
		$c = $area->getAreaCollectionObject();
		return parent::getPermissionKeyToolsURL($task) . '&cID=' . $c->getCollectionID() . '&arHandle=' . $area->getAreaHandle();
	}

}

class AreaPermissionAssignment extends PermissionAssignment {

	public function setAreaObject($area) {
		$this->area = $area;
	}


}