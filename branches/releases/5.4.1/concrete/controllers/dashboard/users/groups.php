<?php 
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardUsersGroupsController extends Controller {


	public function view() { 
	
	}	
	
	public function delete($delGroupId, $token = ''){

		$u=new User();
		try {
		
			if(!$u->isSuperUser()) {
				throw new Exception(t('You do not have permission to perform this action.'));
			}
			
			$group = Group::getByID($delGroupId);			
			
			if(!($group instanceof Group)) {
				throw new Exception(t('Invalid group ID.'));
			}
			
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_group_' . $delGroupId, $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$group->delete(); 
			$resultMsg=t('Group deleted successfully.');		
			
			$_REQUEST=array();
			$_GET=array();
			$_POST=array();			
			$this->set('message', $resultMsg);
			$this->view(); 
		} catch(Exception $e) {
			$this->set('error', $e);
		}
	}	
}

?>