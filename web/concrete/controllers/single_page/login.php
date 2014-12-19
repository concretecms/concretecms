<?php
namespace Concrete\Controller\SinglePage;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Concrete\Core\Authentication\LoginException;
use Concrete\Core\Routing\RedirectResponse;
use Config;
use Events;
use Loader;
use Localization;
use Page;
use PageController;
use Permissions;
use Session;
use User;
use UserAttributeKey;
use UserInfo;
use View;

class Login extends PageController
{

    public $helpers = array('form');
    protected $locales = array();

    public function on_before_render()
    {
        if ($this->error->has()) {
            $this->set('error', $this->error);
        }
    }

    /* automagically run by the controller once we're done with the current method */
    /* method is passed to this method, the method that we were just finished running */

    public function account_deactivated()
    {
        $this->error->add(t('This user is inactive. Please contact us regarding this account.'));
    }

    public function session_invalidated()
    {
        $this->error->add(t('Your session has expired. Please sign in again.'));
    }

    /**
     * Concrete5_Controller_Login::callback
     * Call an AuthenticationTypeController method throw a uri.
     * Use: /login/TYPE/METHOD/PARAM1/.../PARAM10
     *
     * @param string $type
     * @param string $method
     * @param null   $a
     * @param null   $b
     * @param null   $c
     * @param null   $d
     * @param null   $e
     * @param null   $f
     * @param null   $g
     * @param null   $h
     * @param null   $i
     * @param null   $j
     * @throws \Concrete\Core\Authentication\AuthenticationTypeFailureException
     * @throws \Exception
     */
    public function callback($type=null, $method = 'callback', $a = null, $b = null, $c = null, $d = null, $e = null, $f = null, $g = null, $h = null, $i = null, $j = null)
    {
        if (!$type) {
            return $this->view();
        }
        $at = AuthenticationType::getByHandle($type);
        if ($at) {
            $this->set('authType', $at);
        }
        if (!method_exists($at->controller, $method)) {
            return $this->view();
        }
        if ($method != 'callback') {
            if (!is_array($at->controller->apiMethods) || !in_array($method, $at->controller->apiMethods)) {
                return $this->view();
            }
        }
        try {
            $params = func_get_args();
            array_shift($params);
            array_shift($params);

            $this->view();
            $this->set('authTypeParams', $params);
            $this->set('authTypeElement', $method);
        } catch (\exception $e) {
            if ($e instanceof AuthenticationTypeFailureException) {
                // Throw again if this is a big`n
                throw $e;
            }
            $this->error->add($e->getMessage());
        }
    }

    /**
     * Concrete5_Controller_Login::authenticate
     * Authenticate the user using a specific authentication type.
     *
     * @param $type    AuthenticationType handle
     */
    public function authenticate($type = '')
    {
        $valt = Loader::helper('validation/token');
        if (!$valt->validate('login_' . $type)) {
            $this->error->add($valt->getErrorMessage());
        } else {
            try {
                $at = AuthenticationType::getByHandle($type);
                $user = $at->controller->authenticate();
                if ($user && $user->isLoggedIn()) {
                    $this->finishAuthentication($at);
                }
            } catch (\exception $e) {
                $this->error->add($e->getMessage());
            }
        }
        $this->view();
    }

    /**
     * @param AuthenticationType $type Required
     * @throws \Exception
     */
    public function finishAuthentication(/* AuthenticationType */
        $type = null
    )
    {
        if (!$type || !($type instanceof AuthenticationType)) {
            return $this->view();
        }
        $db = Loader::db();
        $u = new User();

        $ui = UserInfo::getByID($u->getUserID());
        $aks = UserAttributeKey::getRegistrationList();

        $unfilled = array_values(
            array_filter(
                $aks,
                function ($ak) use ($ui) {
                    return $ak->isAttributeKeyRequiredOnRegister() && !is_object($ui->getAttributeValueObject($ak));
                }));

        if (count($unfilled)) {
            $u->logout(false);

            if (!$this->error) {
                $this->on_start();
            }

            $this->set('required_attributes', $unfilled);
            $this->set('u', $u);

            Session::set('uRequiredAttributeUser', $u->getUserID());
            Session::set('uRequiredAttributeUserAuthenticationType', $type->getAuthenticationTypeHandle());

            $this->view();
            echo $this->getViewObject()->render();
            exit;
        }

        $u->setLastAuthType($type);

        $ue = new \Concrete\Core\User\Event\User($u);
        Events::dispatch('on_user_login', $ue);

        $this->chooseRedirect();
    }

    public function on_start()
    {
        $this->error = Loader::helper('validation/error');
        $this->set('valt', Loader::helper('validation/token'));
        if (Config::get('concrete.user.registration.email_registration')) {
            $this->set('uNameLabel', t('Email Address'));
        } else {
            $this->set('uNameLabel', t('Username'));
        }

        $txt = Loader::helper('text');
        if (strlen(
            $_GET['uName'])
        ) { // pre-populate the username if supplied, if its an email address with special characters the email needs to be urlencoded first,
            $this->set("uName", trim($txt->email($_GET['uName'])));
        }

        $languages = array();
        $locales = array();
        if (Config::get('concrete.i18n.choose_language_login')) {
            $languages = Localization::getAvailableInterfaceLanguages();
            if (count($languages) > 0) {
                array_unshift($languages, 'en_US');
            }
            $locales = array();
            foreach ($languages as $lang) {
                $locales[$lang] = \Punic\Language::getName($lang, $lang);
            }
            asort($locales);
            $locales = array_merge(array('' => tc('Default locale', '** Default')), $locales);
        }
        $this->locales = $locales;
        $this->set('locales', $locales);
    }

    public function chooseRedirect()
    {
        if (!$this->error) {
            $this->error = Loader::helper('validation/error');
        }

        $nh = Loader::helper('validation/numbers');
        $navigation = Loader::helper('navigation');
        $rUrl = false;

        $u = new User(); // added for the required registration attribute change above. We recalc the user and make sure they're still logged in
        if ($u->isRegistered()) {
            if ($u->config('NEWSFLOW_LAST_VIEWED') == 'FIRSTRUN') {
                $u->saveConfig('NEWSFLOW_LAST_VIEWED', 0);
            }
            do {
                // redirect to original destination
                if (Session::has('rcID')) {
                    $rcID = Session::get('rcID');
                    if ($nh->integer($rcID)) {
                        $rc = Page::getByID($rcID);
                    } elseif (strlen($rcID)) {
                        $rcID = trim($rcID, '/');
                        $rc = Page::getByPath('/' . $rcID);
                    }
                    if ($rc instanceof Page && !$rc->isError()) {
                        $rUrl = $navigation->getLinkToCollection($rc);
                        break;
                    }
                }

                // admin to dashboard?
                $dash = Page::getByPath("/dashboard", "RECENT");
                $dbp = new Permissions($dash);
                //should administrator be redirected to dashboard?  defaults to yes if not set.
                $adminToDash = intval(Config::get('concrete.misc.login_admin_to_dashboard'));
                if ($dbp->canRead() && $adminToDash) {
                    if(!$rc instanceof Page || $rc->isError()){
                        $rc = $dash;
                    }
                    $rUrl = $navigation->getLinkToCollection($rc);
                    break;
                }

                //options set in dashboard/users/registration
                $login_redirect_mode = Config::get('concrete.misc.login_redirect');

                //redirect to user profile
                if ($login_redirect_mode == 'PROFILE' && Config::get('concrete.user.profiles_enabled')) {
                    $rUrl = View::url('/members/profile/', $u->getUserID());
                    break;
                }

                //redirect to custom page
                $login_redirect_cid = intval(Config::get('concrete.misc.login_redirect_cid'));
                if ($login_redirect_mode == 'CUSTOM' && $login_redirect_cid > 0) {
                    $rc = Page::getByID($login_redirect_cid);
                    if ($rc instanceof Page && !$rc->isError()) {
                        $rUrl = $navigation->getLinkToCollection($rc);
                        break;
                    }
                }

                break;
            } while (false);

            if ($rUrl) {
                $r = new RedirectResponse($rUrl);
                $r->send();
                exit;
            } else {
                $this->redirect('/');
            }
        } else {
            $this->error->add(t('User is not registered. Check your authentication controller.'));
            $u->logout();
        }
    }

    public function view($type = null, $element = 'form')
    {
        $this->requireAsset('javascript', 'backstretch');
        $this->set('authTypeParams', $this->getSets());
        if (strlen($type)) {
            $at = AuthenticationType::getByHandle($type);
            $this->set('authType', $at);
            $this->set('authTypeElement', $element);
        }
    }

    public function fill_attributes()
    {
        try {
            if (!Session::has('uRequiredAttributeUser') ||
                intval(Session::get('uRequiredAttributeUser')) < 1 ||
                !Session::has('uRequiredAttributeUserAuthenticationType') ||
                !Session::get('uRequiredAttributeUserAuthenticationType')
            ) {
                Session::remove('uRequiredAttributeUser');
                Session::remove('uRequiredAttributeUserAuthenticationType');
                throw new \Exception(t('Invalid Request, please attempt login again.'));
            }
            User::loginByUserID(Session::get('uRequiredAttributeUser'));
            Session::remove('uRequiredAttributeUser');
            $u = new User;
            $at = AuthenticationType::getByHandle(Session::get('uRequiredAttributeUserAuthenticationType'));
            Session::remove('uRequiredAttributeUserAuthenticationType');
            if (!$at) {
                throw new \Exception(t("Invalid Authentication Type"));
            }

            $ui = UserInfo::getByID($u->getUserID());
            $aks = UserAttributeKey::getRegistrationList();

            $unfilled = array_values(
                array_filter(
                    $aks,
                    function ($ak) use ($ui) {
                        return $ak->isAttributeKeyRequiredOnRegister() && !is_object($ui->getAttributeValueObject($ak));
                    }));

            $saveAttributes = array();
            foreach ($unfilled as $attribute) {
                $err = $attribute->validateAttributeForm();
                if ($err == false) {
                    $this->error->add(t('The field "%s" is required', $attribute->getAttributeKeyDisplayName()));
                } elseif ($err instanceof \Concrete\Core\Error\Error) {
                    $this->error->add($err);
                } else {
                    $saveAttributes[] = $attribute;
                }
            }

            if (count($saveAttributes) > 0) {
                $ui->saveUserAttributesForm($saveAttributes);
            }
            $this->finishAuthentication($at);
        } catch (\Exception $e) {
            $this->error->add($e->getMessage());
        }
    }

    public function logout($token = false)
    {
        if (Loader::helper('validation/token')->validate('logout', $token)) {
            $u = new User();
            $u->logout();
            $this->redirect('/');
        }
    }

    public function forward($cID = 0)
    {
        $nh = Loader::helper('validation/numbers');
        if ($nh->integer($cID) && intval($cID) > 0) {
            $this->set('rcID', intval($cID));
            Session::set('rcID', intval($cID));
        }
    }

}
