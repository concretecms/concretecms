<?php 
defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('mail/importer');

class DashboardSettingsMailController extends Controller {
	protected $sendUndefinedTasksToView = false;
	public function on_start() {
		$this->set('importers', MailImporter::getList());
		$subnav = array(
			array(View::url('/dashboard/settings'), t('General')),
			array(View::url('/dashboard/settings/mail'), t('Email'), true),
			array(View::url('/dashboard/settings', 'set_permissions'), t('Access')),
			array(View::url('/dashboard/settings', 'set_developer'), t('Debug')),
			array(View::url('/dashboard/settings', 'manage_attribute_types'), t('Attributes'))
		);
		$this->set('subnav', $subnav);
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
			$this->redirect('/dashboard/settings/mail', 'importer_updated');
		}
	}
	
	public function importer_updated() {
		$this->set('message', t('Importer saved.'));
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