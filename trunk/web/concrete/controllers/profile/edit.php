<?
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
		$this->set("editPage", 1);
	}

	public function save() { 
		$ui = $this->get('ui');

		$uh = Loader::helper('concrete/user');
		$th = Loader::helper('text');
		$vsh = Loader::helper('validation/strings');
		$cvh = Loader::helper('concrete/validation');
	
		$username = $this->post('uName');
		$email = $this->post('uEmail');
		
		/* 
		 * Validation
		 */
		$e = Loader::helper('validation/error');
		if (strlen($username) < USER_USERNAME_MINIMUM) {
			$e->add('A username must be between at least ' . USER_USERNAME_MINIMUM . ' characters long.');
		}

		if (strlen($username) > USER_USERNAME_MAXIMUM) {
			$e->add('A username cannot be more than ' . USER_USERNAME_MAXIMUM . ' characters long.');
		}

		if (strlen($username) >= USER_USERNAME_MINIMUM && !$vsh->alphanum($username)) {
			$e->add('A username may only contain letters or numbers.');
		}
		if (!$cvh->isUniqueUsername($username) && $ui->getUserName() != $username) {
			$e->add("The username '{$username}' already exists. Please choose another");
		}		
		
		if (!$vsh->email($email)) {
			$e->add('Invalid email address provided.');
		} else if (!$cvh->isUniqueEmail($email) && $ui->getUserEmail() != $email) {
			$e->add("The email address '{$email}' is already in use. Please choose another.");
		}

		if (!$e->has()) {
		
			$data['uName'] = $username;
			$data['uEmail'] = $email;
			$ui->update($data);
			
			$ui->setAttribute('first_name', $th->sanitize($this->post('uFirstName'), 64));
			$ui->setAttribute('last_name', $th->sanitize($this->post('uLastName'), 128));
			$ui->setAttribute('sites', $th->sanitize($this->post('uC5Sites')));
			$ui->setAttribute('bio', $th->sanitize($this->post('uBio')));
			$ui->setAttribute('url', $th->sanitize($this->post('uURL'), 255));
		
			$this->set('message', 'Profile Information Saved.');
		} else {
			$this->set('error', $e);
		}
	}
}

?>