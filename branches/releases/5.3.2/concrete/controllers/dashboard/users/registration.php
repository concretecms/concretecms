<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardUsersRegistrationController extends Controller {

	var $helpers = array('form'); 
	
	public function __construct() {
		$this->set('enable_openID',ENABLE_OPENID_AUTHENTICATION);
		$this->set('public_profiles',ENABLE_USER_PROFILES);
		$this->set('email_as_username', USER_REGISTRATION_WITH_EMAIL_ADDRESS);
		$this->set('registration_type',REGISTRATION_TYPE);
	}
	
	public function update_registration_type() { 
		if ($this->isPost()) {
			Config::save('ENABLE_OPENID_AUTHENTICATION', ($this->post('enable_openID')?true:false));
			Config::save('USER_REGISTRATION_WITH_EMAIL_ADDRESS', ($this->post('email_as_username')?true:false));
			
			Config::save('REGISTRATION_TYPE',$this->post('registration_type'));
			switch($this->post('registration_type')) {
				case "enabled":
					Config::save('ENABLE_REGISTRATION', true);
					Config::save('USER_VALIDATE_EMAIL', false);	
					Config::save('USER_VALIDATE_EMAIL_REQUIRED', false);
				break;
				
				case "validate_email":
					Config::save('ENABLE_REGISTRATION', true);
					Config::save('USER_VALIDATE_EMAIL', true);	
					Config::save('USER_VALIDATE_EMAIL_REQUIRED', true);
				break;
				
				case "manual_approve":
					Config::save('ENABLE_REGISTRATION', true);
					Config::save('USER_REGISTRATION_APPROVAL_REQUIRED', true);
					Config::save('USER_VALIDATE_EMAIL', false);	
					Config::save('USER_VALIDATE_EMAIL_REQUIRED', false);
				break;
				
				default: // disabled
					Config::save('ENABLE_REGISTRATION', false);
				break;
			}
			Config::save('REGISTRATION_TYPE',$this->post('registration_type'));
			
			$this->redirect('/dashboard/users/registration',t('Registration settings have been saved.'));
		}
	}
	
	public function update_profiles() { 
		if ($this->isPost()) {
			Config::save('ENABLE_USER_PROFILES', ($this->post('public_profiles')?true:false));
			$message = ($this->post('public_profiles')?t('Public profiles have been enabled'):t('Public profiles have been disabled.'));
			$this->redirect('/dashboard/users/registration',$message);
		}
	}
	
	public function view($message = NULL) {
		if($message) {
			$this->set('message',$message);
		}
		$u = new User();
	}
}
	