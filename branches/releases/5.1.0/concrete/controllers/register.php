<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class RegisterController extends Controller {

	public $helpers = array('form', 'html');
	
	public function __construct() {
		parent::__construct();
		Loader::model('user_attributes');

		$u = new User();
		$this->set('u', $u);
		
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
			$this->set('displayUserName', false);
		} else {
			$this->set('displayUserName', true);
		}
	}
	
	public function forward($cID) {
		$this->set('rcID', $cID);
	}
	
	public function do_register() {
		
		$e = Loader::helper('validation/error');
		$txt = Loader::helper('text');
		$vals = Loader::helper('validation/strings');
		$valc = Loader::helper('concrete/validation');

		$username = $_POST['uName'];
		$password = $_POST['uPassword'];
		$passwordConfirm = $_POST['uPasswordConfirm'];
		
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
			$_POST['uName'] = $_POST['uEmail'];
		}
		
		if (!$vals->email($_POST['uEmail'])) {
			$e->add(t('Invalid email address provided.'));
		} else if (!$valc->isUniqueEmail($_POST['uEmail'])) {
			$e->add(t("The email address %s is already in use. Please choose another.", $_POST['uEmail']));
		}
		
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
			if (strlen($username) < USER_USERNAME_MINIMUM) {
				$e->add(t('A username must be between at least %s characters long.', USER_USERNAME_MINIMUM));
			}
	
			if (strlen($username) > USER_USERNAME_MAXIMUM) {
				$e->add(t('A username cannot be more than %s characters long.', USER_USERNAME_MAXIMUM));
			}
	
			if (strlen($username) >= USER_USERNAME_MINIMUM && !$vals->alphanum($username)) {
				$e->add(t('A username may only contain letters or numbers.'));
			}
			if (!$valc->isUniqueUsername($username)) {
				$e->add(t("The username %s already exists. Please choose another", $username));
			}		
		}
		
		if ($username == USER_SUPER) {
			$e->add(t('Invalid Username'));
		}
		
		if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
			$e->add(t('A password must be between %s and %s characters', USER_PASSWORD_MINIMUM, USER_PASSWORD_MAXIMUM));
		}
			
		if (strlen($password) >= USER_PASSWORD_MINIMUM && !$vals->password($password)) {
			$e->add(t('A password may not contain ", \', >, <, or any spaces.'));
		}

		if ($password) {
			if ($password != $passwordConfirm) {
				$e->add(t('The two passwords provided do not match.'));
			}
		}
	
		$invalidFields = UserAttributeKey::validateSubmittedRequest();
		foreach($invalidFields as $field) {
			$e->add(t("The field %s is required.", $field));
		}

		if (!$e->has()) {
			
			// do the registration
			$data = $_POST;
			$data['uName'] = $username;
			$data['uPassword'] = $password;
			$data['uPasswordConfirm'] = $passwordConfirm;

			$process = UserInfo::register($data);
			if (is_object($process)) {

				$process->updateUserAttributes($data);
				
				// now we log the user in

				$u = new User($_POST['uName'], $_POST['uPassword']);
				// if this is successful, uID is loaded into session for this user
				
				$rcID = $this->post('rcID');
				$nh = Loader::helper('validation/numbers');
				if (!$nh->integer($rcID)) {
					$rcID = 0;
				}
				
				// now we check whether we need to validate this user's email address
				if (defined("USER_VALIDATE_EMAIL")) {
					if (USER_VALIDATE_EMAIL > 0) {
						$ui = UserInfo::getByID($u->getUserID());
						$uHash = $ui->setupValidation();
						
						$mh = Loader::helper('mail');
						$mh->addParameter('uEmail', $_POST['uEmail']);
						$mh->addParameter('uHash', $uHash);
						$mh->to($_POST['uEmail']);
						$mh->load('validate_user_email');
						$mh->sendMail();

						$this->redirect('/register', 'register_success_validate', $rcID);																
					}
				}
				
				if (!$u->isError()) {
					$this->redirect('/register', 'register_success', $rcID);																
				}
				
			}
		} else {
			$this->set('error', $e);
		}
		
	}
	
	public function register_success_validate($rcID = 0) {
		$this->set('rcID', $rcID);
		$this->set('validate', true);
	}
	
	public function register_success($rcID = 0) {
		$this->set('rcID', $rcID);
		$this->set('registered', true);
	}

}

?>