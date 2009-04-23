<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('user_attributes');
class ProfileEditController extends Controller {

	var $helpers = array('html', 'form');
	
	public function __construct() {
		$html = Loader::helper('html');
		parent::__construct();
		$u = new User();
		if (!$u->isRegistered()) {
			$this->set('intro_msg', t('You must sign in order to access this page!'));
			$this->render('/login');
		}
		$this->set('ui', UserInfo::getByID($u->getUserID()));
		$this->set('av', Loader::helper('concrete/avatar'));
	}

	public function save() { 
		$ui = $this->get('ui');

		$uh = Loader::helper('concrete/user');
		$th = Loader::helper('text');
		$vsh = Loader::helper('validation/strings');
		$cvh = Loader::helper('concrete/validation');
		$e = Loader::helper('validation/error');
	
		$data = $this->post();
		
		/* 
		 * Validation
		*/
		
		// validate the user's attributes
		$invalidFields = UserAttributeKey::validateSubmittedRequest();
		foreach($invalidFields as $field) {
			$e->add(t("The field %s is required.", $field));
		}
		
		// validate the user's email
		$email = $this->post('uEmail');
		if (!$vsh->email($email)) {
			$e->add(t('Invalid email address provided.'));
		} else if (!$cvh->isUniqueEmail($email) && $ui->getUserEmail() != $email) {
			$e->add(t("The email address '%s' is already in use. Please choose another.",$email));
		}

		// password
		if(strlen($data['uPasswordNew'])) {
			$passwordNew = $data['uPasswordNew'];
			$passwordNewConfirm = $data['uPasswordNewConfirm'];
			
			if ((strlen($passwordNew) < USER_PASSWORD_MINIMUM) || (strlen($passwordNew) > USER_PASSWORD_MAXIMUM)) {
				$e->add(t('A password must be between %s and %s characters', USER_PASSWORD_MINIMUM, USER_PASSWORD_MAXIMUM));
			}		
			
			if (strlen($passwordNew) >= USER_PASSWORD_MINIMUM && !$cvh->password($passwordNew)) {
				$e->add(t('A password may not contain ", \', >, <, or any spaces.'));
			}
			
			if ($passwordNew) {
				if ($passwordNew != $passwordNewConfirm) {
					$e->add(t('The two passwords provided do not match.'));
				}
			}
			$data['uPasswordConfirm'] = $passwordNew;
			$data['uPassword'] = $passwordNew;
		}		

		if (!$e->has()) {		
			$data['uEmail'] = $email;		
			
			$ui->update($data);
			$ui->updateUserAttributes($data);
		
			$this->set('message', t('Profile Information Saved.'));
		} else {
			$this->set('error', $e);
		}
	}
}

?>