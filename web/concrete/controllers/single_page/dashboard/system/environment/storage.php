<?
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use \Concrete\Core\File\StorageLocation as FileStorageLocation;

class Storage extends DashboardPageController {

	var $helpers = array('form','concrete/ui','validation/token', 'concrete/file');
	
	public function view($updated=false) {
		$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
		if (is_object($fsl)) {
			$this->set('fsl', $fsl);
			$this->set('fslName', $fsl->getName());
			$this->set('fslDirectory', $fsl->getDirectory());
		}
	}
	

	public function storage_saved() {
		$this->set('message', t('File storage locations saved.'));
		$this->view();
	}

	public function storage_deleted() {
		$this->set('message', t('File storage location removed. Files using this location have been reset.'));
		$this->view();
	}

	public function save(){
		$helper_file = Loader::helper('concrete/file');
		$validation_token = Loader::helper('validation/token');
		
		if (!$validation_token->validate("file_storage")) {
			$this->set('error', array($validation_token->getErrorMessage()));
			return;
		}

		if ($_POST['delete']) {

			$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
			if (is_object($fsl)) {
				$fsl->delete();
				$this->redirect('/dashboard/system/environment/storage','storage_deleted');
			}

		} else {
			
			Config::save('DIR_FILES_UPLOADED', $this->post('DIR_FILES_UPLOADED'));

			if ($this->post('fslName') != '' && $this->post('fslDirectory') != '') {
				$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
				if (!is_object($fsl)) {
					FileStorageLocation::add($this->post('fslName'), $this->post('fslDirectory'), FileStorageLocation::ALTERNATE_ID);
				} else {
					$fsl->update($this->post('fslName'), $this->post('fslDirectory'));
				}			
			}

			$this->redirect('/dashboard/system/environment/storage','storage_saved');
		}
	}
	
}
