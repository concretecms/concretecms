<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/dashboard/base');

class DashboardSystemRegistrationProfilesController extends DashboardBaseController {

	public $helpers = array('form'); 
	
	public function __construct() { 
		$this->token = Loader::helper('validation/token');
		$html = Loader::helper('html');			

		$this->set('public_profiles',ENABLE_USER_PROFILES);
	}

	public function update_profiles() { 
		if ($this->isPost()) {
			Config::save('ENABLE_USER_PROFILES', ($this->post('public_profiles')?true:false));
			$message = ($this->post('public_profiles')?t('Public profiles have been enabled'):t('Public profiles have been disabled.'));
			$this->redirect('/dashboard/system/registration/profiles',$message);
		}
	}
	
	public function view($message = NULL) {
		if($message) {
			$this->set('message',$message);
		}
		$u = new User();
	}
}
?>