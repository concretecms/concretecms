<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Mail;

use Concrete\Core\Page\Controller\DashboardPageController;

class Method extends DashboardPageController
{
    public function view()
    {
        $this->set('config', $this->app->make('config'));
    }

    public function save_settings()
    {
        if ($this->token->validate('save_settings')) {
            $config = $this->app->make('config');
            $config->save('concrete.mail.method', strtolower($this->post('MAIL_SEND_METHOD')));
            if ($this->post('MAIL_SEND_METHOD') == 'SMTP') {
                $config->save('concrete.mail.methods.smtp.server', $this->post('MAIL_SEND_METHOD_SMTP_SERVER'));
                $config->save('concrete.mail.methods.smtp.username', $this->post('MAIL_SEND_METHOD_SMTP_USERNAME'));
                $config->save('concrete.mail.methods.smtp.password', $this->post('MAIL_SEND_METHOD_SMTP_PASSWORD'));
                $config->save('concrete.mail.methods.smtp.port', $this->post('MAIL_SEND_METHOD_SMTP_PORT'));
                $config->save('concrete.mail.methods.smtp.encryption', $this->post('MAIL_SEND_METHOD_SMTP_ENCRYPTION'));
                $messages_per_connection = (int) $this->post('MAIL_SEND_METHOD_SMTP_MESSAGES_PER_CONNECTION');
                $config->save('concrete.mail.methods.smtp.messages_per_connection', $messages_per_connection > 0 ? $messages_per_connection : null);
            } else {
                $config->clear('concrete.mail.methods.smtp.server');
                $config->clear('concrete.mail.methods.smtp.username');
                $config->clear('concrete.mail.methods.smtp.password');
                $config->clear('concrete.mail.methods.smtp.port');
                $config->clear('concrete.mail.methods.smtp.encryption');
            }
            $this->flash('success', t('Global mail settings saved.'));
            $this->redirect($this->action(''));
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }
}
