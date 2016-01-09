<?php
namespace Concrete\Core\Permission\Access\Entity;
use Loader;
use Config;
use URL;
use PermissionAccess;
use Group;
class GroupCombinationEntity extends Entity {

	protected $groups = array();

	public function getGroups() {
		return $this->groups;
	}

	public function getAccessEntityTypeLinkHTML() {
		$html = '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/permissions/dialogs/access/entity/types/group_combination" dialog-width="400" dialog-height="300" class="dialog-launch" dialog-modal="false" dialog-title="' . t('Add Group Combination') . '">' . tc('PermissionAccessEntityTypeName', 'Group Combination') . '</a>';
		return $html;
	}

	public static function getAccessEntitiesForUser($user) {
		// finally, the most brutal one. we find any combos that this group would specifically be in.
		// first, we look for any combos that contain any of the groups this user is in. That way if there aren't any we can just skip it.
		$db = Loader::db();
		$ingids = array();
		$db = Loader::db();
		foreach($user->getUserGroups() as $key => $val) {
			$ingids[] = $key;
		}
		$instr = implode(',',$ingids);
		$entities = array();
		if ($user->isRegistered()) {
			$peIDs = $db->GetCol('select distinct pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityTypes paet on pae.petID = paet.petID inner join PermissionAccessEntityGroups paeg on pae.peID = paeg.peID where petHandle = \'group_combination\' and paeg.gID in (' . $instr . ')');
			// now for each one we check to see if it applies
			foreach($peIDs as $peID) {
				$r = $db->GetRow('select count(gID) as peGroups, (select count(UserGroups.gID) from UserGroups where uID = ? and gID in (select gID from PermissionAccessEntityGroups where peID = ?)) as uGroups from PermissionAccessEntityGroups where peID = ?', array(
					$user->getUserID(), $peID, $peID));
				if ($r['peGroups'] == $r['uGroups'] && $r['peGroups'] > 1) {
					$entity = Entity::getByID($peID);
					if (is_object($entity)) {
						$entities[] = $entity;
					}
				}
			}
		}
		return $entities;
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
			Config::save('concrete.misc.access_entity_updated', time());
			foreach($groups as $g) {
				$db->Execute('insert into PermissionAccessEntityGroups (peID, gID) values (?, ?)', array($peID, $g->getGroupID()));
			}
		}
		return \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
	}

	public function getAccessEntityUsers(PermissionAccess $pa) {
		$gl = new UserList();
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
					$this->label .= $g->getGroupDisplayName();
					if ($i + 1 < count($gIDs)) {
						$this->label .= t(' + ');
					}
				}
			}
		}
	}

}
