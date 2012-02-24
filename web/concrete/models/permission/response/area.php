<?
defined('C5_EXECUTE') or die("Access Denied.");
class AreaPermissionResponse extends PermissionResponse {
	
	// legacy support
	public function canWrite() { return in_array('edit_area_contents', $this->allowedPermissions); }
	public function canRead() { return in_array('view_area', $this->allowedPermissions);}
	public function canAddBlocks() { return in_array('add_block', $this->allowedPermissions);}
	public function canAdmin() { return in_array('edit_area_permissions', $this->allowedPermissions);}

	public function loadPermissions() {
		$u = new User();
		if ($u->isSuperUser()) {
			$this->loadSuperUserPermissions();
		} else {
			$accessEntities = $u->getUserAccessEntityObjects();
			$peIDs = array('-1');
			foreach($accessEntities as $pe) {
				$peIDs[] = $pe->getAccessEntityID();
			}
			$db = Loader::db();
			if ($this->object->overrideCollectionPermissions()) { 
				$this->allowedPermissions = $db->GetCol('select pkHandle from PermissionKeys pk inner join PagePermissionAssignments ppa where pk.pkID = ppa.pkID and ppa.cID = ? and ppaArHandle = ? and ppa.peID in (' . implode(',', $peIDs) . ')', array(
					$this->object->getCollectionID(), $this->object->getAreaHandle()
				)); 
			} else { 
				$pc = Page::getByID($this->object->getAreaCollectionInheritID());				
				$this->allowedPermissions = $db->GetCol('select pkHandle from PermissionKeys pk inner join PagePermissionAssignments ppa where pk.pkID = ppa.pkID and ppa.cID = ? and ppa.peID in (' . implode(',', $peIDs) . ')', array(
					$pc->getPermissionsCollectionID()
				)); 
			}
		}
	}
	
	
}