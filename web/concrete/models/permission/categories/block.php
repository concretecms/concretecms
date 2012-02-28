<?
defined('C5_EXECUTE') or die("Access Denied.");
class BlockPermissionKey extends PermissionKey {
	
	protected $permissionObjectToCheck;
	protected $inheritedAreaPermissions = array(
		'view_block' => 'view_area',
		'edit_block' => 'edit_area_contents',
		'edit_block_custom_template' => 'edit_area_contents',
		'edit_block_design' => 'edit_area_contents',
		'edit_block_permissions' => 'edit_area_permissions',
		'delete_block' => 'delete_area_contents'		
	);
	protected $inheritedPagePermissions = array(
		'view_block' => 'view_page',
		'edit_block' => 'edit_page_contents',
		'edit_block_custom_template' => 'edit_page_contents',
		'edit_block_design' => 'edit_page_contents',
		'edit_block_permissions' => 'edit_page_permissions',
		'delete_block' => 'edit_page_contents'		
	);
	
	public function setPermissionObject(Block $b) {
		$this->permissionObject = $b;
		
		// if the area overrides the collection permissions explicitly (with a one on the override column) we check
		if ($b->overrideAreaPermissions()) {
			$this->permissionObjectToCheck = $b;
		} else {
			$a = $b->getBlockAreaObject();
			if ($a->overrideCollectionPermissions()) {
				$this->permissionObjectToCheck = $a;
			} else { 
				$this->permissionObjectToCheck = $a->getAreaCollectionObject();
			}
		}
	}
	
	public function copyFromPageOrAreaToBlock() {
		$db = Loader::db();
		if ($this->permissionObjectToCheck instanceof Page) {
			if (isset($this->inheritedPagePermissions[$this->getPermissionKeyHandle()])) {
				$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPagePermissions[$this->getPermissionKeyHandle()]));
				$r = $db->Execute('select peID, accessType from PagePermissionAssignments where cID = ? and pkID = ?', array(
					$this->permissionObjectToCheck->getPermissionsCollectionID(), $inheritedPKID
				));
			}
		} else if ($this->permissionObjectToCheck instanceof Area) {
			if (isset($this->inheritedAreaPermissions[$this->getPermissionKeyHandle()])) {
				$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedAreaPermissions[$this->getPermissionKeyHandle()]));
				$r = $db->Execute('select peID, accessType from AreaPermissionAssignments where cID = ? and pkID = ?', array(
					$this->permissionObjectToCheck->getCollectionID(), $inheritedPKID
				));
			}
		}
		if (isset($r)) {
			$co = $this->permissionObject->getBlockCollectionObject();
			$arHandle = $this->permissionObject->getAreaHandle();
			while ($row = $r->FetchRow()) {
				$db->Replace('BlockPermissionAssignments', array(
					'cID' => $co->getCollectionID(), 
					'cvID' => $co->getVersionID(), 
					'arHandle' => $arHandle,
					'bID' => $this->permissionObject->getBlockID(), 
					'pkID' => $this->getPermissionKeyID(),
					'accessType' => $row['accessType'],
					'peID' => $row['peID']), array('cID', 'cvID', 'bID', 'arHandle', 'peID', 'pkID'), true);				
			}
		}
	}
	
	public static function getByID($pkID) {
		$pk = self::load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			return $pk;
		}
	}
	
	public function getAssignmentList($accessType = BlockPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
		if ($this->permissionObjectToCheck instanceof Block) { 
			$co = $this->permissionObjectToCheck->getBlockCollectionObject();
			$arHandle = $this->permissionObjectToCheck->getAreaHandle();
			$r = $db->Execute('select accessType, peID, pdID from BlockPermissionAssignments where cID = ? and cvID = ? and arHandle = ? and bID = ? and pkID = ? ' . $filterString, array(
				$co->getCollectionID(), $co->getVersionID(), $arHandle, $this->permissionObject->getBlockID(), $this->getPermissionKeyID()
			));
		} else if ($this->permissionObjectToCheck instanceof Area && isset($this->inheritedAreaPermissions[$this->getPermissionKeyHandle()])) { 
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedAreaPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select accessType, peID, pdID from AreaPermissionAssignments where cID = ? and arHandle = ? and pkID = ? ' . $filterString, array(
				$this->permissionObjectToCheck->getCollectionID(), $this->permissionObjectToCheck->getAreaHandle(), $inheritedPKID
			));
		} else if ($this->permissionObjectToCheck instanceof Page && isset($this->inheritedPagePermissions[$this->getPermissionKeyHandle()])) { 
			// this is a page
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPagePermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select accessType, peID, pdID from PagePermissionAssignments where cID = ? and pkID = ? ' . $filterString, array(
				$this->permissionObjectToCheck->getPermissionsCollectionID(), $inheritedPKID
			));
		} else {
			return array();
		}

 		$list = array();
 		$class = str_replace('BlockPermissionKey', 'BlockPermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'BlockPermissionAssignment';
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
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = BlockPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}

		$co = $this->permissionObject->getBlockCollectionObject();
		$arHandle = $this->permissionObject->getAreaHandle();
		
		$db->Replace('BlockPermissionAssignments', array(
			'cID' => $co->getCollectionID(),
			'cvID' => $co->getVersionID(),
			'arHandle' => $arHandle,
			'bID' => $this->permissionObject->getBlockID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'cvID', 'arHandle', 'peID', 'pkID'), true);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$co = $this->permissionObject->getBlockCollectionObject();
		$arHandle = $this->permissionObject->getAreaHandle();
		$db->Execute('delete from BlockPermissionAssignments where cID = ? and cvID = ? and bID = ? and arHandle = ? and peID = ? and pkID = ?', array($co->getCollectionID(), $co->getVersionID(), $this->permissionObject->getBlockID(), $arHandle, $pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		$b = $this->getPermissionObject();
		$c = $b->getBlockCollectionObject();
		$arHandle = $b->getAreaHandle();
		return parent::getPermissionKeyToolsURL($task) . '&cID=' . $c->getCollectionID() . '&cvID=' . $c->getVersionID() . '&bID=' . $b->getBlockID() . '&arHandle=' . $arHandle;
	}

}

class BlockPermissionAssignment extends PermissionAssignment {



}