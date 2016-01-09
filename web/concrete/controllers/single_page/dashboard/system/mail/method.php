<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Mail;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Config;
use Exception;
class Method extends DashboardPageController {
	protected $sendUndefinedTasksToView = false;

	public function settings_updated() {
		$this->set("message", t('Global mail settings saved.'));
	}

	public function save_settings() {
		if (!Loader::helper('validation/token')->validate('save_settings')) {
			$this->error->add(t('Invalid Token.'));
			return;
		}

		Config::save('concrete.mail.method', strtolower($this->post('MAIL_SEND_METHOD')));
		if ($this->post('MAIL_SEND_METHOD') == 'SMTP') {
			Config::save('concrete.mail.methods.smtp.server', $this->post('MAIL_SEND_METHOD_SMTP_SERVER'));
			Config::save('concrete.mail.methods.smtp.username', $this->post('MAIL_SEND_METHOD_SMTP_USERNAME'));
			Config::save('concrete.mail.methods.smtp.password', $this->post('MAIL_SEND_METHOD_SMTP_PASSWORD'));
			Config::save('concrete.mail.methods.smtp.port', $this->post('MAIL_SEND_METHOD_SMTP_PORT'));
			Config::save('concrete.mail.methods.smtp.encryption', $this->post('MAIL_SEND_METHOD_SMTP_ENCRYPTION'));
		} else {
			Config::clear('concrete.mail.methods.smtp.server');
			Config::clear('concrete.mail.methods.smtp.username');
			Config::clear('concrete.mail.methods.smtp.password');
			Config::clear('concrete.mail.methods.smtp.port');
			Config::clear('concrete.mail.methods.smtp.encryption');
		}
		$this->redirect("/dashboard/system/mail/method", "settings_updated");
	}


}
