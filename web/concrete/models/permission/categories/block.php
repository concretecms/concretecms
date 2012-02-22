<?
defined('C5_EXECUTE') or die("Access Denied.");
class BlockPermissionKey extends PermissionKey {
	
	protected $block;
	protected $permissionsObject;
	protected $inheritedAreaPermissions = array(
		'view_block' => 'view_area',
		'edit_block' => 'edit_area_contents',
		'edit_block_custom_template' => 'edit_area_contents',
		'edit_block_design' => 'edit_area_contents',
		'edit_block_permissions' => 'edit_area_permissions',
		'delete_block' => 'edit_area_contents'		
	);
	protected $inheritedPagePermissions = array(
		'view_block' => 'view_page',
		'edit_block' => 'edit_page_contents',
		'edit_block_custom_template' => 'edit_page_contents',
		'edit_block_design' => 'edit_page_contents',
		'edit_block_permissions' => 'edit_page_permissions',
		'delete_block' => 'edit_page_contents'		
	);
	
	
	public function getBlockObject() {
		return $this->block;
	}

	public function setBlockObject(Block $b) {
		$this->block = $b;
		
		// if the area overrides the collection permissions explicitly (with a one on the override column) we check
		if ($b->overrideAreaPermissions()) {
			$this->permissionsObject = $b;
		} else {
			$a = $b->getBlockAreaObject();
			if ($a->overrideCollectionPermissions()) {
				$this->permissionsObject = $a;
			} else { 
				$this->permissionsObject = $a->getAreaCollectionObject();
			}
		}
	}
	
	public function copyFromPageOrAreaToBlock() {
		$db = Loader::db();
		if ($this->permissionsObject instanceof Page) {
			if (isset($this->inheritedPagePermissions[$this->getPermissionKeyHandle()])) {
				$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPagePermissions[$this->getPermissionKeyHandle()]));
				$r = $db->Execute('select peID, accessType from PagePermissionAssignments where cID = ? and pkID = ?', array(
					$this->permissionsObject->getCollectionID(), $inheritedPKID
				));
			}
		} else if ($this->permissionsObject instanceof Area) {
			if (isset($this->inheritedAreaPermissions[$this->getPermissionKeyHandle()])) {
				$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedAreaPermissions[$this->getPermissionKeyHandle()]));
				$r = $db->Execute('select peID, accessType from AreaPermissionAssignments where cID = ? and pkID = ?', array(
					$this->permissionsObject->getCollectionID(), $inheritedPKID
				));
			}
		}
		if (isset($r)) {
			$co = $this->block->getBlockCollectionObject();
			$arHandle = $this->block->getAreaHandle();
			while ($row = $r->FetchRow()) {
				$db->Replace('BlockPermissionAssignments', array(
					'cID' => $co->getCollectionID(), 
					'cvID' => $co->getVersionID(), 
					'arHandle' => $arHandle,
					'bID' => $this->block->getBlockID(), 
					'pkID' => $this->getPermissionKeyID(),
					'accessType' => $row['accessType'],
					'peID' => $row['peID']), array('cID', 'cvID', 'bID', 'arHandle', 'peID', 'pkID'), true);				
			}
		}
	}
	
	public static function getByID($pkID, Block $block) {
		$pk = self::load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			$pk->setBlockObject($block);
			return $pk;
		}
	}
	
	public function getAssignmentList($accessType = BlockPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		if ($this->permissionsObject instanceof Block) { 
			$co = $this->permissionsObject->getBlockCollectionObject();
			$arHandle = $this->permissionsObject->getAreaHandle();
			$r = $db->Execute('select peID, pdID from BlockPermissionAssignments where cID = ? and cvID = ? and arHandle = ? and bID = ? and accessType = ? and pkID = ?', array(
				$co->getCollectionID(), $co->getVersionID(), $arHandle, $this->block->getBlockID(), $accessType, $this->getPermissionKeyID()
			));
		} else if ($this->permissionsObject instanceof Area && isset($this->inheritedAreaPermissions[$this->getPermissionKeyHandle()])) { 
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedAreaPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select peID, 0 as pdID from AreaPermissionAssignments where cID = ? and arHandle = ? and accessType = ? and pkID = ?', array(
				$this->permissionsObject->getCollectionID(), $this->permissionsObject->getAreaHandle(), $accessType, $inheritedPKID
			));
		} else if ($this->permissionsObject instanceof Page && isset($this->inheritedPagePermissions[$this->getPermissionKeyHandle()])) { 
			// this is a page
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPagePermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select peID, 0 as pdID from PagePermissionAssignments where cID = ? and accessType = ? and pkID = ?', array(
				$this->permissionsObject->getCollectionID(), $accessType, $inheritedPKID
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
 			$ppa->setAccessType($accessType);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setBlockObject($this->block);
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

		$co = $this->block->getBlockCollectionObject();
		$arHandle = $this->block->getAreaHandle();
		
		$db->Replace('BlockPermissionAssignments', array(
			'cID' => $co->getCollectionID(),
			'cvID' => $co->getVersionID(),
			'arHandle' => $arHandle,
			'bID' => $this->block->getBlockID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'cvID', 'arHandle', 'peID', 'pkID'), true);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from BlockPermissionAssignments where cID = ? and cvID = ? and bID = ? and arHandle = ? and peID = ?', array($co->getCollectionID(), $co->getVersionID(), $this->block->getBlockID(), $a->getAreaHandle(), $pe->getAccessEntityID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		$b = $this->getBlockObject();
		$c = $b->getBlockCollectionObject();
		$arHandle = $b->getAreaHandle();
		return parent::getPermissionKeyToolsURL($task) . '&cID=' . $c->getCollectionID() . '&cvID=' . $c->getVersionID() . '&bID=' . $b->getBlockID() . '&arHandle=' . $arHandle;
	}

}

class BlockPermissionAssignment extends PermissionAssignment {

	public function setBlockObject($block) {
		$this->block = $block;
	}


}