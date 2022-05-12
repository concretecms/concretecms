<?php
namespace Concrete\Controller\SinglePage;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Context\FrontendFormContext;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Attribute\Key\UserKey as UserAttributeKey;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Support\Facade\UserInfo;
use Concrete\Core\User\User;

class Register extends PageController
{
    public $helpers = ['form', 'html'];

    protected $displayUserName = true;

    public function on_start()
    {
        $allowedTypes = ['validate_email', 'enabled'];
        $token = $this->app->make('token');
        $this->set('token', $token);
        $config = $this->app->make(Repository::class);
        $currentType = $config->get('concrete.user.registration.type');

        if (!in_array($currentType, $allowedTypes)) {
            return $this->replace('/page_not_found');
        }
        $u = $this->app->make(User::class);
        $this->set('u', $u);
        if (!$this->displayUserName) {
            // something has overridden this controller and we want to honor that
            $displayUserName = false;
        } else {
            $displayUserName = $config->get('concrete.user.registration.display_username_field');
        }

        $this->displayUserName = $displayUserName;
        $this->set('displayUserName', $displayUserName);
        $this->requireAsset('css', 'core/frontend/captcha');
        $this->set('renderer', new Renderer(new FrontendFormContext()));

        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('user');
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();
        $attributeSets = [];
        foreach ($setManager->getAttributeSets() as $set) {
            foreach ($set->getAttributeKeys() as $ak) {
                if ($ak->isAttributeKeyEditableOnRegister()) {
                    $attributeSets[$set->getAttributeSetDisplayName()][] = $ak;
                }
            }
        }
        $this->set('attributeSets', $attributeSets);
        $unassignedAttributes = [];
        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            if ($ak->isAttributeKeyEditableOnRegister()) {
                $unassignedAttributes[] = $ak;
            }
        }
        $this->set('unassignedAttributes', $unassignedAttributes);
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
            $username = $_POST['uName'];
            $password = $_POST['uPassword'];
            $passwordConfirm = $_POST['uPasswordConfirm'];

            // clean the username
            $username = trim($username);
            $username = preg_replace('/ +/', ' ', $username);

            if ($ip->isDenylisted()) {
                $e->add($ip->getErrorMessage());
            }

            if ($config->get('concrete.user.registration.captcha')) {
                $captcha = $this->app->make('helper/validation/captcha');
                if (!$captcha->check()) {
                    $e->add(t('Incorrect image validation code. Please check the image and re-enter the letters or numbers as necessary.'));
                }
            }

            if ($this->displayUserName) {
                $this->app->make('validator/user/name')->isValid($username, $e);
            }

            $this->app->make('validator/user/email')->isValid($_POST['uEmail'], $e);

            $this->app->make('validator/password')->isValid($password, $e);

            $displayConfirmPasswordField = $config->get('concrete.user.registration.display_confirm_password_field');
            if ($password && $displayConfirmPasswordField) {
                if ($password != $passwordConfirm) {
                    $e->add(t('The two passwords provided do not match.'));
                }
            }

            $aks = UserAttributeKey::getRegistrationList();

            foreach ($aks as $uak) {
                $controller = $uak->getController();
                $validator = $controller->getValidator();
                $response = $validator->validateSaveValueRequest(
                    $controller,
                    $this->request,
                    $uak->isAttributeKeyRequiredOnRegister()
                );
                if (!$response->isValid()) {
                    $error = $response->getErrorObject();
                    $e->add($error);
                }
            }
        } else {
            $e->add(t('Invalid token.'));
        }

        if (!$e->has()) {
            // do the registration
            $data = $_POST;
            if ($this->displayUserName) {
                $data['uName'] = $username;
            } else {
                $userService = $this->app->make(\Concrete\Core\Application\Service\User::class);
                $data['uName'] = $userService->generateUsernameFromEmail($_POST['uEmail']);
            }
            $data['uPassword'] = $password;
            $data['uPasswordConfirm'] = $passwordConfirm;

            $process = $this->app->make('user/registration')->createFromPublicRegistration($data);
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

                    $mh->addParameter('uID', $process->getUserID());
                    $mh->addParameter('user', $process);
                    $mh->addParameter('uName', $process->getUserName());
                    $mh->addParameter('uEmail', $process->getUserEmail());
                    $attribs = UserAttributeKey::getRegistrationList();
                    $attribValues = [];
                    foreach ($attribs as $ak) {
                        $attribValues[] = $ak->getAttributeKeyDisplayName('text') . ': ' . $process->getAttribute($ak->getAttributeKeyHandle(),
                                'display');
                    }
                    $mh->addParameter('attribs', $attribValues);
                    $mh->addParameter('siteName', tc('SiteName', \Core::make('site')->getSite()->getSiteName()));

                    if ($config->get('concrete.email.register_notification.address')) {
                        if (Config::get('concrete.email.register_notification.name')) {
                            $fromName = Config::get('concrete.email.register_notification.name');
                        } else {
                            $fromName = t('Website Registration Notification');
                        }
                        $mh->from(Config::get('concrete.email.register_notification.address'), $fromName);
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $mh->from($adminUser->getUserEmail(), t('Website Registration Notification'));
                        }
                    }

                    $mh->load('user_register');

                    $mh->sendMail();
                }

                // now we log the user in
                if ($config->get('concrete.user.registration.email_registration')) {
                    $u = new User($this->post('uEmail'), $this->post('uPassword'));
                } else {
                    $u = new User($_POST['uName'], $_POST['uPassword']);
                }
                // if this is successful, uID is loaded into session for this user

                $rcID = $this->post('rcID');
                $nh = $this->app->make('helper/validation/numbers');
                if (!$nh->integer($rcID)) {
                    $rcID = 0;
                }

                // Call deactivate() separately because someone might be still attaching
                // to the on_user_deactivate method during the registration.
                // This used to be in the non-validation case only but with the workflow,
                // we need to default the new user to inactive (uIsActive=0).
                $process->deactivate();

                $redirectMethod = null;

                // now we check whether we need to validate this user's email address
                if ($config->get('concrete.user.registration.validate_email')) {
                    $this->app->make('user/status')->sendEmailValidation($process);

                    //$this->redirect('/register', 'register_success_validate', $rcID);
                    $redirectMethod = 'register_success_validate';
                    $u->logout();
                } else {
                    $process->markValidated();
                    if (!$process->triggerActivate('register_activate', USER_SUPER_ID)) {
                        $redirectMethod = 'register_pending';
                        $this->set('message', $this->getRegisterPendingMsg());
                        $u->logout();
                    }
                }

                if (!$u->isError()) {
                    //$this->redirect('/register', 'register_success', $rcID);
                    if (!$redirectMethod) {
                        $redirectMethod = 'register_success';
                    }
                }

                $requestFormat = $_REQUEST['format'] ?? null;
                if ($requestFormat != 'JSON') {
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
        $msgs = [];
        $msgs[] = t('You are registered but you need to validate your email address. Some or all functionality on this site will be limited until you do so.');
        $msgs[] = t('An email has been sent to your email address. Click on the URL contained in the email to validate your email address.');

        return $msgs;
    }

    public function getRegisterPendingMsg()
    {
        return t('You are registered but a site administrator must review your account, you will not be able to login until your account has been approved.');
    }
}
