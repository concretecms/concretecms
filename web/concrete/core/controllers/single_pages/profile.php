<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Profile extends Controller {
	
	public $helpers = array('html', 'form', 'text'); 
	
	public function on_start(){
		$this->error = Loader::helper('validation/error');
		$this->addHeaderItem(Loader::helper('html')->css('ccm.profile.css'));
	}
	
	public function view($userID = 0) {
		if(!ENABLE_USER_PROFILES) {
			header("HTTP/1.0 404 Not Found");
			$this->render("/page_not_found");
		}
		
		$html = Loader::helper('html');
		$canEdit = false;
		$u = new User();

		if ($userID > 0) {
			$profile = UserInfo::getByID($userID);
			if (!is_object($profile)) {
				throw new Exception('Invalid User ID.');
			}
		} else if ($u->isRegistered()) {
			$profile = UserInfo::getByID($u->getUserID());
			$canEdit = true;
		} else {
			$this->set('intro_msg', t('You must sign in order to access this page!'));
			Loader::controller('/login');
			$this->render('/login');
		}
		$this->set('profile', $profile);
		$this->set('av', Loader::helper('concrete/avatar'));
		$this->set('t', Loader::helper('text'));
		$this->set('canEdit',$canEdit);
	}
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}	
	
	/*
	public function add_friend(){
		UsersFriends::addFriend( intval($_REQUEST['fuID']) );
		$this->view( intval($_REQUEST['fuID']) );
	}
	
	public function remove_friend(){
		UsersFriends::removeFriend( intval($_REQUEST['fuID']) );
		$this->view( intval($_REQUEST['fuID']) );
	}	
	*/
}