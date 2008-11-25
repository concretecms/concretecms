<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardGroupsController extends Controller {


	public function view() { 
	
	}	
	
	public function delete(){
		$u=new User();
		$delGroupId=intval($_REQUEST['gID']);
		if(!$u->isSuperUser()) 
			throw new Exception(t('You do not have permission to perform this action.'));
		if(!$delGroupId) 
			throw new Exception(t('No group id was specified.'));				
		$group = Group::getByID( intval($_REQUEST['gID']) );
		if($group){	
			$group->delete(); 
			$resultMsg=t('Group deleted successfully.');		
		}else{
			$resultMsg=t('Group not found.');
		}
		$_REQUEST=array();
		$_GET=array();
		$_POST=array();			
		$this->set('message', $resultMsg);
		$this->view(); 
	}	
}

?>