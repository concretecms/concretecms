<?php
namespace Concrete\Core\Permission\Access\Entity;
use Loader;
use Group;
use Config;
use PermissionAccess;
use URL;
class GroupEntity extends Entity {

	protected $group = false;
	public function getGroupObject() {return $this->group;}

	public function getAccessEntityUsers(PermissionAccess $pa) {
		return $this->group->getGroupMembers();
	}

	public function getAccessEntityTypeLinkHTML() {
		$html = '<a href="' . URL::to('/ccm/system/dialogs/group/search') . '" class="dialog-launch" dialog-width="640" dialog-height="480" dialog-modal="false" dialog-title="' . t('Add Group') . '">' . tc('PermissionAccessEntityTypeName', 'Group') . '</a>';
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
				$entity = \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
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
			Config::save('concrete.misc.access_entity_updated', time());
			$db->Execute('insert into PermissionAccessEntityGroups (peID, gID) values (?, ?)', array($peID, $g->getGroupID()));
		}
		return \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
	}

	public function load() {
		$db = Loader::db();
		$gID = $db->GetOne('select gID from PermissionAccessEntityGroups where peID = ?', array($this->peID));
		if ($gID) {
			$g = Group::getByID($gID);
			if (is_object($g)) {
				$this->group = $g;
				$this->label = $g->getGroupDisplayName();
			} else {
				$this->label = t('(Deleted Group)');
			}
		}
	}
}
