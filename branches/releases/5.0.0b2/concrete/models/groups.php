<?
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
 * The group list object takes care of presenting the groups in the system, and can be used to filter out internal groups.
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class GroupList extends Object {
	
		var $gArray = array();
		
		function GroupList($obj, $omitRequiredGroups = false) {
			$db = Loader::db();
			$q = "select gID from Groups ";
			$q .= ($omitRequiredGroups) ? "where gID > 2 " : "";
			$q .= "order by gID asc";
			$r = $db->query($q);

			// If allowedBlocks is null, that means everything is allowed. But if it's not, then we have to see 
			// the btHandle exists within the allowedBlocks array. If it does, we don't return it
	
			if ($r) {
				while ($row = $r->fetchRow()) {
					$g = Group::getByID($row['gID']);
					$g->setPermissionsForObject($obj);
					$this->gArray[] = $g;
				}
				$r->free();
			}
			
			return $this;
		}
		
		function getGroupList() {
			return $this->gArray;
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
		
		public static function getByID($gID) {
			$db = Loader::db();
			$row = $db->getRow("select * from Groups where gID = ?", array($gID));
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
		
		function getGroupStartDate() {
			return $this->cgStartDate;
		}
		
		function getGroupEndDate() {
			return $this->cgEndDate;
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
					
		
	}
		
?>