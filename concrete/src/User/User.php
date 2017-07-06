<?php
namespace Concrete\Core\User;

use Concrete\Core\Foundation\Object;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Page\Page;
use Concrete\Core\User\Group\GroupList;
use Hautelook\Phpass\PasswordHash;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\User\Point\Action\Action as UserPointAction;

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
     * @param bool $login = false Set to true to make the user the current one
     * @param bool $cacheItemsOnLogin = false Set to true to cache some items when $login is true
     *
     * @return User|null
     */
    public static function getByUserID($uID, $login = false, $cacheItemsOnLogin = true)
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        $v = array($uID);
        $q = "SELECT uID, uName, uIsActive, uLastOnline, uTimezone, uDefaultLanguage, uLastPasswordChange FROM Users WHERE uID = ? LIMIT 1";
        $r = $db->query($q, $v);
        $row = $r ? $r->FetchRow() : null;
        $nu = null;
        if ($row) {
            $nu = new static();
            $nu->setPropertiesFromArray($row);
            $nu->uGroups = $nu->_getUserGroups(true);
            $nu->superUser = ($nu->getUserID() == USER_SUPER_ID);
            if ($login) {
                $nu->persist($cacheItemsOnLogin);
                $nu->recordLogin();
            }
        }

        return $nu;
    }

    /**
     * @param int $uID
     *
     * @return User
     */
    public static function loginByUserID($uID)
    {
        return static::getByUserID($uID, true);
    }

    /**
     * Return true if user is logged in.
     *
     * @return bool
     */
    public static function isLoggedIn()
    {
        $app = Application::getFacadeApplication();
        $session = $app['session'];

        return $session->has('uID') && $session->get('uID') > 0;
    }

    /**
     * @return bool
     */
    public function checkLogin()
    {
        $app = Application::getFacadeApplication();
        $session = $app['session'];
        $config = $app['config'];

        $aeu = $config->get('concrete.misc.access_entity_updated');
        if ($aeu && $aeu > $session->get('accessEntitiesUpdated')) {
            self::refreshUserGroups();
        }

        $invalidate = $app->make('Concrete\Core\Session\SessionValidatorInterface')->handleSessionValidation($session);
        if ($invalidate) {
            $this->loadError(USER_SESSION_EXPIRED);
        }

        if ($session->get('uID') > 0) {
            $db = $app['database']->connection();

            $row = $db->GetRow("select * from Users where uID = ? and uName = ?", array($session->get('uID'), $session->get('uName')));
            $checkUID = (isset($row['uID'])) ? ($row['uID']) : (false);

            if ($checkUID == $session->get('uID')) {
                if (!$row['uIsActive']) {
                    return false;
                }

                if ($row['uLastPasswordChange'] > $session->get('uLastPasswordChange')) {
                    $this->loadError(USER_SESSION_EXPIRED);

                    return false;
                }

                if (!empty($row['uIsPasswordReset'])) {
                    return false;
                }

                $session->set('uOnlineCheck', time());
                if (($session->get('uOnlineCheck') - $session->get('uLastOnline') > (ONLINE_NOW_TIMEOUT / 2))) {
                    $db->query("update Users set uLastOnline = ? where uID = ?", array($session->get('uOnlineCheck'), $this->uID));
                    $session->set('uLastOnline', $session->get('uOnlineCheck'));
                }

                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * @return UserInfo|null
     */
    public function getUserInfoObject()
    {
        return UserInfo::getByID($this->uID);
    }

    public function __construct()
    {
        $app = Application::getFacadeApplication();
        $args = func_get_args();
        $session = $app['session'];
        $config = $app['config'];

        if (isset($args[1])) {
            // first, we check to see if the username and password match the admin username and password
            // $username = uName normally, but if not it's email address

            $username = $args[0];
            $password = $args[1];
            $disableLogin = isset($args[2]) ? (bool) $args[2] : false;
            if (!$disableLogin) {
                $session->remove('uGroups');
                $session->remove('accessEntities');
            }
            $v = array($username);
            if ($config->get('concrete.user.registration.email_registration')) {
                $q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage, uPassword, uLastPasswordChange from Users where uEmail = ?";
            } else {
                $q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage, uPassword, uLastPasswordChange from Users where uName = ?";
            }

            $db = $app->make('Concrete\Core\Database\Connection\Connection');
            $r = $db->query($q, $v);
            if ($r) {
                $row = $r->fetchRow();
                $pw_is_valid_legacy = ($config->get('concrete.user.password.legacy_salt') && self::legacyEncryptPassword($password) == $row['uPassword']);
                $pw_is_valid = $pw_is_valid_legacy || $this->getUserPasswordHasher()->checkPassword($password, $row['uPassword']);
                if ($row['uID'] && $row['uIsValidated'] === '0' && $config->get('concrete.user.registration.validate_email')) {
                    $this->loadError(USER_NON_VALIDATED);
                } elseif ($row['uID'] && $row['uIsActive'] && $pw_is_valid) {
                    $this->uID = $row['uID'];
                    $this->uName = $row['uName'];
                    $this->uIsActive = $row['uIsActive'];
                    $this->uTimezone = $row['uTimezone'];
                    $this->uDefaultLanguage = $row['uDefaultLanguage'];
                    $this->uLastPasswordChange = $row['uLastPasswordChange'];
                    $this->uGroups = $this->_getUserGroups($disableLogin);
                    if ($row['uID'] == USER_SUPER_ID) {
                        $this->superUser = true;
                    } else {
                        $this->superUser = false;
                    }
                    $this->recordLogin();
                    if (!$disableLogin) {
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
                $this->getUserPasswordHasher()->HashPassword($password); // HashPassword and CheckPassword are slow functions.
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
            } elseif ($session->has('uID')) {
                $this->uID = $session->get('uID');
                $this->uName = $session->get('uName');
                $this->uTimezone = $session->get('uTimezone');
                if ($session->has('uDefaultLanguage')) {
                    $this->uDefaultLanguage = $session->get('uDefaultLanguage');
                }
                $this->superUser = ($session->get('uID') == USER_SUPER_ID) ? true : false;
            } else {
                $this->uID = null;
                $this->uName = null;
                $this->superUser = false;
                $this->uDefaultLanguage = null;
                $this->uTimezone = null;
            }
            $this->uGroups = $this->_getUserGroups();
            if (!isset($args[2]) && !$req->hasCustomRequestUser()) {
                $session->set('uGroups', $this->uGroups);
            }
        }

        return $this;
    }

    /**
     * Increment number of logins and update login timestamps.
     *
     * @throws \Exception
     */
    public function recordLogin()
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();
        $uLastLogin = $db->getOne("select uLastLogin from Users where uID = ?", array($this->uID));

        /** @var \Concrete\Core\Permission\IPService $iph */
        $iph = $app->make('helper/validation/ip');
        $ip = $iph->getRequestIP();
        $db->query("update Users set uLastIP = ?, uLastLogin = ?, uPreviousLogin = ?, uNumLogins = uNumLogins + 1 where uID = ?", array(($ip === false) ? ('') : ($ip->getIp()), time(), $uLastLogin, $this->uID));
    }

    /**
     * Update PageStatistics
     *
     * @param Page $c
     */
    public function recordView($c)
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        $uID = ($this->uID > 0) ? $this->uID : 0;
        $cID = $c->getCollectionID();
        $v = array($cID, $uID);
        $db->query("insert into PageStatistics (cID, uID, date) values (?, ?, NOW())", $v);
    }

    /**
     * $salt is retained for compatibilty with older versions of concerete5, but not used.
     *
     * @param string $uPassword
     * @param null $salt
     * @return string
     */
    public function encryptPassword($uPassword, $salt = null)
    {
        return $this->getUserPasswordHasher()->HashPassword($uPassword);
    }

    /**
     * This is for compatibility with passwords generated in older versions of concrete5.
     * Use only for checking password hashes, not generating new ones to store.
     *
     * @param string $uPassword
     * @return string
     */
    public function legacyEncryptPassword($uPassword)
    {
        $app = Application::getFacadeApplication();

        return md5($uPassword . ':' . $app['config']->get('concrete.user.password.legacy_salt'));
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return isset($this->uIsActive) ? $this->uIsActive : null;
    }

    /**
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->superUser;
    }

    /**
     * @return int|null
     */
    public function getLastOnline()
    {
        return isset($this->uLastOnline) ? $this->uLastOnline : null;
    }

    /**
     * @return string|null
     */
    public function getUserName()
    {
        return $this->uName;
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        return $this->getUserID() > 0;
    }

    /**
     * @return string
     */
    public function getUserID()
    {
        return $this->uID;
    }

    /**
     * @return string|null
     */
    public function getUserTimezone()
    {
        return $this->uTimezone;
    }

    /**
     * Return date in yyyy-mm-dd H:i:s format.
     *
     * @return string
     */
    public function getUserSessionValidSince()
    {
        return $this->uLastPasswordChange;
    }

    /**
     * @param string $authType
     * @throws \Exception
     */
    public function setAuthTypeCookie($authType)
    {
        $app = Application::getFacadeApplication();
        $config = $app['config'];
        $jar = $app['cookie'];

        $cookie = array($this->getUserID(), $authType);
        $at = AuthenticationType::getByHandle($authType);
        $cookie[] = $at->controller->buildHash($this);

        $jar->set(
            'ccmAuthUserHash',
            implode(':', $cookie),
            time() + USER_FOREVER_COOKIE_LIFETIME,
            DIR_REL . '/',
            $config->get('concrete.session.cookie.cookie_domain'),
            $config->get('concrete.session.cookie.cookie_secure'),
            $config->get('concrete.session.cookie.cookie_httponly')
        );
    }

    /**
     * @param AuthenticationType $at
     */
    public function setLastAuthType(AuthenticationType $at)
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();
        $db->Execute('UPDATE Users SET uLastAuthTypeID=? WHERE uID=?', array($at->getAuthenticationTypeID(), $this->getUserID()));
    }

    /**
     * @return int
     */
    public function getLastAuthType()
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();
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

    /**
     * @param bool $hard
     */
    public function logout($hard = true)
    {
        $app = Application::getFacadeApplication();
        $events = $app['director'];

        // First, we check to see if we have any collection in edit mode
        $this->unloadCollectionEdit();
        $this->unloadAuthenticationTypes();

        $this->invalidateSession($hard);
        $events->dispatch('on_user_logout');
    }

    /**
     * @param bool $hard
     */
    public function invalidateSession($hard = true)
    {
        $app = Application::getFacadeApplication();
        $session = $app['session'];
        $config = $app['config'];
        $cookie = $app['cookie'];

        // @todo remove this hard option if `Session::clear()` does what we need.
        if (!$hard) {
            $session->clear();
        } else {
            $session->invalidate();
        }

        if ($cookie->has('ccmAuthUserHash') && $cookie->get('ccmAuthUserHash')) {
            $cookie->set('ccmAuthUserHash',
                '',
                315532800,
                DIR_REL . '/',
                $config->get('concrete.session.cookie.cookie_domain'),
                $config->get('concrete.session.cookie.cookie_secure'),
                $config->get('concrete.session.cookie.cookie_httponly')
            );
        }

        $loginCookie = sprintf('%s_LOGIN', $app['config']->get('concrete.session.name'));
        if ($cookie->has($loginCookie) && $cookie->get($loginCookie)) {
            $cookie->clear($loginCookie, 1);
        }
    }

    public static function verifyAuthTypeCookie()
    {
        if ($cookie = array_get($_COOKIE, 'ccmAuthUserHash')) {
            list($_uID, $authType, $uHash) = explode(':', $cookie);
            $at = AuthenticationType::getByHandle($authType);
            $u = self::getByUserID($_uID);
            if ((!is_object($u)) || $u->isError()) {
                return;
            }
            if ($at->controller->verifyHash($u, $uHash)) {
                self::loginByUserID($_uID);
            }
        }
    }

    /**
     * Returns an array of Group objects the user belongs to.
     *
     * @return array
     */
    public function getUserGroupObjects()
    {
        $gs = new GroupList();
        $gs->filterByUserID($this->uID);

        return $gs->getResults();
    }

    /**
     * Returns an array of group ids the user belongs to.
     *
     * @return array
     */
    public function getUserGroups()
    {
        return $this->uGroups;
    }


    /**
     * Sets a default language for a user record.
     *
     * @param string $lang
     */
    public function setUserDefaultLanguage($lang)
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();
        $session = $app['session'];

        $this->uDefaultLanguage = $lang;
        $session->set('uDefaultLanguage', $lang);
        $db->Execute('update Users set uDefaultLanguage = ? where uID = ?', array($lang, $this->getUserID()));
    }

    /**
     * Gets the default language for the logged-in user.
     *
     * @return string
     */
    public function getUserDefaultLanguage()
    {
        return $this->uDefaultLanguage;
    }

    /**
     * Returns date in yyyy-mm-dd H:i:s format.
     *
     * @return string
     */
    public function getLastPasswordChange()
    {
        return $this->uLastPasswordChange;
    }

    /**
     * Checks to see if the current user object is registered. If so, it queries that records
     * default language. Otherwise, it falls back to sitewide settings.
     *
     * @return string
     */
    public function getUserLanguageToDisplay()
    {
        if ($this->getUserDefaultLanguage() != '') {
            return $this->getUserDefaultLanguage();
        } else {
            $app = Application::getFacadeApplication();
            $config = $app['config'];

            return $config->get('concrete.locale');
        }
    }

    public function refreshUserGroups()
    {
        $app = Application::getFacadeApplication();
        $session = $app['session'];

        $session->remove('uGroups');
        $session->remove('accessEntities');
        $ug = $this->_getUserGroups();
        $session->set('uGroups', $ug);
        $this->uGroups = $ug;
    }

    /**
     * @return array
     */
    public function getUserAccessEntityObjects()
    {
        $app = Application::getFacadeApplication();
        $req = Request::getInstance();
        $session = $app['session'];

        if ($req->hasCustomRequestUser()) {
            // we bypass session-saving performance
            // and we don't save them in session.
            return PermissionAccessEntity::getForUser($this);
        }

        if ($session->has('accessEntities')) {
            $entities = $session->get('accessEntities');
        } else {
            $entities = PermissionAccessEntity::getForUser($this);
            $session->set('accessEntities', $entities);
            $session->set('accessEntitiesUpdated', time());
        }

        return $entities;
    }

    /**
     * @param bool $disableLogin
     * @return array
     */
    public function _getUserGroups($disableLogin = false)
    {
        $app = Application::getFacadeApplication();
        $req = Request::getInstance();
        $session = $app['session'];
        $ug = [];

        if (($session->has('uGroups')) && (!$disableLogin) && (!$req->hasCustomRequestUser())) {
            $ug = $session->get('uGroups');
        } else {
            $db = $app['database']->connection();
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

    /**
     * @param Group $g
     */
    public function enterGroup($g)
    {
        $app = Application::getFacadeApplication();
        // takes a group object, and, if the user is not already in the group, it puts them into it
        $dt = $app->make('helper/date');

        if (is_object($g)) {
            if (!$this->inGroup($g)) {
                $gID = $g->getGroupID();
                $db = $app['database']->connection();

                $db->Replace('UserGroups', array(
                    'uID' => $this->getUserID(),
                    'gID' => $g->getGroupID(),
                    'ugEntered' => $dt->getOverridableNow(),
                ),
                array('uID', 'gID'), true);

                if ($g->isGroupBadge()) {
                    $action = UserPointAction::getByHandle('won_badge');
                    if (is_object($action)) {
                        $action->addDetailedEntry($this, $g);
                    }

                    $mh = $app->make('mail');
                    $ui = UserInfo::getByID($this->getUserID());
                    $mh->addParameter('badgeName', $g->getGroupDisplayName(false));
                    $mh->addParameter('uDisplayName', $ui->getUserDisplayName());
                    $mh->addParameter('uProfileURL', (string) $ui->getUserPublicProfileURL());
                    $mh->addParameter('siteName', tc('SiteName', $app['site']->getSite()->getSiteName()));
                    $mh->to($ui->getUserEmail());
                    $mh->load('won_badge');
                    $mh->sendMail();
                }

                $ue = new \Concrete\Core\User\Event\UserGroup($this);
                $ue->setGroupObject($g);

                $app['director']->dispatch('on_user_enter_group', $ue);
            }
        }
    }

    /**
     * @param Group $g
     */
    public function exitGroup($g)
    {

        // takes a group object, and, if the user is in the group, they exit the group
        if (is_object($g)) {
            $app = Application::getFacadeApplication();
            $db = $app['database']->connection();

            $gID = $g->getGroupID();

            $ue = new \Concrete\Core\User\Event\UserGroup($this);
            $ue->setGroupObject($g);
            $app['director']->dispatch('on_user_exit_group', $ue);

            $q = 'delete from UserGroups where uID = ? and gID = ?';
            $r = $db->executeQuery($q, array($this->uID, $gID));
        }
    }

    /**
     * Return true if user is in Group.
     *
     * @param Group $g
     * @return bool
     */
    public function inGroup($g)
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        $v = array($this->uID);
        $cnt = $db->GetOne("select Groups.gID from UserGroups inner join Groups on UserGroups.gID = Groups.gID where uID = ? and gPath like " . $db->quote($g->getGroupPath() . '%'), $v);

        return $cnt > 0;
    }

    /**
     * @param int $mcID
     * @param int $ocID
     */
    public function loadMasterCollectionEdit($mcID, $ocID)
    {
        $app = Application::getFacadeApplication();
        $session = $app['session'];
        // basically, this function loads the master collection ID you're working on into session
        // so you can work on it without the system failing because you're editing a template
        $session->set('mcEditID', $mcID);
        $session->set('ocID', $ocID);
    }

    /**
     * Loads a page in edit mode.
     *
     * @param Page $c
     * @return bool
     */
    public function loadCollectionEdit(&$c)
    {
        $c->refreshCache();

        // can only load one page into edit mode at a time.
        if ($c->isCheckedOut()) {
            return false;
        }

        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        $cID = $c->getCollectionID();
        // first, we check to see if we have a collection in edit mode. If we do, we relinquish it
        $this->unloadCollectionEdit(false);

        $q = 'select cIsCheckedOut, cCheckedOutDatetime from Pages where cID = ?';
        $r = $db->executeQuery($q, array($cID));
        if ($r) {
            $row = $r->fetchRow();
            if (!$row['cIsCheckedOut']) {
                $app['session']->set('editCID', $cID);
                $uID = $this->getUserID();
                $dh = $app->make('helper/date');
                $datetime = $dh->getOverridableNow();
                $q2 = 'update Pages set cIsCheckedOut = ?, cCheckedOutUID = ?, cCheckedOutDatetime = ?, cCheckedOutDatetimeLastEdit = ? where cID = ?';
                $r2 = $db->executeQuery($q2, array(1, $uID, $datetime, $datetime, $cID));

                $c->cIsCheckedOut = 1;
                $c->cCheckedOutUID = $uID;
                $c->cCheckedOutDatetime = $datetime;
                $c->cCheckedOutDatetimeLastEdit = $datetime;
            }
        }

        return true;
    }

    /**
     * @param bool $removeCache
     */
    public function unloadCollectionEdit($removeCache = true)
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        if ($this->getUserID() > 0) {
            $col = $db->GetCol('select cID from Pages where cCheckedOutUID = ?', array($this->getUserID()));
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

    /**
     * @param string $cfKey
     * @return string|null
     */
    public function config($cfKey)
    {
        if ($this->isRegistered()) {
            $app = Application::getFacadeApplication();
            $db = $app['database']->connection();

            $val = $db->GetOne("select cfValue from ConfigStore where uID = ? and cfKey = ?", array($this->getUserID(), $cfKey));

            return $val;
        }

        return null;
    }

    /**
     * @param Page $c
     */
    public function markPreviousFrontendPage(Page $c)
    {
        $app = Application::getFacadeApplication();
        $app['session']->set('frontendPreviousPageID', $c->getCollectionID());
    }

    /**
     * @return int
     */
    public function getPreviousFrontendPageID()
    {
        $app = Application::getFacadeApplication();

        return (int) $app['session']->get('frontendPreviousPageID');
    }

    /**
     * @param string $cfKey
     * @param string $cfValue
     */
    public function saveConfig($cfKey, $cfValue)
    {
        $app = Application::getFacadeApplication();
        $app['database']->connection()->Replace('ConfigStore', array('cfKey' => $cfKey, 'cfValue' => $cfValue, 'uID' => $this->getUserID()), array('cfKey', 'uID'), true);
    }

    /**
     * @param Page $c
     */
    public function refreshCollectionEdit(&$c)
    {
        if ($this->isLoggedIn() && $c->getCollectionCheckedOutUserID() == $this->getUserID()) {
            $app = Application::getFacadeApplication();
            $db = $app['database']->connection();
            $cID = $c->getCollectionID();

            $dh = $app->make('helper/date');
            $datetime = $dh->getOverridableNow();

            $q = 'update Pages set cCheckedOutDatetimeLastEdit = ? where cID = ?';
            $r = $db->executeQuery($q, array($datetime, $cID));

            $c->cCheckedOutDatetimeLastEdit = $datetime;
        }
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function forceCollectionCheckInAll()
    {
        // This function forces checkin to take place
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

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
        $app = Application::getFacadeApplication();
        $config = $app['config'];
        if (isset($this->hasher)) {
            return $this->hasher;
        }
        $this->hasher = new PasswordHash(
            $config->get('concrete.user.password.hash_cost_log2'),
            $config->get('concrete.user.password.hash_portable'));

        return $this->hasher;
    }

    /**
     * Manage user session writing.
     *
     * @param bool $cache_interface
     */
    public function persist($cache_interface = true)
    {
        $this->refreshUserGroups();

        $app = Application::getFacadeApplication();

        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = $app['session'];
        $session->set('uID', $this->getUserID());
        $session->set('uName', $this->getUserName());
        $session->set('uBlockTypesSet', false);
        $session->set('uGroups', $this->getUserGroups());
        $session->set('uLastOnline', $this->getLastOnline());
        $session->set('uTimezone', $this->getUserTimezone());
        $session->set('uDefaultLanguage', $this->getUserDefaultLanguage());
        $session->set('uLastPasswordChange', $this->getLastPasswordChange());

        $cookie = $app['cookie'];
        $cookie->set(sprintf('%s_LOGIN', $app['config']->get('concrete.session.name')), 1);

        if ($cache_interface) {
            $app->make('helper/concrete/ui')->cacheInterfaceItems();
        }
    }

    /**
     * @param bool $cache_interface
     */
    public function logIn($cache_interface = true)
    {
        $this->persist($cache_interface);
    }
}
