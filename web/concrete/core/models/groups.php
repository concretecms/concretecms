<?

	class Concrete5_Model_GroupList extends Object {

		public $gArray = array();

		/**
		 * Get all groups should only really be run when you're sure there aren't a million groups in the system
		 */
		function __construct($obj, $omitRequiredGroups = false, $getAllGroups = false) {
			if ($getAllGroups) {
				$db = Loader::db();
				$minGID = ($omitRequiredGroups) ? 2 : 0;
				$q = "select gID from Groups where gID > $minGID order by gID asc";	
				$r = $db->Execute($q);
				while ($row = $r->FetchRow()) {
					$g = Group::getByID($row['gID']);
					$g->setPermissionsForObject($obj);
					if(!in_array($g,$this->gArray)) 
						$this->gArray[] = $g;
				}
			} else {
				$groups = $this->getRelevantGroups($obj, $omitRequiredGroups);
				foreach($groups as $g) {
					if(!$g) continue;
					$g->setPermissionsForObject($obj);
					if(!in_array($g,$this->gArray)) 
						$this->gArray[] = $g;
				}
			}
		}

		protected function getRelevantGroups($obj, $omitRequiredGroups = false) {
			$db = Loader::db();
			if ($obj instanceof UserInfo) { 
				$table = 'UserGroups';
				$uID = $obj->getUserID();						
				if ($uID) {
					$where = "uID = {$uID}";
				}
			}

			$groups = array();
			if ($where) {
				$q = "select distinct gID from $table where 1=1 and {$where} and gID > 0 order by gID asc";
				$gs = $db->GetCol($q);

				if (!$omitRequiredGroups) {
					if (!in_array(GUEST_GROUP_ID, $gs)) {
						$gs[] = GUEST_GROUP_ID;
					}
					if (!in_array(REGISTERED_GROUP_ID, $gs)) {
						$gs[] = REGISTERED_GROUP_ID;
					}
				}

				sort($gs);

				foreach($gs as $gID) {
					$g = Group::getByID( $gID );
					$groups[] = $g;
				}
			}
			return $groups;
		}

		function getGroupList() {
			return $this->gArray;
		}

	}

	class Concrete5_Model_Group extends Object {
	
		var $ctID;
		var $permissionSet;
		private $permissions = array(); // more advanced version of permissions
		
		/* 
		 * Takes the numeric id of a group and returns a group object
		 * @parem string $gID
		 * @return object Group
		*/
		public static function getByID($gID) {
			$db = Loader::db();
			$g = CacheLocal::getEntry('group', $gID);
			if (is_object($g)) { 
				return $g;
			}

			$row = $db->getRow("select * from Groups where gID = ?", array($gID));
			if (isset($row['gID'])) {
				$g = new Group;
				$g->setPropertiesFromArray($row);
				CacheLocal::set('group', $gID, $g);
				return $g;
			}
		}
		
		/* 
		 * Takes the name of a group and returns a group object
		 * @parem string $gName
		 * @return object Group
		*/
		public static function getByName($gName) {
			$db = Loader::db();
			$row = $db->getRow("select * from Groups where gName = ?", array($gName));
			if (isset($row['gID'])) {
				$g = new Group;
				$g->setPropertiesFromArray($row);
				return $g;
			}
		}
		
		public function getGroupMembers($type = null) {
			$db = Loader::db();
			if ($type != null) {
				$r = $db->query("select uID, type from UserGroups where gID = ? and type = ?", array($this->gID, $type));
			} else {
				$r = $db->query("select uID, type from UserGroups where gID = ?", array($this->gID));
			}
			
			
			$members = array();
			while ($row = $r->fetchRow()) {
				$ui = UserInfo::getByID($row['uID']);
				$ui->setGroupMemberType($row['type']);
				$members[] = $ui;
			}
			return $members;			
		}

		public function setPermissionsForObject($obj) {
			$this->pObj = $obj;
			$db = Loader::db();
			if ($obj instanceof UserInfo) { 
				$uID = $this->pObj->getUserID();						
				if ($uID) {
					$q = "select gID, ugEntered, UserGroups.type from UserGroups where gID = '{$this->gID}' and uID = {$uID}";
					$r = $db->query($q);
					if ($r) {
						$row = $r->fetchRow();
						if ($row['gID']) {
							$this->inGroup = true;
							$this->gDateTimeEntered = $row['ugEntered'];
							$this->gMemberType = $row['type'];
						}
					}
				}
			}
		}
		
		public function getGroupMembersNum($type = null) {
			$db = Loader::db();
			if ($type != null) {
				$cnt = $db->GetOne("select count(uID) from UserGroups where gID = ? and type = ?", array($this->gID, $type));
			} else {
				$cnt = $db->GetOne("select count(uID) from UserGroups where gID = ?", array($this->gID));
			}
			return $cnt;
		}
		

		/**
		 * Deletes a group
		 * @return void
		 */
		function delete(){
			// we will NOT let you delete the required groups
			if ($this->gID == REGISTERED_GROUP_ID || $this->gID == GUEST_GROUP_ID) {
				return false;
			}
			
			// run any internal event we have for group deletion
			$ret = Events::fire('on_group_delete', $this);
			if ($ret < 0) {
				return false;
			}
			
			$db = Loader::db(); 
			$r = $db->query("DELETE FROM UserGroups WHERE gID = ?",array(intval($this->gID)) );
			$r = $db->query("DELETE FROM Groups WHERE gID = ?",array(intval($this->gID)) );
		}

		function inGroup() {
			return $this->inGroup;
		}
		
		function getGroupDateTimeEntered() {
			return $this->gDateTimeEntered;
		}

		function getGroupMemberType() {
			return $this->gMemberType;
		}
		
		function getGroupID() {
			return $this->gID;
		}
		
		function getGroupName() {
			return $this->gName;
		}
		
		function getGroupDescription() {
			return $this->gDescription;
		}
		
		/**
		 * Gets the group start date
		 * if user is specified, returns in the current user's timezone
		 * @param string $type (system || user)
		 * @return string date formated like: 2009-01-01 00:00:00 
		*/
		function getGroupStartDate($type = 'system') {
			if(ENABLE_USER_TIMEZONES && $type == 'user') {
				$dh = Loader::helper('date');
				return $dh->getLocalDateTime($this->cgStartDate);
			} else {
				return $this->cgStartDate;
			}
		}

		/**
		 * Gets the group end date 
		 * if user is specified, returns in the current user's timezone
		 * @param string $type (system || user)
		 * @return string date formated like: 2009-01-01 00:00:00 
		*/
		function getGroupEndDate($type = 'system') {
			if(ENABLE_USER_TIMEZONES && $type == 'user') {
				$dh = Loader::helper('date');
				return $dh->getLocalDateTime($this->cgEndDate);
			} else {
				return $this->cgEndDate;
			}
		}


		public function isGroupExpirationEnabled() {
			return $this->gUserExpirationIsEnabled;
		}
		
		public function getGroupExpirationMethod() {
			return $this->gUserExpirationMethod;
		}
		
		public function getGroupExpirationDateTime() {
			return $this->gUserExpirationSetDateTime;
		}
		public function getGroupExpirationAction() {
			return $this->gUserExpirationAction;
		}
		
		public function getGroupExpirationIntervalDays() {
			return floor($this->gUserExpirationInterval / 1440);
		}
		
		public function getGroupExpirationIntervalHours() {			
			return floor(($this->gUserExpirationInterval % 1440) / 60);
		}
		
		public function getGroupExpirationIntervalMinutes() {
			return floor(($this->gUserExpirationInterval % 1440) % 60);
		}
		
		function update($gName, $gDescription) {
			$db = Loader::db();
			if ($this->gID) {
				$v = array($gName, $gDescription, $this->gID);
				$r = $db->prepare("update Groups set gName = ?, gDescription = ? where gID = ?");
				$res = $db->Execute($r, $v);
				$group = Group::getByID($this->gID);
		        Events::fire('on_group_update', $this);
        		
        		return $group;
			}
		}
		
		/** Creates a new user group.
		* @param string $gName
		* @param string $gDescription
		* @return Group
		*/
		public static function add($gName, $gDescription) {
			$db = Loader::db();
			$v = array($gName, $gDescription);
			$r = $db->prepare("insert into Groups (gName, gDescription) values (?, ?)");
			$res = $db->Execute($r, $v);
			
			if ($res) {
				$ng = Group::getByID($db->Insert_ID());
				Events::fire('on_group_add', $ng);
				return $ng;
			}
		}
		
		public function removeGroupExpiration() {
			$db = Loader::db();
			$db->Execute('update Groups set gUserExpirationIsEnabled = 0, gUserExpirationMethod = null, gUserExpirationSetDateTime = null, gUserExpirationInterval = 0, gUserExpirationAction = null where gID = ?', array($this->getGroupID()));
		}
		
		public function setGroupExpirationByDateTime($datetime, $action) {
			$db = Loader::db();
			$db->Execute('update Groups set gUserExpirationIsEnabled = 1, gUserExpirationMethod = \'SET_TIME\', gUserExpirationInterval = 0, gUserExpirationSetDateTime = ?, gUserExpirationAction = ? where gID = ?', array($datetime, $action, $this->gID));
		}

		public function setGroupExpirationByInterval($days, $hours, $minutes, $action) {
			$db = Loader::db();
			$interval = $minutes + ($hours * 60) + ($days * 1440);
			$db->Execute('update Groups set gUserExpirationIsEnabled = 1, gUserExpirationMethod = \'INTERVAL\', gUserExpirationSetDateTime = null, gUserExpirationInterval = ?, gUserExpirationAction = ? where gID = ?', array($interval, $action, $this->gID));
		}
					
		
	}
		
?>
