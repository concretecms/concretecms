<?

class RegisterController extends Controller {

	public $helpers = array('form', 'html');
	
	public function __construct() {
		parent::__construct();
		Loader::model('user_attributes');
	}
	
	public function view() {
		$u = new User();
		$this->set('u', $u);
		
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
			$this->set('displayUserName', false);
		} else {
			$this->set('displayUserName', true);
		}
	}
	
	public function do_register() {
		
		$e = Loader::helper('validation/error');
		$txt = Loader::helper('text');
		$vals = Loader::helper('validation/strings');
		$valc = Loader::helper('concrete/validation');

		$username = $txt->sanitize($_POST['uName']);
		$password = $txt->sanitize($_POST['uPassword']);
		$passwordConfirm = $txt->sanitize($_POST['uPasswordConfirm']);
		
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
			$_POST['uName'] = $_POST['uEmail'];
		}
		
		if (!$vals->email($_POST['uEmail'])) {
			$e->add('Invalid email address provided.');
		} else if (!$valc->isUniqueEmail($_POST['uEmail'])) {
			$e->add("The email address '{$_POST['uEmail']}' is already in use. Please choose another.");
		}
		
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
			if (strlen($username) < USER_USERNAME_MINIMUM) {
				$e->add('A username must be between at least ' . USER_USERNAME_MINIMUM . ' characters long.');
			}
	
			if (strlen($username) > USER_USERNAME_MAXIMUM) {
				$e->add('A username cannot be more than ' . USER_USERNAME_MAXIMUM . ' characters long.');
			}
	
			if (strlen($username) >= USER_USERNAME_MINIMUM && !$vals->alphanum($username)) {
				$e->add('A username may only contain letters or numbers.');
			}
			if (!$valc->isUniqueUsername($username)) {
				$e->add("The username '{$username}' already exists. Please choose another");
			}		
		}
		
		if ($username == USER_SUPER) {
			$e->add('Invalid Username');
		}
		
		if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
			$e->add('A password must be between ' . USER_PASSWORD_MINIMUM . ' and ' . USER_PASSWORD_MAXIMUM . ' characters');
		}
			
		if (strlen($password) >= USER_PASSWORD_MINIMUM && !$vals->alphanum($password)) {
			$e->add('A password may only contain letters or numbers.');
		}

		if ($password) {
			if ($password != $passwordConfirm) {
				$e->add('The two passwords provided do not match.');
			}
		}
	
		$invalidFields = UserAttributeKey::validateSubmittedRequest();
		foreach($invalidFields as $field) {
			$e->add("The field '{$field}' is required.");
		}

		if (!$e->has()) {
			
			// do the registration
			$process = UserInfo::register($_POST);
			if (is_object($process)) {
				// now we log the user in

				$u = new User($_POST['uName'], $_POST['uPassword']);
				// if this is successful, uID is loaded into session for this user
				
				
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
						$this->redirect('/register', 'register_success_validate');																
					}
				}
				
				if (!$u->isError()) {
					$this->redirect('/register', 'register_success');																
				}
				
			}
		} else {
			$this->set('error', $e);
		}
		
		$this->view();
	}
	
	public function register_success_validate() {
		$this->set('validate', true);
	}
	
	public function register_success() {
		$this->set('registered', true);
	}

}

?>