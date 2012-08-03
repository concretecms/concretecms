<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PermissionAccess extends Object {
	
	protected $paID;
	protected $paIDList = array();
	
	public function setPermissionKey($permissionKey) {
		$this->pk = $permissionKey;
	}
	
	public function getPermissionObject() {
		return $this->pk->getPermissionObject();
	}

	public function getPermissionObjectToCheck() {
		return $this->pk->getPermissionObjectToCheck();
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
	
	public function validateAndFilterAccessEntities($accessEntities) {
		$entities = array();
		foreach($accessEntities as $ae) {
			if ($ae->validate($this)) {
				$entities[] = $ae;
			}
		}
		return $entities;
	}
	
	public function validateAccessEntities($accessEntities) {
		$valid = false;
		$accessEntities = $this->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$valid = true;
			}
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
				$valid = false;
			}
		}
		return $valid;
	}
	
	public function validate() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		$accessEntities = $u->getUserAccessEntityObjects();
		return $this->validateAccessEntities($accessEntities);
	}
	
	public static function createByMerge($permissions) {
		$class = get_class($permissions[0]);
		$p = new $class();
		foreach($permissions as $px) {
			$p->paIDList[] = $px->getPermissionAccessID();
		}
		$p->pk = $permissions[0]->pk;
		$p->paID = -1;
		return $p;
	}
	
	public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		if (count($this->paIDList) > 0) {
			$q = 'select paID, peID, pdID, accessType from PermissionAccessList where paID in (' . implode(',', $this->paIDList) . ')';
			return $this->deliverAccessListItems($q, $accessType, $filterEntities);
		} else {
			$filter = $accessType . ':';
			foreach($filterEntities as $pae) {
				$filter .= $pae->getAccessEntityID() . ':';
			}
			$filter = trim($filter, ':');
			$items = CacheLocal::getEntry('permission_access_list_items', $this->getPermissionAccessID() . $filter . strtolower(get_class($this->pk)));
			if (is_array($items)) {
				return $items;
			}				
			$q = 'select paID, peID, pdID, accessType from PermissionAccessList where paID = ' . $this->getPermissionAccessID();
			$items = $this->deliverAccessListItems($q, $accessType, $filterEntities);
			CacheLocal::set('permission_access_list_items', $this->getPermissionAccessID() . $filter . strtolower(get_class($this->pk)), $items);
			return $items;
		}
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
	
	public function clearWorkflows() {
		$db = Loader::db();
		$db->Execute('delete from PermissionAccessWorkflows where paID = ?', array($this->getPermissionAccessID()));
	}

	public function attachWorkflow(Workflow $wf) {
		$db = Loader::db();
		$db->Replace('PermissionAccessWorkflows', array('paID' => $this->getPermissionAccessID(), 'wfID' => $wf->getWorkflowID()), array('paID', 'wfID'), true);
	}	

	public function getWorkflows() {
		$db = Loader::db();
		$r = $db->Execute('select wfID from PermissionAccessWorkflows where paID = ?', array($this->getPermissionAccessID()));
		$workflows = array();
		while ($row = $r->FetchRow()) {
			$wf = Workflow::getByID($row['wfID']);
			if (is_object($wf)) {
				$workflows[] = $wf;
			}
		}
		return $workflows;
	}

	public function duplicate($newPA = false) {
		$db = Loader::db();
		if (!$newPA) {
			$newPA = self::create($this->pk);
		}
		$listItems = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL);
		foreach($listItems as $li) {
			$newPA->addListItem($li->getAccessEntityObject(), $li->getPermissionDurationObject(), $li->getAccessType());
		}
		$workflows = $this->getWorkflows();
		foreach($workflows as $wf) {
			$newPA->attachWorkflow($wf);
		}
		$newPA->setPermissionKey($this->pk);
		return $newPA;
	}

	public function markAsInUse() {
		$db = Loader::db();
		$db->Execute('update PermissionAccess set paIsInUse = 1 where paID = ?', array($this->paID));
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
		$db->Execute('insert into PermissionAccess (paIsInUse) values (0)');
		return PermissionAccess::getByID($db->Insert_ID(), $pk);
	}
	
	public static function getByID($paID, PermissionKey $pk) {
		$db = Loader::db();
		$row = $db->GetRow('select paID, paIsInUse from PermissionAccess where paID = ?', array($paID));
		if ($row['paID']) {
			$class = str_replace('PermissionKey', 'PermissionAccess', get_class($pk));
			$obj = new $class();
			$obj->setPropertiesFromArray($row);
			$obj->setPermissionKey($pk);
			return $obj;
		}
	}
	
}