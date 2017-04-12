<?php
namespace Concrete\Authentication\Concrete;

use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;
use Config;
use Core;
use Database;
use Exception;
use Session;
use UserInfo;
use View;

class Controller extends AuthenticationTypeController
{
    public $apiMethods = ['forgot_password', 'v', 'change_password', 'password_changed', 'email_validated', 'invalid_token', 'required_password_upgrade'];

    public function getHandle()
    {
        return 'concrete';
    }

    public function deauthenticate(User $u)
    {
        $cookie = array_get($_COOKIE, 'ccmAuthUserHash', '');
        if ($cookie) {
            list($uID, $authType, $hash) = explode(':', $cookie);
            if ($authType == 'concrete') {
                $db = Database::connection();
                $db->executeQuery('DELETE FROM authTypeConcreteCookieMap WHERE uID=? AND token=?', [$uID, $hash]);
            }
        }
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-user"></i>';
    }

    public function verifyHash(User $u, $hash)
    {
        $uID = $u->getUserID();
        $db = Database::connection();
        $q = $db->fetchColumn(
            'SELECT validThrough FROM authTypeConcreteCookieMap WHERE uID=? AND token=?',
            [$uID, $hash]
        );
        $bool = time() < $q;
        if (!$bool) {
            $db->executeQuery('DELETE FROM authTypeConcreteCookieMap WHERE uID=? AND token=?', [$uID, $hash]);
        } else {
            $newTime = strtotime('+2 weeks');
            $db->executeQuery('UPDATE authTypeConcreteCookieMap SET validThrough=?', [$newTime]);
        }

        return $bool;
    }

    public function view()
    {
    }

    public function buildHash(User $u, $test = 1)
    {
        if ($test > 10) {
            // This should only ever happen if by some stroke of divine intervention,
            // we end up pulling 10 hashes that already exist. the chances of this are very very low.
            throw new \Exception(t('There was a database error, try again.'));
        }
        $db = Database::connection();

        $validThrough = strtotime('+2 weeks');
        $token = $this->genString();
        try {
            $db->executeQuery(
                'INSERT INTO authTypeConcreteCookieMap (token, uID, validThrough) VALUES (?,?,?)',
                [$token, $u->getUserID(), $validThrough]
            );
        } catch (\Exception $e) {
            // HOLY CRAP.. SERIOUSLY?
            $this->buildHash($u, ++$test);
        }

        return $token;
    }

    private function genString($a = 16)
    {
        if (function_exists('random_bytes')) { // PHP7+
            return bin2hex(random_bytes($a));
        }
        if (function_exists('mcrypt_create_iv')) {
            // Use /dev/urandom if available, otherwise fall back to PHP's rand (below)
            // Don't use (MCRYPT_DEV_URANDOM|MCRYPT_RAND) here, because we prefer
            // openssl first.
            // Use @ here because otherwise mcrypt throws a noisy warning if
            // /dev/urandom is missing.
            $iv = @mcrypt_create_iv($a, MCRYPT_DEV_URANDOM);
            if ($iv !== false) {
                return bin2hex($iv);
            }
        }
        // don't use elseif, we need the fallthrough here.
        if (function_exists('openssl_random_pseudo_bytes')) {
            $iv = openssl_random_pseudo_bytes($a, $crypto_strong);
            if ($iv !== false && $crypto_strong) {
                return bin2hex($iv);
            }
        }
        // this means we've not yet returned, so MCRYPT_DEV_URANDOM isn't available.
        if (function_exists('mcrypt_create_iv')) {
            // terrible, but still better than what we're doing below
            $iv = mcrypt_create_iv($a, MCRYPT_RAND);
            if ($iv !== false) {
                return bin2hex($iv);
            }
        }
        // This really is a last resort.
        $o = '';
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}|":<>?\'\\';
        $l = strlen($chars);
        while ($a--) {
            $o .= substr($chars, rand(0, $l), 1);
        }

        return md5($o);
    }

    public function isAuthenticated(User $u)
    {
        return $u->isLoggedIn();
    }

    public function saveAuthenticationType($values)
    {
    }

    /**
     * Called when a user tries to log in after his password has been reset by "Global Password Reset".
     */
    public function required_password_upgrade()
    {
        $email = $this->post('uEmail');
        $token = $this->app->make(Token::class);
        $this->set('token', $token);

        if ($email) {
            $errorValidator = Core::make('helper/validation/error');
            $userInfo = Core::make('Concrete\Core\User\UserInfoRepository')->getByName(Session::get('uPasswordResetUserName'));

            try {
                if ($userInfo && $email != $userInfo->getUserEmail()) {
                    throw new \Exception(t(sprintf('Invalid email address %s provided resetting a password', $email)));
                }
            } catch (\Exception $e) {
                $errorValidator->add($e);
            }

            $this->forgot_password();
        } else {
            $this->set('authType', $this->getAuthenticationType());
            $this->set('intro_msg', Core::make('config/database')->get('concrete.password.reset.message'));
        }
    }

    /**
     * Called when a user wants a password reset email sent, is passed in the user's email address.
     */
    public function forgot_password()
    {
        $loginData['success'] = 0;
        $error = Core::make('helper/validation/error');
        $vs = Core::make('helper/validation/strings');
        $em = $this->post('uEmail');
        $token = $this->app->make(Token::class);
        $this->set('authType', $this->getAuthenticationType());
        $this->set('token', $token);

        if ($em) {
            try {
                if (!$token->validate()) {
                    throw new \Exception($token->getErrorMessage());
                }

                if (!$vs->email($em)) {
                    throw new \Exception(t('Invalid email address.'));
                }

                $oUser = UserInfo::getByEmail($em);
                if ($oUser) {
                    $mh = Core::make('helper/mail');
                    //$mh->addParameter('uPassword', $oUser->resetUserPassword());
                    if (Config::get('concrete.user.registration.email_registration')) {
                        $mh->addParameter('uName', $oUser->getUserEmail());
                    } else {
                        $mh->addParameter('uName', $oUser->getUserName());
                    }
                    $mh->to($oUser->getUserEmail());

                    //generate hash that'll be used to authenticate user, allowing them to change their password
                    $h = new \Concrete\Core\User\ValidationHash();
                    $uHash = $h->add($oUser->getUserID(), intval(UVTYPE_CHANGE_PASSWORD), true);
                    $changePassURL = View::url(
                        '/login',
                        'callback',
                        $this->getAuthenticationType()->getAuthenticationTypeHandle(),
                        'change_password',
                        $uHash);

                    $mh->addParameter('changePassURL', $changePassURL);

                    $fromEmail = (string) Config::get('concrete.email.forgot_password.address');
                    if (!strpos($fromEmail, '@')) {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $fromEmail = $adminUser->getUserEmail();
                        } else {
                            $fromEmail = '';
                        }
                    }
                    if ($fromEmail) {
                        $fromName = (string) Config::get('concrete.email.forgot_password.name');
                        if ($fromName === '') {
                            $fromName = t('Forgot Password');
                        }
                        $mh->from($fromEmail, $fromName);
                    }

                    $mh->addParameter('siteName', tc('SiteName', \Core::make('site')->getSite()->getSiteName()));
                    $mh->load('forgot_password');
                    @$mh->sendMail();
                }
            } catch (\Exception $e) {
                $error->add($e);
            }
            if ($error->has()) {
                $this->set('error', $error);
            } else {
                $this->redirect('/login', $this->getAuthenticationType()->getAuthenticationTypeHandle(),
                    'password_sent');
            }
        }
    }

    public function change_password($uHash = '')
    {
        $this->set('authType', $this->getAuthenticationType());
        $db = Database::connection();
        $h = Core::make('helper/validation/identifier');
        $e = Core::make('helper/validation/error');
        $ui = UserInfo::getByValidationHash($uHash);
        if (is_object($ui)) {
            $hashCreated = $db->fetchColumn('SELECT uDateGenerated FROM UserValidationHashes WHERE uHash=?', [$uHash]);
            if ($hashCreated < (time() - (USER_CHANGE_PASSWORD_URL_LIFETIME))) {
                $h->deleteKey('UserValidationHashes', 'uHash', $uHash);
                throw new \Exception(
                    t(
                        'Key Expired. Please visit the forgot password page again to have a new key generated.'));
            } else {
                if (isset($_POST['uPassword']) && strlen($_POST['uPassword'])) {
                    Core::make('validator/password')->isValid($_POST['uPassword'], $e);

                    if (strlen($_POST['uPassword']) && $_POST['uPasswordConfirm'] != $_POST['uPassword']) {
                        $e->add(t('The two passwords provided do not match.'));
                    }

                    if (!$e->has()) {
                        $ui->changePassword($_POST['uPassword']);
                        $h->deleteKey('UserValidationHashes', 'uHash', $uHash);
                        $this->set('passwordChanged', true);

                        $this->redirect(
                            '/login',
                            $this->getAuthenticationType()->getAuthenticationTypeHandle(),
                            'password_changed');
                    } else {
                        $this->set('uHash', $uHash);
                        $this->set('authTypeElement', 'change_password');
                        $this->set('error', $e);
                    }
                } else {
                    $this->set('uHash', $uHash);
                    $this->set('authTypeElement', 'change_password');
                }
            }
        } else {
            throw new \Exception(
                t(
                    'Invalid Key. Please visit the forgot password page again to have a new key generated.'));
        }
    }

    public function password_changed()
    {
        $this->view();
    }

    public function email_validated($mode = false)
    {
        if ($mode) {
            $this->set('workflowPending', true);
            $this->set('validated', false);
        } else {
            $this->set('validated', true);
        }
        $this->view();
    }

    public function invalid_token()
    {
        $this->view();
    }

    public function authenticate()
    {
        $post = $this->post();

        if (empty($post['uName']) || empty($post['uPassword'])) {
            throw new Exception(t('Please provide both username and password.'));
        }
        $uName = $post['uName'];
        $uPassword = $post['uPassword'];

        /** @var \Concrete\Core\Permission\IPService $ip_service */
        $ip_service = Core::make('ip');
        if ($ip_service->isBlacklisted()) {
            throw new \Exception($ip_service->getErrorMessage());
        }

        $user = new User($uName, $uPassword);
        if (!is_object($user) || !($user instanceof User) || $user->isError()) {
            switch ($user->getError()) {
                case USER_SESSION_EXPIRED:
                    throw new \Exception(t('Your session has expired. Please sign in again.'));
                    break;
                case USER_NON_VALIDATED:
                    throw new \Exception(
                        t(
                            'This account has not yet been validated. Please check the email associated with this account and follow the link it contains.'));
                    break;
                case USER_INVALID:
                    // Log failed auth
                    $ip_service->logFailedLogin();
                    if ($ip_service->failedLoginsThresholdReached()) {
                        $ip_service->addToBlacklistForThresholdReached();
                        throw new \Exception($ip_service->getErrorMessage());
                    }

                    if ($this->isPasswordReset()) {
                        Session::set('uPasswordResetUserName', $this->post('uName'));
                        $this->redirect('/login/', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'required_password_upgrade');
                    }

                    if (Config::get('concrete.user.registration.email_registration')) {
                        throw new \Exception(t('Invalid email address or password.'));
                    } else {
                        throw new \Exception(t('Invalid username or password.'));
                    }
                    break;
                case USER_INACTIVE:
                    throw new \Exception(t('This user is inactive. Please contact us regarding this account.'));
                    break;
            }
        }
        if (isset($post['uMaintainLogin']) && $post['uMaintainLogin']) {
            $user->setAuthTypeCookie('concrete');
        }

        return $user;
    }

    private function isPasswordReset()
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        return $db->GetOne('select uIsPasswordReset from Users where uName = ?', [$this->post('uName')]);
    }

    public function v($hash = '')
    {
        $ui = UserInfo::getByValidationHash($hash);
        if (is_object($ui)) {
            $ui->markValidated();
            $this->set('uEmail', $ui->getUserEmail());
            if ($ui->triggerActivate('register_activate', USER_SUPER_ID)) {
                $mode = '';
            } else {
                $mode = 'workflow';
            }
            $this->redirect('/login/callback/concrete', 'email_validated', $mode);
            exit;
        }
        $this->redirect('/login/callback/concrete', 'invalid_token');
    }
}
