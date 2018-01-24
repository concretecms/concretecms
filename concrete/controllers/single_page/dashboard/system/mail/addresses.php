<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Mail;

use Concrete\Core\Page\Controller\DashboardPageController;

class Addresses extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('defaultName', $config->get('concrete.email.default.name'));
        $this->set('defaultAddress', $config->get('concrete.email.default.address'));
        $this->set('forgotPasswordName', $config->get('concrete.email.forgot_password.name'));
        $this->set('forgotPasswordAddress', $config->get('concrete.email.forgot_password.address'));
        $this->set('formBlockAddress', $config->get('concrete.email.form_block.address'));
        $this->set('spamNotificationAddress', $config->get('concrete.spam.notify_email'));
        $this->set('registerNotificationAddress', $config->get('concrete.email.register_notification.address'));
        $this->set('validateRegistrationName', $config->get('concrete.email.validate_registration.name'));
        $this->set('validateRegistrationAddress', $config->get('concrete.email.validate_registration.address'));
        $this->set('workflowNotificationName', $config->get('concrete.email.workflow_notification.name'));
        $this->set('workflowNotificationAddress', $config->get('concrete.email.workflow_notification.address'));
    }

    public function save()
    {
        if (!$this->token->validate('addresses')) {
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));
            $this->view();
        } else {
            $config = $this->app->make('config');
            $config->save('concrete.email.default.name', $this->request->post('defaultName'));
            $config->save('concrete.email.default.address', $this->request->post('defaultAddress') ? $this->request->post('defaultAddress') : 'concrete5-noreply@concrete5');
            $config->save('concrete.email.forgot_password.name', $this->request->post('forgotPasswordName'));
            $config->save('concrete.email.forgot_password.address', $this->request->post('forgotPasswordAddress'));
            $config->save('concrete.email.form_block.address', $this->request->post('formBlockAddress'));
            $config->save('concrete.spam.notify_email', $this->request->post('spamNotificationAddress'));
            $config->save('concrete.email.register_notification.address', $this->request->post('registerNotificationAddress'));
            $config->save('concrete.email.validate_registration.name', $this->request->post('validateRegistrationName'));
            $config->save('concrete.email.validate_registration.address', $this->request->post('validateRegistrationAddress'));
            $config->save('concrete.email.workflow_notification.name', $this->request->post('workflowNotificationName'));
            $config->save('concrete.email.workflow_notification.address', $this->request->post('workflowNotificationAddress'));
            $this->flash('message', t('Successfully saved system email addresses.'));
            $this->redirect('/dashboard/system/mail/addresses');
        }
    }
}
