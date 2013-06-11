<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Mail_Method extends DashboardBaseController {
	protected $sendUndefinedTasksToView = false;
	
	public function settings_updated() {
		$this->set("message", t('Global mail settings saved.'));
	}
	
	public function save_settings() {
		if (!Loader::helper('validation/token')->validate('save_settings')) {
			$this->error->add(t('Invalid Token.'));
			return;
		}

		Config::save('MAIL_SEND_METHOD', $this->post('MAIL_SEND_METHOD'));
		if ($this->post('MAIL_SEND_METHOD')== 'SMTP') {
			Config::save('MAIL_SEND_METHOD_SMTP_SERVER', $this->post('MAIL_SEND_METHOD_SMTP_SERVER'));
			Config::save('MAIL_SEND_METHOD_SMTP_USERNAME', $this->post('MAIL_SEND_METHOD_SMTP_USERNAME'));
			Config::save('MAIL_SEND_METHOD_SMTP_PASSWORD', $this->post('MAIL_SEND_METHOD_SMTP_PASSWORD'));
			Config::save('MAIL_SEND_METHOD_SMTP_PORT', $this->post('MAIL_SEND_METHOD_SMTP_PORT'));
			Config::save('MAIL_SEND_METHOD_SMTP_ENCRYPTION', $this->post('MAIL_SEND_METHOD_SMTP_ENCRYPTION'));
		} else {
			Config::clear('MAIL_SEND_METHOD_SMTP_SERVER');
			Config::clear('MAIL_SEND_METHOD_SMTP_USERNAME');
			Config::clear('MAIL_SEND_METHOD_SMTP_PASSWORD');
			Config::clear('MAIL_SEND_METHOD_SMTP_PORT');
			Config::clear('MAIL_SEND_METHOD_SMTP_ENCRYPTION');
		}
		$this->redirect("/dashboard/system/mail/method", "settings_updated");
	}

		
}