<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_AuthenticationType_Concrete extends AuthenticationTypeController {
	public $apiMethods = array('forgot_password', 'change_password');

	public function authenticate() {
		$post = $this->post();

		if (!isset($post['uName']) || !isset($post['uPassword'])) {
			throw new Exception('Please provide both username and password.');
		}
		$uName = $post['uName'];
		$uPassword = $post['uPassword'];

		$user = new User($uName,$uPassword);
		if (!is_object($user) || !($user instanceof User) || $user->isError()) {
			switch($user->getError()) {
				case USER_NON_VALIDATED:
					throw new Exception(t('This account has not yet been validated. Please check the email associated with this account and follow the link it contains.'));
					break;
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
		if ($post['uMaintainLogin']) {
			$user->setAuthTypeCookie('concrete');
		}

		$this->completeAuthentication($user);

	}

	public function deauthenticate(User $u) {
		list($uID, $authType, $hash) = explode(':', $_COOKIE['ccmAuthUserHash']);
		if ($authType == 'concrete') {
			$db = Loader::db();
			$db->execute('DELETE FROM authTypeConcreteCookieMap WHERE uID=? AND token=?',array($uID,$hash));
		}
	}

	public function verifyHash(User $u, $hash) {
		$uID = $u->getUserID();
		$db = Loader::db();
		$q = $db->getOne('SELECT validThrough FROM authTypeConcreteCookieMap WHERE uID=? AND token=?',array($uID,$hash));
		$bool = time() < $q;
		if (!$bool) {
			$db->execute('DELETE FROM authTypeConcreteCookieMap WHERE uID=? AND token=?',array($uID,$hash));
		} else {
			$newTime = strtotime('+2 weeks');
			$db->execute('UPDATE authTypeConcreteCookieMap SET validThrough=?',array($newTime));
		}
		return $bool;
	}

	private function genString($a=20) {
		$o = '';
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}|":<>?\'\\';
		$l = strlen($chars);
		while ($a--) {
			$o .= substr($chars, rand(0,$l), 1);
		}
		return md5($o);
	}

	public function view() {}

	public function buildHash(User $u,$test=1) {
		if ($test>10) {
			// This should only ever happen if by some stroke of divine intervention,
			// we end up pulling 10 hashes that already exist. the chances of this are very very low.
			throw new exception('There was a database error, try again.');
		}
		$db = Loader::db();

		$validThrough = strtotime('+2 weeks');
		$token = $this->genString();
		try {
			$db->execute('INSERT INTO authTypeConcreteCookieMap (token, uID, validThrough) VALUES (?,?,?)',array($token, $u->getUserID(), $validThrough));
		} catch (exception $e) {
			// HOLY CRAP.. SERIOUSLY?
			$this->buildHash($u,$test++);
		}
		return $token;
	}

	public function isAuthenticated(User $u) {
		return ($u->isLoggedIn());
	}

	public function saveAuthenticationType($values) {}
	
	
	public function forgot_password() {
		$loginData['success']=0;
		$error = Loader::helper('validation/error');
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
			//$mh->addParameter('uPassword', $oUser->resetUserPassword());
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
				$mh->addParameter('uName', $oUser->getUserEmail());
			} else {
				$mh->addParameter('uName', $oUser->getUserName());
			}
			$mh->to($oUser->getUserEmail());

			//generate hash that'll be used to authenticate user, allowing them to change their password
			$h = Loader::helper('validation/identifier');
			$uHash = $h->generate('UserValidationHashes', 'uHash');
			$db = Loader::db();
			$db->Execute("DELETE FROM UserValidationHashes WHERE uID=?", array( $oUser->uID ) );
			$db->Execute("insert into UserValidationHashes (uID, uHash, uDateGenerated, type) values (?, ?, ?, ?)", array($oUser->uID, $uHash, time(),intval(UVTYPE_CHANGE_PASSWORD)));
			$changePassURL = BASE_URL . View::url('/login', 'callback', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'change_password', $uHash);
			
			$mh->addParameter('changePassURL', $changePassURL);

			if (defined('EMAIL_ADDRESS_FORGOT_PASSWORD')) {
				$mh->from(EMAIL_ADDRESS_FORGOT_PASSWORD,  t('Forgot Password'));
			} else {
				$adminUser = UserInfo::getByID(USER_SUPER_ID);
				if (is_object($adminUser)) {
					$mh->from($adminUser->getUserEmail(),  t('Forgot Password'));
				}
			}
			$mh->load('forgot_password');
			@$mh->sendMail();

		} catch(Exception $e) {
			$error->add($e);
		}

		if(!$error->has()) {
			$this->redirect('/login', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'password_sent');
		} else {
			$this->set('authType', $this->getAuthenticationType());
			$this->set('authTypeElement', 'forgot_password');
		}
	}
	
	public function change_password($uHash = '') {
		$db = Loader::db();
		$h = Loader::helper('validation/identifier');
		$e = Loader::helper('validation/error');
		$ui = UserInfo::getByValidationHash($uHash);
		if (is_object($ui)){
			$hashCreated = $db->GetOne("select uDateGenerated FROM UserValidationHashes where uHash=?", array($uHash));
			if($hashCreated < (time()-(USER_CHANGE_PASSWORD_URL_LIFETIME))) {
				$h->deleteKey('UserValidationHashes','uHash',$uHash);
				throw new Exception( t('Key Expired. Please visit the forgot password page again to have a new key generated.') );
			}else{

				if(strlen($_POST['uPassword'])){

					$userHelper = Loader::helper('concrete/user');
					$userHelper->validNewPassword($_POST['uPassword'],$e);

					if(strlen($_POST['uPassword']) && $_POST['uPasswordConfirm']!=$_POST['uPassword']){
						$e->add(t('The two passwords provided do not match.'));
					}

					if (!$e->has()){
						$ui->changePassword( $_POST['uPassword'] );
						$h->deleteKey('UserValidationHashes','uHash',$uHash);
						$this->set('passwordChanged', true);

						$u = $ui->getUserObject();
						if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
							$_POST['uName'] =  $ui->getUserEmail();
						} else {
							$_POST['uName'] =  $u->getUserName();
						}
						
						$this->authenticate();
						return;
					}else{
						$this->set('uHash', $uHash);
						$this->set('authType', $this->getAuthenticationType());
						$this->set('authTypeElement', 'change_password');
						$this->set('errorMsg', join( '<br>', $e->getList() ) );
						$this->set('error', $e);
					}
				}else{
					$this->set('uHash', $uHash);
					$this->set('authType', $this->getAuthenticationType());
					$this->set('authTypeElement', 'change_password');
				}
			}
		}else{
			throw new Exception( t('Invalid Key. Please visit the forgot password page again to have a new key generated.') );
		}
	}
	
}
