<?php
namespace Concrete\Controller\SinglePage;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Concrete\Core\Routing\RedirectResponse;
use Localization;
use Page;
use PageController;
use Permissions;
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
     * Use: /login/TYPE/METHOD/PARAM1/.../PARAM10.
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
     *
     * @throws \Concrete\Core\Authentication\AuthenticationTypeFailureException
     * @throws \Exception
     */
    public function callback($type = null, $method = 'callback', $a = null, $b = null, $c = null, $d = null, $e = null, $f = null, $g = null, $h = null, $i = null, $j = null)
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
        $valt = $this->app->make('token');
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

        if (isset($at)) {
            $this->set('lastAuthType', $at);
        }

        $this->view();
    }

    /**
     * @param AuthenticationType $type Required
     *
     * @throws \Exception
     */
    public function finishAuthentication(/* AuthenticationType */
        $type = null
    ) {
        if (!$type || !($type instanceof AuthenticationType)) {
            return $this->view();
        }
        $u = new User();
        $config = $this->app->make('config');
        if ($config->get('concrete.i18n.choose_language_login')) {
            $userLocale = $this->post('USER_LOCALE');
            if (is_string($userLocale) && ($userLocale !== '')) {
                if ($userLocale !== 'en_US') {
                    $availableLocales = Localization::getAvailableInterfaceLanguages();
                    if (!in_array($userLocale, $availableLocales)) {
                        $userLocale = '';
                    }
                }
                if ($userLocale !== '') {
                    if (Localization::activeLocale() !== $userLocale) {
                        Localization::changeLocale($userLocale);
                    }
                    $u->setUserDefaultLanguage($userLocale);
                }
            }
        }

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

            $session = $this->app->make('session');
            $session->set('uRequiredAttributeUser', $u->getUserID());
            $session->set('uRequiredAttributeUserAuthenticationType', $type->getAuthenticationTypeHandle());

            $this->view();
            echo $this->getViewObject()->render();
            exit;
        }

        $u->setLastAuthType($type);

        $ue = new \Concrete\Core\User\Event\User($u);
        $this->app->make('director')->dispatch('on_user_login', $ue);

        $this->chooseRedirect();
    }

    public function on_start()
    {
        $config = $this->app->make('config');
        $this->error = $this->app->make('helper/validation/error');
        $this->set('valt', $this->app->make('helper/validation/token'));
        if ($config->get('concrete.user.registration.email_registration')) {
            $this->set('uNameLabel', t('Email Address'));
        } else {
            $this->set('uNameLabel', t('Username'));
        }

        $txt = $this->app->make('helper/text');
        if (isset($_GET['uName']) && strlen($_GET['uName'])
        ) { // pre-populate the username if supplied, if its an email address with special characters the email needs to be urlencoded first,
            $this->set("uName", trim($txt->email($_GET['uName'])));
        }

        $languages = array();
        $locales = array();
        if ($config->get('concrete.i18n.choose_language_login')) {
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
        $config = $this->app->make('config');
        $session = $this->app->make('session');

        if (!$this->error) {
            $this->error = $this->app->make('helper/validation/error');
        }

        $nh = $this->app->make('helper/validation/numbers');
        $navigation = $this->app->make('helper/navigation');
        $rUrl = false;

        $u = new User(); // added for the required registration attribute change above. We recalc the user and make sure they're still logged in
        if ($u->isRegistered()) {
            if ($u->config('NEWSFLOW_LAST_VIEWED') == 'FIRSTRUN') {
                $u->saveConfig('NEWSFLOW_LAST_VIEWED', 0);
            }
            do {
                // redirect to original destination
                if ($session->has('rUri')) {
                    $rUrl = $session->get('rUri');
                    $session->remove('rUri');
                    if ($rUrl) {
                        break;
                    }
                }
                if ($session->has('rcID')) {
                    $rcID = $session->get('rcID');
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
                $adminToDash = intval($config->get('concrete.misc.login_admin_to_dashboard'));
                if ($dbp->canRead() && $adminToDash) {
                    if (!$rc instanceof Page || $rc->isError()) {
                        $rc = $dash;
                    }
                    $rUrl = $navigation->getLinkToCollection($rc);
                    break;
                }

                //options set in dashboard/users/registration
                $login_redirect_mode = $config->get('concrete.misc.login_redirect');

                //redirect to user profile
                if ($login_redirect_mode == 'PROFILE') {
                    $profileURL = $u->getUserInfoObject()->getUserPublicProfileUrl();
                    if ($profileURL) {
                        $rUrl = $profileURL;
                    }
                    break;
                }

                //redirect to custom page
                $login_redirect_cid = intval($config->get('concrete.misc.login_redirect_cid'));
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
            $session = $this->app->make('session');
            if (!$session->has('uRequiredAttributeUser') ||
                intval($session->get('uRequiredAttributeUser')) < 1 ||
                !$session->has('uRequiredAttributeUserAuthenticationType') ||
                !$session->get('uRequiredAttributeUserAuthenticationType')
            ) {
                $session->remove('uRequiredAttributeUser');
                $session->remove('uRequiredAttributeUserAuthenticationType');
                throw new \Exception(t('Invalid Request, please attempt login again.'));
            }
            User::loginByUserID($session->get('uRequiredAttributeUser'));
            $session->remove('uRequiredAttributeUser');
            $u = new User();
            $at = AuthenticationType::getByHandle($session->get('uRequiredAttributeUserAuthenticationType'));
            $session->remove('uRequiredAttributeUserAuthenticationType');
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
        if ($this->app->make('token')->validate('logout', $token)) {
            $u = new User();
            $u->logout();
            $this->redirect('/');
        }
    }

    public function forward($cID = 0)
    {
        $nh = $this->app->make('helper/validation/numbers');
        if ($nh->integer($cID) && intval($cID) > 0) {
            $this->set('rcID', intval($cID));
            $this->app->make('session')->set('rcID', intval($cID));
        }
    }
}
