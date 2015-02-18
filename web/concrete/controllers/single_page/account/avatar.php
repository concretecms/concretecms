<?php
namespace Concrete\Controller\SinglePage\Account;
use \Concrete\Controller\SinglePage\Account\EditProfile as AccountProfileEditPageController;
use Loader;

class Avatar extends AccountProfileEditPageController {
	
	public function view() {
		parent::view();
		$this->requireAsset('javascript', 'swfobject');
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
            $image = \Image::load($thumb);
            $profile->updateUserAvatar($image);
        }

		$this->redirect('/account/avatar', 'saved');
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
		$this->redirect('/account/avatar', 'deleted');
	}

}


?>