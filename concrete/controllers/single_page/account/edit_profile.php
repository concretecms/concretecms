<?php
namespace Concrete\Controller\SinglePage\Account;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Controller\AccountPageController;
use Concrete\Core\Validation\ResponseInterface;
use Config;
use UserInfo;
use Exception;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Loader;
use User;
use UserAttributeKey;
use Concrete\Core\Localization\Localization;

class EditProfile extends AccountPageController
{
    public function view()
    {
        $profile = $this->get('profile');
        if (!is_object($profile)) {
            throw new UserMessageException(t('You must be logged in to access this page.'));
        }

        $locales = array();
        $languages = Localization::getAvailableInterfaceLanguages();
        if (count($languages) > 0) {
            array_unshift($languages, Localization::BASE_LOCALE);
        }
        if (count($languages) > 0) {
            foreach ($languages as $lang) {
                $locales[$lang] = \Punic\Language::getName($lang, $lang);
            }
            asort($locales);
            $locales = array_merge(array('' => tc('Default locale', '** Default')), $locales);
        }
        $this->set('locales', $locales);
    }

    public function save_complete()
    {
        $this->set('success', t('Profile updated successfully.'));
        $this->view();
    }

    public function callback($type, $method = 'callback')
    {
        $at = AuthenticationType::getByHandle($type);
        $this->view();
        if (!method_exists($at->controller, $method)) {
            throw new UserMessageException('Invalid method.');
        }
        if ($method != 'callback') {
            if (!is_array($at->controller->apiMethods) || !in_array($method, $at->controller->apiMethods)) {
                throw new UserMessageException("Invalid method.");
            }
        }
        try {
            $message = call_user_method($method, $at->controller);
            if (trim($message)) {
                $this->set('message', $message);
            }
        } catch (Exception $e) {
            if ($e instanceof AuthenticationTypeFailureException) {
                // Throw again if this is a big`n
                throw $e;
            }
            $this->error->add($e->getMessage());
        }
    }

    public function save()
    {
        $this->view();
        $ui = $this->get('profile');

        /** @var Application $app */
        $app = $this->app;

        /** @var Strings $vsh */
        $vsh = $app->make('helper/validation/strings');

        /** @var Validation $cvh */
        $cvh = $app->make('helper/concrete/validation');

        /** @var Token $valt */
        $valt = $app->make('token');

        $data = $this->post();

        if (!$valt->validate('profile_edit')) {
            $this->error->add($valt->getErrorMessage());
        }

        // validate the user's email
        $email = $this->post('uEmail');
        if (!$vsh->email($email)) {
            $this->error->add(t('Invalid email address provided.'));
        } else {
            if (!$cvh->isUniqueEmail($email) && $ui->getUserEmail() != $email) {
                $this->error->add(t("The email address '%s' is already in use. Please choose another.", $email));
            }
        }

        /**
         * Username validation
         */
        if ($username = $this->post('uName')) {
            if (!$cvh->username($username)) {
                $this->error->add(t('Invalid username provided.'));
            } elseif (!$cvh->isUniqueUsername($username)) {
                $this->error->add(t("The username '%s' is already in use. Please choose another.", $username));
            }
        }

        // password
        if (strlen($data['uPasswordNew'])) {
            $passwordNew = $data['uPasswordNew'];
            $passwordNewConfirm = $data['uPasswordNewConfirm'];

            \Core::make('validator/password')->isValid($passwordNew, $this->error);

            if ($passwordNew) {
                if ($passwordNew != $passwordNewConfirm) {
                    $this->error->add(t('The two passwords provided do not match.'));
                }
            }
            $data['uPasswordConfirm'] = $passwordNew;
            $data['uPassword'] = $passwordNew;
        }

        $aks = UserAttributeKey::getEditableInProfileList();

        foreach ($aks as $uak) {
            $controller = $uak->getController();
            $validator = $controller->getValidator();
            $response = $validator->validateSaveValueRequest($controller, $this->request, $uak->isAttributeKeyRequiredOnProfile());
            /**
             * @var $response ResponseInterface
             */
            if (!$response->isValid()) {
                $error = $response->getErrorObject();
                $this->error->add($error);
            }
        }

        if (!$this->error->has()) {
            $data['uEmail'] = $email;
            if (Config::get('concrete.misc.user_timezones')) {
                $data['uTimezone'] = $this->post('uTimezone');
            }

            $ui->saveUserAttributesForm($aks);
            $ui->update($data);
            $this->redirect("/account/edit_profile", "save_complete");
        }
    }
}
