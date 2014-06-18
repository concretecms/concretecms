<?php
namespace Concrete\Core\User;
use \Concrete\Core\Foundation\Object;
use Loader;
use Config;
use UserInfo as CoreUserInfo;
use Request;
use \Concrete\Core\Authentication\AuthenticationType;
use Events;
use Page;
use GroupList;
use Session;
use \Hautelook\Phpass\PasswordHash;
use \Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Core;

class User extends Object {

	public $uID = '';
	public $uName = '';
	public $uGroups = array();
	public $superUser = false;
	public $uTimezone = NULL;
	protected $uDefaultLanguage = null;
	// an associative array of all access entity objects that are associated with this user.
	protected $accessEntities = array();
	protected $hasher;

	/** Return an User instance given its id (or null if it's not found)
	* @param int $uID The id of the user
	* @param boolean $login = false Set to true to make the user the current one
	* @param boolean $cacheItemsOnLogin = false Set to true to cache some items when $login is true
	* @return User|null
	*/
	public static function getByUserID($uID, $login = false, $cacheItemsOnLogin = true) {
		$db = Loader::db();
		$v = array($uID);
		$q = "SELECT uID, uName, uIsActive, uLastOnline, uTimezone, uDefaultLanguage FROM Users WHERE uID = ? LIMIT 1";
		$r = $db->query($q, $v);
		$row = $r ? $r->FetchRow() : null;
		$nu = null;
		if ($row) {
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
				$session = Core::make('session');
				$session->set('uID', $row['uID']);
				$session->set('uName', $row['uName']);
				$session->set('uBlockTypesSet', false);
				$session->set('uGroups', $nu->uGroups);
				$session->set('uLastOnline', $row['uLastOnline']);
				$session->set('uTimezone', $row['uTimezone']);
				$session->set('uDefaultLanguage', $row['uDefaultLanguage']);
				$session->set('uID', $row['uID']);
				if ($cacheItemsOnLogin) {
					Loader::helper('concrete/ui')->cacheInterfaceItems();
				}
				$nu->recordLogin();
			}
		}
		return $nu;
	}

	/**
	 * @param int $uID
	 * @return User
	 */
	public function loginByUserID($uID) {
		return User::getByUserID($uID, true);
	}

	public static function isLoggedIn() {
		$session = Core::make('session');
		return $session->has('uID') && $session->get('uID') > 0 && $session->has('uName') && $session->get('uName') != '';
	}

	public function checkLogin() {


		$aeu = Config::get('ACCESS_ENTITY_UPDATED');
		if ($aeu && $aeu > Session::get('accessEntitiesUpdated')) {
			User::refreshUserGroups();
		}

		if (Session::get('uID') > 0) {
			$db = Loader::db();
			$row = $db->GetRow("select uID, uIsActive from Users where uID = ? and uName = ?", array(Session::get('uID'), Session::get('uName')));
			$checkUID = $row['uID'];
			if ($checkUID == Session::get('uID')) {
				if (!$row['uIsActive']) {
					return false;
				}
				Session::set('uOnlineCheck', time());
				if ((Session::get('uOnlineCheck') - Session::get('uLastOnline') > (ONLINE_NOW_TIMEOUT / 2))) {
					$db = Loader::db();
					$db->query("update Users set uLastOnline = ? where uID = ?", array(Session::get('uOnlineCheck'), $this->uID));
					Session::set('uLastOnline', Session::get('uOnlineCheck'));
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
				Session::remove('uGroups');
				Session::remove('accessEntities');
			}
			$v = array($username);
			if (defined('USER_REGISTRATION_WITH_EMAIL_ADDRESS') && USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
				$q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage, uPassword from Users where uEmail = ?";
			} else {
				$q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage, uPassword from Users where uName = ?";
			}
			$db = Loader::db();
			$r = $db->query($q, $v);
			if ($r) {
				$row = $r->fetchRow();
				$pw_is_valid_legacy = (defined('PASSWORD_SALT') && User::legacyEncryptPassword($password) == $row['uPassword']);
				$pw_is_valid = $pw_is_valid_legacy || $this->getUserPasswordHasher()->checkPassword($password, $row['uPassword']);
				if ($row['uID'] && $row['uIsValidated'] === '0' && defined('USER_VALIDATE_EMAIL') && USER_VALIDATE_EMAIL == TRUE) {
					$this->loadError(USER_NON_VALIDATED);
				} else if ($row['uID'] && $row['uIsActive'] && $pw_is_valid) {
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
						$session = Core::make('session');
						$session->set('uID', $row['uID']);
						$session->set('uName', $row['uName']);
						$session->set('superUser', $this->superUser);
						$session->set('uBlockTypesSet', false);
						$session->set('uGroups', $this->uGroups);
						$session->set('uTimezone', $this->uTimezone);
						$session->set('uDefaultLanguage', $row['uDefaultLanguage']);
						Loader::helper('concrete/ui')->cacheInterfaceItems();
					}
				} else if ($row['uID'] && !$row['uIsActive']) {
					$this->loadError(USER_INACTIVE);
				} else {
					$this->loadError(USER_INVALID);
				}
				$r->free();
				if ($pw_is_valid_legacy) {
					// this password was generated on a previous version of Concrete5.
					// We re-hash it to make it more secure.
					$v = array($this->getUserPasswordHasher()->HashPassword($password), $this->uID);
					$db->execute($db->prepare("update Users set uPassword = ? where uID = ?"), $v);
				}
			} else {
				$this->getUserPasswordHasher()->hashpassword($password); // hashpassword and checkpassword are slow functions.
								 	// We run one here just take time.
									// Without it an attacker would be able to tell that the
									// username doesn't exist using a timing attack.
				$this->loadError(USER_INVALID);
			}
		} else {
			$req = Request::getInstance();
			if ($req->hasCustomRequestUser()) {
				$this->uID = null;
				$this->uName = null;
				$this->superUser = false;
				$this->uDefaultLanguage = null;
				$this->uTimezone = null;
				$ux = $req->getCustomRequestUser();
				if ($ux && is_object($ux)) {
					$this->uID = $ux->getUserID();
					$this->uName = $ux->getUserName();
					$this->superUser = $ux->getUserID() == USER_SUPER_ID;
					if ($ux->getUserDefaultLanguage()) {
						$this->uDefaultLanguage = $ux->getUserDefaultLanguage();
					}
					$this->uTimezone = $ux->getUserTimezone();
				}
			} else if (Session::has('uID')) {
				$this->uID = Session::get('uID');
				$this->uName = Session::get('uName');
				$this->uTimezone = Session::get('uTimezone');
				if (Session::has('uDefaultLanguage')) {
					$this->uDefaultLanguage = Session::get('uDefaultLanguage');
				}
				$this->superUser = (Session::get('uID') == USER_SUPER_ID) ? true : false;
			} else {
				$this->uID = null;
				$this->uName = null;
				$this->superUser = false;
				$this->uDefaultLanguage = null;
				$this->uTimezone = null;
			}
			$this->uGroups = $this->_getUserGroups();
			if (!isset($args[2]) && !$req->hasCustomRequestUser()) {
				Session::set('uGroups', $this->uGroups);
			}
		}

		return $this;
	}

	function recordLogin() {
		$db = Loader::db();
		$uLastLogin = $db->getOne("select uLastLogin from Users where uID = ?", array($this->uID));

		$db->query("update Users set uLastIP = ?, uLastLogin = ?, uPreviousLogin = ?, uNumLogins = uNumLogins + 1 where uID = ?", array(ip2long(Loader::helper('validation/ip')->getRequestIP()), time(), $uLastLogin, $this->uID));
	}

	function recordView($c) {
		$db = Loader::db();
		$uID = ($this->uID > 0) ? $this->uID : 0;
		$cID = $c->getCollectionID();
		$v = array($cID, $uID);
		$db->query("insert into PageStatistics (cID, uID, date) values (?, ?, NOW())", $v);

	}

    // $salt is retained for compatibilty with older versions of concerete5, but not used.
	public function encryptPassword($uPassword, $salt = null) {
		return $this->getUserPasswordHasher()->HashPassword($uPassword);
    }

	// this is for compatibility with passwords generated in older versions of Concrete5.
	// Use only for checking password hashes, not generating new ones to store.
	public function legacyEncryptPassword($uPassword) {
		return md5($uPassword . ':' . PASSWORD_SALT);
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

	function setAuthTypeCookie($authType) {
		$cookie = array($this->getUserID(),$authType);
		$at = AuthenticationType::getByHandle($authType);
		$cookie[] = $at->controller->buildHash($this);
		setcookie(
			'ccmAuthUserHash',
			implode(':', $cookie),
			time() + USER_FOREVER_COOKIE_LIFETIME,
			DIR_REL . '/',
			defined('SESSION_COOKIE_PARAM_DOMAIN') ? SESSION_COOKIE_PARAM_DOMAIN : '',
			defined('SESSION_COOKIE_PARAM_SECURE') ? SESSION_COOKIE_PARAM_SECURE : false,
			defined('SESSION_COOKIE_PARAM_HTTPONLY') ? SESSION_COOKIE_PARAM_HTTPONLY : false
		);
	}

	public function setLastAuthType(AuthenticationType $at) {
		$db = Loader::db();
		$db->Execute('UPDATE Users SET uLastAuthTypeID=? WHERE uID=?', array($at->getAuthenticationTypeID(), $this->getUserID()));
	}

	public function getLastAuthType() {
		$db = Loader::db();
		$id = $db->getOne('SELECT uLastAuthTypeID FROM Users WHERE uID=?', array($this->getUserID()));
		return intval($id);
	}

	function unloadAuthenticationTypes() {
		$ats = AuthenticationType::getList();
		foreach ($ats as $at) {
			$at->controller->deauthenticate($this);
		}
	}

	function logout($hard = true) {
		// First, we check to see if we have any collection in edit mode
		$this->unloadCollectionEdit();
		$this->unloadAuthenticationTypes();
		@session_unset();
		if ($hard == true) {
			@session_destroy();
		}

		Events::dispatch('on_user_logout');

		if (isset($_COOKIE['ccmUserHash']) && $_COOKIE['ccmUserHash']) {
			setcookie("ccmUserHash", "", 315532800, DIR_REL . '/',
			(defined('SESSION_COOKIE_PARAM_DOMAIN')?SESSION_COOKIE_PARAM_DOMAIN:''),
			(defined('SESSION_COOKIE_PARAM_SECURE')?SESSION_COOKIE_PARAM_SECURE:false),
			(defined('SESSION_COOKIE_PARAM_HTTPONLY')?SESSION_COOKIE_PARAM_HTTPONLY:false));
		}
	}

	function verifyAuthTypeCookie() {
		if ($_COOKIE['ccmAuthUserHash']) {
			list($_uID, $authType, $uHash) = explode(':', $_COOKIE['ccmAuthUserHash']);
			$at = AuthenticationType::getByHandle($authType);
			$u = User::getByUserID($_uID);
			if ($u->isError()) {
				return;
			}
			if ($at->controller->verifyHash($u, $uHash)) {
				User::loginByUserID($_uID);
			}
		}
	}

	public function getUserGroupObjects() {
		$gs = new GroupList();
		$gs->filterByUserID($this->uID);
		return $gs->get();
	}

	function getUserGroups() {
		return $this->uGroups;
	}

	/**
	 * Sets a default language for a user record
	 */
	public function setUserDefaultLanguage($lang) {
		$db = Loader::db();
		$this->uDefaultLanguage = $lang;
		Session::set('uDefaultLanguage', $lang);
		$db->Execute('update Users set uDefaultLanguage = ? where uID = ?', array($lang, $this->getUserID()));
	}

	/**
	 * Gets the default language for the logged-in user
	 */
	public function getUserDefaultLanguage() {
		return $this->uDefaultLanguage;
	}

	/**
	 * Checks to see if the current user object is registered. If so, it queries that records
	 * default language. Otherwise, it falls back to sitewide settings.
	 */
	public function getUserLanguageToDisplay() {
		if ($this->getUserDefaultLanguage() != '') {
			return $this->getUserDefaultLanguage();
		} else if (defined('LOCALE')) {
			return LOCALE;
		} else {
			return SITE_LOCALE;
		}
	}


	function refreshUserGroups() {
		$session = Core::make('session');
		$session->remove('uGroups');
		$session->remove('accessEntities');
		$ug = $this->_getUserGroups();
		$session->set('uGroups', $ug);
		$this->uGroups = $ug;
	}

	public function getUserAccessEntityObjects() {
		$req = Request::getInstance();
		if ($req->hasCustomRequestUser()) {
			// we bypass session-saving performance
			// and we don't save them in session.
			return PermissionAccessEntity::getForUser($this);
		}

		if (Session::has('accessEntities')) {
			$entities = Session::get('accessEntities');
		} else {
			$entities = PermissionAccessEntity::getForUser($this);
			Session::set('accessEntities', $entities);
			Session::set('accessEntitiesUpdated', time());
		}
		return $entities;
	}

	function _getUserGroups($disableLogin = false) {
		$req = Request::getInstance();
		if ((Session::has('uGroups')) && (!$disableLogin) && (!$req->hasCustomRequestUser())) {
			$ug = Session::get('uGroups');
		} else {
			$db = Loader::db();
			if ($this->uID) {
				$ug[REGISTERED_GROUP_ID] = REGISTERED_GROUP_ID;

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

	function enterGroup($g) {
		// takes a group object, and, if the user is not already in the group, it puts them into it
		$dt = Loader::helper('date');

		if (is_object($g)) {
			if (!$this->inGroup($g)) {
				$gID = $g->getGroupID();
				$db = Loader::db();
				$db->Replace('UserGroups', array(
					'uID' => $this->getUserID(),
					'gID' => $g->getGroupID(),
					'ugEntered' => $dt->getSystemDateTime()
				),
				array('uID', 'gID'), true);

				if ($g->isGroupBadge()) {

					$action = UserPointAction::getByHandle('won_badge');
					if (is_object($action)) {
						$action->addDetailedEntry($this, $g);
					}

					$mh = Loader::helper('mail');
					$ui = CoreUserInfo::getByID($this->getUserID());
					$mh->addParameter('badgeName', $g->getGroupName());
					$mh->addParameter('uDisplayName', $ui->getUserDisplayName());
					$mh->addParameter('uProfileURL', BASE_URL . View::url('/account/profile/public_profile', 'view', $this->getUserID()));
					$mh->to($ui->getUserEmail());
					$mh->load('won_badge');
					$mh->sendMail();
				}

				$ue = new \Concrete\Core\User\Event\UserGroup($this);
				$ue->setGroupObject($g);
				Events::dispatch('on_user_enter_group', $ue);

			}
		}
	}


	function exitGroup($g) {
		// takes a group object, and, if the user is in the group, they exit the group
		if (is_object($g)) {
			$gID = $g->getGroupID();
			$db = Loader::db();


			$ue = new \Concrete\Core\User\Event\UserGroup($this);
			$ue->setGroupObject($g);
			Events::dispatch('on_user_exit_group', $ue);

			$q = "delete from UserGroups where uID = '{$this->uID}' and gID = '{$gID}'";
			$r = $db->query($q);
		}
	}

	function inGroup($g) {
		$db = Loader::db();
		$v = array($this->uID, $g->getGroupID());
		$cnt = $db->GetOne("select gID from UserGroups where uID = ? and gID = ?", $v);
		return $cnt > 0;
	}

	function loadMasterCollectionEdit($mcID, $ocID) {
		// basically, this function loads the master collection ID you're working on into session
		// so you can work on it without the system failing because you're editing a template
		Session::set('mcEditID', $mcID);
		Session::set('ocID', $ocID);

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
				Session::set('editCID', $cID);
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

			$q = "update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null where cCheckedOutUID = ?";
			$db->query($q, array($this->getUserID()));
		}
	}

	public function config($cfKey) {
		if ($this->isRegistered()) {
			$db = Loader::db();
			$val = $db->GetOne("select cfValue from Config where uID = ? and cfKey = ?", array($this->getUserID(), $cfKey));
			return $val;
		}
	}

	public function markPreviousFrontendPage(Page $c) {
		Session::set('frontendPreviousPageID', $c->getCollectionID());
	}

	public function getPreviousFrontendPageID() {
		return Session::get('frontendPreviousPageID');
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

	/**
	 * @see PasswordHash
	 *
	 * @return PasswordHash
	 */
	function getUserPasswordHasher() {
		if (isset($this->hasher)) {
			return $this->hasher;
		}
		$this->hasher = new PasswordHash(PASSWORD_HASH_COST_LOG2, PASSWORD_HASH_PORTABLE);
		return $this->hasher;
	}

}
