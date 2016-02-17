<?
namespace Concrete\Controller\SinglePage\Account;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\Service\Validation;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Concrete\Core\Page\Controller\AccountPageController;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\CSRF\Token;
use Config;
use Exception;
use Loader;
use Localization;
use User;
use UserAttributeKey;
use UserInfo;

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
            if ($uak->isAttributeKeyRequiredOnProfile()) {
                $e1 = $uak->validateAttributeForm();
                if ($e1 == false) {
                    $this->error->add(t('The field "%s" is required', $uak->getAttributeKeyDisplayName()));
                } else {
                    if ($e1 instanceof \Concrete\Core\Error\Error) {
                        $this->error->add($e1);
                    }
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
