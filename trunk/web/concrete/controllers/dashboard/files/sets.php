<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardFilesSetsController extends Controller {

	var $helpers = array('form','validation/token','concrete/interface'); 

	public function on_start(){

	}
	
	public function view() {
		Loader::model('file_set');
		$file_set = new FileSet();
		
		$file_sets = $file_set->Find('fsType = ' . FileSet::TYPE_PUBLIC);		
		$this->set('file_sets',$file_sets);
		$this->set('action',$this->post('file-sets-edit-or-delete-action'));
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
		$file_set->fsID		= 'null';
		$file_set->fsName 	= $this->post('file_set_name');
		$file_set->fsType 	= FileSet::TYPE_PUBLIC;
		$file_set->uID		= $u->getUserID();
		$file_set->save();
		
		$this->view();
	}
	
	public function file_sets_edit_or_delete(){
		extract($this->getHelperObjects());
		
		if (!$validation_token->validate("file_sets_edit_or_delete")) {			
			$this->set('error', array($validation_token->getErrorMessage()));
			$this->view();
			return;
		}
		
		if(!$this->post('fsID')){
			$this->set('error', array(t('Invalid ID')));
			$this->view();			
		}
		
		
		switch($this->post('file-sets-edit-or-delete-action')){
			case 'edit-form':
				$this->exportFileSet($this->post('fsID'));
				break;
			case 'delete':
				$this->deleteFileSet($this->post('fsID'));
				$this->set('message',t('Set Deleted'));
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
		$file_set->save();
		
		$this->set('message',t('Changes Saved'));
		$this->view();
	}
	
	protected function exportFileSet($id){
		Loader::model('file_set');	
		$file_set = new FileSet();
		$file_set->Load('fsID = ?',array($id));
		$this->set('file_set',$file_set);
	}
	
	protected function deleteFileSet($id){
		Loader::model('file_set');	
		$utility = new FileSet();
		$file_sets = $utility->Find('fsID = ?',array($id));
		$file_set = $file_sets[0];
		$file_set->delete();
	}
}

?>