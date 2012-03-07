<?
defined('C5_EXECUTE') or die("Access Denied.");
class AreaPermissionKey extends PermissionKey {
	
	protected $area;
	protected $permissionObjectToCheck;
	protected $inheritedPermissions = array(
		'view_area' => 'view_page',
		'edit_area_contents' => 'edit_page_contents',
		'add_layout_to_area' => 'edit_page_contents',
		'edit_area_design' => 'edit_page_design',
		'edit_area_permissions' => 'edit_page_permissions',
		'delete_area_contents' => 'edit_page_contents'		
	);
	
	protected $blockTypeInheritedPermissions = array(
		'add_block_to_area' => 'add_block',
		'add_stack_to_area' => 'add_stack'
	);
	
	public function setPermissionObject(Area $a) {
		$ax = $a;
		if ($a->isGlobalArea()) {
			$cx = Stack::getByName($a->getAreaHandle());
			$a = Area::get($cx, STACKS_AREA_NAME);
		}

		$this->permissionObject = $a;
		
		// if the area overrides the collection permissions explicitly (with a one on the override column) we check
		if ($a->overrideCollectionPermissions()) {
			$this->permissionObjectToCheck = $a;
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
					$this->permissionObjectToCheck = $inheritArea;
				}
			}
			
			if (!$this->permissionObjectToCheck) { 
				$this->permissionObjectToCheck = $a->getAreaCollectionObject();
			}
		}
	}
	
	public function copyFromPageToArea() {
		$db = Loader::db();

		if (isset($this->inheritedPermissions[$this->getPermissionKeyHandle()])) {
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select peID, accessType from PagePermissionAssignments where cID = ? and pkID = ?', array(
				$this->permissionObjectToCheck->getPermissionsCollectionID(), $inheritedPKID
			));
		} else if (isset($this->blockTypeInheritedPermissions[$this->getPermissionKeyHandle()])) { 
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->blockTypeInheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select peID, accessType from BlockTypePermissionAssignments where pkID = ?', array(
				$inheritedPKID
			));
		}		
		if ($r) { 
			while ($row = $r->FetchRow()) {
				$db->Replace('AreaPermissionAssignments', array(
					'cID' => $this->permissionObject->getCollectionID(), 
					'arHandle' => $this->permissionObject->getAreaHandle(), 
					'pkID' => $this->getPermissionKeyID(),
					'accessType' => $row['accessType'],
					'peID' => $row['peID']), array('cID', 'arHandle', 'peID', 'pkID'), true);				
			}
		}
	}
	
	public static function getByID($pkID) {
		$pk = self::load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			return $pk;
		}
	}
	
	public function getAssignmentList($accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);

		if ($this->permissionObjectToCheck instanceof Area) { 
			$r = $db->Execute('select accessType, peID, pdID from AreaPermissionAssignments where cID = ? and arHandle = ? and pkID = ? ' . $filterString, array(
				$this->permissionObjectToCheck->getCollectionID(), $this->permissionObjectToCheck->getAreaHandle(), $this->getPermissionKeyID()
			));
		} else if (isset($this->inheritedPermissions[$this->getPermissionKeyHandle()])) { 
			// this is a page
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select accessType, peID, pdID from PagePermissionAssignments where cID = ? and pkID = ? ' . $filterString, array(
				$this->permissionObjectToCheck->getPermissionsCollectionID(), $inheritedPKID
			));
		} else if (isset($this->blockTypeInheritedPermissions[$this->getPermissionKeyHandle()])) { 
			// this is a page
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->blockTypeInheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select accessType, peID, pdID from BlockTypePermissionAssignments where pkID = ? ' . $filterString, array(
				$inheritedPKID
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
 			$ppa->setAccessType($row['accessType']);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setPermissionObject($this->permissionObject);
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
			'cID' => $this->permissionObject->getCollectionID(),
			'arHandle' => $this->permissionObject->getAreaHandle(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'arHandle', 'peID', 'pkID'), true);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from AreaPermissionAssignments where cID = ? and arHandle = ? and peID = ? and pkID = ?', array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		$area = $this->getPermissionObject();
		$c = $area->getAreaCollectionObject();
		return parent::getPermissionKeyToolsURL($task) . '&cID=' . $c->getCollectionID() . '&arHandle=' . $area->getAreaHandle();
	}

}

class AreaPermissionAssignment extends PermissionAssignment {




}