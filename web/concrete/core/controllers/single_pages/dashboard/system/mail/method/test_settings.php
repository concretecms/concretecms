<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Mail_Method_TestSettings extends DashboardBaseController {
	protected $sendUndefinedTasksToView = false;
	
	public function successful($mailRecipient) {
		$this->set('mailRecipient', $mailRecipient);
		$this->set("message", t('The test email has been successfully sent to %s.', $mailRecipient) . "\n" . t('You will receive a test message from %s', EMAIL_DEFAULT_FROM_ADDRESS));
	}
	
	public function test() {
		if (!Loader::helper('validation/token')->validate('test')) {
			$this->error->add(t('Invalid Token.'));
			return;
		}
		$mailRecipient = $this->post('mailRecipient');
		if(!is_string($mailRecipient)) {
			$mailRecipient = '';
		}
		if(!ENABLE_EMAILS) {
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
				/* @var $mail MailHelper */
				$mail = Loader::helper('mail');
				$mail->setTesting(true);
				$mail->setSubject(t(/*i18n: %s is the site name*/'Test message from %s', SITE));
				$mail->to($mailRecipient);
				$body = t('This is a test message.');
				$body .= "\n\n" . t('Configuration:');
				$body .= "\n- " . t('Send mail method: %s', MAIL_SEND_METHOD);
				switch(MAIL_SEND_METHOD) {
					case 'SMTP':
						$body .= "\n- " . t('SMTP Server: %s', Config::get('MAIL_SEND_METHOD_SMTP_SERVER'));
						$body .= "\n- " . t('SMTP Port: %s', (Config::get('MAIL_SEND_METHOD_SMTP_PORT') == '') ? tc('SMTP Port', 'default'): Config::get('MAIL_SEND_METHOD_SMTP_PORT'));
						$body .= "\n- " . t('SMTP Encryption: %s', (Config::get('MAIL_SEND_METHOD_SMTP_ENCRYPTION') == '') ? tc('SMTP Encryption', 'none'): Config::get('MAIL_SEND_METHOD_SMTP_ENCRYPTION'));
						if(Config::get('MAIL_SEND_METHOD_SMTP_USERNAME') == '') {
							$body .= "\n- " . t('SMTP Authentication: none');
						}
						else {
							$body .= "\n- " . t('SMTP Username: %s', Config::get('MAIL_SEND_METHOD_SMTP_USERNAME'));
							$body .= "\n- " . t('SMTP Password: %s', tc('Password', '<hidden>'));
						}
						break;
				}
				$mail->setBody($body);
				$mail->sendMail();
				$this->redirect('/dashboard/system/mail/method/test_settings/', 'successful', rawurlencode($mailRecipient));
			}
			catch(Exception $x) {
				$this->error->add(t('The following error was found while trying to send the test email:') . '<br />' . nl2br(h($x->getMessage())));
			}
		}
		$this->set('mailRecipient', $mailRecipient);
	}
}
