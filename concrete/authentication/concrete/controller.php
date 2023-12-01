<?php
namespace Concrete\Authentication\Concrete;

use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Encryption\PasswordHasher;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Session\SessionValidatorInterface;
use Concrete\Core\User\Exception\FailedLoginThresholdExceededException;
use Concrete\Core\User\Exception\InvalidCredentialsException;
use Concrete\Core\User\Exception\UserDeactivatedException;
use Concrete\Core\User\Exception\UserException;
use Concrete\Core\User\Exception\UserPasswordExpiredException;
use Concrete\Core\User\Exception\UserPasswordResetException;
use Concrete\Core\User\Login\LoginService;
use Concrete\Core\User\Login\PasswordUpgrade;
use Concrete\Core\User\PersistentAuthentication\CookieService;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\User\ValidationHash;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Validator\String\EmailValidator;
use Concrete\Core\View\View;
use Exception;
use Throwable;

class Controller extends AuthenticationTypeController
{
    protected const REQUIRED_PASSWORD_UPGRADE_SESSIONKEY = 'ccmPasswordReset';

    public $apiMethods = ['forgot_password', 'v', 'change_password', 'password_changed', 'email_validated', 'invalid_token', 'required_password_upgrade'];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeController::getHandle()
     */
    public function getHandle()
    {
        return 'concrete';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeControllerInterface::deauthenticate()
     */
    public function deauthenticate(User $u)
    {
        $authCookie = $this->app->make(CookieService::class)->getCookie();
        if ($authCookie === null || $authCookie->getAuthenticationTypeHandle() !== 'concrete') {
            return;
        }
        $hasher = $this->app->make(PasswordHasher::class);
        $db = $this->app->make(Connection::class);
        foreach ($db->fetchAllAssociative('select token from authTypeConcreteCookieMap WHERE uID = ? ORDER BY validThrough DESC', [$authCookie->getUserID()]) as $row) {
            if ($hasher->checkPassword($authCookie->getToken(), $row['token'])) {
                $db->delete('authTypeConcreteCookieMap', ['uID' => $authCookie->getUserID(), 'token' => $row['token']]);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeController::getAuthenticationTypeIconHTML()
     */
    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fas fa-user"></i>';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeControllerInterface::verifyHash()
     */
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeController::view()
     */
    public function view()
    {
        $this->set('user', $this->app->make(User::class));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeControllerInterface::buildHash()
     */
    public function buildHash(User $u, $test = 1)
    {
        $db = $this->app->make(Connection::class);

        $validThrough = time() + (int) $this->app->make(Repository::class)->get('concrete.session.remember_me.lifetime');
        $hasher = $this->app->make(PasswordHasher::class);

        $tries = 10;
        do {
            $token = $this->app->make(Identifier::class)->getString(32);

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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeControllerInterface::isAuthenticated()
     */
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
        $userInfo = null;
        $loginWithEmail = (bool) $this->app->make(Repository::class)->get('concrete.user.registration.email_registration');
        $session = $this->app->make(SessionValidatorInterface::class)->getActiveSession();
        if ($session !== null) {
            $data = $session->get(static::REQUIRED_PASSWORD_UPGRADE_SESSIONKEY);
            if (is_array($data)) {
                $type = $data['type'] ?? null;
                if (in_array($type, [PasswordUpgrade::PASSWORD_RESET_KEY, PasswordUpgrade::PASSWORD_EXPIRED_KEY], true)) {
                    $uName = $data['uName'] ?? null;
                    if (is_string($uName) && $uName !== '') {
                        if ($loginWithEmail) {
                            $userInfo = $this->app->make(UserInfoRepository::class)->getByEmail($uName);
                        } else {
                            $userInfo = $this->app->make(UserInfoRepository::class)->getByName($uName);
                        }
                    }
                }
            }
        }
        if ($userInfo === null) {
            if ($session !== null) {
                $session->remove(static::REQUIRED_PASSWORD_UPGRADE_SESSIONKEY);
            }
            // We arrived at the required_password_upgrade step but the user didn't specify hasn't fulfilled the login/password form
            $this->redirect('/login', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'view');
        }
        $token = $this->app->make(Token::class);
        $this->set('token', $token);
        if ($this->request->isMethod('POST')) {
            $error = $this->app->make('helper/validation/error');
            $this->passwordUpgrade($userInfo, $error, false);
        }
        $this->set('authType', $this->getAuthenticationType());
        $this->set('intro_msg', $this->app->make(PasswordUpgrade::class)->getPasswordResetMessage($type));
    }

    /**
     * Called when a user wants a password reset email sent, is passed in the user's email address.
     */
    public function forgot_password()
    {
        $token = $this->app->make(Token::class);
        $this->set('authType', $this->getAuthenticationType());
        $this->set('token', $token);
        $error = $this->app->make('helper/validation/error');
        if (!$this->request->isMethod('POST')) {
            return;
        }
        if (!$error->has()) {
            $accessControlCategoryService = $this->app->make('ip/access/control/forgot_password');
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
                $error->add(new UserMessageException(t('Unable to request password reset: too many attempts. Please try again later.')));
            }
        }
        $userInfo = null;
        if (!$error->has()) {
            $email = $this->request->request->get('uEmail');
            $email = is_string($email) ? trim($email) : '';
            if ($email === '') {
                $error->add(t('Please specify a valid email address'));
            } else {
                $e = $this->app->make('error');
                if (!$this->app->make(EmailValidator::class)->isValid($email, $e)) {
                    $error->add($e->toText());
                } else {
                    $userInfo = $this->app->make(UserInfoRepository::class)->getByEmail($email);
                    // $userInfo may be null, but for security reasons we don't show any error message
                }
            }
        }
        $this->passwordUpgrade($userInfo, $error, true);
    }

    private function passwordUpgrade(?UserInfo $userInfo, ErrorList $error, bool $isForgotPassword)
    {
        $token = $this->app->make(Token::class);
        $this->set('authType', $this->getAuthenticationType());
        $this->set('token', $token);
        if (!$error->has() && !$token->validate()) {
            $error->add(new UserMessageException($token->getErrorMessage()));
        }
        if (!$error->has() && $userInfo !== null) {
            $mh = $this->app->make('helper/mail');
            if ($this->app->make(Repository::class)->get('concrete.user.registration.email_registration')) {
                $mh->addParameter('uName', $userInfo->getUserEmail());
            } else {
                $mh->addParameter('uName', $userInfo->getUserName());
            }
            $mh->to($userInfo->getUserEmail());
            $mh->addParameter('isForgotPassword', $isForgotPassword);
            //generate hash that'll be used to authenticate user, allowing them to change their password
            $h = new ValidationHash();
            $uHash = $h->add($userInfo->getUserID(), UVTYPE_CHANGE_PASSWORD, true);
            $changePassURL = View::url(
                '/login',
                'callback',
                $this->getAuthenticationType()->getAuthenticationTypeHandle(),
                'change_password',
                $uHash
            );
            $mh->addParameter('changePassURL', $changePassURL);
            $fromEmail = (string) $this->app->make(Repository::class)->get('concrete.email.forgot_password.address');
            if (!strpos($fromEmail, '@')) {
                $fromEmail = (string) $this->app->make(Repository::class)->get('concrete.email.default.address');
            }
            if ($fromEmail !== '') {
                $fromName = (string) $this->app->make(Repository::class)->get('concrete.email.forgot_password.name');
                if ($fromName === '') {
                    $fromName = $isForgotPassword ? t('Forgot Password') : t('Password Reset');
                }
                $mh->from($fromEmail, $fromName);
            }
            $mh->addParameter('siteName', h(tc('SiteName', $this->app->make('site')->getSite()->getSiteName())));
            $mh->load('forgot_password');
            $mh->setIsThrowOnFailure(true);
            try {
                $mh->sendMail();
            } catch (Throwable $x) {
                $error->add($x);
            }
        }
        if (!$error->has()) {
            $session = $this->app->make(SessionValidatorInterface::class)->getActiveSession();
            if ($session !== null) {
                $session->remove(static::REQUIRED_PASSWORD_UPGRADE_SESSIONKEY);
            }
            $this->redirect('/login', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'password_sent');
        }
        $this->set('authType', $this->getAuthenticationType());
        $this->set('token', $token);
        $this->set('callbackError', $error);
    }

    public function change_password($uHash = '')
    {
        $this->set('authType', $this->getAuthenticationType());
        $e = $this->app->make('helper/validation/error');
        if (is_string($uHash)) {
            $ui = $this->app->make(UserInfoRepository::class)->getByValidationHash($uHash);
        } else {
            $ui = null;
        }
        if (is_object($ui)) {
            $vh = new ValidationHash();
            if ($vh->isValid($uHash)) {
                if (isset($_POST['uPassword']) && strlen($_POST['uPassword'])) {
                    $this->app->make('validator/password')->isValidFor($_POST['uPassword'], $ui, $e);

                    if (strlen($_POST['uPassword']) && $_POST['uPasswordConfirm'] != $_POST['uPassword']) {
                        $e->add(t('The two passwords provided do not match.'));
                    }

                    if (!$e->has()) {
                        $ui->changePassword($_POST['uPassword']);
                        $h = $this->app->make('helper/validation/identifier');
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeControllerInterface::authenticate()
     */
    public function authenticate()
    {
        $uName = $this->request->request->get('uName');
        $uName = is_string($uName) ? trim($uName) : '';
        $uPassword = $this->request->request->get('uPassword');
        if (!is_string($uPassword)) {
            $uPassword = '';
        }
        if ($uName === '' || $uPassword === '') {
            $config = $this->app->make(Repository::class);
            if ($config->get('concrete.user.registration.email_registration')) {
                throw new UserMessageException(t('Please provide both email address and password.'));
            } else {
                throw new UserMessageException(t('Please provide both username and password.'));
            }
        }
        $failedLogins = $this->app->make('failed_login');
        if ($failedLogins->isDenylisted()) {
            throw new UserMessageException($failedLogins->getErrorMessage());
        }


        $loginService = $this->app->make(LoginService::class);


        try {
            $user = $loginService->login($uName, $uPassword);
        } catch (UserPasswordResetException $e) {
            $this->app->make('session')->set(static::REQUIRED_PASSWORD_UPGRADE_SESSIONKEY, ['type' => PasswordUpgrade::PASSWORD_RESET_KEY, 'uName' => $uName]);
            $this->redirect('/login/', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'required_password_upgrade');
        } catch (UserPasswordExpiredException $e) {
            $this->app->make('session')->set(static::REQUIRED_PASSWORD_UPGRADE_SESSIONKEY, ['type' => PasswordUpgrade::PASSWORD_EXPIRED_KEY, 'uName' => $uName]);
            $this->redirect('/login/', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'required_password_upgrade');
        } catch (UserException $e) {
            $this->handleFailedLogin($loginService, $uName, $uPassword, $e);
        }

        if ($user->isError()) {
            throw new UserMessageException(t('Unknown login error occurred. Please try again.'));
        }

        if ($this->request->request->get('uMaintainLogin')) {
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

    public function v($hash = '')
    {
        $ui = $this->app->make(UserInfoRepository::class)->getByValidationHash($hash);
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
