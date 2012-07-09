<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_BlockPermissionAssignment extends PermissionAssignment {
	
		
	protected $permissionObjectToCheck;
	protected $inheritedAreaPermissions = array(
		'view_block' => 'view_area',
		'edit_block' => 'edit_area_contents',
		'edit_block_custom_template' => 'edit_area_contents',
		'edit_block_design' => 'edit_area_contents',
		'edit_block_permissions' => 'edit_area_permissions',
		'schedule_guest_access' => 'schedule_area_contents_guest_access',
		'delete_block' => 'delete_area_contents'		
	);
	protected $inheritedPagePermissions = array(
		'view_block' => 'view_page',
		'edit_block' => 'edit_page_contents',
		'edit_block_custom_template' => 'edit_page_contents',
		'edit_block_design' => 'edit_page_contents',
		'edit_block_permissions' => 'edit_page_permissions',
		'schedule_guest_access' => 'schedule_page_contents_guest_access',
		'delete_block' => 'edit_page_contents'		
	);
	
	public function setPermissionObject(Block $b) {
		$this->permissionObject = $b;
		
		// if the area overrides the collection permissions explicitly (with a one on the override column) we check
		if ($b->overrideAreaPermissions()) {
			$this->permissionObjectToCheck = $b;
		} else {
			$a = $b->getBlockAreaObject();
			if (is_object($a)) {
				if ($a->overrideCollectionPermissions()) {
					$this->permissionObjectToCheck = $a;
				} else {
					$this->permissionObjectToCheck = $a->getAreaCollectionObject();			
				}
			} else { 
				$this->permissionObjectToCheck = Page::getCurrentPage();
			}
		}
	}

	public function getPermissionAccessObject() {
		$db = Loader::db();
		if ($this->permissionObjectToCheck instanceof Block) { 
			$co = $this->permissionObjectToCheck->getBlockCollectionObject();
			$arHandle = $this->permissionObjectToCheck->getAreaHandle();
			$paID = $db->GetOne('select paID from BlockPermissionAssignments where cID = ? and cvID = ? and bID = ? and pkID = ? ' . $filterString, array(
				$co->getCollectionID(), $co->getVersionID(), $this->permissionObject->getBlockID(), $this->pk->getPermissionKeyID()
			));
			$pae = PermissionAccess::getByID($paID, $this->pk);
		} else if ($this->permissionObjectToCheck instanceof Area && isset($this->inheritedAreaPermissions[$this->pk->getPermissionKeyHandle()])) { 

			$pk = PermissionKey::getByHandle($this->inheritedAreaPermissions[$this->pk->getPermissionKeyHandle()]);
			$pk->setPermissionObject($this->permissionObjectToCheck);
			$pae = $pk->getPermissionAccessObject();			

		} else if ($this->permissionObjectToCheck instanceof Page && isset($this->inheritedPagePermissions[$this->pk->getPermissionKeyHandle()])) { 
			$pk = PermissionKey::getByHandle($this->inheritedPagePermissions[$this->pk->getPermissionKeyHandle()]);
			$pk->setPermissionObject($this->permissionObjectToCheck);
			$pae = $pk->getPermissionAccessObject();			

		}
		return $pae;
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$co = $this->permissionObject->getBlockCollectionObject();
		$db->Execute('update BlockPermissionAssignments set paID = 0 where pkID = ? and bID = ? and cvID = ? and cID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getBlockID(), $co->getVersionID(), $co->getCollectionID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$co = $this->permissionObject->getBlockCollectionObject();
		$arHandle = $this->permissionObject->getAreaHandle();
		$db->Replace('BlockPermissionAssignments', array(
			'cID' => $co->getCollectionID(), 
			'paID' => $pa->getPermissionAccessID(), 
			'cvID' => $co->getVersionID(),
			'bID' => $this->permissionObject->getBlockID(),
			'pkID' => $this->pk->getPermissionKeyID()), array('cID', 'cvID', 'bID', 'pkID'), true);
		$pa->markAsInUse();
	}
	
	
	public function getPermissionKeyToolsURL($task = false) {
		$b = $this->getPermissionObject();
		$c = $b->getBlockCollectionObject();
		$arHandle = $b->getAreaHandle();
		return parent::getPermissionKeyToolsURL($task) . '&cID=' . $c->getCollectionID() . '&cvID=' . $c->getVersionID() . '&bID=' . $b->getBlockID() . '&arHandle=' . $arHandle;
	}

	
}