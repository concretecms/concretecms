	<?php defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('3rdparty/Zend/OpenId/Consumer');
Loader::library('3rdparty/Zend/OpenId/Extension/sreg');
class Concrete5_Controller_AuthenticationType_Openid extends AuthenticationTypeController {

	public $apiMethods = array('post_form','hook_form','hook_callback');

	public function install() {
		UserAttributeKey::add('text', array('akHandle' => 'openid_email', 'akName' => t('OpenID Email'), 'akIsSearchable' => false));
		UserAttributeKey::add('text', array('akHandle' => 'openid_identity', 'akName' => t('OpenID Identity'), 'akIsSearchable' => false));
	}

	/**
	 * Display methods.
	 */

	public function view() {
		$this->set('controller',$this);
	}

	public function hook() {
		$this->view();
		$this->set('action',View::url('/account/profile/edit','callback','openid','hook_form'));
	}

	/**
	 * Authentication methods.
	 */
	public function getSReg() {
		return new Zend_OpenId_Extension_Sreg(array(
			'email'=>true,
			'nickname'=>false,
			'fullname'=>false), null, 1.1);
	}

	public function hook_form() {
		if (!isset($_POST['identifier'])) {
			$this->redirect('/login');
		}
		$consumer = new Zend_OpenId_Consumer();
		$callback = View::url('/account/profile/edit','callback','openid','hook_callback');
		if (!$consumer->login($_POST['identifier'],$callback,BASE_URL,$this->getSReg())) {
			throw new Exception(t('Invalid OpenID Identity.'));
		}
	}

	public function post_form() {
		if (!isset($_POST['identifier'])) {
			$this->redirect('/login');
		}
		$consumer = new Zend_OpenId_Consumer();
		if (!$consumer->login($_POST['identifier'],$this->getUrl('callback'),BASE_URL,$this->getSReg())) {
			throw new Exception(t('Invalid OpenID Identity.'));
		}
	}

	public function getUser($email,$identity) {
		$users = new UserList;
		$users->filterByOpenidEmail($email);
		$users->filterByOpenidIdentity($identity);
		$us = $users->get();
		return array_shift($us);
	}

	public function handle_valid_request($email,$identity) {
		$user = $this->getUser($email,$identity);
		if (!$user) {
			throw new Exception(t('No user exists with the given information, edit your profile to connect.'));
		} else {
			User::loginByUserID($user->getUserID());
			$this->finish_auth();
		}
	}

	public function finish_auth() {
		$lc = Loader::controller('/login');
		$lc->authenticate('openid');
		exit;
	}

	public function connect(User $u, $email, $identity) {
		$ui = UserInfo::getByID($u->getUserID());
		$ui->setAttribute('openid_email',$email);
		$ui->setAttribute('openid_identity',$identity);

	}

	public function hook_callback() {
		if (isset($_GET['openid_mode'])) {
			if ($_GET['openid_mode'] == "id_res") {
				$consumer = new Zend_OpenId_Consumer();
				$sreg = $this->getSReg();
				if ($consumer->verify($_GET, $id, $sreg)) {
					$properties = $sreg->getProperties();
					$u = new User;
					if (is_object($u) && !$u->isError() && $u->isLoggedIn()) {
						$this->connect($u,$properties->email,$_GET['openid_identity']);
					}
				} else {
					throw new Exception(t('Invalid OpenID Request.'));
				}
			} else if ($_GET['openid_mode'] == "cancel") {
				throw new Exception(t('User cancelled request.'));
			}
		}
		throw new Exception(t('Something went wrong.'));
	}

	public function callback() {
		if (isset($_GET['openid_mode'])) {
			if ($_GET['openid_mode'] == "id_res") {
				$consumer = new Zend_OpenId_Consumer();
				$sreg = $this->getSReg();
				if ($consumer->verify($_GET, $id, $sreg)) {
					$properties = $sreg->getProperties();
					$this->handle_valid_request($properties->email,$_GET['openid_identity']);
				} else {
					throw new Exception(t('Invalid OpenID Request.'));
				}
			} else if ($_GET['openid_mode'] == "cancel") {
				throw new Exception(t('User cancelled request.'));
			}
		}
		throw new Exception(t('Something went wrong.'));
	}

	/**
	 * Core authentication methods.
	 */
	public function isAuthenticated(User $u) {
		return false;
	}
	public function buildHash(User $u) {
		return 1;
	}
	public function verifyHash(User $u, $hash) {
		return false;
	}
	public function authenticate() {
		// Nothing here, our login is asynchronous (kinda)
	}
	public function deauthenticate(User $u) {
		// Nothing here, we need not do anything to log the user out.
	}

}