<?
Loader::model('user_attributes');
class ProfileController extends Controller {
	
	var $helpers = array('html', 'form'); 
	
	public function on_start(){
		$this->error = Loader::helper('validation/error');
	}
	
	public function view($userID = 0) {
		$html = Loader::helper('html');
	
		$u = new User();
		
		if ($userID > 0) {
			$profile = UserInfo::getByID($userID);
			if (!is_object($profile)) {
				throw new Exception('Invalid User ID.');
			}
		} else if ($u->isRegistered()) {
			$profile = UserInfo::getByID($u->getUserID());
		} else {
			$this->set('intro_msg', t('You must sign in order to access this page!'));
			$this->render('/login');
		}
		$this->set('profile', $profile);
		$this->set('av', Loader::helper('concrete/avatar'));
		$this->set('t', Loader::helper('text'));
	}
	

	public function on_before_render() {
		$this->set('error', $this->error);
	}	
}