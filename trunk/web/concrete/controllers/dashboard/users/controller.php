<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardUsersController extends Controller {


	public function view() { 
		
	}
	
	
	public function delete($delUserId){
		$u=new User();
		try {

			if(!$u->isSuperUser()) {
				throw new Exception(t('You do not have permission to perform this action.'));
			}

			if ($delUserId == USER_SUPER_ID) {
				throw new Exception(t('You may not remove the super user account.'));
			}			

			if($delUserId==$u->getUserID()) {
				throw new Exception(t('You cannot delete your own user account.'));
			}

			$delUI=UserInfo::getByID($delUserId); 
			
			if ($delUserId == GUEST_GROUP_ID || $delUserId == REGISTERED_GROUP_ID) {
				throw new Exception(t('Invalid user ID.'));
			}
			
			if(!($delUI instanceof UserInfo)) {
				throw new Exception(t('Invalid user ID.'));
			}

			$delUI->delete(); 
			$resultMsg=t('User deleted successfully.');
			
			$_REQUEST=array();
			$_GET=array();
			$_POST=array();		
			$this->set('message', $resultMsg);
			$this->view(); 
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
}

?>