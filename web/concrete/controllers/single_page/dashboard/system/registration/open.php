<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Open extends DashboardPageController
{

    public $helpers = array('form');

    public function update_registration_type()
    {
        if (!$this->token->validate('update_registration_type')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has() && $this->isPost()) {
            Config::save('concrete.user.registration.email_registration', ($this->post('email_as_username') ? true : false));

            Config::save('concrete.user.registration.type', $this->post('registration_type'));
            Config::save('concrete.user.registration.captcha', ($this->post('enable_registration_captcha')) ? true : false);

            switch ($this->post('registration_type')) {
                case "enabled":
                    Config::save('concrete.user.registration.enabled', true);
                    Config::save('concrete.user.registration.validate_email', false);
                    Config::save('concrete.user.registration.approval', false);
                    Config::save('concrete.user.registration.notification', $this->post('register_notification'));
                    Config::save(
                        'concrete.user.registration.notification_email',
                        Loader::helper('security')->sanitizeEmail(
                            $this->post('register_notification_email')));
                    break;

                case "validate_email":
                    Config::save('concrete.user.registration.enabled', true);
                    Config::save('concrete.user.registration.validate_email', true);
                    Config::save('concrete.user.registration.approval', false);
                    Config::save('concrete.user.registration.notification', $this->post('register_notification'));
                    Config::save(
                        'concrete.user.registration.notification_email',
                        Loader::helper('security')->sanitizeEmail(
                            $this->post('register_notification_email')));
                    break;

                case "manual_approve":
                    Config::save('concrete.user.registration.enabled', true);
                    Config::save('concrete.user.registration.approval', true);
                    Config::save('concrete.user.registration.validate_email', false);
                   Config::save('concrete.user.registration.notification', $this->post('register_notification'));
                   Config::save(
                        'concrete.user.registration.notification_email',
                        Loader::helper('security')->sanitizeEmail(
                            $this->post('register_notification_email')));
                    break;

                default: // disabled
                    Config::save('concrete.user.registration.enabled', false);
                    Config::save('concrete.user.registration.notification', false);
                    break;
            }
            Config::save('concrete.user.registration.type', $this->post('registration_type'));
            $this->redirect('/dashboard/system/registration/open', 1);
        }
    }

    public function view($updated = false)
    {
        if ($updated) {
            $this->set('message', t('Registration settings have been saved.'));
        }
        $type =  Config::get('concrete.user.registration.type');
		if (!$type) {
			$type = 'disabled';
		}
        $this->set('email_as_username', Config::get('concrete.user.registration.email_registration'));
        $this->set('registration_type', $type);
        $this->set('enable_registration_captcha', Config::get('concrete.user.registration.captcha'));
        $this->set('register_notification', !!Config::get('concrete.user.registration.notification'));
        $this->set('register_notification_email', Config::get('concrete.user.registration.notification_email'));
    }

}
