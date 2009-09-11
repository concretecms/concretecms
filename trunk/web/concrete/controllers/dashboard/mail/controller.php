<?
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::library('mail/importer');

class DashboardMailController extends Controller {

	public function on_start() {
		$this->set('importers', MailImporter::getList());
	}

	public function edit_importer($miID = false) {
		$this->set('form', Loader::helper('form'));
		$this->set('mi', MailImporter::getByID($miID));
	}
	
	public function save_importer() {
		$miID = $this->post('miID');
		$mi = MailImporter::getByID($miID);
		if (is_object($mi)) {
			$mi->update($this->post());
			$this->redirect('/dashboard/mail', 'importer_updated');
		}
	}
	
	public function importer_updated() {
		$this->set('message', t('Importer saved.'));
		$this->view();
	}
	
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
		} else {
			Config::clear('MAIL_SEND_METHOD_SMTP_SERVER');
			Config::clear('MAIL_SEND_METHOD_SMTP_USERNAME');
			Config::clear('MAIL_SEND_METHOD_SMTP_PASSWORD');
			Config::clear('MAIL_SEND_METHOD_SMTP_PORT');
		}
		$this->redirect("/dashboard/mail", "settings_updated");
	}

		
}

?>