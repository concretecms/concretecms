<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/profile/edit');

class Concrete5_Controller_Profile_Avatar extends ProfileEditController {
	
	public function __construct(){
		parent::__construct();
		$html = Loader::helper('html');
		$this->set('av', Loader::helper('concrete/avatar'));
		$this->addHeaderItem($html->javascript('swfobject.js'));
	}

	
	public function save_thumb(){
		$ui = $this->get('ui');
		if (!is_object($ui) || $ui->getUserID() < 1) {
			return false;
		}
		
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