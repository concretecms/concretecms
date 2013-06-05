<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Environment_FileStorageLocations extends DashboardBaseController {

	var $helpers = array('form','concrete/interface','validation/token', 'concrete/file');
	
	public function view($updated=false) {
		$helper_file = Loader::helper('concrete/file');
		Loader::model('file_storage_location');
		$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
		if (is_object($fsl)) {
			$this->set('fslName', $fsl->getName());
			$this->set('fslDirectory', $fsl->getDirectory());
		}
	}
	

	public function storage_saved() {
		$this->set('message', t('File storage locations saved.'));
		$this->view();
	}

	public function save(){
		$helper_file = Loader::helper('concrete/file');
		$validation_token = Loader::helper('validation/token');
		
		if (!$validation_token->validate("file_storage")) {
			$this->set('error', array($validation_token->getErrorMessage()));
			return;
		}
		
		Config::save('DIR_FILES_UPLOADED', $this->post('DIR_FILES_UPLOADED'));

		if ($this->post('fslName') != '' && $this->post('fslDirectory') != '') {
			Loader::model('file_storage_location');
			$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
			if (!is_object($fsl)) {
				FileStorageLocation::add($this->post('fslName'), $this->post('fslDirectory'), FileStorageLocation::ALTERNATE_ID);
			} else {
				$fsl->update($this->post('fslName'), $this->post('fslDirectory'));
			}			
		}

		$this->redirect('/dashboard/system/environment/file_storage_locations','storage_saved');
	}
	
}