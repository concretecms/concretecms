<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Mail\Method;

use Concrete\Core\Page\Controller\DashboardPageController;
use Exception;

class Test extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('emailEnabled', (bool) $config->get('concrete.email.enabled'));
    }

    public function do_test()
    {
        if ($this->token->validate('test')) {
            $config = $this->app->make('config');
            if (!$config->get('concrete.email.enabled')) {
                $this->error->add(t('The mail system is disabled.'));
            } else {
                $mailRecipient = $this->post('mailRecipient');
                if (!is_string($mailRecipient)) {
                    $mailRecipient = '';
                }
                if ($mailRecipient === '') {
                    $this->error->add(t('The recipient address of the test email has not been specified.'));
                } elseif (!$this->app->make('helper/validation/strings')->email($mailRecipient, true)) {
                    $this->error->add(t("The email address '%s' is not valid.", h($mailRecipient)));
                }
                $numEmails = $this->post('numEmails');
                if (!$this->app->make('helper/validation/numbers')->integer($numEmails) || ($numEmails = (int) $numEmails) < 1) {
                    $this->error->add(t('Please specify an integer greater than zero for the number of the emails to be sent'));
                }
                if (!$this->error->has()) {
                    try {
                        $baseSubject = t(/*i18n: %s is the site name*/'Test message from %s', $this->app->make('site')->getSite()->getSiteName());
                        $mail = $this->app->make('helper/mail');
                        /* @var \Concrete\Core\Mail\Service $mail */
                        for ($cycle = 1; $cycle <= $numEmails; ++$cycle) {
                            $mail->setTesting(true);
                            if ($numEmails > 1) {
                                $mail->setSubject($baseSubject . " [$cycle/$numEmails]");
                            } else {
                                $mail->setSubject($baseSubject);
                            }
                            $mail->to($mailRecipient);
                            $body = t('This is a test message.');
                            $body .= "\n\n" . t('Configuration:');
                            $body .= "\n- " . t('Send mail method: %s', $config->get('concrete.mail.method'));
                            switch ($config->get('concrete.mail.method')) {
                                case 'smtp':
                                    $body .= "\n- " . t('SMTP Server: %s', $config->get('concrete.mail.methods.smtp.server'));
                                    $body .= "\n- " . t('SMTP Port: %s', $config->get('concrete.mail.methods.smtp.port', tc('SMTP Port', 'default')));
                                    $body .= "\n- " . t('SMTP Encryption: %s', $config->get('concrete.mail.methods.smtp.encryption', tc('SMTP Encryption', 'none')));
                                    if (!$config->get('concrete.mail.methods.smtp.username')) {
                                        $body .= "\n- " . t('SMTP Authentication: none');
                                    } else {
                                        $body .= "\n- " . t('SMTP Username: %s', $config->get('concrete.mail.methods.smtp.username'));
                                        $body .= "\n- " . t('SMTP Password: %s', tc('Password', '<hidden>'));
                                    }
                                    break;
                            }
                            $mail->setBody($body);
                            $mail->sendMail();
                        }
                        $this->flash('numEmails', $numEmails);
                        $this->flash('mailRecipient', $mailRecipient);
                        $this->flash('success',
                            t('The test email has been successfully sent to %s.', $mailRecipient)
                            . "\n" . t('You will receive a test message from %s', $config->get('concrete.email.default.address'))
                        );
                        $this->redirect($this->action(''));
                    } catch (Exception $x) {
                        $this->error->add(t('The following error was found while trying to send the test email:') . '<br />' . nl2br(h($x->getMessage())));
                    }
                }
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }
}
