<?php 
class LoginController extends Controller {
	
	public $helpers = array('form');
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
			$this->set('uNameLabel', 'Email Address');
		} else {
			$this->set('uNameLabel', 'Username');
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
				throw new Exception('An ' . strtolower($this->get('uNameLabel')) . ' and password are required.');
			}

			$u = new User($this->post('uName'), $this->post('uPassword'));
			if ($u->isError()) {
				switch($u->getError()) {
					case USER_INVALID:
						throw new Exception('Invalid ' . strtolower($this->get('uNameLabel')) . ' or password.');
						break;
					case USER_INACTIVE:
						throw new Exception('This user is inactive. Please contact us regarding this account.');
						break;
				}
			}

			if ($this->post('uMaintainLogin')) {
				$u->setUserForeverCookie();
			}

			//redirect to a page if specified
			$rcURL = trim($this->post('rcURL')); 
			if( strlen($rcURL)>0 ){
				$this->redirect( $rcURL );
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
		$this->set('intro_msg', 'An email containing your password has been sent to your account address.');
	}
	
	public function logout() {
		$u = new User();
		$u->logout();
		$this->redirect('/');
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
				throw new Exception('Invalid email address.');
			}
			
			$oUser = UserInfo::getByEmail($em);
			if (!$oUser) {
				throw new Exception('We have no record of that email address.');
			}			
			
			$mh = Loader::helper('mail');
			$mh->addParameter('uPassword', $oUser->resetUserPassword());
			$mh->addParameter('uName', $oUser->getUserName());			
			$mh->to($oUser->getUserEmail());
			$mh->load('forgot_password');
			$mh->sendMail();
			
			$this->redirect('/login', 'password_sent');

		} catch(Exception $e) {
			$this->error->add($e);
		}
	}
	
}
