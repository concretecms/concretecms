<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Mail\Method;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Exception;

class Test extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('emailEnabled', (bool) $config->get('concrete.email.enabled'));
        $session = $this->app->make('session');
        $sets = $session->getFlashBag()->peek('page_message');
        $setMailRecipient = true;
        foreach ($sets as $set) {
            if ($set[0] === 'mailRecipient') {
                $setMailRecipient = false;
                break;
            }
        }
        if ($setMailRecipient) {
            $mailRecipient = '';
            $me = new User();
            if ($me->isRegistered()) {
                $myInfo = $this->app->make(UserInfoRepository::class)->getByID($me->getUserID());
                if ($myInfo !== null) {
                    $mailRecipient = $myInfo->getUserEmail();
                }
            }
            $this->set('mailRecipient', $mailRecipient);
        }
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
                } else {
                    try {
                        $mail = $this->app->make('helper/mail');
                        $mail->setTesting(true);
                        $mail->setSubject(t(/*i18n: %s is the site name*/'Test message from %s', \Core::make('site')->getSite()->getSiteName()));
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
    }
}
