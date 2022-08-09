<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Validator\String\EmailValidator;

class Open extends DashboardPageController
{
    public function view($updated = false)
    {
        $config = $this->app->make(Repository::class);
        $registrationType = $config->get('concrete.user.registration.type');
        if (!in_array($registrationType, $this->getAvailableRegistrationTypes(), true)) {
            $registrationType = 'disabled';
        }
        $this->set('registrationType', $registrationType);
        $this->set('registerNotification', (bool) $config->get('concrete.user.registration.notification'));
        $this->set('registerNotificationEmail', (string) $config->get('concrete.user.registration.notification_email'));
        $this->set('emailAsUsername', (bool) $config->get('concrete.user.registration.email_registration'));
        $this->set('displayUsernameField', (bool) $config->get('concrete.user.registration.display_username_field'));
        $this->set('displayConfirmPasswordField', (bool) $config->get('concrete.user.registration.display_confirm_password_field'));
        $this->set('enableRegistrationCaptcha', (bool) $config->get('concrete.user.registration.captcha'));
        $this->set('displayUsernameFieldEdit', (bool) $config->get('concrete.user.edit_profile.display_username_field'));
    }

    public function update_registration_type()
    {
        $post = $this->request->request;
        if (!$this->token->validate('update_registration_type')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $registrationType = $post->get('registration_type');
        if (!in_array($registrationType, $this->getAvailableRegistrationTypes(), true)) {
            $this->error->add(t('Please specify the registration type.'));
            $registrationType = null;
        }

        if ($registrationType !== null && $registrationType !== 'disabled') {
            $registerNotification = (bool) $post->get('register_notification');
            if ($registerNotification) {
                $emailValidator = $this->app->make(EmailValidator::class, ['testMXRecord' => true]);
                $registerNotificationEmails = array_unique(preg_split('/\s*,\s*/', $post->get('register_notification_email', ''), -1, PREG_SPLIT_NO_EMPTY));
                if ($registerNotificationEmails === []) {
                    $this->error->add(t('No notification recipient specified.'));
                } else {
                    foreach ($registerNotificationEmails as $registerNotificationEmail) {
                        $emailValidator->isValid($registerNotificationEmail, $this->error);
                    }
                }
            }
        }

        if ($this->error->has()) {
            return $this->view();
        }

        $config = $this->app->make(Repository::class);
        $config->save('concrete.user.registration.enabled', $registrationType !== 'disabled');
        $config->save('concrete.user.registration.type', $registrationType);

        if ($registrationType !== 'disabled') {
            $config->save('concrete.user.registration.validate_email', $registrationType === 'validate_email');
            $config->save('concrete.user.registration.notification', $registerNotification);
            if ($registerNotification) {
                $config->save('concrete.user.registration.notification_email', implode(',', $registerNotificationEmails));
            }
        }
        $config->save('concrete.user.registration.email_registration', (bool) $post->get('email_as_username'));
        $config->save('concrete.user.registration.display_username_field', (bool) $this->get('display_username_field'));
        $config->save('concrete.user.registration.display_confirm_password_field', (bool) $post->get('display_confirm_password_field'));
        $config->save('concrete.user.registration.captcha', (bool) $post->get('enable_registration_captcha'));
        $config->save('concrete.user.edit_profile.display_username_field', (bool) $this->get('display_username_field_on_edit'));

        $this->flash('success', t('Registration settings have been saved.'));

        return $this->buildRedirect($this->action());
    }

    protected function getAvailableRegistrationTypes(): array
    {
        return [
            'disabled',
            'enabled',
            'validate_email',
        ];
    }
}
