<?php

defined('C5_EXECUTE') or die("Access Denied.");
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

	class Concrete5_Model_UserInfo extends Object { 

		public function __toString() {
			return 'UserInfo: ' . $this->getUserID();
		}
	
		/* magic method for user attributes. This is db expensive but pretty damn cool */
		// so if the attrib handle is "my_attribute", then get the attribute with $ui->getUserMyAttribute(), or "uFirstName" become $ui->getUserUfirstname();
		public function __call($nm, $a) {
			if (substr($nm, 0, 7) == 'getUser') {
				$nm = preg_replace('/(?!^)[[:upper:]]/','_\0', $nm);
				$nm = strtolower($nm);
				$nm = str_replace('get_user_', '', $nm);
				
				return $this->getAttribute($nm);
			}			
		}
		
		/**
		 * returns the UserInfo object for a give user's uID
		 * @param int $uID
		 * @return UserInfo
		 */
		public static function getByID($uID) {
			return UserInfo::get('where uID = ?', $uID);
		}
		
		/**
		 * returns the UserInfo object for a give user's username
		 * @param string $uName
		 * @return UserInfo
		 */
		public static function getByUserName($uName) {
			return UserInfo::get('where uName = ?', $uName);
		}
		
		/**
		 * returns the UserInfo object for a give user's email address
		 * @param string $uEmail
		 * @return UserInfo
		 */
		public static function getByEmail($uEmail) {
			return UserInfo::get('where uEmail = ?', $uEmail);
		}

		/** 
		 * Returns a user object by open ID. Does not log a user in.
		 * @param string $uOpenID
		 * @return UserInfo
		 */
		public function getByOpenID($uOpenID) {
			return UserInfo::get('inner join UserOpenIDs on Users.uID = UserOpenIDs.uID where uOpenID = ?', $uOpenID);
		}
		
		
		/**
		 * @param string $uHash
		 * @param boolean $unredeemedHashesOnly
		 * @return UserInfo
		 */
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
		
		private function get($where, $var) {
			$db = Loader::db();
			$q = "select Users.uID, Users.uLastLogin, Users.uLastIP, Users.uIsValidated, Users.uPreviousLogin, Users.uIsFullRecord, Users.uNumLogins, Users.uDateAdded, Users.uIsActive, Users.uLastOnline, Users.uHasAvatar, Users.uName, Users.uEmail, Users.uPassword, Users.uTimezone from Users " . $where;
			$r = $db->query($q, array($var));
			if ($r && $r->numRows() > 0) {
				$ui = new UserInfo;
				$row = $r->fetchRow();
				$ui->setPropertiesFromArray($row);
				$r->free();
			}
			
			if (is_object($ui)) {
				return $ui;
			}
		}
		
		const ADD_OPTIONS_NOHASH		= 0;
		const ADD_OPTIONS_SKIP_CALLBACK	= 1;
		
		/**
		 * @param array $data
		 * @param array | false $options
		 * @return UserInfo
		 */
		public static function add($data,$options=false) {
			$options = is_array($options) ? $options : array();
			$db = Loader::db();
			$dh = Loader::helper('date');
			$uDateAdded = $dh->getSystemDateTime();
			
			if ($data['uIsValidated'] == 1) {
				$uIsValidated = 1;
			} else if (isset($data['uIsValidated']) && $data['uIsValidated'] == 0) {
				$uIsValidated = 0;
			} else {
				$uIsValidated = -1;
			}
			
			if (isset($data['uIsFullRecord']) && $data['uIsFullRecord'] == 0) {
				$uIsFullRecord = 0;
			} else {
				$uIsFullRecord = 1;
			}
			
			$password_to_insert = $data['uPassword'];
			if (!in_array(self::ADD_OPTIONS_NOHASH, $options)) {
				$password_to_insert = User::encryptPassword($password_to_insert);			
			}	
			
			if (isset($data['uDefaultLanguage']) && $data['uDefaultLanguage'] != '') {
				$uDefaultLanguage = $data['uDefaultLanguage'];
			}
			$v = array($data['uName'], $data['uEmail'], $password_to_insert, $uIsValidated, $uDateAdded, $uIsFullRecord, $uDefaultLanguage, 1);
			$r = $db->prepare("insert into Users (uName, uEmail, uPassword, uIsValidated, uDateAdded, uIsFullRecord, uDefaultLanguage, uIsActive) values (?, ?, ?, ?, ?, ?, ?, ?)");
			$res = $db->execute($r, $v);
			if ($res) {
				$newUID = $db->Insert_ID();
				$ui = UserInfo::getByID($newUID);
				
				if (is_object($ui) && !in_array(self::ADD_OPTIONS_SKIP_CALLBACK,$options)) {
					// run any internal event we have for user add
					Events::fire('on_user_add', $ui, $data['uPassword']);
				}
				
				return $ui;
			}
		}
		
		public function addSuperUser($uPasswordEncrypted, $uEmail) {
			$db = Loader::db();
			$dh = Loader::helper('date');
			$uDateAdded = $dh->getSystemDateTime();
			
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

			$r = $db->Execute('select avID, akID from UserAttributeValues where uID = ?', array($this->uID));
			Loader::model('attribute/categories/user');
			while ($row = $r->FetchRow()) {
				$uak = UserAttributeKey::getByID($row['akID']);
				$av = $this->getAttributeValueObject($uak);
				if (is_object($av)) {
					$av->delete();
				}
			}

			$r = $db->query("DELETE FROM UsersFriends WHERE friendUID = ?",array(intval($this->uID)) );
			$r = $db->query("DELETE FROM UserSearchIndexAttributes WHERE uID = ?",array(intval($this->uID)) );
			
			$r = $db->query("DELETE FROM UserGroups WHERE uID = ?",array(intval($this->uID)) );
			$r = $db->query("DELETE FROM UserOpenIDs WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM Users WHERE uID = ?",array(intval($this->uID)));
			$r = $db->query("DELETE FROM UserValidationHashes WHERE uID = ?",array(intval($this->uID)));
			
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

		public function canReadPrivateMessage($msg) {
			return $msg->getMessageUserID() == $this->getUserID();
		}
		
		public function sendPrivateMessage($recipient, $subject, $text, $inReplyTo = false) {
			Loader::model('user_private_message');
			if(UserPrivateMessageLimit::isOverLimit($this->getUserID())) {
				return UserPrivateMessageLimit::getErrorObject();
			}
			$antispam = Loader::helper('validation/antispam');
			$messageText = t('Subject: %s', $subject);
			$messageText .= "\n";
			$messageText .= t('Message: %s', $text);
			
			$additionalArgs = array('user' => $this);
			if (!$antispam->check($messageText, 'private_message', $additionalArgs)) {
				return false;
			}
			
			$subject = ($subject == '') ? t('(No Subject)') : $subject;
			$db = Loader::db();
			$dt = Loader::helper('date');
			$v = array($this->getUserID(), $dt->getLocalDateTime(), $subject, $text, $recipient->getUserID());
			$db->Execute('insert into UserPrivateMessages (uAuthorID, msgDateCreated, msgSubject, msgBody, uToID) values (?, ?, ?, ?, ?)', $v);
			
			$msgID = $db->Insert_ID();
			
			if ($msgID > 0) {
				// we add the private message to the sent box of the sender, and the inbox of the recipient
				$v = array($db->Insert_ID(), $this->getUserID(), $this->getUserID(), UserPrivateMessageMailbox::MBTYPE_SENT, 0, 1);
				$db->Execute('insert into UserPrivateMessagesTo (msgID, uID, uAuthorID, msgMailboxID, msgIsNew, msgIsUnread) values (?, ?, ?, ?, ?, ?)', $v);
				$v = array($db->Insert_ID(), $recipient->getUserID(), $this->getUserID(), UserPrivateMessageMailbox::MBTYPE_INBOX, 1, 1);
				$db->Execute('insert into UserPrivateMessagesTo (msgID, uID, uAuthorID, msgMailboxID, msgIsNew, msgIsUnread) values (?, ?, ?, ?, ?, ?)', $v);
			}
			
			// If the message is in reply to another message, we make a note of that here
			if (is_object($inReplyTo)) {
				$db->Execute('update UserPrivateMessagesTo set msgIsReplied = 1 where uID = ? and msgID = ?', array($this->getUserID(), $inReplyTo->getMessageID()));
			}
			
			// send the email notification
			if ($recipient->getAttribute('profile_private_messages_notification_enabled')) {
				$mh = Loader::helper('mail');
				$mh->addParameter('msgSubject', $subject);
				$mh->addParameter('msgBody', $text);
				$mh->addParameter('msgAuthor', $this->getUserName());
				$mh->addParameter('msgDateCreated', $msgDateCreated);
				$mh->addParameter('profileURL', BASE_URL . View::url('/profile', 'view', $this->getUserID()));
				$mh->addParameter('profilePreferencesURL', BASE_URL . View::url('/profile/edit'));
				$mh->to($recipient->getUserEmail());
				
				Loader::library('mail/importer');
				$mi = MailImporter::getByHandle("private_message");
				if (is_object($mi) && $mi->isMailImporterEnabled()) {
					$mh->load('private_message_response_enabled');
					// we store information ABOUT the message here. The mail handler has to know how to handle this.
					$data = new stdClass;
					$data->msgID = $msgID;
					$data->toUID = $recipient->getUserID();
					$data->fromUID = $this->getUserID();
					$mh->enableMailResponseProcessing($mi, $data);
				} else {
					$mh->load('private_message');
				}
				$mh->sendMail();
			}
		}
		
		/**
		 * gets the user object of the current UserInfo object ($this)
		 * @return User
		 */
		public function getUserObject() {
			// returns a full user object - groups and everything - for this userinfo object
			$nu = User::getByUserID($this->uID);
			return $nu;
		}

		/** 
		 * Sets the attribute of a user info object to the specified value, and saves it in the database 
		*/
		public function setAttribute($ak, $value) {
			Loader::model('attribute/categories/user');
			if (!is_object($ak)) {
				$ak = UserAttributeKey::getByHandle($ak);
			}
			$ak->setAttribute($this, $value);
			$this->reindex();
		}

		public function clearAttribute($ak) {
			$db = Loader::db();
			if (!is_object($ak)) {
				$ak = UserAttributeKey::getByHandle($ak);
			}
			$cav = $this->getAttributeValueObject($ak);
			if (is_object($cav)) {
				$cav->delete();
			}
			$this->reindex();
		}
		
		public function reindex() {
			$attribs = UserAttributeKey::getAttributes($this->getUserID(), 'getSearchIndexValue');
			$db = Loader::db();
	
			$db->Execute('delete from UserSearchIndexAttributes where uID = ?', array($this->getUserID()));
			$searchableAttributes = array('uID' => $this->getUserID());
			$rs = $db->Execute('select * from UserSearchIndexAttributes where uID = -1');
			AttributeKey::reindex('UserSearchIndexAttributes', $searchableAttributes, $attribs, $rs);
		}
		
		/** 
		 * Gets the value of the attribute for the user
		 */
		public function getAttribute($ak, $displayMode = false) {
			Loader::model('attribute/categories/user');
			if (!is_object($ak)) {
				$ak = UserAttributeKey::getByHandle($ak);
			}
			if (is_object($ak)) {
				$av = $this->getAttributeValueObject($ak);
				if (is_object($av)) {
					$args = func_get_args();
					if (count($args) > 1) {
						array_shift($args);
						return call_user_func_array(array($av, 'getValue'), $args);						
					} else {
						return $av->getValue($displayMode);
					}
				}
			}
		}
		
		public function getAttributeField($ak) {
			Loader::model('attribute/categories/user');
			if (!is_object($ak)) {
				$ak = UserAttributeKey::getByHandle($ak);
			}
			$value = $this->getAttributeValueObject($ak);
			$ak->render('form', $value);
		}		
		
		public function getAttributeValueObject($ak, $createIfNotFound = false) {
			$db = Loader::db();
			$av = false;
			$v = array($this->getUserID(), $ak->getAttributeKeyID());
			$avID = $db->GetOne("select avID from UserAttributeValues where uID = ? and akID = ?", $v);
			if ($avID > 0) {
				$av = UserAttributeValue::getByID($avID);
				if (is_object($av)) {
					$av->setUser($this);
					$av->setAttributeKey($ak);
				}
			}
			
			if ($createIfNotFound) {
				$cnt = 0;
			
				// Is this avID in use ?
				if (is_object($av)) {
					$cnt = $db->GetOne("select count(avID) from UserAttributeValues where avID = ?", $av->getAttributeValueID());
				}
				
				if ((!is_object($av)) || ($cnt > 1)) {
					$av = $ak->addAttributeValue();
				}
			}
			
			return $av;
		}
		
		public function update($data) {
			$db = Loader::db();
			if ($this->uID) {
				$uName = $this->getUserName();
				$uEmail = $this->getUserEmail();
				$uHasAvatar = $this->hasAvatar();
				$uTimezone = $this->getUserTimezone();
				if (isset($data['uName'])) {
					$uName = $data['uName'];
				}
				if (isset($data['uEmail'])) {
					$uEmail = $data['uEmail'];
				}
				if (isset($data['uHasAvatar'])) {
					$uHasAvatar = $data['uHasAvatar'];
				}
				if( isset($data['uTimezone'])) { 
					$uTimezone = $data['uTimezone'];
				}
				
				$ux = $this->getUserObject();
				$uDefaultLanguage = $ux->getUserDefaultLanguage();				
				if (isset($data['uDefaultLanguage']) && $data['uDefaultLanguage'] != '') {
					$uDefaultLanguage = $data['uDefaultLanguage'];
					if ($_SESSION['uID'] == $this->uID) {
						$_SESSION['uDefaultLanguage'] = $uDefaultLanguage; // make sure to keep the new uDefaultLanguage in there
					} 					
				}
				
				$testChange = false;
				
				if ($data['uPassword'] != null) {
					if (User::encryptPassword($data['uPassword']) == User::encryptPassword($data['uPasswordConfirm'])) {
						$v = array($uName, $uEmail, User::encryptPassword($data['uPassword']), $uHasAvatar, $uTimezone, $uDefaultLanguage, $this->uID);
						$r = $db->prepare("update Users set uName = ?, uEmail = ?, uPassword = ?, uHasAvatar = ?, uTimezone = ?, uDefaultLanguage = ? where uID = ?");
						$res = $db->execute($r, $v);
						
						$testChange = true;

					} else {
						$updateGroups = false;
					}
				} else {
					$v = array($uName, $uEmail, $uHasAvatar, $uTimezone, $uDefaultLanguage, $this->uID);
					$r = $db->prepare("update Users set uName = ?, uEmail = ?, uHasAvatar = ?, uTimezone = ?, uDefaultLanguage = ? where uID = ?");
					$res = $db->execute($r, $v);
				}

				// now we check to see if the user is updated his or her own logged in record
				if (isset($_SESSION['uID']) && $_SESSION['uID'] == $this->uID) {
					$_SESSION['uName'] = $uName; // make sure to keep the new uName in there
				}

				// run any internal event we have for user update
				$ui = UserInfo::getByID($this->uID);
				Events::fire('on_user_update', $ui);
				
				if ($testChange) {
					Events::fire('on_user_change_password', $ui, $data['uPassword']);
				}				
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
				
			$datetime = $dh->getSystemDateTime();
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
		
		/**
		 * @param array $data
		 * @return UserInfo
		 */
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
			Events::fire('on_user_validate', $this);
			return true;
		}
		
		function changePassword($newPassword) { 
			$db = Loader::db();
			if ($this->uID) {
				$v = array(User::encryptPassword($newPassword), $this->uID);
				$q = "update Users set uPassword = ? where uID = ?";
				$r = $db->prepare($q);
				$res = $db->execute($r, $v);

				Events::fire('on_user_change_password', $this, $newPassword);

				return $res;
			}
		}

		function activate() {
			$db = Loader::db();
			$q = "update Users set uIsActive = 1 where uID = '{$this->uID}'";
			$r = $db->query($q);
			Events::fire('on_user_activate', $this);
		}

		function deactivate() {
			$db = Loader::db();
			$q = "update Users set uIsActive = 0 where uID = '{$this->uID}'";
			$r = $db->query($q);
			Events::fire('on_user_deactivate', $this);
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

		function getLastIPAddress() {
			return long2ip($this->uLastIP);
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

		/* 
		 * returns the user's timezone
		 * @return string timezone
		*/
		function getUserTimezone() {
			return $this->uTimezone;
		}
		
		/**
		* Gets the date a user was added to the system, 
		* if user is specified, returns in the current user's timezone
		* @param string $type (system || user)
		* @return string date formated like: 2009-01-01 00:00:00 
		*/
		function getUserDateAdded($type = 'system', $datemask = 'Y-m-d H:i:s') {
			$dh = Loader::helper('date');
			if(ENABLE_USER_TIMEZONES && $type == 'user') {
				return $dh->getLocalDateTime($this->uDateAdded, $datemask);
			} else {
				return $dh->date($datemask, strtotime($this->uDateAdded));
			}
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

		function canAdmin() {
			return strpos($this->permissionSet, 'adm') > -1;
		}

		/** 
		 * File manager permissions at the user level 
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

		function getUserStartDate($type = 'system') {
			// time-release permissions for users
			if(ENABLE_USER_TIMEZONES && $type == 'user') {
				$dh = Loader::helper('date');
				return $dh->getLocalDateTime($this->upStartDate);
			} else {
				return $this->upStartDate;
			}
		}

		/**
		* Gets the date a user was last active on the site 
		* if user is specified, returns in the current user's timezone
		* @param string $type (system || user)
		* @return string date formated like: 2009-01-01 00:00:00 
		*/
		function getLastOnline($type = 'system') {
			if(ENABLE_USER_TIMEZONES && $type == 'user') {
				$dh = Loader::helper('date');
				return $dh->getLocalDateTime($this->uLastOnline);
			} else {
				return $this->uLastOnline;
			}
		}
		
		
		function getUserEndDate($type = 'system') {
			if(ENABLE_USER_TIMEZONES && $type == 'user') {
				$dh = Loader::helper('date');
				return $dh->getLocalDateTime($this->upEndDate);
			} else {
				return $this->upEndDate;
			}
		}
	}
