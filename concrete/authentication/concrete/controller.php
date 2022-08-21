<?php
namespace Concrete\Authentication\Concrete;

use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Encryption\PasswordHasher;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\IpAccessControlService;
use Concrete\Core\Session\SessionValidatorInterface;
use Concrete\Core\User\Exception\FailedLoginThresholdExceededException;
use Concrete\Core\User\Exception\InvalidCredentialsException;
use Concrete\Core\User\Exception\UserDeactivatedException;
use Concrete\Core\User\Exception\UserException;
use Concrete\Core\User\Exception\UserPasswordResetException;
use Concrete\Core\User\Login\LoginService;
use Concrete\Core\User\PersistentAuthentication\CookieService;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\User\ValidationHash;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Validator\String\EmailValidator;
use Concrete\Core\View\View;
use Exception;

class Controller extends AuthenticationTypeController
{
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
        $uID = (int) $u->getUserID();
        $db = $this->app->make(Connection::class);
        $hasher = $this->app->make(PasswordHasher::class);
        $validRow = false;
        $validThrough = time();
        $rows = $db->fetchAllAssociative('SELECT validThrough, token FROM authTypeConcreteCookieMap WHERE uID = ? AND validThrough > ? ORDER BY validThrough DESC', [$uID, $validThrough]);
        foreach ($rows as $row) {
            if ($hasher->checkPassword($hash, $row['token'])) {
                $validRow = true;
                break;
            }
        }

        // delete all invalid entries for this user
        $db->executeStatement('DELETE FROM authTypeConcreteCookieMap WHERE uID = ? AND validThrough < ?', [$uID, $validThrough]);

        return $validRow;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeController::view()
     */
    public function view()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Authentication\AuthenticationTypeControllerInterface::buildHash()
     */
    public function buildHash(User $u, $test = 1)
    {
        if ($test > 10) {
            // This should only ever happen if by some stroke of divine intervention,
            // we end up pulling 10 hashes that already exist. the chances of this are very very low.
            throw new UserMessageException(t('There was a database error, try again.'));
        }
        $db = $this->app->make(Connection::class);

        $validThrough = time() + (int) $this->app->make(Repository::class)->get('concrete.session.remember_me.lifetime');
        $token = $this->app->make(Identifier::class)->getString(32);
        $hasher = $this->app->make(PasswordHasher::class);
        try {
            $db->insert('authTypeConcreteCookieMap', [
                'token' => $hasher->hashPassword($token),
                'uID' => $u->getUserID(),
                'validThrough' => $validThrough,
            ]);
        } catch (\Exception $e) {
            // HOLY CRAP.. SERIOUSLY?
            $this->buildHash($u, ++$test);
        }

        return $token;
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
        $email = $this->request->request->get('uEmail');
        $email = is_string($email) ? trim($email) : '';
        $token = $this->app->make(Token::class);
        $this->set('token', $token);

        if ($email !== '') {
            $errorValidator = $this->app->make('helper/validation/error');
            $userInfo = null;
            if ($this->app->make(SessionValidatorInterface::class)->hasActiveSession()) {
                $uName = $this->app->make('session')->get('uPasswordResetUserName');
                if (is_string($uName) && $uName !== '') {
                    $userInfo = $this->app->make(UserInfoRepository::class)->getByName($uName);
                }
            }
            try {
                if ($userInfo && $email != $userInfo->getUserEmail()) {
                    throw new UserMessageException(t('Invalid email address %s provided resetting a password', $email));
                }
            } catch (\Exception $e) {
                $errorValidator->add($e);
            }

            $this->forgot_password();
        } else {
            $this->set('authType', $this->getAuthenticationType());
            $this->set('intro_msg', $this->app->make('config/database')->get('concrete.password.reset.message'));
        }
    }

    /**
     * Called when a user wants a password reset email sent, is passed in the user's email address.
     */
    public function forgot_password()
    {
        $error = $this->app->make('helper/validation/error');
        $em = $this->request->request->get('uEmail');
        $em = is_string($em) ? trim($em) : '';
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
                    throw new UserMessageException(t('Unable to request password reset: too many attempts. Please try again later.'));
                }

                if (!$token->validate()) {
                    throw new UserMessageException($token->getErrorMessage());
                }

                $e = $this->app->make('error');
                if (!$this->app->make(EmailValidator::class)->isValid($em, $e)) {
                    throw new UserMessageException($e->toText());
                }

                $oUser = $this->app->make(UserInfoRepository::class)->getByEmail($em);
                if ($oUser) {
                    $mh = $this->app->make('helper/mail');
                    //$mh->addParameter('uPassword', $oUser->resetUserPassword());
                    if ($this->app->make(Repository::class)->get('concrete.user.registration.email_registration')) {
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

                    $fromEmail = (string) $this->app->make(Repository::class)->get('concrete.email.forgot_password.address');
                    if (!strpos($fromEmail, '@')) {
                        $adminUser = $this->app->make(UserInfoRepository::class)->getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $fromEmail = $adminUser->getUserEmail();
                        } else {
                            $fromEmail = '';
                        }
                    }
                    if ($fromEmail) {
                        $fromName = (string) $this->app->make(Repository::class)->get('concrete.email.forgot_password.name');
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
            $this->app->make('session')->set('uPasswordResetUserName', $uName);
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

    private function isPasswordReset()
    {
        $db = $this->app->make(Connection::class);

        if ($this->app->make(Repository::class)->get('concrete.user.registration.email_registration')) {
            return $db->fetchOne('select uIsPasswordReset from Users where uEmail = ?', [$this->post('uName')]);
        } else {
            return $db->fetchOne('select uIsPasswordReset from Users where uName = ?', [$this->post('uName')]);
        }
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
