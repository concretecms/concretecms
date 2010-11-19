<?php 
/**
 * Contains the group and grouplist classes.
 * @package Users 
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * The group list object takes care of presenting the groups in the system as they apply to various objects. If you need to just display/filter
 * all groups in the system you should probably use Group Search
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class GroupList extends Object {
	
		var $gArray = array();
		
		/**
		 * Get all groups should only really be run when you're sure there aren't a million groups in the system
		 */
		function GroupList($obj, $omitRequiredGroups = false, $getAllGroups = false) {
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
		
		function getGroupList() {
			return $this->gArray;
		}
		
		/** 
		 * @todo Make this entire thing less repetive and make it jive with the function below so we're not repeating ourselves
		 */
		private function getRelevantGroups($obj, $omitRequiredGroups = false) {
			$db = Loader::db();
			switch(strtolower(get_class($obj))) {
				case 'block':
					$table = 'CollectionVersionBlockPermissions';
					$c = $obj->getBlockCollectionObject();
					$cID = $c->getCollectionID();
					$cvID = $c->getVersionID();
					$bID = $obj->getBlockID();
					$where = "cID = '{$cID}' and cvID = '{$cvID}' and bID = '{$bID}'";
					break;
				case 'fileset':
					$table = 'FileSetPermissions';
					$fsID = $obj->getFileSetID();
					$where = "fsID = '{$fsID}'";
					break;
				case 'filesetlist':
					$table = 'FileSetPermissions';
					$fsIDs = array();
					foreach($obj->sets as $fs) {
						$fsIDs[] = $fs->getFileSetID();
					}
					$where = "fsID in (" . implode(',', $fsIDs) . ")";
					break;
				case 'taskpermissionlist':
					$tpis = $obj->getTaskPermissionIDs();
					$table = 'TaskPermissionUserGroups';
					$where = "tpID in (" . implode(',', $tpis) . ")";
					break;
				case 'file':
					$table = 'FilePermissions';
					$fID = $obj->getFileID();
					$where = "fID = '{$fID}'";
					break;
				case 'page':
					$table = 'PagePermissions';
					$cID = $obj->getPermissionsCollectionID();
					$where = "cID = '{$cID}'";
					break;
				case 'area':
					$table = 'AreaGroups';
					$c = $obj->getAreaCollectionObject();
					$cID = ($obj->getAreaCollectionInheritID() > 0) ? $obj->getAreaCollectionInheritID() : $c->getCollectionID();
					$where = "cID = " . $cID . " and arHandle = " . $db->quote($obj->getAreaHandle());
					break;
				case 'userinfo':
					$table = 'UserGroups';
					$uID = $obj->getUserID();						
					if ($uID) {
						$where = "uID = {$uID}";
					}
					break;
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
		
		function getGroupUpdateAction($obj) {
			switch(strtolower(get_class($obj))) {
				case 'block':
					$cID = $obj->getBlockCollectionID();
					$bID = $obj->getBlockID();
					$arHandle = $obj->getAreaHandle();
					$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&amp;bID={$bID}&amp;arHandle={$arHandle}&amp;mode=edit&amp;btask=update_groups";
					break;
				case 'page':
					$cID = $obj->getCollectionID();
					$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&amp;mode=edit&amp;ctask=update_groups";
					break;
			}
			return $str;			
		}
			
	}

/**
 * Users in Concrete can be grouped together, and entire groups can be given permissions to do certain things.
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

	class Group extends Object {
	
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
			$row = $db->getRow("select * from Groups where gID = ?", array($gID));
			if (isset($row['gID'])) {
				$g = new Group;
				$g->setPropertiesFromArray($row);
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
		
		public function getGroupMembersNum($type = null) {
			$db = Loader::db();
			if ($type != null) {
				$cnt = $db->GetOne("select count(uID) from UserGroups where gID = ? and type = ?", array($this->gID, $type));
			} else {
				$cnt = $db->GetOne("select count(uID) from UserGroups where gID = ?", array($this->gID));
			}
			return $cnt;
		}
		
		public function setPermissionsForObject($obj) {
		
			$this->pObj = $obj;
			
			$db = Loader::db();
			
			switch(strtolower(get_class($obj))) {
				case 'block':
					$c = $obj->getBlockCollectionObject();
					$cID = $c->getCollectionID();
					$cvID = $c->getVersionID();
					$bID = $obj->getBlockID();
					$gID = $this->gID;
					$q = "select cbgPermissions from CollectionVersionBlockPermissions where cID = '{$cID}' and cvID = '{$cvID}' and bID = '{$bID}' and gID = '{$gID}'";
					$permissions = $db->getOne($q);
					if ($permissions) {
						$this->permissionSet = $permissions;
					}
					break;
				case 'filesetlist':
					$fsIDs = array();
					foreach($obj->sets as $fs) {
						$fsIDs[] = $fs->getFileSetID();
					}
					$where = "fsID in (" . implode(',', $fsIDs) . ")";

					$gID = $this->gID;
					$q = "select max(canRead) as canRead, max(canWrite) as canWrite, max(canSearch) as canSearch, max(canAdmin) as canAdmin from FileSetPermissions where {$where} and gID = '{$gID}' group by gID";
					$p = $db->GetRow($q);

					$this->permissions = $p;

					break;
				case 'taskpermission':
					$q = "select canRead from TaskPermissionUserGroups where tpID = ? and gID = ?";
					$permissions = $db->GetRow($q, array($obj->getTaskPermissionID(), $this->gID));
					if ($permissions) {
						$this->permissions = $permissions;
					}
					break;
				case 'fileset':
					$fsID = $obj->getFileSetID();
					$gID = $this->gID;
					$q = "select canRead, canSearch, canWrite, canAdmin, canAdd from FileSetPermissions where fsID = '{$fsID}' and gID = '{$gID}'";
					$permissions = $db->GetRow($q);
					if ($permissions) {
						$this->permissions = $permissions;
					}
					
					$q = "select extension from FilePermissionFileTypes where fsID = '{$fsID}' and gID = '{$gID}'";
					$extensions = $db->GetCol($q);
					$this->permissions['canAddExtensions'] = $extensions;
					break;
				case 'file':
					$fID = $obj->getFileID();
					$gID = $this->gID;
					$q = "select canRead, canWrite, canSearch, canAdmin from FilePermissions where fID = '{$fID}' and gID = '{$gID}'";
					$permissions = $db->GetRow($q);
					if ($permissions) {
						$this->permissions = $permissions;
					}
					break;
				case 'page':
					//$cID = $obj->getCollectionID();
					$cID = $obj->getPermissionsCollectionID();
					$gID = $this->gID;
					$q = "select cgPermissions, cgStartDate, cgEndDate from PagePermissions where cID = '{$cID}' and gID = '{$gID}'";
					$r = $db->query($q);
					if ($r) {
						$row = $r->fetchRow();
						$this->permissionSet = $row['cgPermissions'];
						$this->cgStartDate = $row['cgStartDate'];
						$this->cgEndDate = $row['cgEndDate'];
					}
					$q = "select count(*) from PagePermissionPageTypes where cID = '{$cID}' and gID = '{$gID}'";
					$total = $db->getOne($q, $v);
					if ($total > 0) {
						$this->canAddPages = true;
					}
					break;
				case 'area':
					$c = $obj->getAreaCollectionObject();
					$cID = ($obj->getAreaCollectionInheritID() > 0) ? $obj->getAreaCollectionInheritID() : $c->getCollectionID();
		
		
					$gID = $this->gID;
					$v = array($cID, $obj->getAreaHandle(), $gID);
					$q = "select agPermissions from AreaGroups where cID = ? and arHandle = ? and gID = ?";
					$r = $db->query($q, $v);
					if ($r) {
						$row = $r->fetchRow();
						$this->permissionSet = $row['agPermissions'];
					}
					$q = "select count(*) from AreaGroupBlockTypes where cID = ? and arHandle = ? and gID = ?";
					$total = $db->getOne($q, $v);
					if ($total > 0) {
						$this->canAddBlocks = true;
					}
					break;
				case 'userinfo':
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
					break;
			}
			
			// if we have a permissions array, we set the character tokens for backwards compatibility 
			if ($this->permissions['canRead']) {
				$this->permissionSet .= 'r:';
			}
			if ($this->permissions['canWrite']) {
				$this->permissionSet .= 'wa:';
			}
			if ($this->permissions['canAdmin']) {
				$this->permissionSet .= 'adm:';
			}

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
			$r = $db->query("DELETE FROM CollectionVersionBlockPermissions WHERE gID = ?",array(intval($this->gID)) );
			$r = $db->query("DELETE FROM PagePermissionPageTypes WHERE gID = ?",array(intval($this->gID)) );
			$r = $db->query("DELETE FROM PagePermissions WHERE gID = ?",array(intval($this->gID)) );
			$r = $db->query("DELETE FROM AreaGroupBlockTypes WHERE gID = ?",array(intval($this->gID)) );
			$r = $db->query("DELETE FROM AreaGroups WHERE gID = ?",array(intval($this->gID)) ); 
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

		function canRead() {
			return strpos($this->permissionSet, 'r') > -1;
		}
		
		function canReadVersions() {
			return strpos($this->permissionSet, 'rv') > -1;
		}
		
		function canLimitedWrite() {
			return strpos($this->permissionSet, 'wu') > -1;
		}
		
		function canWrite() {
			return strpos($this->permissionSet, 'wa') > -1;
		}
		
		function canDeleteBlock() {
			return strpos($this->permissionSet, 'db') > -1;
		}
		
		function canDeleteCollection() {
			return strpos($this->permissionSet, 'dc') > -1;
		}
		
		function canApproveCollection() {
			return strpos($this->permissionSet, 'av') > -1;
		}
		
		function canAddSubContent() {
			return $this->canAddPages;
		}
		
		function canAddSubCollection() {
			return strpos($this->permissionSet, 'ac') > -1;
		}
		
		function canAddBlocks() {
			return $this->canAddBlocks;
		}
		
		function canAdminCollection() {
			return strpos($this->permissionSet, 'adm') > -1;
		}

		function canAdmin() {
			return strpos($this->permissionSet, 'adm') > -1;
		}
		
		/** 
		 * File manager permissions at the group level 
		 */
		public function canSearchFiles() {
			return $this->permissions['canSearch'];
		}
		
		public function getFileReadLevel() {
			return $this->permissions['canRead'];
		}
		public function getFileSearchLevel() {
			return $this->permissions['canSearch'];
		}
		public function getFileWriteLevel() {
			return $this->permissions['canWrite'];
		}
		public function getFileAdminLevel() {
			return $this->permissions['canAdmin'];
		}
		public function getFileAddLevel() {
			return $this->permissions['canAdd'];
		}
		public function getAllowedFileExtensions() {
			return $this->permissions['canAddExtensions'];
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
			}
		}
		
		function add($gName, $gDescription) {
			$db = Loader::db();
			$v = array($gName, $gDescription);
			$r = $db->prepare("insert into Groups (gName, gDescription) values (?, ?)");
			$res = $db->Execute($r, $v);
			
			if ($res) {
				$ng = Group::getByID($db->Insert_ID());
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