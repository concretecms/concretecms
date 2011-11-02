<?
defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('mail/importer');

class DashboardSystemMailMethodController extends Controller {
	protected $sendUndefinedTasksToView = false;
	
	public function settings_updated() {
		$this->set("message", t('Global mail settings saved.'));
	}
	
	public function save_settings() {
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
		$this->redirect("/dashboard/settings/mail", "settings_updated");
	}

		
}

?>