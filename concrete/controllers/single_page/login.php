<?php
namespace Concrete\Controller\SinglePage;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\User\PostLoginLocation;
use Exception;
use PageController;
use Concrete\Core\User\User;
use UserAttributeKey;
use UserInfo;

class Login extends PageController
{
    public $helpers = ['form'];
    protected $locales = [];

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
        $config = $this->app->make('config');
        $this->error->add(t($config->get('concrete.user.deactivation.message')));
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
        } catch (Exception $e) {
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
                if ($user && $user->isRegistered()) {
                    return $this->finishAuthentication($at, $user);
                }
            } catch (Exception $e) {
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
     * @throws Exception
     */
    public function finishAuthentication(
        AuthenticationType $type,
        User $u
    ) {
        if (!$type || !($type instanceof AuthenticationType)) {
            return $this->view();
        }
        $config = $this->app->make('config');
        if ($config->get('concrete.i18n.choose_language_login')) {
            $userLocale = $this->post('USER_LOCALE');
            if (is_string($userLocale) && ($userLocale !== '')) {
                if ($userLocale !== Localization::BASE_LOCALE) {
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

            return $this->getViewObject()->render();
        }

        $u->setLastAuthType($type);

        $ue = new \Concrete\Core\User\Event\User($u);
        $this->app->make('director')->dispatch('on_user_login', $ue);

        return new RedirectResponse(
            $this->app->make('url/manager')->resolve(['/login', 'login_complete'])
        );
    }

    public function login_complete()
    {
        // Move this functionality to a redirected endpoint rather than from within the previous method because
        // session isn't set until we redirect and reload.
        $u = new User();
        if (!$this->error) {
            $this->error = $this->app->make('helper/validation/error');
        }

        if ($u->isRegistered()) {
            $pll = $this->app->make(PostLoginLocation::class);
            $response = $pll->getPostLoginRedirectResponse(true);

            return $response;
        } else {
            $this->error->add(t('User is not registered. Check your authentication controller.'));
            $u->logout();
        }
    }

    public function on_start()
    {
        $config = $this->app->make('config');
        $this->error = $this->app->make('helper/validation/error');
        $this->set('valt', $this->app->make('helper/validation/token'));

        $txt = $this->app->make('helper/text');
        if (isset($_GET['uName']) && strlen($_GET['uName'])
        ) { // pre-populate the username if supplied, if its an email address with special characters the email needs to be urlencoded first,
            $this->set('uName', trim($txt->email($_GET['uName'])));
        }

        $loc = Localization::getInstance();
        $loc->pushActiveContext(Localization::CONTEXT_SITE);
        if ($config->get('concrete.user.registration.email_registration')) {
            $this->set('uNameLabel', t('Email Address'));
        } else {
            $this->set('uNameLabel', t('Username'));
        }
        $languages = [];
        $locales = [];

        if ($config->get('concrete.i18n.choose_language_login')) {
            $languages = Localization::getAvailableInterfaceLanguages();
            if (count($languages) > 0) {
                array_unshift($languages, Localization::BASE_LOCALE);
            }
            $locales = [];
            foreach ($languages as $lang) {
                $locales[$lang] = \Punic\Language::getName($lang, $lang);
            }
            asort($locales);
            $locales = array_merge(['' => tc('Default locale', '** Default')], $locales);
        }
        $loc->popActiveContext();
        $this->locales = $locales;
        $this->set('locales', $locales);
    }

    /**
     * @deprecated Use the getPostLoginUrl method of \Concrete\Core\User\PostLoginLocation
     *
     * @see \Concrete\Core\User\PostLoginLocation::getPostLoginUrl()
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        $pll = $this->app->make(PostLoginLocation::class);
        $url = $pll->getPostLoginUrl(true);
        
        return $url;
    }

    /**
     * @deprecated Use the getSessionPostLoginUrl method of \Concrete\Core\User\PostLoginLocation
     *
     * @see \Concrete\Core\User\PostLoginLocation::getSessionPostLoginUrl()
     *
     * @return string|false
     */
    public function getRedirectUrlFromSession()
    {
        $pll = $this->app->make(PostLoginLocation::class);
        $url = $pll->getSessionPostLoginUrl(true);

        return $url === '' ? false : $url;
    }

    public function view($type = null, $element = 'form')
    {
        $this->requireAsset('javascript', 'backstretch');
        $this->set('authTypeParams', $this->getSets());
        if (strlen($type)) {
            try {
                $at = AuthenticationType::getByHandle($type);
                $this->set('authType', $at);
                $this->set('authTypeElement', $element);
            } catch (\Exception $e) {
                // Don't fail loudly
            }
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
                throw new Exception(t('Invalid Request, please attempt login again.'));
            }
            User::loginByUserID($session->get('uRequiredAttributeUser'));
            $session->remove('uRequiredAttributeUser');
            $u = new User();
            $at = AuthenticationType::getByHandle($session->get('uRequiredAttributeUserAuthenticationType'));
            $session->remove('uRequiredAttributeUserAuthenticationType');
            if (!$at) {
                throw new Exception(t('Invalid Authentication Type'));
            }

            $ui = UserInfo::getByID($u->getUserID());
            $aks = UserAttributeKey::getRegistrationList();

            $unfilled = array_values(
                array_filter(
                    $aks,
                    function ($ak) use ($ui) {
                        return $ak->isAttributeKeyRequiredOnRegister() && !is_object($ui->getAttributeValueObject($ak));
                    }));

            $saveAttributes = [];
            foreach ($unfilled as $attribute) {
                $controller = $attribute->getController();
                $validator = $controller->getValidator();
                $response = $validator->validateSaveValueRequest($controller, $this->request);
                /* @var \Concrete\Core\Validation\ResponseInterface $response */
                if ($response->isValid()) {
                    $saveAttributes[] = $attribute;
                } else {
                    $error = $response->getErrorObject();
                    $this->error->add($error);
                }
            }

            if (count($saveAttributes) > 0) {
                $ui->saveUserAttributesForm($saveAttributes);
            }

            return $this->finishAuthentication($at, $u);
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }
    }

    /**
     * @deprecated
     */
    public function logout($token = false)
    {
        if ($this->app->make('token')->validate('logout', $token)) {
            $u = new User();
            $u->logout();
            $this->redirect('/');
        }
    }

    /**
     * @param $token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function do_logout($token = false)
    {
        $factory = $this->app->make(ResponseFactoryInterface::class);
        /* @var ResponseFactoryInterface $factory */
        $valt = $this->app->make('token');
        /* @var \Concrete\Core\Validation\CSRF\Token $valt */

        if ($valt->validate('do_logout', $token)) {
            // Resolve the current logged in user and log them out
            $this->app->make(User::class)->logout();

            // Determine the destination URL
            $url = $this->app->make('url/manager')->resolve(['/']);

            // Return a new redirect to the homepage.
            return $factory->redirect((string) $url, 302);
        }

        return $factory->error($valt->getErrorMessage());
    }

    public function forward($cID = 0)
    {
        $nh = $this->app->make('helper/validation/numbers');
        if ($nh->integer($cID, 1)) {
            $rcID = (int) $cID;
            $this->set('rcID', $rcID);
            $pll = $this->app->make(PostLoginLocation::class);
            $pll->setSessionPostLoginUrl($rcID);
        }
    }
}
