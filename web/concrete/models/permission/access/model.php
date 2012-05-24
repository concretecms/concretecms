<?
defined('C5_EXECUTE') or die("Access Denied.");

class PermissionAccess extends Object {
	
	public function getPermissionAccessID() {return $this->paID;}
	public function isPermissionAccessInUse() {return $this->paIsInUse;}
	public function getAccessListItems($accessType = false, $filterEntities = array()) {

		$db = Loader::db();
		$class = get_called_class();
		$class = str_replace('PermissionAccess', 'PermissionAccessListItem', $class);
		if (!class_exists($class)) {
			$class = 'PermissionAccessListItem';
		}

		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
 		$r = $db->Execute('select peID, pdID, accessType from PermissionAccessList where paID = ? ' . $filterString, array(
 			$this->getPermissionAccessID()
 		));
 		$list = array();

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

	public static function create() {
		$db = Loader::db();
		$class = get_called_class();
		$db->Execute('insert into PermissionAccess (paIsInUse) values (0)');
		return call_user_func_array(array($class, 'getByID'), array($db->Insert_ID()));
	}
	
	public static function getByID($paID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from PermissionAccess where paID = ?', array($paID));
		if ($row['paID']) {
			$class = get_called_class();
			$obj = new $class();
			$obj->setPropertiesFromArray($row);
			return $obj;
		}
	}
	
}