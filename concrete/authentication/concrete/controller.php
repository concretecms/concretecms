<?php
namespace Concrete\Authentication\Concrete;

use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Encryption\PasswordHasher;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\IpAccessControlService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Exception\FailedLoginThresholdExceededException;
use Concrete\Core\User\Exception\InvalidCredentialsException;
use Concrete\Core\User\Exception\UserDeactivatedException;
use Concrete\Core\User\Exception\UserException;
use Concrete\Core\User\Exception\UserPasswordResetException;
use Concrete\Core\User\Login\LoginService;
use Concrete\Core\User\User;
use Concrete\Core\User\PersistentAuthentication\CookieService;
use Concrete\Core\User\ValidationHash;
use Concrete\Core\Validation\CSRF\Token;
use Config;
use Core;
use Database;
use Exception;
use Session;
use UserInfo;
use View;
use Concrete\Core\Validator\String\EmailValidator;

class Controller extends AuthenticationTypeController
{
    public $apiMethods = ['forgot_password', 'v', 'change_password', 'password_changed', 'email_validated', 'invalid_token', 'required_password_upgrade'];

    public function getHandle()
    {
        return 'concrete';
    }

    public function deauthenticate(User $u)
    {
        $authCookie = $this->app->make(CookieService::class)->getCookie();
        if ($authCookie === null || $authCookie->getAuthenticationTypeHandle() !== 'concrete') {
            return;
        }
        $hasher = $this->app->make(PasswordHasher::class);
        $db = $this->app->make(Connection::class);
        foreach ($db->fetchAll('select token from authTypeConcreteCookieMap WHERE uID = ? ORDER BY validThrough DESC', [$authCookie->getUserID()]) as $row) {
            if ($hasher->checkPassword($authCookie->getToken(), $row['token'])) {
                $db->delete('authTypeConcreteCookieMap', ['uID' => $authCookie->getUserID(), 'token' => $row['token']]);
            }
        }
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fas fa-user"></i>';
    }

    public function verifyHash(User $u, $hash)
    {
        if (!str_contains($hash, '@')) {
            return false;
        }
        [$id, $token] = explode('@', $hash, 2);

        $id = (int) $id;
        $uID = (int) $u->getUserID();
        $db = $this->app->make(Connection::class);
        $hasher = $this->app->make(PasswordHasher::class);

        // Find valid hash with matching key and user
        $hash = $db->fetchOne(
            'SELECT token FROM authTypeConcreteCookieMap WHERE uID = ? AND validThrough > ? AND id=?',
            [$uID, time(), $id]
        );

        return $hasher->checkPassword($token, $hash);
    }

    public function view()
    {
    }

    public function buildHash(User $u, $test = 1)
    {
        $db = $this->app->make(Connection::class);

        $validThrough = time() + (int) $this->app->make(Repository::class)->get('concrete.session.remember_me.lifetime');
        $hasher = $this->app->make(PasswordHasher::class);

        $tries = 10;
        do {
            $token = $this->genString(32);

            try {
                // Truncate the list down to 9 entries
                $id = $db->fetchOne(
                    'SELECT ID from authTypeConcreteCookieMap where uID = ? order by ID desc limit 1 offset 9',
                    [$u->getUserID()]
                );
                $db->executeStatement(
                    'DELETE from authTypeConcreteCookieMap where (uID = ? and ID <= ?) or validThrough < ?',
                    [$u->getUserID(), $id ?: 0, time()]
                );

                $db->insert('authTypeConcreteCookieMap', [
                    'token' => $hasher->hashPassword($token),
                    'uID' => $u->getUserID(),
                    'validThrough' => $validThrough,
                ]);
                $insertId = $db->lastInsertId();
                break;
            } catch (\Exception $e) {
            }

            if ($tries-- === 0) {
                throw new UserMessageException(t('There was a database error, try again.'));
            }
        } while (1);

        return $insertId . '@' . $token;
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
        return $u->isRegistered();
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
        $error = $this->app->make('helper/validation/error');
        $em = $this->post('uEmail');
        $token = $this->app->make(Token::class);
        $this->set('authType', $this->getAuthenticationType());
        $this->set('token', $token);

        if ($em) {
            try {

                $accessControlCategoryService = $this->app->make('ip/access/control/forgot_password');
                /**
                 * @var $accessControlCategoryService IpAccessControlService
                 */
                if ($accessControlCategoryService->isDenylisted()) {
                    $forgotPasswordThresholdReached = true;
                } else {
                    $forgotPasswordThresholdReached = $accessControlCategoryService->isThresholdReached();
                    $accessControlCategoryService->registerEvent();
                    if ($forgotPasswordThresholdReached) {
                        $accessControlCategoryService->addToDenylistForThresholdReached();
                    }
                }

                if ($forgotPasswordThresholdReached) {
                    throw new \Exception(t('Unable to request password reset: too many attempts. Please try again later.'));
                }

                if (!$token->validate()) {
                    throw new \Exception($token->getErrorMessage());
                }

                $e = $this->app->make('error');
                if (!$this->app->make(EmailValidator::class)->isValid($em, $e)) {
                    throw new \Exception($e->toText());
                }

                $oUser = UserInfo::getByEmail($em);
                if ($oUser) {
                    $mh = $this->app->make('helper/mail');
                    //$mh->addParameter('uPassword', $oUser->resetUserPassword());
                    if (Config::get('concrete.user.registration.email_registration')) {
                        $mh->addParameter('uName', $oUser->getUserEmail());
                    } else {
                        $mh->addParameter('uName', $oUser->getUserName());
                    }
                    $mh->to($oUser->getUserEmail());

                    //generate hash that'll be used to authenticate user, allowing them to change their password
                    $h = new ValidationHash();
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

                    $mh->addParameter('siteName', tc('SiteName', $this->app->make('site')->getSite()->getSiteName()));
                    $mh->load('forgot_password');
                    @$mh->sendMail();
                }
            } catch (\Exception $e) {
                $error->add($e);
            }
            if ($error->has()) {
                $this->set('callbackError', $error);
            } else {
                $this->redirect('/login', $this->getAuthenticationType()->getAuthenticationTypeHandle(),
                    'password_sent');
            }
        }
    }

    public function change_password($uHash = '')
    {
        $this->set('authType', $this->getAuthenticationType());
        $e = Core::make('helper/validation/error');
        if (is_string($uHash)) {
            $ui = UserInfo::getByValidationHash($uHash);
        } else {
            $ui = null;
        }
        if (is_object($ui)) {
            $vh = new ValidationHash();
            if ($vh->isValid($uHash)) {
                if (isset($_POST['uPassword']) && strlen($_POST['uPassword'])) {
                    Core::make('validator/password')->isValidFor($_POST['uPassword'], $ui, $e);

                    if (strlen($_POST['uPassword']) && $_POST['uPasswordConfirm'] != $_POST['uPassword']) {
                        $e->add(t('The two passwords provided do not match.'));
                    }

                    if (!$e->has()) {
                        $ui->changePassword($_POST['uPassword']);
                        $h = Core::make('helper/validation/identifier');
                        $h->deleteKey('UserValidationHashes', 'uHash', $uHash);
                        $this->set('passwordChanged', true);

                        $this->redirect('/login', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'password_changed');
                    } else {
                        $this->set('error', $e);
                    }
                }
                $this->set('uHash', $uHash);
                $this->set('authTypeElement', 'change_password');
                return;
            }
        }
        $this->redirect('/login', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'invalid_token');
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
            /** @var Repository $config */
            $config = $this->app->make(Repository::class);

            if ($config->get('concrete.user.registration.email_registration')) {
                throw new Exception(t('Please provide both email address and password.'));
            } else {
                throw new Exception(t('Please provide both username and password.'));
            }
        }

        $uName = $post['uName'];
        $uPassword = $post['uPassword'];

        /** @var \Concrete\Core\Permission\IPService $ip_service */
        $ip_service = Core::make('ip');
        if ($ip_service->isDenylisted()) {
            throw new \Exception($ip_service->getErrorMessage());
        }


        $loginService = $this->app->make(LoginService::class);


        try {
            $user = $loginService->login($uName, $uPassword);
        } catch (UserPasswordResetException $e) {
            Session::set('uPasswordResetUserName', $this->post('uName'));
            $this->redirect('/login/', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'required_password_upgrade');
        } catch (UserException $e) {
            $this->handleFailedLogin($loginService, $uName, $uPassword, $e);
        }

        if ($user->isError()) {
            throw new UserMessageException(t('Unknown login error occurred. Please try again.'));
        }

        if (isset($post['uMaintainLogin']) && $post['uMaintainLogin']) {
            $user->setAuthTypeCookie('concrete');
        }

        $loginService->logLoginAttempt($uName);

        return $user;
    }

    protected function handleFailedLogin(LoginService $loginService, $uName, $uPassword, UserException $e)
    {
        if ($e instanceof InvalidCredentialsException) {
            // Track the failed login
            try {
                $loginService->failLogin($uName, $uPassword);
            } catch (FailedLoginThresholdExceededException $e) {
                $loginService->logLoginAttempt($uName, ['Failed Login Threshold Exceeded', $e->getMessage()]);

                // Rethrow the failed threshold error
                throw $e;
            } catch (UserDeactivatedException $e) {
                $loginService->logLoginAttempt($uName, ['User Deactivated', $e->getMessage()]);

                // Rethrow the user deactivated exception
                throw $e;
            }
        }

        $loginService->logLoginAttempt($uName, ['Invalid Credentials', $e->getMessage()]);

        // Rethrow the exception
        throw $e;
    }

    private function isPasswordReset()
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        if (Config::get('concrete.user.registration.email_registration')) {
            return $db->GetOne('select uIsPasswordReset from Users where uEmail = ?', [$this->post('uName')]);
        } else {
            return $db->GetOne('select uIsPasswordReset from Users where uName = ?', [$this->post('uName')]);
        }
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
