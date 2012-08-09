<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * The user object deals primarily with logging users in and session-related activities.
 *
 * @package Users
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

	class Concrete5_Model_User extends Object { 
	
		public $uID = '';
		public $uName = '';
		public $uGroups = array();
		public $superUser = false;
		public $uTimezone = NULL;
		protected $uDefaultLanguage = null;
		// an associative array of all access entity objects that are associated with this user.
		protected $accessEntities = array();
		
		/**
		 * @param int $uID
		 * @param boolean $login
		 * @return User
		 */
		public static function getByUserID($uID, $login = false, $cacheItemsOnLogin = true) {
			$db = Loader::db();
			$v = array($uID);
			$q = "SELECT uID, uName, uIsActive, uLastOnline, uTimezone, uDefaultLanguage FROM Users WHERE uID = ?";
			$r = $db->query($q, $v);
			if ($r) {
				$row = $r->fetchRow();
				$nu = new User();
				$nu->uID = $row['uID'];
				$nu->uName = $row['uName'];
				$nu->uIsActive = $row['uIsActive'];
				$nu->uDefaultLanguage = $row['uDefaultLanguage'];
				$nu->uLastLogin = $row['uLastLogin'];
				$nu->uTimezone = $row['uTimezone'];
				$nu->uGroups = $nu->_getUserGroups(true);
				$nu->superUser = ($nu->getUserID() == USER_SUPER_ID);
				if ($login) {
					User::regenerateSession();
					$_SESSION['uID'] = $row['uID'];
					$_SESSION['uName'] = $row['uName'];
					$_SESSION['uBlockTypesSet'] = false;
					$_SESSION['uGroups'] = $nu->uGroups;
					$_SESSION['uLastOnline'] = $row['uLastOnline'];
					$_SESSION['uTimezone'] = $row['uTimezone'];
					$_SESSION['uDefaultLanguage'] = $row['uDefaultLanguage'];
					if ($cacheItemsOnLogin) { 
						Loader::helper('concrete/interface')->cacheInterfaceItems();
					}
					$nu->recordLogin();
				}
			}
			return $nu;
		}
		
		protected static function regenerateSession() {
			unset($_SESSION['dashboardMenus']);
			unset($_SESSION['ccmQuickNavRecentPages']);
			unset($_SESSION['accessEntities']);
			@session_regenerate_id(true);
		}
		
		/**
		 * @param int $uID
		 * @return User
		 */
		public function loginByUserID($uID) {
			return User::getByUserID($uID, true);
		}
		
		public static function isLoggedIn() {
			return $_SESSION['uID'] > 0 && $_SESSION['uName'] != '';
		}
		
		public function checkLogin() {
			$aeu = Config::get('ACCESS_ENTITY_UPDATED');
			if ($aeu && $aeu > $_SESSION['accessEntitiesUpdated']) {
				User::refreshUserGroups();
			}
			
			if ($_SESSION['uID'] > 0) {
				$db = Loader::db();
				$row = $db->GetRow("select uID, uIsActive from Users where uID = ? and uName = ?", array($_SESSION['uID'], $_SESSION['uName']));
				$checkUID = $row['uID'];
				if ($checkUID == $_SESSION['uID']) {
					if (!$row['uIsActive']) {
						return false;
					}
					$_SESSION['uOnlineCheck'] = time();
					if (($_SESSION['uOnlineCheck'] - $_SESSION['uLastOnline']) > (ONLINE_NOW_TIMEOUT / 2)) {
						$db = Loader::db();
						$db->query("update Users set uLastOnline = {$_SESSION['uOnlineCheck']} where uID = {$this->uID}");
						$_SESSION['uLastOnline'] = $_SESSION['uOnlineCheck'];
					}
					return true;
				} else {
					return false;
				}
			}
		}
		
		public function __construct() {
			$args = func_get_args();
			
			if (isset($args[1])) {
				// first, we check to see if the username and password match the admin username and password
				// $username = uName normally, but if not it's email address
				
				$username = $args[0];
				$password = $args[1];
				if (!$args[2]) {
					$_SESSION['uGroups'] = false;
				}
				$password = User::encryptPassword($password, PASSWORD_SALT);
				$v = array($username, $password);
				if (defined('USER_REGISTRATION_WITH_EMAIL_ADDRESS') && USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
					$q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage from Users where uEmail = ? and uPassword = ?";
				} else {
					$q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage from Users where uName = ? and uPassword = ?";
				}
				$db = Loader::db();
				$r = $db->query($q, $v);
				if ($r) {
					$row = $r->fetchRow(); 
					if ($row['uID'] && $row['uIsValidated'] === '0' && defined('USER_VALIDATE_EMAIL_REQUIRED') && USER_VALIDATE_EMAIL_REQUIRED == TRUE) {
						$this->loadError(USER_NON_VALIDATED);
					} else if ($row['uID'] && $row['uIsActive']) {
						$this->uID = $row['uID'];
						$this->uName = $row['uName'];
						$this->uIsActive = $row['uIsActive'];
						$this->uTimezone = $row['uTimezone'];
						$this->uDefaultLanguage = $row['uDefaultLanguage'];
						$this->uGroups = $this->_getUserGroups($args[2]);
						if ($row['uID'] == USER_SUPER_ID) {
							$this->superUser = true;
						} else {
							$this->superUser = false;
						}
						$this->recordLogin();
						if (!$args[2]) {
							User::regenerateSession();
							$_SESSION['uID'] = $row['uID'];
							$_SESSION['uName'] = $row['uName'];
							$_SESSION['superUser'] = $this->superUser;
							$_SESSION['uBlockTypesSet'] = false;
							$_SESSION['uGroups'] = $this->uGroups;
							$_SESSION['uTimezone'] = $this->uTimezone;
							$_SESSION['uDefaultLanguage'] = $this->uDefaultLanguage;
							Loader::helper('concrete/interface')->cacheInterfaceItems();
						}
					} else if ($row['uID'] && !$row['uIsActive']) {
						$this->loadError(USER_INACTIVE);
					} else {
						$this->loadError(USER_INVALID);
					}
					$r->free();
				} else {
					$this->loadError(USER_INVALID);
				}
			} else {
				$req = Request::get();
				if ($req->hasCustomRequestUser()) {
					$this->uID = null;
					$this->uName = null;
					$this->superUser = false;
					$this->uDefaultLanguage = null;
					$this->uTimezone = null;
					$ux = $req->getCustomRequestUser();
					if ($ux) {
						$this->uID = $ux->getUserID();
						$this->uName = $ux->getUserName();
						$this->superUser = $ux->getUserID() == USER_SUPER_ID;
						if ($ux->getUserDefaultLanguage()) {
							$this->uDefaultLanguage = $ux->getUserDefaultLanguage();
						}
						$this->uTimezone = $ux->getUserTimezone();
					}
				} else if (isset($_SESSION['uID'])) {
					$this->uID = $_SESSION['uID'];
					$this->uName = $_SESSION['uName'];
					$this->uTimezone = $_SESSION['uTimezone'];
					if (isset($_SESSION['uDefaultLanguage'])) {
						$this->uDefaultLanguage = $_SESSION['uDefaultLanguage'];
					}
					$this->superUser = ($_SESSION['uID'] == USER_SUPER_ID) ? true : false;
				} else {
					$this->uID = null;
					$this->uName = null;
					$this->superUser = false;
					$this->uDefaultLanguage = null;
					$this->uTimezone = null;
				}
				$this->uGroups = $this->_getUserGroups();
				if (!isset($args[2]) && !$req->hasCustomRequestUser()) {
					$_SESSION['uGroups'] = $this->uGroups;
				}
			}
			
			return $this;
		}
		
		function recordLogin() {
			$db = Loader::db();
			$uLastLogin = $db->getOne("select uLastLogin from Users where uID = ?", array($this->uID));
			
			$db->query("update Users set uLastIP = ?, uLastLogin = ?, uPreviousLogin = ?, uNumLogins = uNumLogins + 1 where uID = ?", array(ip2long($_SERVER['REMOTE_ADDR']), time(), $uLastLogin, $this->uID));
		}
		
		function recordView($c) {
			$db = Loader::db();
			$uID = ($this->uID > 0) ? $this->uID : 0;
			$cID = $c->getCollectionID();
			$v = array($cID, $uID);
			$db->query("insert into PageStatistics (cID, uID, date) values (?, ?, NOW())", $v);
			
		}
		
		public function encryptPassword($uPassword, $salt = PASSWORD_SALT) {
			return md5($uPassword . ':' . $salt);
		}
		
		function isActive() {
			return $this->uIsActive;
		}
		
		function isSuperUser() {
			return $this->superUser;
		}
		
		function getLastOnline() {
			return $this->uLastOnline;
		}
		
		function getUserName() {
			return $this->uName;
		}
		
		function isRegistered() {
			return $this->getUserID() > 0;
		}
		
		function getUserID() {
			return $this->uID;
		}
		
		function getUserTimezone() {
			return $this->uTimezone;
		}
		
		function logout() {
			// First, we check to see if we have any collection in edit mode
			$this->unloadCollectionEdit();
			@session_unset();
			@session_destroy();
			Events::fire('on_user_logout');
			if ($_COOKIE['ccmUserHash']) {
				setcookie("ccmUserHash", "", 315532800, DIR_REL . '/');
			}
		}
		
		function checkUserForeverCookie() {
			if ($_COOKIE['ccmUserHash']) {
				$hashVal = explode(':', $_COOKIE['ccmUserHash']);
				$_uID = $hashVal[0];
				$uHash = $hashVal[1];
				if ($uHash == md5(PASSWORD_SALT . $_uID)) {
					User::loginByUserID($_uID);
				}
			}
		}
		
		function setUserForeverCookie() {
			$hashVal = md5(PASSWORD_SALT . $this->getUserID());
			setcookie("ccmUserHash", $this->getUserID() . ':' . $hashVal, time() + 1209600, DIR_REL . '/');
		}
		
		function getUserGroups() {
			$ugtmp = array();
			// we have to do this because we don't have a localized version of the guest and registered group names
			// when we called _getUserGroups() below. So we have to push out the defining of the guest and registered
			// names til runtime
			
			foreach($this->uGroups as $key => $value) {
				$ugtmp[$key] = $value;
				if ($key == GUEST_GROUP_ID) {
					$ugtmp[$key] = GUEST_GROUP_NAME;
				}
				if ($key == REGISTERED_GROUP_ID) {
					$ugtmp[$key] = REGISTERED_GROUP_NAME;
				}
			}
			return $ugtmp;
		}
		
		/** 
		 * Sets a default language for a user record 
		 */
		public function setUserDefaultLanguage($lang) {
			$db = Loader::db();
			$this->uDefaultLanguage = $lang;
			$_SESSION['uDefaultLanguage'] = $lang;
			$db->Execute('update Users set uDefaultLanguage = ? where uID = ?', array($lang, $this->getUserID()));
		}
		
		/** 
		 * Gets the default language for the logged-in user
		 */
		public function getUserDefaultLanguage() {
			return $this->uDefaultLanguage;
		}
		
		function refreshUserGroups() {
			unset($_SESSION['uGroups']);
			unset($_SESSION['accessEntities']);
			$ug = $this->_getUserGroups();
			$_SESSION['uGroups'] = $ug;
			$this->uGroups = $ug;
		}
		
		public function getUserAccessEntityObjects() {
			$req = Request::get();
			if ($req->hasCustomRequestUser()) {
				// we bypass session-saving performance
				// and we don't save them in session.
				return PermissionAccessEntity::getForUser($this);
			}
			
			if (isset($_SESSION['accessEntities'])) {
				$entities = $_SESSION['accessEntities'];
			} else {
				$entities = PermissionAccessEntity::getForUser($this);
				$_SESSION['accessEntities'] = $entities;
				$_SESSION['accessEntitiesUpdated'] = time();
			}
			return $entities;
		}
		
		function _getUserGroups($disableLogin = false) {
			$req = Request::get();
			if ((!empty($_SESSION['uGroups'])) && (!$disableLogin) && (!$req->hasCustomRequestUser())) {
				$ug = $_SESSION['uGroups'];
			} else {
				$db = Loader::db();
				if ($this->uID) {
					$ug[REGISTERED_GROUP_ID] = REGISTERED_GROUP_ID;
					//$_SESSION['uGroups'][REGISTERED_GROUP_ID] = REGISTERED_GROUP_NAME;

					$uID = $this->uID;
					$q = "select Groups.gID, Groups.gName, Groups.gUserExpirationIsEnabled, Groups.gUserExpirationSetDateTime, Groups.gUserExpirationInterval, Groups.gUserExpirationAction, Groups.gUserExpirationMethod, UserGroups.ugEntered from UserGroups inner join Groups on (UserGroups.gID = Groups.gID) where UserGroups.uID = '$uID'";
					$r = $db->query($q);
					if ($r) {
						while ($row = $r->fetchRow()) {
							$expire = false;					
							if ($row['gUserExpirationIsEnabled']) {
								switch($row['gUserExpirationMethod']) {
									case 'SET_TIME':
										if (time() > strtotime($row['gUserExpirationSetDateTime'])) {
											$expire = true;
										}
										break;
									case 'INTERVAL':
										if (time() > strtotime($row['ugEntered']) + ($row['gUserExpirationInterval'] * 60)) {
											$expire = true;
										}
										break;
								}	
							}
							
							if ($expire) {
								if ($row['gUserExpirationAction'] == 'REMOVE' || $row['gUserExpirationAction'] == 'REMOVE_DEACTIVATE') {
									$db->Execute('delete from UserGroups where uID = ? and gID = ?', array($uID, $row['gID']));
								}
								if ($row['gUserExpirationAction'] == 'DEACTIVATE' || $row['gUserExpirationAction'] == 'REMOVE_DEACTIVATE') {
									$db->Execute('update Users set uIsActive = 0 where uID = ?', array($uID));
								}
							} else {
								$ug[$row['gID']] = $row['gName'];
							}
							
						}
						$r->free();
					}
				}
				
				// now we populate also with guest information, since presumably logged-in users 
				// see the same stuff as guest
				$ug[GUEST_GROUP_ID] = GUEST_GROUP_ID;
			}
			
			return $ug;
		}
		
		function enterGroup($g, $joinType = "") {
			// takes a group object, and, if the user is not already in the group, it puts them into it
			$dt = Loader::helper('date');
			
			if (is_object($g)) {
				$gID = $g->getGroupID();
				$db = Loader::db();
				$db->Replace('UserGroups', array(
					'uID' => $this->getUserID(),
					'gID' => $g->getGroupID(),
					'type' => $joinType,
					'ugEntered' => $dt->getSystemDateTime()
				),
				array('uID', 'gID'), true);
				Events::fire('on_user_enter_group', $this, $g);
			}
		}
		
		public function updateGroupMemberType($g, $joinType) {
			if ($g instanceof Group) {
				$db = Loader::db();
				$dt = Loader::helper('date');
				$db->Execute('update UserGroups set type = ?, ugEntered = ? where uID = ? and gID = ?', array($joinType, $dt->getSystemDateTime(), $this->uID, $g->getGroupID()));
			}
		}
		
		function exitGroup($g) {
			// takes a group object, and, if the user is in the group, they exit the group
			if (is_object($g)) {
				$gID = $g->getGroupID();
				$db = Loader::db();
				
				$ret = Events::fire('on_user_exit_group', $this, $g);
				$q = "delete from UserGroups where uID = '{$this->uID}' and gID = '{$gID}'";
				$r = $db->query($q);	
			}		
		}
		
		function getGroupMemberType($g) {
			$db = Loader::db();
			$r = $db->GetOne("select type from UserGroups where uID = ? and gID = ?", array($this->getUserID(), $g->getGroupID()));
			return $r;
		}
		
		function inGroup($g, $joinType = null) {
			$db = Loader::db();
			if (isset($joinType) && is_object($g)) {
				$v = array($this->uID, $g->getGroupID(), $joinType);
				$cnt = $db->GetOne("select gID from UserGroups where uID = ? and gID = ? and type = ?", $v);
			} else if (is_object($g)) {
				$v = array($this->uID, $g->getGroupID());
				$cnt = $db->GetOne("select gID from UserGroups where uID = ? and gID = ?", $v);
			}
			
			return $cnt > 0;
		}
		
		function loadMasterCollectionEdit($mcID, $ocID) {
			// basically, this function loads the master collection ID you're working on into session
			// so you can work on it without the system failing because you're editing a template
			$_SESSION['mcEditID'] = $mcID;
			$_SESSION['ocID'] = $ocID;
		}
		
		function loadCollectionEdit(&$c) {
			$c->refreshCache();

			// can only load one page into edit mode at a time.
			if ($c->isCheckedOut()) {
				return false;
			}
			
			$db = Loader::db();
			$cID = $c->getCollectionID();
			// first, we check to see if we have a collection in edit mode. If we do, we relinquish it
			$this->unloadCollectionEdit(false);
			
			$q = "select cIsCheckedOut, cCheckedOutDatetime from Pages where cID = '{$cID}'";
			$r = $db->query($q);
			if ($r) {
				$row = $r->fetchRow();
				if (!$row['cIsCheckedOut']) {
					$_SESSION['editCID'] = $cID;
					$uID = $this->getUserID();
					$dh = Loader::helper('date');
					$datetime = $dh->getSystemDateTime();
					$q2 = "update Pages set cIsCheckedOut = 1, cCheckedOutUID = '{$uID}', cCheckedOutDatetime = '{$datetime}', cCheckedOutDatetimeLastEdit = '{$datetime}' where cID = '{$cID}'";
					$r2 = $db->query($q2);
					
					$c->cIsCheckedOut = 1;
					$c->cCheckedOutUID = $uID;
					$c->cCheckedOutDatetime = $datetime;
					$c->cCheckedOutDatetimeLastEdit = $datetime;
				}
			}
			
		}
			
		function unloadCollectionEdit($removeCache = true) {		
			// first we remove the cached versions of all of these pages
			$db = Loader::db();
			if ($this->getUserID() > 0) { 
				$col = $db->GetCol("select cID from Pages where cCheckedOutUID = " . $this->getUserID());
				foreach($col as $cID) {
					$p = Page::getByID($cID);
					if ($removeCache) {
						$p->refreshCache();
					}
				}
				
				$q = "update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null where cCheckedOutUID = " . $this->getUserID();
				$r = $db->query($q);
			}
		}
		
		public function config($cfKey) {
			if ($this->isRegistered()) {
				$db = Loader::db();
				$val = $db->GetOne("select cfValue from Config where uID = ? and cfKey = ?", array($this->getUserID(), $cfKey));
				return $val;
			}
		}
		
		public function saveConfig($cfKey, $cfValue) {
			$db = Loader::db();
			$db->Replace('Config', array('cfKey' => $cfKey, 'cfValue' => $cfValue, 'uID' => $this->getUserID()), array('cfKey', 'uID'), true);
		}
		
		function refreshCollectionEdit(&$c) {
			if ($this->isLoggedIn() && $c->getCollectionCheckedOutUserID() == $this->getUserID()) {
				$db = Loader::db();
				$cID = $c->getCollectionID();
				
				$dh = Loader::helper('date');
				$datetime = $dh->getSystemDateTime();
					
				$q = "update Pages set cCheckedOutDatetimeLastEdit = '{$datetime}' where cID = '{$cID}'";
				$r = $db->query($q);
					
				$c->cCheckedOutDatetimeLastEdit = $datetime;
			}
		}
		
		function forceCollectionCheckInAll() {
			// This function forces checkin to take place
			$db = Loader::db();
			$q = "update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null";
			$r = $db->query($q);
			return $r;
		}
				
	}