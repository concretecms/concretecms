<?
defined('C5_EXECUTE') or die("Access Denied.");

class GroupCombinationPermissionAccessEntity extends PermissionAccessEntity {
	
	protected $groups = array();
	
	public function getGroups() {
		return $this->groups;
	}
	
	public function getAccessEntityTypeLinkHTML() {
		$html = '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/permissions/dialogs/access/entity/types/group_combination" dialog-width="400" dialog-height="300" class="dialog-launch" dialog-modal="false" dialog-title="' . t('Add Group Combination') . '">' . t('Group Combination') . '</a>';
		return $html;
	}

	public static function getOrCreate($groups) {
		$db = Loader::db();
		$petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = \'group_combination\'');
		$q = 'select pae.peID from PermissionAccessEntities pae ';
		$i = 1;
		foreach($groups as $g) {
			$q .= 'left join PermissionAccessEntityGroups paeg' . $i . ' on pae.peID = paeg' . $i . '.peID ';
			$i++;
		}
		$q .= 'where petID = ? ';
		$i = 1;
		foreach($groups as $g) {
			$q .= 'and paeg' . $i . '.gID = ' . $g->getGroupID() . ' ';
			$i++;
		}
		$peID = $db->GetOne($q, array($petID));
		if (!$peID) { 
			$db->Execute("insert into PermissionAccessEntities (petID) values (?)", array($petID));
			$peID = $db->Insert_ID();
			foreach($groups as $g) {
				$db->Execute('insert into PermissionAccessEntityGroups (peID, gID) values (?, ?)', array($peID, $g->getGroupID()));
			}
		}
		return PermissionAccessEntity::getByID($peID);
	}
	
	public function getAccessEntityUsers() {
		$gl = new GroupList();
		foreach($this->groups as $g) {
			$gl->filterByGroupID($g->getGroupID());
		}
		return $gl->get();
	}

	public function load() {
		$db = Loader::db();
		$gIDs = $db->GetCol('select gID from PermissionAccessEntityGroups where peID = ? order by gID asc', array($this->peID));
		if ($gIDs && is_array($gIDs)) {
			for ($i = 0; $i < count($gIDs); $i++) { 
				$g = Group::getByID($gIDs[$i]);
				if (is_object($g)) {
					$this->groups[] = $g;
					$this->label .= $g->getGroupName();
					if ($i + 1 < count($gIDs)) {
						$this->label .= t(' + ');
					}
				}
			}
		}
	}

}