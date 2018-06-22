<?php

namespace Concrete\Controller\SinglePage\Account;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Controller\AccountPageController;
use Config;
use Exception;
use UserAttributeKey;

class EditProfile extends AccountPageController
{
    public function view()
    {
        $profile = $this->get('profile');
        if (!is_object($profile)) {
            throw new UserMessageException(t('You must be logged in to access this page.'));
        }

        $locales = [];
        $languages = Localization::getAvailableInterfaceLanguages();
        if (count($languages) > 0) {
            array_unshift($languages, Localization::BASE_LOCALE);
        }
        if (count($languages) > 0) {
            foreach ($languages as $lang) {
                $locales[$lang] = \Punic\Language::getName($lang, $lang);
            }
            asort($locales);
            $locales = array_merge(['' => tc('Default locale', '** Default')], $locales);
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
                throw new UserMessageException('Invalid method.');
            }
        }
        try {
            $message = call_user_func([$at->controller, $method]);
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
        /* @var \Concrete\Core\User\UserInfo $ui */

        $app = $this->app;

        $valt = $app->make('token');

        $data = $this->post();

        if (!$valt->validate('profile_edit')) {
            $this->error->add($valt->getErrorMessage());
        }

        // validate the user's email
        $email = $this->post('uEmail');
        $app->make('validator/user/email')->isValidFor($email, $ui, $this->error);

        // Username validation
        $username = $this->post('uName');
        if ($username) {
            $app->make('validator/user/name')->isValidFor($username, $ui, $this->error);
        }

        // password
        if (strlen($data['uPasswordNew'])) {
            $passwordNew = $data['uPasswordNew'];
            $passwordNewConfirm = $data['uPasswordNewConfirm'];

            $app->make('validator/password')->isValid($passwordNew, $this->error);

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
            $this->redirect('/account/edit_profile', 'save_complete');
        }
    }
}
