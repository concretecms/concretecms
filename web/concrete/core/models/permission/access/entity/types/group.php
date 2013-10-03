<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_GroupPermissionAccessEntity extends PermissionAccessEntity {

	protected $group = false;
	public function getGroupObject() {return $this->group;}

	public function getAccessEntityUsers(PermissionAccess $pa) {
		return $this->group->getGroupMembers();
	}
	
	public function getAccessEntityTypeLinkHTML() {
		$html = '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/select_group?include_core_groups=1" class="dialog-launch" dialog-modal="false" dialog-title="' . t('Add Group') . '">' . tc('PermissionAccessEntityTypeName', 'Group') . '</a>';
		return $html;
	}

	public static function getAccessEntitiesForUser($user) {
		$entities = array();
		$ingids = array();
		$db = Loader::db();
		foreach($user->getUserGroups() as $key => $val) {
			$ingids[] = $key;
		}
		$instr = implode(',',$ingids);
		$peIDs = $db->GetCol('select pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityTypes paet on pae.petID = paet.petID inner join PermissionAccessEntityGroups paeg on pae.peID = paeg.peID where petHandle = \'group\' and paeg.gID in (' . $instr . ')');
		if (is_array($peIDs)) {
			foreach($peIDs as $peID) { 
				$entity = PermissionAccessEntity::getByID($peID);
				if (is_object($entity)) { 
					$entities[] = $entity;
				}
			}
		}

		return $entities;		
	}
	
	public static function getOrCreate(Group $g) {
		$db = Loader::db();
		$petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = \'group\'');
		$peID = $db->GetOne('select pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityGroups paeg on pae.peID = paeg.peID where petID = ? and paeg.gID = ?', 
			array($petID, $g->getGroupID()));
		if (!$peID) { 
			$db->Execute("insert into PermissionAccessEntities (petID) values(?)", array($petID));
			$peID = $db->Insert_ID();
			Config::save('ACCESS_ENTITY_UPDATED', time());
			$db->Execute('insert into PermissionAccessEntityGroups (peID, gID) values (?, ?)', array($peID, $g->getGroupID()));
		}
		return PermissionAccessEntity::getByID($peID);
	}
	
	public function load() {
		$db = Loader::db();
		$gID = $db->GetOne('select gID from PermissionAccessEntityGroups where peID = ?', array($this->peID));
		if ($gID) {
			$g = Group::getByID($gID);
			if (is_object($g)) {
				$this->group = $g;
				$this->label = $g->getGroupName();
			} else {
				$this->label = t('(Deleted Group)');
			}
		}
	}
}
