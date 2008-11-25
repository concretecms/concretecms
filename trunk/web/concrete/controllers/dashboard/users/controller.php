<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardUsersController extends Controller {


	public function view() { 
		
	}
	
	
	public function delete(){
		$u=new User();
		$delUserId=intval($_REQUEST['uID']);
		if(!$delUserId) 
			throw new Exception(t('No user id was specified.'));		
		if(!$u->isSuperUser()) 
			throw new Exception(t('You do not have permission to perform this action.'));
		if($delUserId==$u->getUserID()) 
			throw new Exception(t('You cannot delete your own user account.'));			
		$delUI=UserInfo::getByID($delUserId); 
		if($delUI){ 
			$delUI->delete(); 
			$resultMsg=t('User deleted successfully.');
		}else{
			$resultMsg=t('User not found.');
		}
		$_REQUEST=array();
		$_GET=array();
		$_POST=array();		
		$this->set('message', $resultMsg);
		$this->view(); 
	}
}

?>