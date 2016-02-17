<?
namespace Concrete\Controller\SinglePage;

use PageController;
use Config;
use User;
use UserInfo;
use UserAttributeKey;

class Register extends PageController
{
    public $helpers = array('form', 'html');

    protected $displayUserName = true;

    public function on_start()
    {
        if (!in_array(Config::get('concrete.user.registration.type'), array('validate_email', 'enabled', 'manual_approve'))) {
            $this->replace('/page_not_found');
        }
        $u = new User();
        $this->set('u', $u);
        $this->set('displayUserName', $this->displayUserName);
        $this->requireAsset('css', 'core/frontend/captcha');
    }

    public function forward($cID = 0)
    {
        $this->set('rcID', $this->app->make('helper/security')->sanitizeInt($cID));
    }

    public function do_register()
    {
        $config = $this->app->make('config');
        $e = $this->app->make('error');
        $ip = $this->app->make('helper/validation/ip');
        $vals = $this->app->make('helper/validation/strings');
        $valc = $this->app->make('helper/concrete/validation');
        $token = $this->app->make('token');

        if ($token->validate('register.do_register')) {
            $username = $this->post('uName');
            $password = $this->post('uPassword');
            $passwordConfirm = $this->post('uPasswordConfirm');

            // clean the username
            $username = trim($username);
            $username = preg_replace("/ +/", " ", $username);

            if ($ip->isBanned()) {
                $e->add($ip->getErrorMessage());
            }

            if ($config->get('concrete.user.registration.captcha')) {
                $captcha = $this->app->make('helper/validation/captcha');
                if (!$captcha->check()) {
                    $e->add(t("Incorrect image validation code. Please check the image and re-enter the letters or numbers as necessary."));
                }
            }

            if (!$vals->email($this->post('uEmail'))) {
                $e->add(t('Invalid email address provided.'));
            } elseif (!$valc->isUniqueEmail($this->post('uEmail'))) {
                $e->add(t("The email address %s is already in use. Please choose another.", $this->post('uEmail')));
            }

            if ($this->displayUserName) {
                if (strlen($username) < $config->get('concrete.user.username.minimum')) {
                    $e->add(t('A username must be at least %s characters long.',
                        $config->get('concrete.user.username.minimum')));
                }

                if (strlen($username) > $config->get('concrete.user.username.maximum')) {
                    $e->add(t('A username cannot be more than %s characters long.',
                        $config->get('concrete.user.username.maximum')));
                }

                if (strlen($username) >= $config->get('concrete.user.username.minimum') && strlen($username) <= $config->get('concrete.user.username.maximum') && !$valc->username($username)) {
                    if ($config->get('concrete.user.username.allow_spaces')) {
                        $e->add(t('A username may only contain letters, numbers, spaces (not at the beginning/end), dots (not at the beginning/end), underscores (not at the beginning/end).'));
                    } else {
                        $e->add(t('A username may only contain letters, numbers, dots (not at the beginning/end), underscores (not at the beginning/end).'));
                    }
                }
                if (!$valc->isUniqueUsername($username)) {
                    $e->add(t("The username %s already exists. Please choose another", $username));
                }
            }

            if ($username == USER_SUPER) {
                $e->add(t('Invalid Username'));
            }

            $this->app->make('validator/password')->isValid($password, $e);

            if ($password) {
                if ($password != $passwordConfirm) {
                    $e->add(t('The two passwords provided do not match.'));
                }
            }

            $aks = UserAttributeKey::getRegistrationList();

            foreach ($aks as $uak) {
                if ($uak->isAttributeKeyRequiredOnRegister()) {
                    $e1 = $uak->validateAttributeForm();
                    if ($e1 == false) {
                        $e->add(t('The field "%s" is required', $uak->getAttributeKeyDisplayName()));
                    } elseif ($e1 instanceof \Concrete\Core\Error\Error) {
                        $e->add($e1);
                    }
                }
            }
        } else {
            $e->add(t('Invalid token.'));
        }

        if (!$e->has()) {

            // do the registration
            $data = $this->post();
            $data['uName'] = $username;
            $data['uPassword'] = $password;
            $data['uPasswordConfirm'] = $passwordConfirm;

            $process = $this->app->make('user.registration')->createFromPublicRegistration($data);
            if (is_object($process)) {
                $process->saveUserAttributesForm($aks);

                if ($config->get('concrete.user.registration.notification')) { //do we notify someone if a new user is added?
                    $mh = $this->app->make('mail');
                    if ($config->get('concrete.user.registration.notification_email')) {
                        $mh->to($config->get('concrete.user.registration.notification_email'));
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $mh->to($adminUser->getUserEmail());
                        }
                    }

                    $mh->addParameter('uID',    $process->getUserID());
                    $mh->addParameter('user',   $process);
                    $mh->addParameter('uName',  $process->getUserName());
                    $mh->addParameter('uEmail', $process->getUserEmail());
                    $attribs = UserAttributeKey::getRegistrationList();
                    $attribValues = array();
                    foreach ($attribs as $ak) {
                        $attribValues[] = $ak->getAttributeKeyDisplayName('text') . ': ' . $process->getAttribute($ak->getAttributeKeyHandle(), 'display');
                    }
                    $mh->addParameter('attribs', $attribValues);
                    $mh->addParameter('siteName', tc('SiteName', $config->get('concrete.site')));

                    if ($config->get('concrete.user.registration.notification_email')) {
                        $mh->from($config->get('concrete.user.registration.notification_email'),  t('Website Registration Notification'));
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $mh->from($adminUser->getUserEmail(),  t('Website Registration Notification'));
                        }
                    }
                    if ($config->get('concrete.user.registration.type') == 'manual_approve') {
                        $mh->load('user_register_approval_required');
                    } else {
                        $mh->load('user_register');
                    }
                    $mh->sendMail();
                }

                // now we log the user in
                if ($config->get('concrete.user.registration.email_registration')) {
                    $u = new User($this->post('uEmail'), $this->post('uPassword'));
                } else {
                    $u = new User($this->post('uName'), $this->post('uPassword'));
                }
                // if this is successful, uID is loaded into session for this user

                $rcID = $this->post('rcID');
                $nh = $this->app->make('helper/validation/numbers');
                if (!$nh->integer($rcID)) {
                    $rcID = 0;
                }

                $redirectMethod = '';

                // now we check whether we need to validate this user's email address
                if ($config->get('concrete.user.registration.validate_email')) {
                    $uHash = $process->setupValidation();

                    $mh = $this->app->make('mail');
                    $fromEmail = (string) $config->get('concrete.email.validate_registration.address');
                    if (strpos($fromEmail, '@')) {
                        $fromName = (string) $config->get('concrete.email.validate_registration.name');
                        if ($fromName === '') {
                            $fromName = t('Validate Email Address');
                        }
                        $mh->from($fromEmail,  $fromName);
                    }
                    $mh->addParameter('uEmail', $this->post('uEmail'));
                    $mh->addParameter('uHash', $uHash);
                    $mh->addParameter('site', tc('SiteName', $config->get('concrete.site')));
                    $mh->to($this->post('uEmail'));
                    $mh->load('validate_user_email');
                    $mh->sendMail();

                    //$this->redirect('/register', 'register_success_validate', $rcID);
                    $redirectMethod = 'register_success_validate';
                    $u->logout();
                } elseif ($config->get('concrete.user.registration.approval')) {
                    $ui = UserInfo::getByID($u->getUserID());
                    $ui->deactivate();
                    // Email to the user when he/she registered but needs approval
                    $mh = $this->app->make('mail');
                    $mh->addParameter('uEmail', $this->post('uEmail'));
                    $mh->addParameter('site', tc('SiteName', $config->get('concrete.site')));
                    $mh->to($this->post('uEmail'));
                    $mh->load('user_register_approval_required_to_user');
                    $mh->sendMail();

                    //$this->redirect('/register', 'register_pending', $rcID);
                    $redirectMethod = 'register_pending';
                    $this->set('message', $this->getRegisterPendingMsg());
                    $u->logout();
                }

                if (!$u->isError()) {
                    //$this->redirect('/register', 'register_success', $rcID);
                    if (!$redirectMethod) {
                        $redirectMethod = 'register_success';
                    }
                }

                if ($_REQUEST['format'] != 'JSON') {
                    $this->redirect('/register', $redirectMethod, $rcID);
                }
            }
        } else {
            $this->set('error', $e);
        }
    }

    public function register_success_validate($rcID = 0)
    {
        $this->set('rcID', $rcID);
        $this->set('registerSuccess', 'validate');
        $this->set('successMsg', $this->getRegisterSuccessValidateMsgs());
    }

    public function register_success($rcID = 0)
    {
        $this->set('rcID', $rcID);
        $this->set('registerSuccess', 'registered');
        $this->set('successMsg', $this->getRegisterSuccessMsg());
    }

    public function register_pending($rcID = 0)
    {
        $this->set('rcID', $rcID);
        $this->set('registerSuccess', 'pending');
        $this->set('successMsg', $this->getRegisterPendingMsg());
    }

    public function getRegisterSuccessMsg()
    {
        return t('Your account has been created, and you are now logged in.');
    }

    public function getRegisterSuccessValidateMsgs()
    {
        $msgs = array();
        $msgs[] = t('You are registered but you need to validate your email address. Some or all functionality on this site will be limited until you do so.');
        $msgs[] = t('An email has been sent to your email address. Click on the URL contained in the email to validate your email address.');

        return $msgs;
    }

    public function getRegisterPendingMsg()
    {
        return t('You are registered but a site administrator must review your account, you will not be able to login until your account has been approved.');
    }
}