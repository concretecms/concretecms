<?php
namespace Concrete\Authentication\Concrete;

use Concrete\Core\Authentication\AuthenticationTypeController;
use Config;
use Exception;
use Loader;
use User;
use UserInfo;
use View;

class Controller extends AuthenticationTypeController
{

    public $apiMethods = array('forgot_password', 'v', 'change_password', 'password_changed', 'email_validated', 'invalid_token');

    public function getHandle()
    {
        return 'concrete';
    }

    public function deauthenticate(User $u)
    {
        list($uID, $authType, $hash) = explode(':', $_COOKIE['ccmAuthUserHash']);
        if ($authType == 'concrete') {
            $db = Loader::db();
            $db->execute('DELETE FROM authTypeConcreteCookieMap WHERE uID=? AND token=?', array($uID, $hash));
        }
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-user"></i>';
    }

    public function verifyHash(User $u, $hash)
    {
        $uID = $u->getUserID();
        $db = Loader::db();
        $q = $db->getOne(
            'SELECT validThrough FROM authTypeConcreteCookieMap WHERE uID=? AND token=?',
            array($uID, $hash));
        $bool = time() < $q;
        if (!$bool) {
            $db->execute('DELETE FROM authTypeConcreteCookieMap WHERE uID=? AND token=?', array($uID, $hash));
        } else {
            $newTime = strtotime('+2 weeks');
            $db->execute('UPDATE authTypeConcreteCookieMap SET validThrough=?', array($newTime));
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
        $db = Loader::db();

        $validThrough = strtotime('+2 weeks');
        $token = $this->genString();
        try {
            $db->execute(
                'INSERT INTO authTypeConcreteCookieMap (token, uID, validThrough) VALUES (?,?,?)',
                array($token, $u->getUserID(), $validThrough));
        } catch (\Exception $e) {
            // HOLY CRAP.. SERIOUSLY?
            $this->buildHash($u, ++$test);
        }
        return $token;
    }

    private function genString($a = 16)
    {
        if (function_exists('mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv($a, MCRYPT_DEV_URANDOM));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($a));
        }
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
        return ($u->isLoggedIn());
    }

    public function saveAuthenticationType($values)
    {
    }

    /**
     * Called when a user wants a password reset email sent, is passed in the user's email address.
     */
    public function forgot_password()
    {
        $loginData['success'] = 0;
        $error = Loader::helper('validation/error');
        $vs = Loader::helper('validation/strings');
        $em = $this->post('uEmail');

        if ($em) {
            try {
                if (!$vs->email($em)) {
                    throw new \Exception(t('Invalid email address.'));
                }

                $oUser = UserInfo::getByEmail($em);
                if (!$oUser) {
                    throw new \Exception(t('We have no record of that email address.'));
                }

                $mh = Loader::helper('mail');
                //$mh->addParameter('uPassword', $oUser->resetUserPassword());
                if (Config::get('concrete.user.registration.email_registration')) {
                    $mh->addParameter('uName', $oUser->getUserEmail());
                } else {
                    $mh->addParameter('uName', $oUser->getUserName());
                }
                $mh->to($oUser->getUserEmail());

                //generate hash that'll be used to authenticate user, allowing them to change their password
                $h = new \Concrete\Core\User\ValidationHash;
                $uHash = $h->add($oUser->uID, intval(UVTYPE_CHANGE_PASSWORD), true);
                $changePassURL = BASE_URL . View::url(
                        '/login',
                        'callback',
                        $this->getAuthenticationType()->getAuthenticationTypeHandle(),
                        'change_password',
                        $uHash);

                $mh->addParameter('changePassURL', $changePassURL);

                if (defined('EMAIL_ADDRESS_FORGOT_PASSWORD')) {
                    $mh->from(EMAIL_ADDRESS_FORGOT_PASSWORD, t('Forgot Password'));
                } else {
                    $adminUser = UserInfo::getByID(USER_SUPER_ID);
                    if (is_object($adminUser)) {
                        $mh->from($adminUser->getUserEmail(), t('Forgot Password'));
                    }
                }

                $mh->addParameter('siteName', Config::get('concrete.site'));
                $mh->load('forgot_password');
                @$mh->sendMail();

            } catch (\Exception $e) {
                $error->add($e);
            }

            $this->redirect('/login', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'password_sent');
        } else {
            $this->set('authType', $this->getAuthenticationType());
        }
    }

    public function change_password($uHash = '')
    {
        $this->set('authType', $this->getAuthenticationType());
        $db = Loader::db();
        $h = Loader::helper('validation/identifier');
        $e = Loader::helper('validation/error');
        $ui = UserInfo::getByValidationHash($uHash);
        if (is_object($ui)) {
            $hashCreated = $db->GetOne("SELECT uDateGenerated FROM UserValidationHashes WHERE uHash=?", array($uHash));
            if ($hashCreated < (time() - (USER_CHANGE_PASSWORD_URL_LIFETIME))) {
                $h->deleteKey('UserValidationHashes', 'uHash', $uHash);
                throw new \Exception(
                    t(
                        'Key Expired. Please visit the forgot password page again to have a new key generated.'));
            } else {

                if (strlen($_POST['uPassword'])) {

                    $userHelper = Loader::helper('concrete/user');
                    $userHelper->validNewPassword($_POST['uPassword'], $e);

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

    public function email_validated()
    {
        $this->view();
    }

    public function invalid_token()
    {
        $this->view();
    }

    public function authenticate()
    {
        $post = $this->post();

        if (!isset($post['uName']) || !isset($post['uPassword'])) {
            throw new Exception(t('Please provide both username and password.'));
        }
        $uName = $post['uName'];
        $uPassword = $post['uPassword'];

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
        if ($post['uMaintainLogin']) {
            $user->setAuthTypeCookie('concrete');
        }

        return $user;
    }

    public function v($hash = '')
    {
        $ui = \UserInfo::getByValidationHash($hash);
        if (is_object($ui)) {
            $ui->markValidated();
            $this->set('uEmail', $ui->getUserEmail());
            $this->set('validated', true);
            $this->redirect('/login/callback/concrete', 'email_validated');
            exit;
        }
        $this->redirect('/login/callback/concrete', 'invalid_token');
    }

}
