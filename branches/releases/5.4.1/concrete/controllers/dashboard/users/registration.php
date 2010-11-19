<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardUsersRegistrationController extends Controller {

	var $helpers = array('form'); 
	
	public function __construct() { 
		$this->token = Loader::helper('validation/token');
		$html = Loader::helper('html');		
		$this->addHeaderItem($html->javascript('ccm.sitemap.js'));		
		
		$this->set('enable_openID',ENABLE_OPENID_AUTHENTICATION);
		$this->set('public_profiles',ENABLE_USER_PROFILES);
		$this->set('email_as_username', USER_REGISTRATION_WITH_EMAIL_ADDRESS);
		$this->set('registration_type',REGISTRATION_TYPE);
		$this->set('user_timezones',ENABLE_USER_TIMEZONES);	
		$this->set('enable_registration_captcha',ENABLE_REGISTRATION_CAPTCHA);
		
		//login redirection
		$this->set('site_login_redirect', Config::get('LOGIN_REDIRECT') );
		$this->set('login_redirect_cid', intval(Config::get('LOGIN_REDIRECT_CID')) ); 
		$adminToDash=Config::get('LOGIN_ADMIN_TO_DASHBOARD');
		$this->set('site_login_admin_to_dashboard', intval($adminToDash) );				
	}
	
	public function update_registration_type() { 
		if ($this->isPost()) {
			Config::save('ENABLE_OPENID_AUTHENTICATION', ($this->post('enable_openID')?true:false));
			Config::save('USER_REGISTRATION_WITH_EMAIL_ADDRESS', ($this->post('email_as_username')?true:false));
			
			Config::save('REGISTRATION_TYPE',$this->post('registration_type'));
			Config::save('ENABLE_REGISTRATION_CAPTCHA', ($this->post('enable_registration_captcha')) ? true : false);
			
			switch($this->post('registration_type')) {
				case "enabled":
					Config::save('ENABLE_REGISTRATION', true);
					Config::save('USER_VALIDATE_EMAIL', false);	
					Config::save('USER_VALIDATE_EMAIL_REQUIRED', false);
					Config::save('USER_REGISTRATION_APPROVAL_REQUIRED', false);
				break;
				
				case "validate_email":
					Config::save('ENABLE_REGISTRATION', true);
					Config::save('USER_VALIDATE_EMAIL', true);	
					Config::save('USER_VALIDATE_EMAIL_REQUIRED', true);
					Config::save('USER_REGISTRATION_APPROVAL_REQUIRED', false);
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
	
	public function update_login_redirect(){ 
		if ($this->token->validate("update_login_redirect")) {	
			if ($this->isPost()) {
				Config::save('LOGIN_REDIRECT', $this->post('LOGIN_REDIRECT'));
				Config::save('LOGIN_REDIRECT_CID', intval($this->post('LOGIN_REDIRECT_CID')) );
				Config::save('LOGIN_ADMIN_TO_DASHBOARD', intval($this->post('LOGIN_ADMIN_TO_DASHBOARD')) );
				
				$this->redirect( '/dashboard/users/registration', 'login_redirect_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}	
	}	
	
	public function update_user_timezones() { 
		if ($this->isPost()) {
			Config::save('ENABLE_USER_TIMEZONES', ($this->post('user_timezones')?true:false));
			$message = ($this->post('user_timezones')?t('User time zones have been enabled'):t('User time zones have been disabled.'));
			$this->redirect('/dashboard/users/registration',$message);
		}
	}

	
	
	public function view($message = NULL) {
		if($message) {
			if($message=='login_redirect_saved'){
				$this->set('message', t('Login redirection saved.'));
			}else{
				$this->set('message',$message);
			}
		}
		$u = new User();
	}
}
	