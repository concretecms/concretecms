<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Mail\Method;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use Exception;

class Test extends DashboardPageController {
	protected $sendUndefinedTasksToView = false;

	public function successful($mailRecipient) {
		$this->set('mailRecipient', $mailRecipient);
		$this->set("message", t('The test email has been successfully sent to %s.', $mailRecipient) . "\n" . t('You will receive a test message from %s', Config::get('concrete.email.default.address')));
	}

	public function do_test() {
		if (!Loader::helper('validation/token')->validate('test')) {
			$this->error->add(t('Invalid Token.'));
			return;
		}
		$mailRecipient = $this->post('mailRecipient');
		if(!is_string($mailRecipient)) {
			$mailRecipient = '';
		}
		if(!Config::get('concrete.email.enabled')) {
			$this->error->add(t('The mail system is disabled.'));
		}
		elseif(!strlen($mailRecipient)) {
			$this->error->add(t('The recipient address of the test email has not been specified.'));
		}
		elseif(!Loader::helper('validation/strings')->email($mailRecipient, true)) {
			$this->error->add(t("The email address '%s' is not valid.", h($mailRecipient)));
		}
		else {
			try {
				/* @var $mail \Concrete\Core\Mail\Service */
				$mail = Loader::helper('mail');
				$mail->setTesting(true);
				$mail->setSubject(t(/*i18n: %s is the site name*/'Test message from %s', Config::get('concrete.site')));
				$mail->to($mailRecipient);
				$body = t('This is a test message.');
				$body .= "\n\n" . t('Configuration:');
				$body .= "\n- " . t('Send mail method: %s', Config::get('concrete.mail.method'));
				switch(Config::get('concrete.mail.method')) {
					case 'smtp':
						$body .= "\n- " . t('SMTP Server: %s', Config::get('concrete.mail.methods.smtp.server'));
						$body .= "\n- " . t('SMTP Port: %s', Config::get('concrete.mail.methods.smtp.port', tc('SMTP Port', 'default')));
						$body .= "\n- " . t('SMTP Encryption: %s', Config::get('concrete.mail.methods.smtp.encryption', tc('SMTP Encryption', 'none')));
						if(!Config::get('concrete.mail.methods.smtp.username')) {
							$body .= "\n- " . t('SMTP Authentication: none');
						}
						else {
							$body .= "\n- " . t('SMTP Username: %s', Config::get('concrete.mail.methods.smtp.username'));
							$body .= "\n- " . t('SMTP Password: %s', tc('Password', '<hidden>'));
						}
						break;
				}
				$mail->setBody($body);
				$mail->sendMail();
				$this->redirect('/dashboard/system/mail/method/test/', 'successful', rawurlencode($mailRecipient));
			}
			catch(Exception $x) {
				$this->error->add(t('The following error was found while trying to send the test email:') . '<br />' . nl2br(h($x->getMessage())));
			}
		}
		$this->set('mailRecipient', $mailRecipient);
	}
}
