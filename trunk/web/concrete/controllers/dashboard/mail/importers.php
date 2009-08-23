<?
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::library('mail/importer');
class DashboardMailImportersController extends Controller {

	public function view() {
		$this->set('importers', MailImporter::getList());
	}
	
	public function edit($miID = false) {
		$this->set('form', Loader::helper('form'));
		$this->set('mi', MailImporter::getByID($miID));
	}
	
	public function save() {
		$miID = $this->post('miID');
		$mi = MailImporter::getByID($miID);
		if (is_object($mi)) {
			$mi->update($this->post());
			$this->redirect('/dashboard/mail/importers', 'importer_updated');
		}
	}
	
	public function importer_updated() {
		$this->set('message', t('Importer saved.'));
		$this->view();
	}
}

?>