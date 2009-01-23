<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * While the User object deals more with logging users in and relating them to core Concrete items, like Groups, the UserInfo object is made to grab auxiliary data about a user, including their user attributes. Additionally, the UserInfo object is the object responsible for adding/registering users in the system.
 *
 * @package Users
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

	class UserInfo extends Object {
	
		/* magic method for user attributes. This is db expensive but pretty damn cool */
		
		public function __call($nm, $a) {
			if (substr($nm, 0, 7) == 'getUser') {
				$nm = preg_replace('/(?!^)[[:upper:]]/','_\0', $nm);
				$nm = strtolower($nm);
				$nm = str_replace('get_user_', '', $nm);
				
				$db = Loader::db();
				$v = array($this->uID, $nm);
				$r = $db->getOne("select value from UserAttributeValues inner join UserAttributeKeys on UserAttributeValues.ukID = UserAttributeKeys.ukID where uID = ? and UserAttributeKeys.ukHandle = ?", $v);
				return $r;
			}			
		}
		
		public static function getByID($uID, $userPermissionsArray = null) {
			return UserInfo::get('where uID = ?', $uID, $userPermissionsArray);
		}
		
		public static function getByUserName($uName, $userPermissionsArray = null) {
			return UserInfo::get('where uName = ?', $uName, $userPermissionsArray);
		}
		
		public static function getByEmail($uEmail, $userPermissionsArray = null) {
			return UserInfo::get('where uEmail = ?', $uEmail, $userPermissionsArray);
		}
		
		public static function getByValidationHash($uHash, $unredeemedHashesOnly = true) {
			$db = Loader::db();
			if ($unredeemedHashesOnly) {
				$uID = $db->GetOne("select uID from UserValidationHashes where uHash = ? and uDateRedeemed = 0", array($uHash));
			} else {
				$uID = $db->GetOne("select uID from UserValidationHashes where uHash = ?", array($uHash));
			}
			if ($uID) {
				$ui = UserInfo::getByID($uID);
				return $ui;
			}
		}
		
		private function get($where, $var, $userPermissionsArray = null) {
			$db = Loader::db();
			$q = "select Users.uID, Users.uLastLogin, Users.uIsValidated, Users.uPreviousLogin, Users.uIsFullRecord, Users.uNumLogins, Users.uDateAdded, Users.uIsActive, Users.uLastOnline, Users.uHasAvatar, Users.uName, Users.uEmail, Users.uPassword from Users " . $where;
			$r = $db->query($q, array($var));
			if ($r && $r->numRows() > 0) {
				$ui = new UserInfo;
				$row = $r->fetchRow();
				$ui->setPropertiesFromArray($row);
				$r->free();
			}
			
			if (is_object($ui)) {
				if ($userPermissionsArray) {
					$ui->permissionSet = $userPermissionsArray['permissionSet'];
					$ui->upStartDate = $userPermissionsArray['upStartDate'];
					$ui->upEndDate = $userPermissionsArray['upEndDate'];
				}

				return $ui;
			}
		}
		
		public static function add($data) {
			$db = Loader::db();
			$dh = Loader::helper('date');
			$uDateAdded = $dh->getLocalDateTime();
			
			if (isset($data['uIsValidated']) && $data['uIsValidated'] == 0) {
				$uIsValidated = 0;
			} else {
				$uIsValidated = -1;
			}
			
			if (isset($data['uIsFullRecord']) && $data['uIsFullRecord'] == 0) {
				$uIsFullRecord = 0;
			} else {
				$uIsFullRecord = 1;
			}
			
			$v = array($data['uName'], $data['uEmail'], User::encryptPassword($data['uPassword']), $uIsValidated, $uDateAdded, $uIsFullRecord, 1);
			$r = $db->prepare("insert into Users (uName, uEmail, uPassword, uIsValidated, uDateAdded, uIsFullRecord, uIsActive) values (?, ?, ?, ?, ?, ?, ?)");
			$res = $db->execute($r, $v);
			if ($res) {
				$newUID = $db->Insert_ID();
				$ui = UserInfo::getByID($newUID);
				
				if (is_object($ui)) {
					// run any internal event we have for user add
					Events::fire('on_user_add', $ui);
				}
				
				return $ui;
			}
		}
		
		public function addSuperUser($uPasswordEncrypted, $uEmail) {
			$db = Loader::db();
			$dh = Loader::helper('date');
			$uDateAdded = $dh->getLocalDateTime();
			
			$v = array(USER_SUPER_ID, USER_SUPER, $uEmail, $uPasswordEncrypted, 1, $uDateAdded);
			$r = $db->prepare("insert into Users (uID, uName, uEmail, uPassword, uIsActive, uDateAdded) values (?, ?, ?, ?, ?, ?)");
			$res = $db->execute($r, $v);
			if ($res) {
				$newUID = $db->Insert_ID();
				return UserInfo::getByID($newUID);
			}
		}
		
		/**
		 * Deletes a user
		 * @return void
		 */
		public function delete(){
			// we will NOT let you delete the admin user
			if ($this->uID == USER_SUPER_ID) {
				return false;
			}

			// run any internal event we have for user deletion
			$ret = Events::fire('on_user_delete', $this);
			if ($ret < 0) {
				return false;
			}
			
			$db = Loader::db();  
			$r = $db->query("DELETE FROM UserGroups WHERE uID = ?",array(intval($this->uID)) );
			$r = $db->query("DELETE FROM Users WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM UserValidationHashes WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM UserAttributeValues WHERE uID = ?",array(intval($this->uID)));
			
			$r = $db->query("DELETE FROM AreaGroupBlockTypes WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM CollectionVersionBlockPermissions WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM PagePermissionPageTypes WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM AreaGroups WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM PagePermissions WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM Piles WHERE uID = ?",array(intval($this->uID)));
			
			$r = $db->query("UPDATE Blocks set uID=? WHERE uID = ?",array( intval(USER_SUPER_ID), intval($this->uID)));
			$r = $db->query("UPDATE Pages set uID=? WHERE uID = ?",array( intval(USER_SUPER_ID), intval($this->uID)));			
		}

		/**
		 * Called only by the getGroupMembers function it sets the "type" of member for this group. Typically only used programmatically
		 * @param string $type
		 * @return void
		 */
		public function setGroupMemberType($type) {
			$this->gMemberType = $type;
		}
		
		public function getGroupMemberType() {
			return $this->gMemberType;
		}
		
		public function getUserObject() {
			// returns a full user object - groups and everything - for this userinfo object
			$nu = User::getByUserID($this->uID);
			return $nu;
		}

		/* TODO: cleanup this and make more usable */
		
		public function updateUserAttributes($data) {
			Loader::model('user_attributes');
			$db = Loader::db();
			$keys = UserAttributeKey::getList();
			foreach($keys as $v) {
				$db->query("delete from UserAttributeValues where uID = {$this->uID} and ukID = " . $v->getKeyID());

				if ($data['uak_' . $v->getKeyID()]) {
					$v2 = array($this->uID, $v->getKeyID(), $data['uak_' . $v->getKeyID()]);
					$db->query("insert into UserAttributeValues (uID, ukID, value) values (?, ?, ?)", $v2);
				}
			}
		}
		
		public function updateSelectedUserAttributes($keyArray, $data) {
			Loader::model('user_attributes');
			if (is_array($keyArray)) {
				$db = Loader::db();
				$keys = UserAttributeKey::getList();
				foreach($keys as $v) {
					if (in_array($v->getKeyID(), $keyArray)) {
						$db->query("delete from UserAttributeValues where uID = {$this->uID} and ukID = " . $v->getKeyID());
		
						if ($data['uak_' . $v->getKeyID()]) {
							$v2 = array($this->uID, $v->getKeyID(), $data['uak_' . $v->getKeyID()]);
							$db->query("insert into UserAttributeValues (uID, ukID, value) values (?, ?, ?)", $v2);
						}
					}
				}
			}
		}
		
		/** 
		 * Sets the attribute of a user info object to the specified value, and saves it in the database 
		 */
		public function setAttribute($attributeHandle, $value) {
			Loader::model('user_attributes');
			$uk = UserAttributeKey::getByHandle($attributeHandle);
			if (is_object($uk)) {
				$uk->saveValue($this->getUserID(), $value);
			} else {
				throw new Exception(t('Invalid user attribute key.'));
			}
		}
		
		public function update($data) {
			$db = Loader::db();
			if ($this->uID) {
				$uName = $this->getUserName();
				$uEmail = $this->getUserEmail();
				$uHasAvatar = $this->hasAvatar();
				if (isset($data['uName'])) {
					$uName = $data['uName'];
				}
				if (isset($data['uEmail'])) {
					$uEmail = $data['uEmail'];
				}
				if (isset($data['uHasAvatar'])) {
					$uHasAvatar = $data['uHasAvatar'];
				}
				
				if ($data['uPassword'] != null) {
					if (User::encryptPassword($data['uPassword']) == User::encryptPassword($data['uPasswordConfirm'])) {
						$v = array($uName, $uEmail, User::encryptPassword($data['uPassword']), $uHasAvatar, $this->uID);
						$r = $db->prepare("update Users set uName = ?, uEmail = ?, uPassword = ?, uHasAvatar = ? where uID = ?");
						$res = $db->execute($r, $v);
					} else {
						$updateGroups = false;
					}
				} else {
					$v = array($uName, $uEmail, $uHasAvatar, $this->uID);
					$r = $db->prepare("update Users set uName = ?, uEmail = ?, uHasAvatar = ? where uID = ?");
					$res = $db->execute($r, $v);
				}

				// now we check to see if the user is updated his or her own logged in record
				if (isset($_SESSION['uID']) && $_SESSION['uID'] == $this->uID) {
					$_SESSION['uName'] = $data['uName']; // make sure to keep the new uName in there
				}

				// run any internal event we have for user update
				Events::fire('on_user_update', $this);
				
				return $res;
			}
		}
				
		public function updateGroups($groupArray) {
			$db = Loader::db();
			$q = "select gID from UserGroups where uID = '{$this->uID}'";
			$r = $db->query($q);
			if ($r) {
				$existingGIDArray = array();
				while ($row = $r->fetchRow()) {
					$existingGIDArray[] = $row['gID'];
				}
			}

			$dh = Loader::helper('date');
				
			$datetime = $dh->getLocalDateTime();
			if (is_array($groupArray)) {
				foreach ($groupArray as $gID) {
					$key = array_search($gID, $existingGIDArray);
					if ($key !== false) {
						// we remove this item from the existing GID array
						unset($existingGIDArray[$key]);
					} else {
						// this item is new, so we add it.
						$q = "insert into UserGroups (uID, gID, ugEntered) values ({$this->uID}, $gID, '{$datetime}')";
						$r = $db->query($q);
					}
				}
			}

				// now we go through the existing GID Array, and remove everything, since whatever is left is not wanted.
			if (count($existingGIDArray) > 0) {
				$inStr = implode(',', $existingGIDArray);
				$q2 = "delete from UserGroups where uID = '{$this->uID}' and gID in ({$inStr})";
				$r2 = $db->query($q2);
			}
		}
		
		public function register($data) {
			// slightly different than add. this is public facing
			if (defined("USER_VALIDATE_EMAIL")) {
				if (USER_VALIDATE_EMAIL > 0) {
					$data['uIsValidated'] = 0;	
				}
			}
			$ui = UserInfo::add($data);
			return $ui;
		}

		
		public function setupValidation() {
			$db = Loader::db();
			$hash = $db->GetOne("select uHash from UserValidationHashes where uID = ? order by uDateGenerated desc", array($this->uID));
			if ($hash) {
				return $hash;
			} else {
				$h = Loader::helper('validation/identifier');
				$hash = $h->generate('UserValidationHashes', 'uHash');
				$db->Execute("insert into UserValidationHashes (uID, uHash, uDateGenerated) values (?, ?, ?)", array($this->uID, $hash, time()));
				return $hash;
			}
		}
		
		function markValidated() {
			$db = Loader::db();
			$v = array($this->uID);
			$db->query("update Users set uIsValidated = 1, uIsFullRecord = 1 where uID = ?", $v);
			$db->query("update UserValidationHashes set uDateRedeemed = " . time() . " where uID = ?", $v);
			return true;
		}
		
		function changePassword($newPassword) {
			$db = Loader::db();
			if ($this->uID) {
				$v = array(User::encryptPassword($newPassword), $this->uID);
				$q = "update Users set uPassword = ? where uID = ?";
				$r = $db->prepare($q);
				$res = $db->execute($r, $v);
				return $res;
			}
		}

		function activate() {
			$db = Loader::db();
			$q = "update Users set uIsActive = 1 where uID = '{$this->uID}'";
			$r = $db->query($q);
		}

		function deactivate() {
			$db = Loader::db();
			$q = "update Users set uIsActive = 0 where uID = '{$this->uID}'";
			$r = $db->query($q);
		}
		
		
		function resetUserPassword() {
			// resets user's password, and returns the value of the reset password
			$db = Loader::db();
			if ($this->uID > 0) {
				$newPassword = '';
				$salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
				for ($i = 0; $i < 7; $i++) {
					$newPassword .= substr($salt, rand() %strlen($salt), 1);
				}
				$v = array(User::encryptPassword($newPassword), $this->uID);
				$q = "update Users set uPassword = ? where uID = ?";
				$r = $db->query($q, $v);
				if ($r) {
					return $newPassword;
				}
			}
		}
		
		function hasAvatar() {
			return $this->uHasAvatar;
		}
		
		function getLastLogin() {
			return $this->uLastLogin;
		}
		
		function getPreviousLogin() {
			return $this->uPreviousLogin;
		}
		
		function isActive() {
			return $this->uIsActive;
		}
		
		public function isValidated() {
			return $this->uIsValidated;
		}
		
		public function isFullRecord() {
			return $this->uIsFullRecord;
		}
		
		function getNumLogins() {
			return $this->uNumLogins;
		}
		
		function getUserID() {
			return $this->uID;
		}

		function getUserName() {
			return $this->uName;
		}

		function getUserPassword() {
			return $this->uPassword;
		}

		function getUserEmail() {
			return $this->uEmail;
		}

		function getUserDateAdded() {
			return $this->uDateAdded;
		}

		/* userinfo permissions modifications - since users can now have their own permissions on a collection, block ,etc..*/

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
			return strpos($this->permissionSet, 'as') > -1;
		}

		function canAddSubCollection() {
			return strpos($this->permissionSet, 'ac') > -1;
		}

		function canAddBlock() {
			return strpos($this->permissionSet, 'ab') > -1;
		}

		function canAdminCollection() {
			return strpos($this->permissionSet, 'adm') > -1;
		}

		function getUserStartDate() {
			// time-release permissions for users
			return $this->upStartDate;
		}

		function getLastOnline() {
			return $this->uLastOnline;
		}

		function getUserEndDate() {
			return $this->upEndDate;
		}

	}

	class UserInfoList extends Object {

	   // obtains relevant users for the passed object. Since users can now
	   // act just like groups (permissions-wise)

	   var $uiArray = array();

		function UserInfoList($obj) {
			$db = Loader::db();
			switch(strtolower(get_class($obj))) {
				case 'page':
					$cID = $obj->getPermissionsCollectionID();
					$q = "select uID, cgPermissions, cgStartDate, cgEndDate from PagePermissions where cID = '{$cID}' and uID > 0";
					$r = $db->query($q);
					if ($r) {
						$userPermissionsArray = array();
						$userIDArray = array();
						$upTemp = array();
						while ($row = $r->fetchRow()) {
							$userIDArray[] = $row['uID'];
							$upTemp['permissionSet'] = $row['cgPermissions'];
							$upTemp['upStartDate'] = $row['cgStartDate'];
							$upTemp['upEndDate'] = $row['cgEndDate'];
							$userPermissionsArray[$row['uID']] = $upTemp;
						}
					}
					$q = "select uID from PagePermissionPageTypes where cID = '{$cID}' and uID > 0";
					$r = $db->query($q);
					if ($r) {
						while ($row = $r->fetchRow()) {
							if (!in_array($row['uID'], $userIDArray)) {
								$userIDArray[] = $row['uID'];
								$userPermissionsArray[$row['uID']] = array();
							}
						}
					}
					foreach($userIDArray as $uID) {
						$this->uiArray[] = UserInfo::getByID($uID, $userPermissionsArray[$uID]);
					}
					unset($userPermissionsArray);
					unset($upTemp);
					unset($userIDArray);
					break;
				case 'block':
					$c = $obj->getBlockCollectionObject();
					$cID = $c->getCollectionID();
					$cvID = $c->getVersionID();
					$bID = $obj->getBlockID();
					$gID = $this->gID;
					$q = "select uID, cbgPermissions from CollectionVersionBlockPermissions where cID = '{$cID}' and cvID = '{$cvID}' and bID = '{$bID}' and uID > 0";
					$r = $db->query($q);
					if ($r) {
						while ($row = $r->fetchRow()) {
								$userPermissionsArray['permissionSet'] = $row['cbgPermissions'];
								$this->uiArray[] = UserInfo::getByID($row['uID'], $userPermissionsArray);
						}
					}
					break;
				case 'area':
					
					$c = $obj->getAreaCollectionObject();
					$cID = ($obj->getAreaCollectionInheritID() > 0) ? $obj->getAreaCollectionInheritID() : $c->getCollectionID();
					$gID = $this->gID;
					$v = array($cID, $obj->getAreaHandle());
					$q = "select uID, agPermissions from AreaGroups where cID =  ? and arHandle = ? and uID > 0";
					$r = $db->query($q, $v);
					if ($r) {
						while ($row = $r->fetchRow()) {
							$userPermissionsArray['permissionSet'] = $row['agPermissions'];
							$this->uiArray[] = UserInfo::getByID($row['uID'], $userPermissionsArray);
						}
					}
					break;
			}

			return $this;

        }


		function getUserInfoList() {
			return $this->uiArray;
		}
	}
