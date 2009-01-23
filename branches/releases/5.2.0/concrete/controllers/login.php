<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class LoginController extends Controller {
	
	public $helpers = array('form');
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
			$this->set('uNameLabel', t('Email Address'));
		} else {
			$this->set('uNameLabel', t('Username'));
		}
		if(strlen($_GET['uName'])) { // pre-populate the username if supplied
			$this->set("uName",$_GET['uName']);
		}
	}
	
	/* automagically run by the controller once we're done with the current method */
	/* method is passed to this method, the method that we were just finished running */
	public function on_before_render() {
		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
	}
	
	public function do_login() { 
	
		$vs = Loader::helper('validation/strings');
		try {
			if ((!$vs->notempty($this->post('uName'))) || (!$vs->notempty($this->post('uPassword')))) {
				if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
					throw new Exception(t('An email address and password are required.'));
				} else {
					throw new Exception(t('A username and password are required.'));
				}
			}

			$u = new User($this->post('uName'), $this->post('uPassword'));
			if ($u->isError()) {
				switch($u->getError()) {
					case USER_INVALID:
						if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
							throw new Exception(t('Invalid email address or password.'));
						} else {
							throw new Exception(t('Invalid username or password.'));						
						}
						break;
					case USER_INACTIVE:
						throw new Exception(t('This user is inactive. Please contact us regarding this account.'));
						break;
				}
			}

			if ($this->post('uMaintainLogin')) {
				$u->setUserForeverCookie();
			}
			
			$rcID = $this->post('rcID');
			$nh = Loader::helper('validation/numbers');
			if ($nh->integer($rcID)) {
				header('Location: ' . BASE_URL . DIR_REL . '/index.php?cID=' . $rcID);
				exit;
			}

			$dash = Page::getByPath("/dashboard", "RECENT");
			$dbp = new Permissions($dash);
			if ($dbp->canRead()) {
				$this->redirect('/dashboard');
			} else {
				$this->redirect('/');
			}
			
			
		} catch(Exception $e) {
			$this->error->add($e);
		}
	}
	
	public function password_sent() {
		$this->set('intro_msg', t('An email containing your password has been sent to your account address.'));
	}
	
	public function logout() {
		$u = new User();
		$u->logout();
		$this->redirect('/');
	}
	
	public function forward($cID) {
		$this->set('rcID', $cID);
	}
	
	// responsible for validating a user's email address
	public function v($hash) {
		$ui = UserInfo::getByValidationHash($hash);
		if (is_object($ui)) {
			$ui->markValidated();
			$this->set('uEmail', $ui->getUserEmail());
			$this->set('validated', true);
		}
	}
	
	public function forgot_password() {
		$vs = Loader::helper('validation/strings');
		$em = $this->post('uEmail');
		try {
			if (!$vs->email($em)) {
				throw new Exception(t('Invalid email address.'));
			}
			
			$oUser = UserInfo::getByEmail($em);
			if (!$oUser) {
				throw new Exception(t('We have no record of that email address.'));
			}			
			
			$mh = Loader::helper('mail');
			$mh->addParameter('uPassword', $oUser->resetUserPassword());
			$mh->addParameter('uName', $oUser->getUserName());			
			$mh->to($oUser->getUserEmail());
			if (defined('EMAIL_ADDRESS_FORGOT_PASSWORD')) {
				$mh->from(EMAIL_ADDRESS_FORGOT_PASSWORD,  t('Forgot Password'));
			} else {
				$adminUser = UserInfo::getByID(USER_SUPER_ID);
				if (is_object($adminUser)) {
					$mh->from($adminUser->getUserEmail(),  t('Forgot Password'));
				} else {
					$mh->from('info@concrete5.org', t('Forgot Password'));
				}
			}
			$mh->load('forgot_password');
			$mh->sendMail();
			
			$this->redirect('/login', 'password_sent');

		} catch(Exception $e) {
			$this->error->add($e);
		}
	}
	
}
