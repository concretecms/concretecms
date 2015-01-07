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
use Group;
use Zend\Stdlib\DateTime;

class User extends Object
{
    public $uID = '';
    public $uName = '';
    public $uGroups = array();
    public $superUser = false;
    public $uTimezone = null;
    protected $uDefaultLanguage = null;
    // an associative array of all access entity objects that are associated with this user.
    protected $accessEntities = array();
    protected $hasher;
    protected $uLastPasswordChange;

    /** Return an User instance given its id (or null if it's not found)
	* @param int $uID The id of the user
	* @param boolean $login = false Set to true to make the user the current one
	* @param boolean $cacheItemsOnLogin = false Set to true to cache some items when $login is true
	* @return User|null
	*/
    public static function getByUserID($uID, $login = false, $cacheItemsOnLogin = true)
    {
        $db = Loader::db();
        $v = array($uID);
        $q = "SELECT uID, uName, uIsActive, uLastOnline, uTimezone, uDefaultLanguage, uLastPasswordChange FROM Users WHERE uID = ? LIMIT 1";
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
            $nu->uLastPasswordChange = $row['uLastPasswordChange'];
            if ($login) {
                $nu->persist($cacheItemsOnLogin);
                $nu->recordLogin();
            }
        }

        return $nu;
    }

    /**
	 * @param int $uID
	 * @return User
	 */
    public function loginByUserID($uID)
    {
        return User::getByUserID($uID, true);
    }

    public static function isLoggedIn()
    {
        $session = Core::make('session');

        return $session->has('uID') && $session->get('uID') > 0 && $session->has('uName')
            && $session->get('uName') != '' && $session->has('uLastPasswordChange');
    }

    public function checkLogin()
    {

        $session = Core::make('session');
        $aeu = Config::get('concrete.misc.access_entity_updated');
        if ($aeu && $aeu > $session->get('accessEntitiesUpdated')) {
            User::refreshUserGroups();
        }

        if ($session->get('uID') > 0) {
            $db = Loader::db();
            $row = $db->GetRow("select uID, uIsActive, uLastPasswordChange from Users where uID = ? and uName = ?", array($session->get('uID'), $session->get('uName')));
            $checkUID = (isset($row['uID']))?($row['uID']):(false);

            if ($checkUID == $session->get('uID')) {
                if (!$row['uIsActive']) {
                    return false;
                }

                if($row['uLastPasswordChange'] > $session->get('uLastPasswordChange')) {
                    $this->loadError(USER_SESSION_EXPIRED);
                    return false;
                }

                $session->set('uOnlineCheck', time());
                if (($session->get('uOnlineCheck') - $session->get('uLastOnline') > (ONLINE_NOW_TIMEOUT / 2))) {
                    $db = Loader::db();
                    $db->query("update Users set uLastOnline = ? where uID = ?", array($session->get('uOnlineCheck'), $this->uID));
                    $session->set('uLastOnline', $session->get('uOnlineCheck'));
                }

                return true;
            } else {
                return false;
            }
        }
    }

    public function __construct()
    {
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
            if (Config::get('concrete.user.registration.email_registration')) {
                $q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage, uPassword, uLastPasswordChange from Users where uEmail = ?";
            } else {
                $q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage, uPassword, uLastPasswordChange from Users where uName = ?";
            }
            $db = Loader::db();
            $r = $db->query($q, $v);
            if ($r) {
                $row = $r->fetchRow();
                $pw_is_valid_legacy = (defined('PASSWORD_SALT') && User::legacyEncryptPassword($password) == $row['uPassword']);
                $pw_is_valid = $pw_is_valid_legacy || $this->getUserPasswordHasher()->checkPassword($password, $row['uPassword']);
                if ($row['uID'] && $row['uIsValidated'] === '0' && Config::get('concrete.user.registration.validate_email')) {
                    $this->loadError(USER_NON_VALIDATED);
                } elseif ($row['uID'] && $row['uIsActive'] && $pw_is_valid) {
                    $this->uID = $row['uID'];
                    $this->uName = $row['uName'];
                    $this->uIsActive = $row['uIsActive'];
                    $this->uTimezone = $row['uTimezone'];
                    $this->uDefaultLanguage = $row['uDefaultLanguage'];
                    $this->uLastPasswordChange = $row['uLastPasswordChange'];
                    $this->uGroups = $this->_getUserGroups($args[2]);
                    if ($row['uID'] == USER_SUPER_ID) {
                        $this->superUser = true;
                    } else {
                        $this->superUser = false;
                    }
                    $this->recordLogin();
                    if (!$args[2]) {
                        $this->persist();
                    }
                } elseif ($row['uID'] && !$row['uIsActive']) {
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
            } elseif (Session::has('uID')) {
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

    public function recordLogin()
    {
        $db = Loader::db();
        $uLastLogin = $db->getOne("select uLastLogin from Users where uID = ?", array($this->uID));

        /** @var \Concrete\Core\Permission\IPService $iph */
        $iph = Core::make('helper/validation/ip');
        $ip = $iph->getRequestIP();
        $db->query("update Users set uLastIP = ?, uLastLogin = ?, uPreviousLogin = ?, uNumLogins = uNumLogins + 1 where uID = ?", array(($ip === false)?(''):($ip->getIp()), time(), $uLastLogin, $this->uID));
    }

    public function recordView($c)
    {
        $db = Loader::db();
        $uID = ($this->uID > 0) ? $this->uID : 0;
        $cID = $c->getCollectionID();
        $v = array($cID, $uID);
        $db->query("insert into PageStatistics (cID, uID, date) values (?, ?, NOW())", $v);

    }

    // $salt is retained for compatibilty with older versions of concerete5, but not used.
    public function encryptPassword($uPassword, $salt = null)
    {
        return $this->getUserPasswordHasher()->HashPassword($uPassword);
    }

    // this is for compatibility with passwords generated in older versions of Concrete5.
    // Use only for checking password hashes, not generating new ones to store.
    public function legacyEncryptPassword($uPassword)
    {
        return md5($uPassword . ':' . PASSWORD_SALT);
    }

    public function isActive()
    {
        return $this->uIsActive;
    }

    public function isSuperUser()
    {
        return $this->superUser;
    }

    public function getLastOnline()
    {
        return $this->uLastOnline;
    }

    public function getUserName()
    {
        return $this->uName;
    }

    public function isRegistered()
    {
        return $this->getUserID() > 0;
    }

    public function getUserID()
    {
        return $this->uID;
    }

    public function getUserTimezone()
    {
        return $this->uTimezone;
    }

    public function getUserSessionValidSince()
    {
        return $this->uLastPasswordChange;
    }

    public function setAuthTypeCookie($authType)
    {
        $cookie = array($this->getUserID(),$authType);
        $at = AuthenticationType::getByHandle($authType);
        $cookie[] = $at->controller->buildHash($this);
        setcookie(
            'ccmAuthUserHash',
            implode(':', $cookie),
            time() + USER_FOREVER_COOKIE_LIFETIME,
            DIR_REL . '/',
            Config::get('concrete.session.cookie.domain'),
            Config::get('concrete.session.cookie.secure'),
            Config::get('concrete.session.cookie.httponly')
        );
    }

    public function setLastAuthType(AuthenticationType $at)
    {
        $db = Loader::db();
        $db->Execute('UPDATE Users SET uLastAuthTypeID=? WHERE uID=?', array($at->getAuthenticationTypeID(), $this->getUserID()));
    }

    public function getLastAuthType()
    {
        $db = Loader::db();
        $id = $db->getOne('SELECT uLastAuthTypeID FROM Users WHERE uID=?', array($this->getUserID()));

        return intval($id);
    }

    public function unloadAuthenticationTypes()
    {
        $ats = AuthenticationType::getList();
        foreach ($ats as $at) {
            $at->controller->deauthenticate($this);
        }
    }

    public function logout($hard = true)
    {
        // First, we check to see if we have any collection in edit mode
        $this->unloadCollectionEdit();
        $this->unloadAuthenticationTypes();

        $this->invalidateSession($hard);
        Events::dispatch('on_user_logout');
    }

    public function invalidateSession($hard = true) {
        // @todo remove this hard option if `Session::clear()` does what we need.
        if (!$hard) {
            Session::clear();
        } else {
            Session::invalidate();
        }

        if (isset($_COOKIE['ccmUserHash']) && $_COOKIE['ccmUserHash']) {
            setcookie("ccmUserHash", "", 315532800, DIR_REL . '/',
                      Config::get('concrete.session.cookie.domain'),
                      Config::get('concrete.session.cookie.secure'),
                      Config::get('concrete.session.cookie.httponly'));
        }
    }

    public function verifyAuthTypeCookie()
    {
        if ($_COOKIE['ccmAuthUserHash']) {
            list($_uID, $authType, $uHash) = explode(':', $_COOKIE['ccmAuthUserHash']);
            $at = AuthenticationType::getByHandle($authType);
            $u = User::getByUserID($_uID);
            if ((!is_object($u)) || $u->isError()) {
                return;
            }
            if ($at->controller->verifyHash($u, $uHash)) {
                User::loginByUserID($_uID);
            }
        }
    }

    public function getUserGroupObjects()
    {
        $gs = new GroupList();
        $gs->filterByUserID($this->uID);

        return $gs->getResults();
    }

    public function getUserGroups()
    {
        return $this->uGroups;
    }

    /**
	 * Sets a default language for a user record
	 */
    public function setUserDefaultLanguage($lang)
    {
        $db = Loader::db();
        $this->uDefaultLanguage = $lang;
        Session::set('uDefaultLanguage', $lang);
        $db->Execute('update Users set uDefaultLanguage = ? where uID = ?', array($lang, $this->getUserID()));
    }

    /**
	 * Gets the default language for the logged-in user
	 */
    public function getUserDefaultLanguage()
    {
        return $this->uDefaultLanguage;
    }

    /**
     * Gets the default language for the logged-in user
     */
    public function getLastPasswordChange()
    {
        return $this->uLastPasswordChange;
    }

    /**
	 * Checks to see if the current user object is registered. If so, it queries that records
	 * default language. Otherwise, it falls back to sitewide settings.
	 */
    public function getUserLanguageToDisplay()
    {
        if ($this->getUserDefaultLanguage() != '') {
            return $this->getUserDefaultLanguage();
        } else {
            return Config::get('concrete.locale');
        }
    }


    public function refreshUserGroups()
    {
        $session = Core::make('session');
        $session->remove('uGroups');
        $session->remove('accessEntities');
        $ug = $this->_getUserGroups();
        $session->set('uGroups', $ug);
        $this->uGroups = $ug;
    }

    public function getUserAccessEntityObjects()
    {
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

    public function _getUserGroups($disableLogin = false)
    {
        $req = Request::getInstance();
        if ((Session::has('uGroups')) && (!$disableLogin) && (!$req->hasCustomRequestUser())) {
            $ug = Session::get('uGroups');
        } else {
            $db = Loader::db();
            if ($this->uID) {
                $ug[REGISTERED_GROUP_ID] = REGISTERED_GROUP_ID;

                $uID = $this->uID;
                $q = "select gID from UserGroups where uID = ?";
                $r = $db->query($q, array($uID));
                while ($row = $r->fetch()) {
                    $g = Group::getByID($row['gID']);
                    if ($g->isUserExpired($this)) {
                        $this->exitGroup($g);
                    } else {
                        $ug[$row['gID']] = $row['gID'];
                    }
                }
            }

            // now we populate also with guest information, since presumably logged-in users
            // see the same stuff as guest
            $ug[GUEST_GROUP_ID] = GUEST_GROUP_ID;
        }

        return $ug;
    }

    public function enterGroup($g)
    {
        // takes a group object, and, if the user is not already in the group, it puts them into it
        $dt = Loader::helper('date');

        if (is_object($g)) {
            if (!$this->inGroup($g)) {
                $gID = $g->getGroupID();
                $db = Loader::db();
                $db->Replace('UserGroups', array(
                    'uID' => $this->getUserID(),
                    'gID' => $g->getGroupID(),
                    'ugEntered' => $dt->getOverridableNow()
                ),
                array('uID', 'gID'), true);

                if ($g->isGroupBadge()) {

                    $action = UserPointAction::getByHandle('won_badge');
                    if (is_object($action)) {
                        $action->addDetailedEntry($this, $g);
                    }

                    $mh = Loader::helper('mail');
                    $ui = CoreUserInfo::getByID($this->getUserID());
                    $mh->addParameter('badgeName', $g->getGroupDisplayName(false));
                    $mh->addParameter('uDisplayName', $ui->getUserDisplayName());
                    $mh->addParameter('uProfileURL', BASE_URL . View::url('/members/profile', 'view', $this->getUserID()));
                    $mh->addParameter('siteName', Config::get('concrete.site'));
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

    public function exitGroup($g)
    {
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

    public function inGroup($g)
    {
        $db = Loader::db();
        $v = array($this->uID, $g->getGroupID());
        $cnt = $db->GetOne("select gID from UserGroups where uID = ? and gID = ?", $v);

        return $cnt > 0;
    }

    public function loadMasterCollectionEdit($mcID, $ocID)
    {
        // basically, this function loads the master collection ID you're working on into session
        // so you can work on it without the system failing because you're editing a template
        Session::set('mcEditID', $mcID);
        Session::set('ocID', $ocID);

    }

    public function loadCollectionEdit(&$c)
    {
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
                $datetime = $dh->getOverridableNow();
                $q2 = "update Pages set cIsCheckedOut = 1, cCheckedOutUID = '{$uID}', cCheckedOutDatetime = '{$datetime}', cCheckedOutDatetimeLastEdit = '{$datetime}' where cID = '{$cID}'";
                $r2 = $db->query($q2);

                $c->cIsCheckedOut = 1;
                $c->cCheckedOutUID = $uID;
                $c->cCheckedOutDatetime = $datetime;
                $c->cCheckedOutDatetimeLastEdit = $datetime;
            }
        }

    }

    public function unloadCollectionEdit($removeCache = true)
    {
        // first we remove the cached versions of all of these pages
        $db = Loader::db();
        if ($this->getUserID() > 0) {
            $col = $db->GetCol("select cID from Pages where cCheckedOutUID = " . $this->getUserID());
            foreach ($col as $cID) {
                $p = Page::getByID($cID);
                if ($removeCache) {
                    $p->refreshCache();
                }
            }

            $q = "update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null where cCheckedOutUID = ?";
            $db->query($q, array($this->getUserID()));
        }
    }

    public function config($cfKey)
    {
        if ($this->isRegistered()) {
            $db = Loader::db();
            $val = $db->GetOne("select cfValue from ConfigStore where uID = ? and cfKey = ?", array($this->getUserID(), $cfKey));

            return $val;
        }
    }

    public function markPreviousFrontendPage(Page $c)
    {
        Session::set('frontendPreviousPageID', $c->getCollectionID());
    }

    public function getPreviousFrontendPageID()
    {
        return Session::get('frontendPreviousPageID');
    }

    public function saveConfig($cfKey, $cfValue)
    {
        $db = Loader::db();
        $db->Replace('ConfigStore', array('cfKey' => $cfKey, 'cfValue' => $cfValue, 'uID' => $this->getUserID()), array('cfKey', 'uID'), true);
    }

    public function refreshCollectionEdit(&$c)
    {
        if ($this->isLoggedIn() && $c->getCollectionCheckedOutUserID() == $this->getUserID()) {
            $db = Loader::db();
            $cID = $c->getCollectionID();

            $dh = Loader::helper('date');
            $datetime = $dh->getOverridableNow();

            $q = "update Pages set cCheckedOutDatetimeLastEdit = '{$datetime}' where cID = '{$cID}'";
            $r = $db->query($q);

            $c->cCheckedOutDatetimeLastEdit = $datetime;
        }
    }

    public function forceCollectionCheckInAll()
    {
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
    public function getUserPasswordHasher()
    {
        if (isset($this->hasher)) {
            return $this->hasher;
        }
        $this->hasher = new PasswordHash(Config::get('concrete.user.password.hash_cost_log2'), Config::get('concrete.user.password.hash_portable'));

        return $this->hasher;
    }

    /**
     * Manage user session writing
     * @param bool $cache_interface
     */
    public function persist($cache_interface = true)
    {
        $this->refreshUserGroups();

        /** @var Session $session */
        $session = Core::make('session');
        $session->set('uID', $this->getUserID());
        $session->set('uName', $this->getUserName());
        $session->set('uBlockTypesSet', false);
        $session->set('uGroups', $this->getUserGroups());
        $session->set('uLastOnline', $this->getLastOnline());
        $session->set('uTimezone', $this->getUserTimezone());
        $session->set('uDefaultLanguage', $this->getUserDefaultLanguage());
        $session->set('uLastPasswordChange', $this->getLastPasswordChange());

        if ($cache_interface) {
            Loader::helper('concrete/ui')->cacheInterfaceItems();
        }
    }

    public function logIn($cache_interface = true)
    {
        $this->persist($cache_interface);
    }

}
