<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Files_Sets extends Controller {
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
		
		$fsp = new Permissions($fs);
		if ($fsp->canDeleteFileSet()) { 
			$fs->delete(); 
			$this->redirect('/dashboard/files/sets', 'file_set_deleted');
		} else {
			throw new Exception(t('You do not have permission to delete this file set.'));
		}
	}
	
	public function view_detail($fsID, $action = false) {
		Loader::model('file_set');
		$fs = FileSet::getByID($fsID);
		$ph = Loader::controller('/dashboard/system/permissions/files');
		$this->set('ph', $ph);		
		$this->set('fs', $fs);	
		if ($action == 'file_set_updated') {
			$this->set('message', t('File set updated successfully.'));
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
			return;
		}
		$setName = trim($this->post('file_set_name'));
		if (!$setName) {
			$this->set('error', array(t('Please Enter a Name')));
			$this->view();
			return;
		}
		if (preg_match('/[<>;{}?"`]/i', $setName)) {
			$this->set('error', array(t('File Set Name cannot contain the characters: %s', Loader::helper('text')->entities('<>;{}?"`'))));
			$this->view();
			return;
		}

		$file_set = new FileSet();
		$file_set->Load('fsID = ?', $this->post('fsID'));		
		$file_set->fsName = $setName;
		$copyPermissionsFromBase = false;
		if ($file_set->fsOverrideGlobalPermissions == 0 && $this->post('fsOverrideGlobalPermissions') == 1) {
			// we are checking the checkbox for the first time
			$copyPermissionsFromBase = true;
		}		
		if ($file_set->fsOverrideGlobalPermissions) {
			$permissions = PermissionKey::getList('file_set');
			foreach($permissions as $pk) {
				$pk->setPermissionObject($file_set);
				$pt = $pk->getPermissionAssignmentObject();
				$paID = $_POST['pkID'][$pk->getPermissionKeyID()];
				$pt->clearPermissionAssignment();
				if ($paID > 0) {
					$pa = PermissionAccess::getByID($paID, $pk);
					if (is_object($pa)) {
						$pt->assignPermissionAccess($pa);
					}			
				}		
			}			
		}
		$file_set->fsOverrideGlobalPermissions = ($this->post('fsOverrideGlobalPermissions') == 1) ? 1 : 0;
		$file_set->save();
		
		parse_str($this->post('fsDisplayOrder'));
		$file_set->updateFileSetDisplayOrder($fID);

		if ($file_set->fsOverrideGlobalPermissions == 0) {
			$file_set->resetPermissions();		
		} 		
		if ($copyPermissionsFromBase) {
			$file_set->acquireBaseFileSetPermissions();
		}

		$this->redirect("/dashboard/files/sets", 'view_detail', $this->post('fsID'), 'file_set_updated');
	}
	
}

?>
