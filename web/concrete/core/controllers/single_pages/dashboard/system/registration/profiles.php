<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Registration_Profiles extends DashboardBaseController {

	public $helpers = array('form'); 
	
	public function __construct() { 
		$this->token = Loader::helper('validation/token');
		$html = Loader::helper('html');			

		$this->set('public_profiles',ENABLE_USER_PROFILES);
		$this->set('gravatar_fallback', Config::get('GRAVATAR_FALLBACK'));
		$this->set('gravatar_max_level', Config::get('GRAVATAR_MAX_LEVEL'));
		$this->set('gravatar_level_options', array('g' => 'G', 'pg' => 'PG', 'r' => 'R', 'x' => 'X'));
		$this->set('gravatar_image_set', Config::get('GRAVATAR_IMAGE_SET'));
		$this->set('gravatar_set_options', array('404' => '404', 'mm' => 'mm', 'identicon' => 'identicon', 'monsterid' => 'monsterid', 'wavatar' => "wavatar"));

	}

	public function update_profiles() { 
		if ($this->isPost()) {
			Config::save('ENABLE_USER_PROFILES', ($this->post('public_profiles')?true:false));
			Config::save('GRAVATAR_FALLBACK', ($this->post('gravatar_fallback')?true:false));
			Config::save('GRAVATAR_MAX_LEVEL', $this->post('gravatar_max_level'));
			Config::save('GRAVATAR_IMAGE_SET', $this->post('gravatar_image_set'));
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