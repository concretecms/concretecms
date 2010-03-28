<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::controller('/profile/edit');

class ProfileAvatarController extends ProfileEditController {
	
	public function __construct(){
		parent::__construct();
		$html = Loader::helper('html');
		$this->set('av', Loader::helper('concrete/avatar'));
		$this->addHeaderItem($html->javascript('swfobject.js'));
	}

	
	public function upload() {

		if (is_uploaded_file($_FILES['uAvatar']['tmp_name'])) {
			$t = Loader::helper('validation/identifier');
			$filename = $t->getString(32);
			
			copy($_FILES['uAvatar']['tmp_name'], DIR_FILES_CACHE . "/" . $filename);

			print(BASE_URL.DIR_REL.'/files/cache/'. $filename);
			exit;
		}
	
	}
	
	public function save_thumb(){
		$ui = $this->get('ui');
		if(isset($_POST['thumbnail']) && strlen($_POST['thumbnail'])) {
			$thumb = base64_decode($_POST['thumbnail']);
			$fp = fopen(DIR_FILES_AVATARS."/".$ui->getUserID().".jpg","w");
			if($fp) {
				fwrite($fp,base64_decode($_POST['thumbnail']));
				fclose($fp);
				$data['uHasAvatar'] = 1;
				$ui->update($data);
			}
		}	
		// remove the uploaded tmp image
		if(strlen($_POST['tmp_avatar'])) {
			unlink(DIR_FILES_CACHE . '/' . $_POST['tmp_avatar']);
		}
		
		$this->redirect('/profile/avatar', 'saved');
	}
	
	public function saved() {
		$this->set('message', 'Avatar updated!');
	}

	public function deleted() {
		$this->set('message', 'Avatar removed.');
	}
	
	public function delete(){ 
		$ui = $this->get('ui');
		$av = $this->get('av');
		
		$av->removeAvatar($ui);
		$this->redirect('/profile/avatar', 'deleted');
	}

}


?>