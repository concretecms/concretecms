<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Validator\String\EmailValidator;
use Exception;

class Open extends DashboardPageController
{
    public $helpers = ['form', 'concrete/dashboard', 'concrete/ui'];

    public function update_registration_type()
    {
        if (!$this->token->validate('update_registration_type')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if ($this->post('register_notification') && !empty($this->post('register_notification_email'))) {
            $emailValidator = $this->app->make(EmailValidator::class, ['testMXRecord' => true]);
            $registerNotificationEmails = explode(',', $this->post('register_notification_email'));
            foreach ($registerNotificationEmails as $key => $registerNotificationEmail) {
                $registerNotificationEmails[$key] = trim($registerNotificationEmail);
                $emailValidator->isValid($registerNotificationEmails[$key], $this->error);
            }
        }

        if (!$this->error->has() && $this->isPost()) {
            $config = $this->app->make(Repository::class);

            $config->save('concrete.user.registration.email_registration', ($this->post('email_as_username') ? true : false));

            $config->save('concrete.user.registration.type', $this->post('registration_type'));
            $config->save('concrete.user.registration.captcha', ($this->post('enable_registration_captcha')) ? true : false);
            $config->save('concrete.user.registration.display_username_field', ($this->post('display_username_field')) ? true : false);
            $config->save('concrete.user.registration.display_confirm_password_field', ($this->post('display_confirm_password_field')) ? true : false);

            switch ($this->post('registration_type')) {
                case 'enabled':
                    $config->save('concrete.user.registration.enabled', true);
                    $config->save('concrete.user.registration.validate_email', false);
                    $config->save('concrete.user.registration.notification', $this->post('register_notification'));
                    if ($this->post('register_notification') && !empty($this->post('register_notification_email'))) {
                        $config->save('concrete.user.registration.notification_email', implode(',', $registerNotificationEmails));
                    }
                    break;

                case 'validate_email':
                    $config->save('concrete.user.registration.enabled', true);
                    $config->save('concrete.user.registration.validate_email', true);
                    $config->save('concrete.user.registration.notification', $this->post('register_notification'));
                    if ($this->post('register_notification') && !empty($this->post('register_notification_email'))) {
                        $config->save('concrete.user.registration.notification_email', implode(',', $registerNotificationEmails));
                    }
                    break;

                default: // disabled
                    $config->save('concrete.user.registration.enabled', false);
                    $config->save('concrete.user.registration.notification', false);
                    break;
            }
            $config->save('concrete.user.registration.type', $this->post('registration_type'));
            $this->redirect('/dashboard/system/registration/open', 1);
        }
        $this->view();
    }

    public function view($updated = false)
    {
        if ($updated) {
            $this->set('message', t('Registration settings have been saved.'));
        }

        $config = $this->app->make(Repository::class);
        $type = $config->get('concrete.user.registration.type');
        if (!$type) {
            $type = 'disabled';
        }
        $this->set('email_as_username', $config->get('concrete.user.registration.email_registration'));
        $this->set('registration_type', $type);
        $this->set('enable_registration_captcha', $config->get('concrete.user.registration.captcha'));
        $this->set('register_notification', (bool) $config->get('concrete.user.registration.notification'));
        $this->set('register_notification_email', $config->get('concrete.user.registration.notification_email'));
        $this->set('display_username_field', $config->get('concrete.user.registration.display_username_field'));
        $this->set('display_confirm_password_field', $config->get('concrete.user.registration.display_confirm_password_field'));
    }
}
