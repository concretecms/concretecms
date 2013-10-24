<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_Account_Profile_Avatar extends AccountProfileEditPageController {
	
	public function view() {
		parent::view();
		$this->requireAsset('swfobject');
	}

	public function on_start() {
		parent::on_start();
		$this->set('av', Loader::helper('concrete/avatar'));
	}
			
	public function save_thumb(){
		$this->view();
		$profile = $this->get('profile');
		if (!is_object($profile) || $profile->getUserID() < 1) {
			return false;
		}
		
		if(isset($_POST['thumbnail']) && strlen($_POST['thumbnail'])) {
			$thumb = base64_decode($_POST['thumbnail']);
			$fp = fopen(DIR_FILES_AVATARS."/".$profile->getUserID().".jpg","w");
			if($fp) {
				fwrite($fp,base64_decode($_POST['thumbnail']));
				fclose($fp);
				$data['uHasAvatar'] = 1;
				$profile->update($data);
			}
		}	

		$this->redirect('/account/profile/avatar', 'saved');
	}
	
	public function saved() {
		$this->set('success', 'Avatar updated!');
		$this->view();
	}

	public function deleted() {
		$this->set('success', 'Avatar removed.');
		$this->view();
	}
	
	public function delete(){ 
		$this->view();
		$profile = $this->get('profile');
		$av = $this->get('av');
		
		$av->removeAvatar($profile);
		$this->redirect('/account/profile/avatar', 'deleted');
	}

}


?>