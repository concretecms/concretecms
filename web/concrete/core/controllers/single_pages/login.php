<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('authentication/open_id');
class Concrete5_Controller_Login extends Controller {

	public $helpers = array('form');
	private $openIDReturnTo;
	protected $locales = array();
	protected $supportsPageCache = true;
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
			$this->set('uNameLabel', t('Email Address'));
		} else {
			$this->set('uNameLabel', t('Username'));
		}

		$txt = Loader::helper('text');
		if (strlen($_GET['uName'])) { // pre-populate the username if supplied, if its an email address with special characters the email needs to be urlencoded first,
			$this->set("uName",trim($txt->email($_GET['uName'])));
		}

		$languages = array();
		$locales = array();
		if (Config::get('LANGUAGE_CHOOSE_ON_LOGIN')) {
			Loader::library('3rdparty/Zend/Locale');
			Loader::library('3rdparty/Zend/Locale/Data');
			$languages = Localization::getAvailableInterfaceLanguages();
			if (count($languages) > 0) {
				array_unshift($languages, 'en_US');
			}
			$locales = array();
			Zend_Locale_Data::setCache(Cache::getLibrary());
			foreach($languages as $lang) {
				$loc = new Zend_Locale($lang);
				$locales[$lang] = Zend_Locale::getTranslation($loc->getLanguage(), 'language', $lang);
				$locRegion = $loc->getRegion();
				if($locRegion !== false) {
					$locRegionName = $loc->getTranslation($loc->getRegion(), 'country', $lang);
					if($locRegionName !== false) {
						$locales[$lang] .= ' (' . $locRegionName . ')';
					}
				}
			}
			asort($locales);
			$locales = array_merge(array('' => t('** Default')), $locales);
		}
		$this->locales = $locales;
		$this->set('locales', $locales);

		$this->openIDReturnTo = BASE_URL . View::url("/login", "complete_openid");
	}

	// automagically run by the controller once we're done with the current method
	// method is passed to this method, the method that we were just finished running
	public function on_before_render() {
		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
	}

	public function complete_openid_email() {
		$email = $this->post('uEmail');
		$vals = Loader::helper('validation/strings');
		$valc = Loader::helper('concrete/validation');
		if (!$vals->email($email)) {
			$this->error->add(t('Invalid email address provided.'));
		} elseif (!$valc->isUniqueEmail($email)) {
			$this->error->add(t("The email address %s is already in use. Please choose another.", $_POST['uEmail']));
		}

		if (!$this->error->has()) {
			// complete the openid record with the provided email
			if (isset($_SESSION['uOpenIDRequested'])) {
				$oa = new OpenIDAuth();
				$ui = $oa->registerUser($_SESSION['uOpenIDRequested'], $email);
				User::loginByUserID($ui->getUserID());
				$oa->reinstatePreviousRequest();
				$this->finishLogin();
			}
		}
	}

	public function view() {
		$this->clearOpenIDSession();
	}

	private function clearOpenIDSession() {
		unset($_SESSION['uOpenIDError']);
		unset($_SESSION['uOpenIDRequested']);
		unset($_SESSION['uOpenIDExistingUser']);
	}

	public function complete_openid() {
		$v = Loader::helper('validation/numbers');
		$oa = new OpenIDAuth();
		$oa->setReturnURL($this->openIDReturnTo);
		$oa->complete();
		$response = $oa->getResponse();
		if ($response->code == OpenIDAuth::E_CANCEL) {
			$this->error->add(t('OpenID Verification Cancelled'));
			$this->clearOpenIDSession();
		} elseif ($response->code == OpenIDAuth::E_FAILURE) {
			$this->error->add(t('OpenID Authentication Failed: %s', $response->message));
			$this->clearOpenIDSession();
		} else {
			switch($response->code) {
				case OpenIDAuth::S_USER_CREATED:
				case OpenIDAuth::S_USER_AUTHENTICATED:
					if ($v->integer($response->message)) {
						User::loginByUserID($response->message);
						$this->set('uOpenID', $response->openid);
						$oa->reinstatePreviousRequest();
						$this->finishLogin();
					}
					break;
				case OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE:
					// we don't have an email address, but the account is valid
					// valid display identifier comes back in message
					$_SESSION['uOpenIDRequested'] = $response->message;
					$_SESSION['uOpenIDError'] = OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE;
					break;
				case OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS:
					// an email address came back with us from the openid server
					// but that email already exists
					$_SESSION['uOpenIDRequested'] = $response->openid;
					$_SESSION['uOpenIDExistingUser'] = $response->user;
					$_SESSION['uOpenIDError'] = OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS;
					break;
			}
		}
		$this->set('oa', $oa);
	}

	public function account_deactivated() {
		$this->error->add(t('This user is inactive. Please contact us regarding this account.'));
	}

	public function do_login() {
		$ip = Loader::helper('validation/ip');
		$vs = Loader::helper('validation/strings');

		$loginData['success']=0;

		try {
			if(!$_COOKIE[SESSION]) {
				throw new Exception(t('Your browser\'s cookie functionality is turned off. Please turn it on.'));
			}
		
			if (!$ip->check()) {
				throw new Exception($ip->getErrorMessage());
			}
			if (OpenIDAuth::isEnabled() && $vs->notempty($this->post('uOpenID'))) {
				$oa = new OpenIDAuth();
				$oa->setReturnURL($this->openIDReturnTo);
				$return = $oa->request($this->post('uOpenID'));
				$resp = $oa->getResponse();
				if ($resp->code == OpenIDAuth::E_INVALID_OPENID) {
					throw new Exception(t('Invalid OpenID.'));
				}
			}

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
			} else {

				if (OpenIDAuth::isEnabled() && $_SESSION['uOpenIDExistingUser'] > 0) {
					$oa = new OpenIDAuth();
					if ($_SESSION['uOpenIDExistingUser'] == $u->getUserID()) {
						// the account we logged in with is the same as the existing user from the open id. that means
						// we link the account to open id and keep the user logged in.
						$oa->linkUser($_SESSION['uOpenIDRequested'], $u);
					} else {
						// The user HAS logged in. But the account they logged into is NOT the same as the one
						// that links to their OpenID. So we log them out and tell them so.
						$u->logout();
						throw new Exception(t('This account does not match the email address provided.'));
					}
				}

				$loginData['success']=1;
				$loginData['msg']=t('Login Successful');
				$loginData['uID'] = intval($u->getUserID());
			}

			$loginData = $this->finishLogin($loginData);

		} catch(Exception $e) {
			$ip->logSignupRequest();
			if ($ip->signupRequestThreshholdReached()) {
				$ip->createIPBan();
			}
			$this->error->add($e);
			$loginData['error']=$e->getMessage();
		}

		if ($_REQUEST['format']=='JSON') {
			$jsonHelper=Loader::helper('json');
			echo $jsonHelper->encode($loginData);
			die;
		}
	}

	protected function finishLogin( $loginData=array() ) {
		$u = new User();
		if ($this->post('uMaintainLogin')) {
			$u->setUserForeverCookie();
		}

		if (count($this->locales) > 0) {
			if (Config::get('LANGUAGE_CHOOSE_ON_LOGIN') && $this->post('USER_LOCALE') != '') {
				$u->setUserDefaultLanguage($this->post('USER_LOCALE'));
			}
		}

		// Verify that the user has filled out all
		// required items that are required on register
		// That means users logging in after new user attributes
		// have been created and required will be prompted here to
		// finish their profile

		$this->set('invalidRegistrationFields', false);
		Loader::model('attribute/categories/user');
		$ui = UserInfo::getByID($u->getUserID());
		$aks = UserAttributeKey::getRegistrationList();

		$unfilledAttributes = array();
		foreach($aks as $uak) {
			if ($uak->isAttributeKeyRequiredOnRegister()) {
				$av = $ui->getAttributeValueObject($uak);
				if (!is_object($av)) {
					$unfilledAttributes[] = $uak;
				}
			}
		}

		if ($this->post('completePartialProfile')) {
			foreach($unfilledAttributes as $uak) {
				$e1 = $uak->validateAttributeForm();
				if ($e1 == false) {
					$this->error->add(t('The field "%s" is required', tc('AttributeKeyName', $uak->getAttributeKeyName())));
				} elseif ($e1 instanceof ValidationErrorHelper) {
					$this->error->add($e1);
				}
			}

			if (!$this->error->has()) {
				// the user has needed to complete a partial profile, and they have done so,
				// and they have no errors. So we save our profile data against the account.
				foreach($unfilledAttributes as $uak) {
					$uak->saveAttributeForm($ui);
					$unfilledAttributes = array();
				}
			}
		}

		if (count($unfilledAttributes) > 0) {
			$u->logout();
			$this->set('invalidRegistrationFields', true);
			$this->set('unfilledAttributes', $unfilledAttributes);
		}
		$txt = Loader::helper('text');
		$rcID = $this->post('rcID');
		$nh = Loader::helper('validation/numbers');

		// set redirect url
		if ($nh->integer($rcID)) {
			$nh = Loader::helper('navigation');
			$rc = Page::getByID($rcID);
			$url = $nh->getLinkToCollection($rc, true);
			$loginData['redirectURL'] = $url;
		} elseif (strlen($rcID)) {
			$rcID = trim($rcID, '/');

			$nc2 = Page::getByPath('/' . $rcID);
			if (is_object($nc2) && !$nc2->isError()) {
				$loginData['redirectURL'] = BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/' . $rcID;
			}
		}

		/*
		//full page login redirect (non-ajax login)
		if( strlen($loginData['redirectURL']) && $_REQUEST['format']!='JSON' ) {
			header('Location: ' . $loginData['redirectURL']);
			exit;
		}
		*/

		$dash = Page::getByPath("/dashboard", "RECENT");
		$dbp = new Permissions($dash);

		Events::fire('on_user_login',$this);

		//End JSON Login
		if ($_REQUEST['format']=='JSON')
			return $loginData;

		//should administrator be redirected to dashboard?  defaults to yes if not set.
		$adminToDash=intval(Config::get('LOGIN_ADMIN_TO_DASHBOARD'));

		//Full page login, standard redirection
		$u = new User(); // added for the required registration attribute change above. We recalc the user and make sure they're still logged in
		if ($u->isRegistered()) {
			if ($u->config('NEWSFLOW_LAST_VIEWED') == 'FIRSTRUN') {
				$u->saveConfig('NEWSFLOW_LAST_VIEWED', 0);
			}

			if ($loginData['redirectURL']) {
				//make double secretly sure there's no caching going on
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Pragma: no-cache");
				header('Expires: Fri, 30 Oct 1998 14:19:41 GMT'); //in the past
				$this->externalRedirect( $loginData['redirectURL'] );
			} elseif ($dbp->canRead() && $adminToDash) {
				$this->redirect('/dashboard');
			} else {
				//options set in dashboard/users/registration
				$login_redirect_cid=intval(Config::get('LOGIN_REDIRECT_CID'));
				$login_redirect_mode=Config::get('LOGIN_REDIRECT');

				//redirect to user profile
				if ($login_redirect_mode=='PROFILE' && ENABLE_USER_PROFILES) {
					$this->redirect( '/profile/', $u->uID );

				//redirect to custom page
				} elseif ($login_redirect_mode=='CUSTOM' && $login_redirect_cid > 0) {
					$redirectTarget = Page::getByID( $login_redirect_cid );
					if (intval($redirectTarget->cID)>0) $this->redirect( $redirectTarget->getCollectionPath());
					else $this->redirect('/');

				//redirect home
				} else $this->redirect('/');
			}
		}
	}

	public function password_sent() {
		$this->set('intro_msg', $this->getPasswordSentMsg() );
	}

	public function getPasswordSentMsg() {
		return t('An email containing instructions on resetting your password has been sent to your account address.');
	}

	public function logout() {
		$u = new User();
		$u->logout();
		$this->redirect('/');
	}

	public function forward($cID = 0) {
		$nh = Loader::helper('validation/numbers');
		if ($nh->integer($cID)) {
			$this->set('rcID', $cID);
		}
	}

	// responsible for validating a user's email address
	public function v($hash = '') {
		$ui = UserInfo::getByValidationHash($hash);
		if (is_object($ui)) {
			$ui->markValidated();
			$this->set('uEmail', $ui->getUserEmail());
			$this->set('validated', true);
		}
	}

	public function change_password($uHash = '') {
		$db = Loader::db();
		$h = Loader::helper('validation/identifier');
		$e = Loader::helper('validation/error');
		$ui = UserInfo::getByValidationHash($uHash);
		if (is_object($ui)) {
			$hashCreated = $db->GetOne("select uDateGenerated FROM UserValidationHashes where uHash=?", array($uHash));
			if ($hashCreated < (time()-(USER_CHANGE_PASSWORD_URL_LIFETIME))) {
				$h->deleteKey('UserValidationHashes','uHash',$uHash);
				throw new Exception( t('Key Expired. Please visit the forgot password page again to have a new key generated.') );
			} else {

				if (strlen($_POST['uPassword'])) {

					$userHelper = Loader::helper('concrete/user');
					$userHelper->validNewPassword($_POST['uPassword'],$e);

					if (strlen($_POST['uPassword']) && $_POST['uPasswordConfirm']!=$_POST['uPassword']) {
						$e->add(t('The two passwords provided do not match.'));
					}

					if (!$e->has()) {
						$ui->changePassword( $_POST['uPassword'] );
						$h->deleteKey('UserValidationHashes','uHash',$uHash);
						$this->set('passwordChanged', true);

						$u = $ui->getUserObject();
						if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
							$_POST['uName'] =  $ui->getUserEmail();
						} else {
							$_POST['uName'] =  $u->getUserName();
						}
						$this->do_login();

						return;
					} else { // This else is always used (due to return above), no need for else statement.
						$this->set('uHash', $uHash);
						$this->set('changePasswordForm', true);
						$this->set('errorMsg', join( '<br>', $e->getList() ) );
					}
				} else {
					$this->set('uHash', $uHash);
					$this->set('changePasswordForm', true);
				}
			}
		} else {
			throw new Exception( t('Invalid Key. Please visit the forgot password page again to have a new key generated.') );
		}
	}

	public function forgot_password() {
		$loginData['success']=0;

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
			$changePassURL=BASE_URL . View::url('/login', 'change_password', $uHash);
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

			$loginData['success']=1;
			$loginData['msg']=$this->getPasswordSentMsg();

		} catch (Exception $e) {
			$this->error->add($e);
			$loginData['error']=$e->getMessage();
		}

		if ($_REQUEST['format']=='JSON') {
			$jsonHelper=Loader::helper('json');
			echo $jsonHelper->encode($loginData);
			die;
		}

		if ($loginData['success']==1) {
			$this->redirect('/login', 'password_sent');
		}
	}

}
