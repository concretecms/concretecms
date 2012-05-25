<?
defined('C5_EXECUTE') or die("Access Denied.");

class PermissionAccess extends Object {
	public function setPermissionKey($permissionKey) {
		$this->pk = $permissionKey;
	}
	
	public function getPermissionObject() {
		return $this->pk->getPermissionObject();
	}
		
	public function getPermissionAccessID() {return $this->paID;}
	public function isPermissionAccessInUse() {return $this->paIsInUse;}
	
	protected function deliverAccessListItems($q, $accessType, $filterEntities) {
		$db = Loader::db();
		$class = str_replace('PermissionKey', 'PermissionAccessListItem', get_class($this->pk));
		if (!class_exists($class)) {
			$class = 'PermissionAccessListItem';
		}
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
		$q = $q . ' ' . $filterString;
 		$list = array();
 		$r = $db->Execute($q);
		while ($row = $r->FetchRow()) {
			$obj = new $class();
			$obj->setPropertiesFromArray($row);
			if ($row['pdID']) {
				$obj->loadPermissionDurationObject($row['pdID']);
			}
			if ($row['peID']) {
				$obj->loadAccessEntityObject($row['peID']);
			}
			$list[] = $obj;
		}
 		return $list;
	}
	
	public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$q = 'select peID, pdID, accessType from PermissionAccessList where paID = ' . $this->getPermissionAccessID();
		return $this->deliverAccessListItems($q, $accessType, $filterEntities);
	}

	protected function buildAssignmentFilterString($accessType, $filterEntities) { 
		$peIDs = '';
		$filters = array();
		if (count($filterEntities) > 0) {
			foreach($filterEntities as $ent) {
				$filters[] = $ent->getAccessEntityID();
			}
			$peIDs .= 'and peID in (' . implode($filters, ',') . ')';
		}
		if ($accessType == 0) {
			$accessType = '';
		} else { 
			$accessType = ' and accessType = ' . $accessType;
		}
		return $peIDs . ' ' . $accessType . ' order by accessType desc'; // we order desc so that excludes come last (-1)
	}
	
	public function duplicate() {
		$db = Loader::db();
		$newPA = self::create();
		$listItems = $this->getAccessListItems();
		foreach($listItems as $li) {
			$newPA->addListItem($li->getAccessEntityObject(), $li->getPermissionDurationObject(), $li->getAccessType());
		}
		$newPA->setPermissionKey($this->pk);
		return $newPA;
	}

	public function addListItem(PermissionAccessEntity $pae, $durationObject = false, $accessType = PermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('PermissionAccessList', array(
			'paID' => $this->getPermissionAccessID(),
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('paID','peID'), false);
	}

	public function removeListItem(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from PermissionAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));	
	}
	
	public function save() {}

	public static function create(PermissionKey $pk) {
		$db = Loader::db();
		$class = get_called_class();
		$db->Execute('insert into PermissionAccess (paIsInUse) values (0)');
		return call_user_func_array(array($class, 'getByID'), array($db->Insert_ID(), $pk));
	}
	
	public static function getByID($paID, PermissionKey $pk) {
		$db = Loader::db();
		$row = $db->GetRow('select * from PermissionAccess where paID = ?', array($paID));
		if ($row['paID']) {
			$class = str_replace('PermissionKey', 'PermissionAccess', get_class($pk));
			$obj = new $class();
			$obj->setPropertiesFromArray($row);
			$obj->setPermissionKey($pk);
			return $obj;
		}
	}
	
}