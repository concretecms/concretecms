<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\File\Set\SetList as FileSetList;
use FileSet;
use Permissions;
use PermissionKey;
use PermissionAccess;
use Loader;
use Exception;

class Sets extends DashboardPageController {
	public $helpers = array('form','validation/token','concrete/ui'); 

	public function view() {
		
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
		
		$fs = FileSet::getByID($fsID);
		$this->set('fs', $fs);	
		if ($action == 'file_set_updated') {
			$this->set('message', t('File set updated successfully.'));
		}
		$this->view();		
	}		
	
	public function file_sets_edit(){
		extract($this->getHelperObjects());
		
		//do my editing
		if (!$validation_token->validate("file_sets_edit")) {			
			$this->error->add($validation_token->getErrorMessage());
		}
		
		if(!$this->post('fsID')){
			$this->error->add(t('Invalid ID'));
		}
		$setName = trim($this->post('file_set_name'));
		if (!$setName) {
			$this->error->add(t('Please Enter a Name'));
		}
		if (preg_match('/[<>;{}?"`]/i', $setName)) {
			$this->error->add(t('File Set Name cannot contain the characters: %s', Loader::helper('text')->entities('<>;{}?"`')));
		}

        if (!$this->error->has()) {
            $file_set = FileSet::getByID($this->post('fsID'));

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

            $fsOverrideGlobalPermissions = ($this->post('fsOverrideGlobalPermissions') == 1) ? 1 : 0;
            $file_set->update($setName, $fsOverrideGlobalPermissions);
            $file_set->updateFileSetDisplayOrder($this->post('fsDisplayOrder'));

            if ($file_set->fsOverrideGlobalPermissions == 0) {
                $file_set->resetPermissions();
            }
            if ($copyPermissionsFromBase) {
                $file_set->acquireBaseFileSetPermissions();
            }

            $this->redirect("/dashboard/files/sets", 'view_detail', $this->post('fsID'), 'file_set_updated');
    	} else {
            $this->view();
        }
    }
	
}

?>
