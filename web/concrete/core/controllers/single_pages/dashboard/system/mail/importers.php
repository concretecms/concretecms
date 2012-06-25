<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Mail_Importers extends Controller {
	protected $sendUndefinedTasksToView = false;
	public function on_start() {	
		$this->set('importers',MailImporter::getList() );
		
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
			$this->redirect('/dashboard/system/mail/importers', 'importer_updated');
		}
	}
	
	public function importer_updated() {
		$this->set('message', t('Importer saved.'));
	}
		
}

?>