<?php

namespace Concrete\Core\User;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Query\LikeBuilder;
use Concrete\Core\Entity\Notification\GroupSignupNotification;
use Concrete\Core\Entity\User\GroupSignup;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Http\Request;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Notification\Type\GroupSignupType;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Session\SessionValidator;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Page\Page;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\Group\GroupRole;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Encryption\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class User extends ConcreteObject
{
    public $uID = '';
    public $uName = '';
    public $uGroups = [];
    public $superUser = false;
    public $uTimezone = null;
    protected $uDefaultLanguage = null;
    // an associative array of all access entity objects that are associated with this user.
    protected $accessEntities = null;
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

        $v = [$uID];
        $q = 'SELECT uID, uName, uIsActive, uLastOnline, uTimezone, uDefaultLanguage, uLastPasswordChange FROM Users WHERE uID = ? LIMIT 1';
        $r = $db->query($q, $v);
        $row = $r ? $r->fetch() : null;
        $nu = null;
        if ($row) {
            $nu = new static();
            $nu->setPropertiesFromArray($row);
            $nu->uGroups = $nu->_getUserGroups(true);
            $nu->superUser = ($nu->getUserID() == USER_SUPER_ID);
            if ($login) {
                $nu->persist($cacheItemsOnLogin);
                $nu->recordLogin();
                $app = Application::getFacadeApplication();
                /** @var NavigationCache $navigationCache */
                $navigationCache = $app->make(NavigationCache::class);
                $navigationCache->clear();
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
     * @deprecated
     * Use isRegistered() instead
     */
    public static function isLoggedIn()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);
        return $u->isRegistered();
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
            $this->refreshUserGroups();
        }

        $invalidate = $app->make('Concrete\Core\Session\SessionValidatorInterface')->handleSessionValidation($session);
        if ($invalidate) {
            $this->loadError(USER_SESSION_EXPIRED);
        }

        if ($session->get('uID') > 0) {
            $db = $app['database']->connection();

            $row = $db->GetRow('select * from Users where uID = ? and uName = ?', [$session->get('uID'), $session->get('uName')]);
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
                    // This code throttles the writing of uLastOnline to the database, so that we're not constantly
                    // updating the Users table. If you need to have the exact up to date metric on when a session
                    // last looked at a page, use uOnlineCheck.
                    $db->query('update Users set uLastOnline = ? where uID = ?', [$session->get('uOnlineCheck'), $this->uID]);
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
        $config = $app['config'];
        $session = $app['session'];
        $validator = $app->make(SessionValidator::class);
        // We need to check for the cookie so that we don't auto create a session when this runs super early.
        if (isset($args[1])) {
            // first, we check to see if the username and password match the admin username and password
            // $username = uName normally, but if not it's email address

            $username = $args[0];
            $password = $args[1];
            $disableLogin = isset($args[2]) ? (bool) $args[2] : false;
            if (!$disableLogin) {
                $session->migrate();
                $session->remove('uGroups');
                $session->remove('accessEntities');
            }
            $v = [$username];
            if ($config->get('concrete.user.registration.email_registration')) {
                $q = 'select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage, uPassword, uLastPasswordChange, uIsPasswordReset from Users where uEmail = ?';
            } else {
                $q = 'select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage, uPassword, uLastPasswordChange, uIsPasswordReset from Users where uName = ?';
            }

            $hasher = $app->make(PasswordHasher::class);
            $db = $app->make('Concrete\Core\Database\Connection\Connection');
            $r = $db->query($q, $v);
            if ($r) {
                $row = $r->fetch();
                if ($row) {
                    $pw_is_valid_legacy = false;
                    if ($config->get('concrete.user.password.legacy_salt')) {
                        $pw_is_valid_legacy = self::legacyEncryptPassword($password) === $row['uPassword'];
                    }

                    $pw_is_valid = $pw_is_valid_legacy || $hasher->checkPassword($password, $row['uPassword']);
                    if ($pw_is_valid && $hasher->needsRehash($row['uPassword'])) {
                        $em = $app->make(EntityManagerInterface::class);

                        try {
                            $em->transactional(function (EntityManagerInterface $em) use ($hasher, $password, $row) {
                                $user = $em->find(\Concrete\Core\Entity\User\User::class, $row['uID']);
                                if (!$user) {
                                    throw new \RuntimeException('User not found.');
                                }

                                $user->setUserPassword($hasher->hashPassword($password));
                            });
                        } catch (\Throwable $e) {
                            $app->make(LoggerInterface::class)->emergency('Unable to rehash password for user {user} ({id}): {message}', [
                                'user' => $row['uName'],
                                'id' => $row['uID'],
                                'message' => $e->getMessage(),
                            ]);
                        }
                    }

                    if ($row['uID'] && $row['uIsValidated'] === '0' && $config->get(
                            'concrete.user.registration.validate_email'
                        )) {
                        $this->loadError(USER_NON_VALIDATED);
                    } elseif ($row['uID'] && $row['uIsActive'] && $pw_is_valid) {
                        if ($row['uIsPasswordReset']) {
                            $this->loadError(USER_PASSWORD_RESET);
                        } else {
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
                        }
                    } elseif ($row['uID'] && !$row['uIsActive']) {
                        $this->loadError(USER_INACTIVE);
                    } else {
                        $this->loadError(USER_INVALID);
                    }
                } else {
                    $this->loadError(USER_INVALID);
                }
            } else {
                $hasher->hashPassword($password); // HashPassword and CheckPassword are slow functions.
                // We run one here just take time.
                // Without it an attacker would be able to tell that the
                // username doesn't exist using a timing attack.
                $this->loadError(USER_INVALID);
            }
        } else {
            $req = Request::getInstance();
            $this->uID = null;
            $this->uName = null;
            $this->superUser = false;
            $this->uDefaultLanguage = null;
            $this->uTimezone = null;
            if ($req->hasCustomRequestUser()) {
                $ux = $req->getCustomRequestUser();
                if ($ux && is_object($ux)) {
                    $this->uID = $ux->getUserID();
                    $this->uName = $ux->getUserName();
                    $this->superUser = $ux->getUserID() == USER_SUPER_ID;
                    if ($ux->getUserDefaultLanguage()) {
                        $this->uDefaultLanguage = $ux->getUserDefaultLanguage();
                    }
                    $this->uTimezone = $ux->getUserTimezone();
                } elseif ($ux === -1) {
                    $this->uID = 0;
                    $this->uName = t('Guest');
                }
                $this->uGroups = $this->_getUserGroups(true);
            } elseif ($validator->hasActiveSession() || $this->uID) {
                if ($session->has('uID')) {
                    $this->uID = $session->get('uID');
                    $this->uName = $session->get('uName');
                    $this->uTimezone = $session->get('uTimezone');
                    if ($session->has('uDefaultLanguage')) {
                        $this->uDefaultLanguage = $session->get('uDefaultLanguage');
                    }
                    $this->superUser = ($session->get('uID') == USER_SUPER_ID) ? true : false;
                }
                $this->uGroups = $this->_getUserGroups();
                if (!isset($args[2]) && !$req->hasCustomRequestUser()) {
                    $session->set('uGroups', $this->uGroups);
                }
            }
        }
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
        $uLastLogin = $db->getOne('select uLastLogin from Users where uID = ?', [$this->uID]);

        // Add a log entry
        $logger = $app->make(LoggerFactory::class)->createLogger(Channels::CHANNEL_AUTHENTICATION);
        $logger->info(t('Login from user {user} (ID {id}) succeeded'), [
            'user' => $this->getUserName(),
            'id' => $this->getUserID(),
        ]);

        /** @var \Concrete\Core\Permission\IPService $iph */
        $iph = $app->make('helper/validation/ip');
        $ip = $iph->getRequestIP();
        $db->query('update Users set uLastIP = ?, uLastLogin = ?, uPreviousLogin = ?, uNumLogins = uNumLogins + 1 where uID = ?', [($ip === false) ? ('') : ($ip->getIp()), time(), $uLastLogin, $this->uID]);
    }

    /**
     * $salt is retained for compatibilty with older versions of concerete5, but not used.
     *
     * @param string $uPassword
     * @param null $salt
     *
     * @return string
     */
    public function encryptPassword($uPassword, $salt = null)
    {
        return app(PasswordHasher::class)->hashPassword($uPassword);
    }

    /**
     * This is for compatibility with passwords generated in older versions of concrete5.
     * Use only for checking password hashes, not generating new ones to store.
     *
     * @param string $uPassword
     *
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
     *
     * @throws \Exception
     */
    public function setAuthTypeCookie($authType)
    {
        $app = Application::getFacadeApplication();
        $at = AuthenticationType::getByHandle($authType);
        $token = $at->getController()->buildHash($this);
        $value = new PersistentAuthentication\CookieValue((int)$this->getUserID(), $authType, $token);
        $app->make(PersistentAuthentication\CookieService::class)->setCookie($value);
    }

    /**
     * @param AuthenticationType $at
     */
    public function setLastAuthType(AuthenticationType $at)
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();
        $db->Execute('UPDATE Users SET uLastAuthTypeID=? WHERE uID=?', [$at->getAuthenticationTypeID(), $this->getUserID()]);
    }

    /**
     * @return int
     */
    public function getLastAuthType()
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();
        $id = $db->getOne('SELECT uLastAuthTypeID FROM Users WHERE uID=?', [$this->getUserID()]);

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
        $logger = $app->make(LoggerFactory::class)->createLogger(Channels::CHANNEL_AUTHENTICATION);
        $logger->info(t('Logout from user {user} (ID {id}) requested'), [
            'user' => $this->getUserName(),
            'id' => $this->getUserID(),
        ]);

        // First, we check to see if we have any collection in edit mode
        $this->unloadCollectionEdit();
        $this->unloadAuthenticationTypes();

        $this->invalidateSession($hard);
        $app->singleton(User::class, function () {
            return new User();
        });
        $events->dispatch('on_user_logout');

        $app = Application::getFacadeApplication();
        /** @var NavigationCache $navigationCache */
        $navigationCache = $app->make(NavigationCache::class);
        $navigationCache->clear();
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
            // Really not sure why this doesn't happen with invalidate, but oh well.
            $cookie->clear($config->get('concrete.session.name'));
        }

        if ($cookie->has('ConcreteSitemapTreeID')) {
            $cookie->clear('ConcreteSitemapTreeID');
        }

        $app->make(PersistentAuthentication\CookieService::class)->deleteCookie();

        $loginCookie = sprintf('%s_LOGIN', $app['config']->get('concrete.session.name'));
        if ($cookie->has($loginCookie) && $cookie->get($loginCookie)) {
            $cookie->clear($loginCookie, 1);
        }
    }

    public static function verifyAuthTypeCookie()
    {
        $app = Application::getFacadeApplication();
        $cookie = $app->make(PersistentAuthentication\CookieService::class)->getCookie();
        if ($cookie === null) {
            return;
        }
        $at = AuthenticationType::getByHandle($cookie->getAuthenticationTypeHandle());
        $u = self::getByUserID($cookie->getUserID());
        if ($u === null || $u->isError()) {
            return;
        }
        if ($at->controller->verifyHash($u, $cookie->getToken())) {
            self::loginByUserID($cookie->getUserID());
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
        $db->Execute('update Users set uDefaultLanguage = ? where uID = ?', [$lang, $this->getUserID()]);
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
     * Function to return Permission Access Entities belonging to this user object
     *
     * @return PermissionAccessEntity[] | array
     */
    public function getUserAccessEntityObjects()
    {
        $app = Application::getFacadeApplication();

        $req = $app->make(Request::class);
        if ($req->hasCustomRequestUser()) {
            // we bypass session-saving performance
            // and we don't save them in session.
            return PermissionAccessEntity::getForUser($this);
        }

        if ($this->accessEntities === null) {
            $session = $app['session'];
            $validator = $app->make(SessionValidator::class);
            // Check if the user is loggged in
            if ($validator->hasActiveSession() && $session->has('uID')) {
                // If a user is logged in and running a script to get the user access entities
                // return the correct access entities
                if ($session->has('accessEntities') && $this->getUserID() == $session->get('uID')) {
                    $entities = $session->get('accessEntities');
                } elseif ($this->getUserID() == $session->get('uID')) {
                    $entities = PermissionAccessEntity::getForUser($this);
                    $session->set('accessEntities', $entities);
                    $session->set('accessEntitiesUpdated', time());
                } else {
                    $entities = PermissionAccessEntity::getForUser($this);
                }
            } else {
                if ((int)$this->getUserID() > 0) {
                    $entities = PermissionAccessEntity::getForUser($this);
                } else {
                    $repository = $app->make(GroupRepository::class);
                    $group = $repository->getGroupByID(GUEST_GROUP_ID);
                    if ($group) {
                        $entities = [GroupEntity::getOrCreate($group)];
                    } else {
                        $entities = [];
                    }
                }
            }

            $this->accessEntities = $entities;
        }

        return $this->accessEntities;
    }

    /**
     * @param bool $disableLogin
     *
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
                $q = 'select gID from UserGroups where uID = ?';
                $r = $db->query($q, [$uID]);
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
     * @param GroupRole $r
     */
    public function changeGroupRole($g, $r)
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        if (is_object($g)) {
            if (!$this->inExactGroup($g)) {
                $db->Replace('UserGroups', [
                    'uID' => $this->getUserID(),
                    'gID' => $g->getGroupID(),
                    'grID' => $r->getId()
                ], ['uID', 'gID'], true);

                $ue = new \Concrete\Core\User\Event\UserGroup($this);
                $ue->setGroupObject($g);

                $app['director']->dispatch('on_user_change_group_role', $ue);
            }
        }
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
            if (!$this->inExactGroup($g)) {
                $gID = $g->getGroupID();
                $db = $app['database']->connection();
                $grID = DEFAULT_GROUP_ROLE_ID;

                $role = $g->getDefaultRole();

                if (is_object($role)) {
                    $grID = $role->getId();
                }

                $db->Replace('UserGroups', [
                    'uID' => $this->getUserID(),
                    'gID' => $g->getGroupID(),
                    'ugEntered' => $dt->getOverridableNow(),
                    'grID' => $grID
                ],
                    ['uID', 'gID'], true);

                $ue = new \Concrete\Core\User\Event\UserGroup($this);
                $ue->setGroupObject($g);

                $app['director']->dispatch('on_user_enter_group', $ue);

                /** @noinspection PhpUnhandledExceptionInspection */
                $subject = new GroupSignup($g, $this);
                /** @var GroupSignupType $type */
                $type = $app->make('manager/notification/types')->driver('group_signup');
                $notifier = $type->getNotifier();

                if (method_exists($notifier, 'notify')) {
                    $subscription = $type->getSubscription($subject);
                    $users = $notifier->getUsersToNotify($subscription, $subject);
                    $notification = new GroupSignupNotification($subject);
                    $notifier->notify($users, $notification);
                }
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
            $r = $db->executeQuery($q, [$this->uID, $gID]);
        }
    }

    /**
     * Return true if user is in Group or any of this groups children.
     *
     * @param Group $g
     *
     * @return bool
     */
    public function inGroup($g)
    {
        $app = Application::getFacadeApplication();
        /** @var \Concrete\Core\Database\Connection\Connection $db */
        $db = $app['database']->connection();
        /** @var $likeBuilder LikeBuilder */
        $likeBuilder = $app->make(LikeBuilder::class);
        $query = $db->createQueryBuilder();
        $query->select('ug.gID')->from('UserGroups', 'ug')
            ->innerJoin('ug', $query->getConnection()->getDatabasePlatform()->quoteSingleIdentifier('Groups'), 'g', 'ug.gID=g.gID')
            ->where($query->expr()->eq('ug.uID', ':userID'))
            ->andWhere($query->expr()->orX(
                $query->expr()->eq('ug.gID', ':gID'),
                $query->expr()->like('g.gPath', ':groupPath')
            ))
            ->setParameter('userID', $this->uID)
            ->setParameter('gID', $g->getGroupID())
            ->setParameter('groupPath', $likeBuilder->escapeForLike($g->getGroupPath()) . '/%')
            ->setMaxResults(1);
        $results = $query->execute()->fetchColumn();

        return (bool)$results;
    }

    /**
     * Return true if user is in this Group.
     * Does not check if user is a member of children.
     *
     * @param Group $g
     *
     * @return bool
     */
    public function inExactGroup($g)
    {
        $app = Application::getFacadeApplication();
        /** @var \Concrete\Core\Database\Connection\Connection $db */
        $db = $app['database']->connection();
        $query = $db->createQueryBuilder();
        $query->select('ug.gID')->from('UserGroups', 'ug')
            ->where($query->expr()->eq('ug.uID', ':userID'))
            ->andWhere(
                $query->expr()->eq('ug.gID', ':gID'))->setParameter('userID', $this->uID)
            ->setParameter('gID', $g->getGroupID())
            ->setMaxResults(1);
        $results = $query->execute()->fetchColumn();

        return (bool)$results;
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
     *
     * @return bool
     */
    public function loadCollectionEdit(&$c)
    {
        $c->refreshCache();

        // clear the cached available areas before entering edit mode
        $app = Application::getFacadeApplication();
        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = $app->make("session");
        $session->remove("used_areas");

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
        $r = $db->executeQuery($q, [$cID]);
        if ($r) {
            $row = $r->fetch();
            if (!$row['cIsCheckedOut']) {
                $app['session']->set('editCID', $cID);
                $uID = $this->getUserID();
                $dh = $app->make('helper/date');
                $datetime = $dh->getOverridableNow();
                $q2 = 'update Pages set cIsCheckedOut = ?, cCheckedOutUID = ?, cCheckedOutDatetime = ?, cCheckedOutDatetimeLastEdit = ? where cID = ?';
                $r2 = $db->executeQuery($q2, [1, $uID, $datetime, $datetime, $cID]);

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
            $col = $db->GetCol('select cID from Pages where cCheckedOutUID = ?', [$this->getUserID()]);
            foreach ($col as $cID) {
                $p = Page::getByID($cID);
                if ($removeCache) {
                    $p->refreshCache();
                }
            }

            $q = 'update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null where cCheckedOutUID = ?';
            $db->query($q, [$this->getUserID()]);
        }
    }

    /**
     * @param string $cfKey
     *
     * @return string|null
     */
    public function config($cfKey)
    {
        if ($this->isRegistered()) {
            $app = Application::getFacadeApplication();
            $db = $app['database']->connection();

            $val = $db->GetOne('select cfValue from ConfigStore where uID = ? and cfKey = ?', [$this->getUserID(), $cfKey]);

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

        return (int)$app['session']->get('frontendPreviousPageID');
    }

    /**
     * @param string $cfKey
     * @param string $cfValue
     */
    public function saveConfig($cfKey, $cfValue)
    {
        $app = Application::getFacadeApplication();
        $app['database']->connection()->Replace('ConfigStore', ['cfKey' => $cfKey, 'cfValue' => $cfValue, 'uID' => $this->getUserID()], ['cfKey', 'uID'], true);
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
            $r = $db->executeQuery($q, [$datetime, $cID]);

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

        $q = 'update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null';
        $r = $db->query($q);

        return $r;
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


        /** @var Repository $config */
        $config = $app->make(Repository::class);

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

        /** @var \Concrete\Core\Cookie\CookieJar $cookie */
        $cookie = $app['cookie'];

        $cookie->set(
            sprintf('%s_LOGIN', $app['config']->get('concrete.session.name')),
            1,
            // $expire
            time() + (int)$config->get('concrete.session.remember_me.lifetime'),
            // $path
            DIR_REL . '/',
            // $domain
            $config->get('concrete.session.cookie.cookie_domain'),
            // $secure
            $config->get('concrete.session.cookie.cookie_secure'),
            // $httpOnly
            $config->get('concrete.session.cookie.cookie_httponly')
        );

        if ($cache_interface) {
            $app->make('helper/concrete/ui')->cacheInterfaceItems();
        }
        $app->instance(User::class, $this);
    }

    /**
     * @param bool $cache_interface
     */
    public function logIn($cache_interface = true)
    {
        $this->persist($cache_interface);
    }
}
