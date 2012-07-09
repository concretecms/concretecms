<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Registration_PublicRegistration extends DashboardBaseController {

	public $helpers = array('form'); 
	
	public function __construct() { 
		$this->token = Loader::helper('validation/token');
		$html = Loader::helper('html');		
		$this->addHeaderItem($html->javascript('ccm.sitemap.js'));		
		
		$this->set('enable_openID',ENABLE_OPENID_AUTHENTICATION);
		$this->set('email_as_username', USER_REGISTRATION_WITH_EMAIL_ADDRESS);
		$this->set('registration_type',REGISTRATION_TYPE);
		$this->set('user_timezones',ENABLE_USER_TIMEZONES);	
		$this->set('enable_registration_captcha',ENABLE_REGISTRATION_CAPTCHA);
		$this->set('register_notification',REGISTER_NOTIFICATION);
		$this->set('register_notification_email',EMAIL_ADDRESS_REGISTER_NOTIFICATION);
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
					Config::save('REGISTER_NOTIFICATION', $this->post('register_notification'));
					Config::save('EMAIL_ADDRESS_REGISTER_NOTIFICATION', $this->post('register_notification_email'));
				break;
				
				case "validate_email":
					Config::save('ENABLE_REGISTRATION', true);
					Config::save('USER_VALIDATE_EMAIL', true);	
					Config::save('USER_VALIDATE_EMAIL_REQUIRED', true);
					Config::save('USER_REGISTRATION_APPROVAL_REQUIRED', false);
					Config::save('REGISTER_NOTIFICATION', $this->post('register_notification'));
					Config::save('EMAIL_ADDRESS_REGISTER_NOTIFICATION', $this->post('register_notification_email'));
				break;
				
				case "manual_approve":
					Config::save('ENABLE_REGISTRATION', true);
					Config::save('USER_REGISTRATION_APPROVAL_REQUIRED', true);
					Config::save('USER_VALIDATE_EMAIL', false);	
					Config::save('USER_VALIDATE_EMAIL_REQUIRED', false);
					Config::save('REGISTER_NOTIFICATION', $this->post('register_notification'));
					Config::save('EMAIL_ADDRESS_REGISTER_NOTIFICATION', $this->post('register_notification_email'));
				break;
				
				default: // disabled
					Config::save('ENABLE_REGISTRATION', false);
					Config::save('REGISTER_NOTIFICATION', false);
				break;
			}
			Config::save('REGISTRATION_TYPE',$this->post('registration_type'));
			
			$this->redirect('/dashboard/system/registration/public_registration',1);
		}
	}
	
	public function view($updated = false) {
		if($updated) {
			$this->set('message',t('Registration settings have been saved.'));
		}
	}

}