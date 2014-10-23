<?php
namespace Concrete\Core\Permission\Access\Entity;
use Loader;
use PermissionAccess;
use URL;
use Config;
use Concrete\Core\User\Group\GroupSet;
class GroupSetEntity extends Entity {

	protected $groupset;

	public function getGroupSet() {
		return $this->groupset;
	}

	public function getAccessEntityTypeLinkHTML() {
		$html = '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/permissions/dialogs/access/entity/types/group_set" dialog-width="400" dialog-height="300" class="dialog-launch" dialog-modal="false" dialog-title="' . t('Add Group Set') . '">' . tc('PermissionAccessEntityTypeName', 'Group Set') . '</a>';
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
		$peIDs = $db->GetCol('select peID from PermissionAccessEntityGroupSets paegs inner join GroupSetGroups gsg on paegs.gsID = gsg.gsID where gsg.gID in (' . $instr . ')');
		if (is_array($peIDs)) {
			foreach($peIDs as $peID) {
				$entity = Entity::getByID($peID);
				if (is_object($entity)) {
					$entities[] = $entity;
				}
			}
		}

		return $entities;
	}

	public function getAccessEntityUsers(PermissionAccess $pa) {
		if (!isset($this->groupset)) {
			$this->load();
		}
		$groups = $this->groupset->getGroups();
		$users = array();
		$ingids = array();
		$db = Loader::db();
		foreach($groups as $group) {
			$ingids[] = $group->getGroupID();
		}
		$instr = implode(',',$ingids);
		$r = $db->Execute('select uID from UserGroups where gID in (' . $instr . ')');
		$users = array();
		while ($row = $r->FetchRow()) {
			$ui = UserInfo::getByID($row['uID']);
			if (is_object($ui)) {
				$users[] = $ui;
			}
		}
		return $users;
	}

	public static function getOrCreate(GroupSet $gs) {
		$db = Loader::db();
		$petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = \'group_set\'');
		$peID = $db->GetOne('select pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityGroupSets paeg on pae.peID = paeg.peID where petID = ? and paeg.gsID = ?',
			array($petID, $gs->getGroupSetID()));
		if (!$peID) {
			$db->Execute("insert into PermissionAccessEntities (petID) values(?)", array($petID));
			Config::save('concrete.misc.access_entity_updated', time());
			$peID = $db->Insert_ID();
			$db->Execute('insert into PermissionAccessEntityGroupSets (peID, gsID) values (?, ?)', array($peID, $gs->getGroupSetID()));
		}
		return \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
	}

	public function load() {
		$db = Loader::db();
		$gsID = $db->GetOne('select gsID from PermissionAccessEntityGroupSets where peID = ?', array($this->peID));
		if ($gsID) {
			$gs = GroupSet::getByID($gsID);
			if (is_object($gs)) {
				$this->groupset = $gs;
				$this->label = $gs->getGroupSetDisplayName();
			}
		}
	}

}
