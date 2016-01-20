<?php
namespace Concrete\Controller\SinglePage\Account;

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
use Localization;

class EditProfile extends AccountPageController
{
    public function view()
    {
        $u = new User();
        $profile = UserInfo::getByID($u->getUserID());
        if (is_object($profile)) {
            $this->set('profile', $profile);
        } else {
            throw new Exception(t('You must be logged in to access this page.'));
        }
        $locales = array();
        $languages = Localization::getAvailableInterfaceLanguages();
        if (count($languages) > 0) {
            array_unshift($languages, 'en_US');
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

    public function callback($type, $method = 'callback')
    {
        $at = AuthenticationType::getByHandle($type);
        $this->view();
        if (!method_exists($at->controller, $method)) {
            throw new exception('Invalid method.');
        }
        if ($method != 'callback') {
            if (!is_array($at->controller->apiMethods) || !in_array($method, $at->controller->apiMethods)) {
                throw new Exception("Invalid method.");
            }
        }
        try {
            $message = call_user_method($method, $at->controller);
            if (trim($message)) {
                $this->set('message', $message);
            }
        } catch (exception $e) {
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

        $uh = Loader::helper('concrete/user');
        $th = Loader::helper('text');
        $vsh = Loader::helper('validation/strings');
        $cvh = Loader::helper('concrete/validation');
        $valt = Loader::helper('validation/token');

        $data = $this->post();

        /*
         * Validation
        */
        //token
        if (!$valt->validate('profile_edit')) {
            $this->error->add($valt->getErrorMessage());
        }

        // validate the user's email
        $email = $this->post('uEmail');
        if (!$vsh->email($email)) {
            $this->error->add(t('Invalid email address provided.'));
        } elseif (!$cvh->isUniqueEmail($email) && $ui->getUserEmail() != $email) {
            $this->error->add(t("The email address '%s' is already in use. Please choose another.", $email));
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
            if ($uak->isAttributeKeyRequiredOnProfile()) {
                $validator = $uak->getAttributeType()->getValidator();
                $response = $validator->validateSaveValueRequest($uak, $this->request);
                /**
                 * @var $response ResponseInterface
                 */
                if (!$response->isValid()) {
                    $error = $response->getErrorObject();
                    $this->error->add($error);
                }
            }
        }

        if (!$this->error->has()) {
            $data['uEmail'] = $email;
            if (Config::get('concrete.misc.user_timezones')) {
                $data['uTimezone'] = $this->post('uTimezone');
            }

            $ui->saveUserAttributesForm($aks);
            $ui->update($data);
            $this->redirect("/account", "save_complete");
        }
    }
}
