<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardFilesSetsController extends Controller {

	public $helpers = array('form','validation/token','concrete/interface'); 

	public function view() {
		Loader::model('file_set');
		$fsl = new FileSetList();
		if (isset($_REQUEST['fsKeywords'])) {
			$fsl->filterByKeywords($_REQUEST['fsKeywords']);
		}
		if (isset($_REQUEST['fsType'])) {
			$fsl->filterByType($_REQUEST['fsType']);
			$this->set('fsType', $_REQUEST['fsType']);
		} else {
			$fsl->filterByType(FileSet::TYPE_PUBLIC);
			$this->set('fsType', FileSet::TYPE_PUBLIC);
		}
		$fileSets = $fsl->getPage();
		$this->set('fileSets',$fileSets);
		$this->set('fsl', $fsl);
	}

	public function file_set_added() {
		$this->set('message', t('New file set added successfully.'));
		$this->view();
	}

	public function file_set_updated() {
		$this->set('message', t('File set updated successfully.'));
		$this->view();
	}
	
	public function file_set_deleted() {
		$this->set('message', t('File set deleted successfully.'));
		$this->view();
	}
	
	public function delete($fsID, $token = '') {

		$u=new User();
		Loader::model('file_set');
		$fs = FileSet::getByID($fsID);
		
			
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('delete_file_set', $token)) {
			throw new Exception($valt->getErrorMessage());
		}
			
		$fs->delete(); 
		$this->redirect('/dashboard/files/sets', 'file_set_deleted');			
	}
	
	public function view_detail($fsID, $action = false) {
		Loader::model('file_set');
		$fs = FileSet::getByID($fsID);
		$ph = Loader::controller('/dashboard/system/permissions/files');
		$this->set('ph', $ph);		
		$this->set('fs', $fs);		
		$this->view();		
	}		
	
	public function file_sets_edit(){
		extract($this->getHelperObjects());
		Loader::model('file_set');
		//do my editing
		if (!$validation_token->validate("file_sets_edit")) {			
			$this->set('error', array($validation_token->getErrorMessage()));
			$this->view();
			return;
		}
		
		if(!$this->post('fsID')){
			$this->set('error', array(t('Invalid ID')));
			$this->view();			
		}

		$file_set = new FileSet();
		$file_set->Load('fsID = ?', $this->post('fsID'));		
		$file_set->fsName = $this->post('file_set_name');
		$file_set->fsOverrideGlobalPermissions = ($this->post('fsOverrideGlobalPermissions') == 1) ? 1 : 0;
		$file_set->save();

		parse_str($this->post('fsDisplayOrder'));
		$file_set->updateFileSetDisplayOrder($fID);
		
		$file_set->resetPermissions();		
		if ($file_set->fsOverrideGlobalPermissions == 1) {
			$p = $this->post();
			$fh = Loader::controller('/dashboard/system/permissions/files');
			$fh->setFileSetPermissions($file_set, $p);			
		}
		
		$this->redirect("/dashboard/files/sets", 'file_set_updated');
	}
	
}

?>