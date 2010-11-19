<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardFilesSetsController extends Controller {

	var $helpers = array('form','validation/token','concrete/interface'); 

	public function on_start(){
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('ccm.filemanager.css'));
		$this->addHeaderItem($html->javascript('ccm.filemanager.js'));
	}
	
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

	public function file_sets_add(){
		extract($this->getHelperObjects());
		Loader::model('file_set');
		
		if (!$validation_token->validate("file_sets_add")) {
			$this->set('error', array($validation_token->getErrorMessage()));
			$this->view();
			return;
		}
		
		if (!$this->post('file_set_name')) {
			$this->set('error', array(t('Please Enter a Name')));
			$this->view();
			return;		
		}
		
		//print('<pre>');print_r(get_included_files());print('</pre>');
		$u = new User();				
		$file_set 			= new FileSet();
		//AS: Adodb Active record is complaining a ?/value array mismatch unless
		//we explicatly set the primary key ID field to null		
		$file_set->fsID		= null;
		$file_set->fsName 	= $this->post('file_set_name');
		$file_set->fsType 	= FileSet::TYPE_PUBLIC;
		$file_set->uID		= $u->getUserID();
		$file_set->fsOverrideGlobalPermissions = ($this->post('fsOverrideGlobalPermissions') == 1) ? 1 : 0;
		$file_set->save();
		$this->redirect('/dashboard/files/sets', 'file_set_added');		
	}
	
	public function file_set_added() {
		$this->set('message', t('New file set added successfully.'));
		$this->view();
	}
	
	public function file_set_deleted() {
		$this->set('message', t('File set deleted successfully.'));
		$this->view();
	}
	
	public function save_sort_order() {
		Loader::model('file_set');
		parse_str($this->post('fsDisplayOrder'));
		$fsID = $this->post('fsID');
		$fs = FileSet::getByID($this->post('fsID'));
		$fs->updateFileSetDisplayOrder($fID);
		$this->redirect('/dashboard/files/sets', 'view_detail', $fsID, 'file_set_order_saved');
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
		$ph = Loader::controller('/dashboard/files/access');
		$this->set('ph', $ph);		
		$this->set('fs', $fs);		
		switch($action) {
			case 'file_set_order_saved':
				$this->set('message', t('File set order updated.'));
				break;
		}
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
		
		$file_set->resetPermissions();		
		if ($file_set->fsOverrideGlobalPermissions == 1) {
			$p = $this->post();
			$fh = Loader::controller('/dashboard/files/access');
			$fh->setFileSetPermissions($file_set, $p);			
		}
		
		$this->set('message',t('Changes Saved'));
		$this->view();
	}
	
}

?>