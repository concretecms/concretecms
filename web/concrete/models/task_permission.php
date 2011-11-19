<?

class TaskPermissionList extends Object {
	
	protected $tasks = array();
	
	public function add($tp) {
		$this->tasks[] = $tp;
	}
	
	public function getTaskPermissionIDs() {
		$tps = array();
		foreach($this->tasks as $tp) {
			$tps[] = $tp->getTaskPermissionID();
		}
		return $tps;
	}
	
	public function populatePackagePermissions($pkg) {
		$db = Loader::db();
		$r = $db->Execute('select tpID from TaskPermissions where pkgID = ? order by tpID asc', array($pkg->getPackageID()));
		$this->tasks = array();
		while ($row = $r->FetchRow()) {
			$this->tasks[] = TaskPermission::getByID($row['tpID']);
		}
		$r->Close();
		return $this->tasks;
	}
	
	public function getTaskPermissions() {return $this->tasks;}
	
	public static function export($xml) {
		$taskpermissions = $xml->addChild("taskpermissions");
		$db = Loader::db();
		$r = $db->Execute('select tpID from TaskPermissions order by tpID asc');
		while ($row = $r->FetchRow()) {
			$tp = TaskPermission::getByID($row['tpID']);
			if (is_object($tp)) {
				$tp->export($taskpermissions);
			}
		}
	}
	
}

class TaskPermission extends Object {
	
	public function getTaskPermissionID() {return $this->tpID;}
	public function getTaskPermissionName() {return $this->tpName;}
	public function getTaskPermissionHandle() {return $this->tpHandle;}
	public function getTaskPermissionDescription() {return $this->tpDescription;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}
	
	public function clearPermissions() {
		$db = Loader::db();
		$db->Execute('delete from TaskPermissionUserGroups where tpID = ?', $this->tpID);	
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from TaskPermissions where tpID = ?', $this->tpID);
		$db->Execute('delete from TaskPermissionUserGroups where tpID = ?', $this->tpID);
	}
	
	public static function getByHandle($tpHandle) {
		$db = Loader::db();
		$row = $db->GetRow("select tpID, tpHandle, tpName, tpDescription, pkgID from TaskPermissions where tpHandle = ?", array($tpHandle));
		if (isset($row['tpID'])) {
			$tp = new TaskPermission();
			$tp->setPropertiesFromArray($row);
			return $tp;
		}
	}

	public static function getByID($tpID) {
		$db = Loader::db();
		$row = $db->GetRow("select tpID, tpHandle, tpName, tpDescription, pkgID from TaskPermissions where tpID = ?", array($tpID));
		if (isset($row['tpID'])) {
			$tp = new TaskPermission();
			$tp->setPropertiesFromArray($row);
			return $tp;
		}
	}

	public function export($xml) {
		$tpc = $xml->addChild("taskpermission");	
		$tpc->addAttribute('handle', $this->getTaskPermissionHandle());
		$tpc->addAttribute('name', $this->getTaskPermissionName());
		$tpc->addAttribute('description', $this->getTaskPermissionDescription());
		$tpc->addAttribute("package", $this->getPackageHandle());
		$db = Loader::db();
		$rows = $db->GetAll('select * from TaskPermissionUserGroups where tpID = ?', array($this->getTaskPermissionID()));
		foreach($rows as $r) {
			$access = $tpc->addChild('access');
			if ($r['gID'] > 0) {
				$g = Group::getByID($r['gID']);
				$node = $access->addChild('group');
				$node->addAttribute('name', $g->getGroupName());
			}
			if ($r['uID'] > 0) {
				$g = UserInfo::getByID($r['uID']);
				$node = $access->addChild('user');
				$node->addAttribute('name', $ui->getUserName());
			}
		}
	}		
	
	public function addAccess($obj) {
		$uID = 0;
		$gID = 0;
		$db = Loader::db();
		if (is_a($obj, 'UserInfo')) {
			$uID = $obj->getUserID();
		} else {
			$gID = $obj->getGroupID();
		}
		
		$db->Replace('TaskPermissionUserGroups', array(
			'tpID' => $this->tpID,
			'uID' => $uID, 
			'gID' => $gID,
			'canRead' => 1
		),
		array('tpID', 'gID', 'uID'), true);		
	}
	
	public function removeAccess($obj) {
		$db = Loader::db();
		if (is_a($obj, 'UserInfo')) {
			$uID = $obj->getUserID();
			$db->Execute('delete from TaskPermissionUserGroups where uID = ?', $obj->getUserID());
		} else {
			$db->Execute('delete from TaskPermissionUserGroups where gID = ?', $obj->getGroupID());
		}		
	}
	
	public static function addTask($tpHandle, $tpName, $tpDescription, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		
		if (!$tpDescription) {
			$tpDescription = '';
		}
		
		$db = Loader::db();
		$v = array($tpHandle, $tpName, $tpDescription, $pkgID);
		$db->Execute('insert into TaskPermissions (tpHandle, tpName, tpDescription, pkgID) values (? ,?, ?, ?)', $v);
		
		$id = $db->Insert_ID();
		if ($id > 0) {
			return TaskPermission::getByID($id);
		}
	}
	
	public function can($obj = false) {
		if (!$this->tpID) {
			return false;
		}
		
		$db = Loader::db();
		
		if ($obj) {
			if (is_a($obj, 'Group')) {
				$r = $db->GetOne("select count(tpID) from TaskPermissionUserGroups where tpID = {$this->tpID} and canRead = 1 and gID = ?", $obj->getGroupID());
			} else {
				$r = $db->GetOne("select count(tpID) from TaskPermissionUserGroups where tpID = {$this->tpID} and canRead = 1 and uID = ?", $obj->getUserID());
			}
		} else {
			// check against logged in user
			$u = new User();
			if ($u->isSuperUser()) {
				return true;
			}
			
			$groups = $u->getUserGroups();
			$groupIDs = array();
			foreach($groups as $key => $value) {
				$groupIDs[] = $key;
			}
			
			$uID = -1;
			if ($u->isRegistered()) {
				$uID = $u->getUserID();
			}
	
			// checks based on uID and gIDs
			$r = $db->GetOne("select count(tpID) from TaskPermissionUserGroups where tpID = {$this->tpID} and canRead = 1 and (gID in (" . implode(',', $groupIDs) . ") or uID = " . $uID . ")");
		}
		
		return $r > 0;	
	}
	
	public function __call($nm, $a) {
		if (substr($nm, 0, 3) == 'can') {
			$txt = Loader::helper('text');
			$permission = $txt->uncamelcase(substr($nm, 3));
			$tp = TaskPermission::getByHandle($permission);
			if (is_object($tp)) {
				return $tp->can();
			} else {
				throw new Exception(t('Invalid task permission.'));
			}
		}
	}


}