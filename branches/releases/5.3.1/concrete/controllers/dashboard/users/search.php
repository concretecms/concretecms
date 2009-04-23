<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardUsersSearchController extends Controller {


	public function sign_in_as_user($uID, $token = null) {
		try {
			$u = new User();
			
			if(!$u->isSuperUser()) {
				throw new Exception(t('You do not have permission to perform this action.'));
			}
	
			$ui = UserInfo::getByID($uID); 
			
			if(!($ui instanceof UserInfo)) {
				throw new Exception(t('Invalid user ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('sudo', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			User::loginByUserID($uID);
			$this->redirect('/');
			
		} catch(Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function delete($delUserId, $token = null){
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
			
			if(!($delUI instanceof UserInfo)) {
				throw new Exception(t('Invalid user ID.'));
			}

			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_account', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$delUI->delete(); 
			$resultMsg=t('User deleted successfully.');
			
			$_REQUEST=array();
			$_GET=array();
			$_POST=array();		
			$this->set('message', $resultMsg);
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
}

?>