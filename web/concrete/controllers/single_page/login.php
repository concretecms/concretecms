<?php
namespace Concrete\Controller\SinglePage;
use PageController;
use Loader;
use Localization;
use \Concrete\Core\Authentication\AuthenticationType;
use \Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Config;
use User;
use Events;
use Page;
use Permissions;
use UserInfo;
use UserAttributeKey;
use Session;

class Login extends PageController {

	public $helpers = array('form');
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
			$languages = Localization::getAvailableInterfaceLanguages();
			if (count($languages) > 0) {
				array_unshift($languages, 'en_US');
			}
			$locales = array();
			\Zend_Locale_Data::setCache(Cache::getLibrary());
			foreach($languages as $lang) {
				$loc = new \Zend_Locale($lang);
				$locales[$lang] = \Zend_Locale::getTranslation($loc->getLanguage(), 'language', $lang);
				$locRegion = $loc->getRegion();
				if($locRegion !== false) {
					$locRegionName = $loc->getTranslation($loc->getRegion(), 'country', $lang);
					if($locRegionName !== false) {
						$locales[$lang] .= ' (' . $locRegionName . ')';
					}
				}
			}
			asort($locales);
			$locales = array_merge(array('' => tc('Default locale', '** Default')), $locales);
		}
		$this->locales = $locales;
		$this->set('locales', $locales);

	}

	/* automagically run by the controller once we're done with the current method */
	/* method is passed to this method, the method that we were just finished running */
	public function on_before_render() {
		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
	}

	public function view($type = NULL, $element = 'form') {
		if(strlen($type)) {
			$at = AuthenticationType::getByHandle($type);
			$this->set('authType', $at);
			$this->set('authTypeElement', $element);
		}
	}

	public function account_deactivated() {
		$this->error->add(t('This user is inactive. Please contact us regarding this account.'));
	}


	/**
	 * Concrete5_Controller_Login::callback
	 * Call an AuthenticationTypeController method throw a uri.
	 * Use: /login/TYPE/METHOD/PARAM1/.../PARAMn
	 *
	 * @param $type		AuthenticationTypeHandle
	 * @param $method	Method to be ran, defaults to "callback"
	 */
	public function callback($type,$method='callback') {
		$at = AuthenticationType::getByHandle($type);
		if (!method_exists($at->controller, $method)) {
			throw new \Exception(t('Invalid method.'));
		}
		if ($method != 'callback') {
			if (!is_array($at->controller->apiMethods) || !in_array($method,$at->controller->apiMethods)) {
				throw new \Exception(t("Invalid method."));
			}
		}
		try {
			$params = func_get_args();
			if (count($params) > 2) {
				array_shift($params);
				array_shift($params);
				$message = call_user_method_array($method, $at->controller,$params);
			} else {
				$message = call_user_method($method, $at->controller);
			}

			if (trim($message)) {
				$this->set('message',$message);
			}
		} catch (\exception $e) {
			if ($e instanceof AuthenticationTypeFailureException) {
				// Throw again if this is a big`n
				throw $e;
			}
			$this->error->add($e->getMessage());
		}
	}

	/**
	 * Concrete5_Controller_Login::authenticate
	 * Authenticate the user using a specific authentication type.
	 *
	 * @param $type	AuthenticationType handle
	 */
	public function authenticate($type) {
		try {
			$at = AuthenticationType::getByHandle($type);
			$at->controller->authenticate();
			$this->finishAuthentication($at);
		} catch (\exception $e) {
			$this->error->add($e->getMessage());
		}
		$this->view();
	}

	public function finishAuthentication(AuthenticationType $type) {
		$db = Loader::db();
		$u = new User();
		if ($u->getUserID() == 1 && $type->getAuthenticationTypeHandle() != 'concrete') {
			$u->logout();
			throw new \Exception(t('You can only identify as the root user using the concrete login.'));
		}

		$ui = UserInfo::getByID($u->getUserID());
		$aks = UserAttributeKey::getRegistrationList();

		$unfilled = array_values(array_filter($aks, function($ak) use ($ui) {
			return $ak->isAttributeKeyRequiredOnRegister() && !is_object($ui->getAttributeValueObject($ak));
		}));

		if (count($unfilled)) {
			$u->logout(false);

			if (!$this->error) {
				$this->on_start();
			}


			$this->set('required_attributes', $unfilled);
			$this->set('u', $u);
			$this->error->add(t('Fill in these required settings in order to continue.'));
		
			Session::set('uRequiredAttributeUser', $u->getUserID());
			Session::set('uRequiredAttributeUserAuthenticationType', $type->getAuthenticationTypeHandle());

			$this->render('/login');
		}

		$u->setLastAuthType($type);

		$ue = new \Concrete\Core\User\Event\User($u);
		Events::dispatch('on_user_login', $ue);

		$this->chooseRedirect();
	}

	public function fill_attributes() {
		try {
			if (!Session::has('uRequiredAttributeUser') ||
		    intval(Session::get('uRequiredAttributeUser')) < 1 ||
		    Session::has('uRequiredAttributeUserAuthenticationType') ||
		    !Session::get('uRequiredAttributeUserAuthenticationType')) {
		    	Session::remove('uRequiredAttributeUser');
		    	Session::remove('uRequiredAttributeUserAuthenticationType');
				throw new \Exception(t('Invalid Request, please attempt login again.'));
			}
			User::loginByUserID(Session::get('uRequiredAttributeUser'));
		    Session::remove('uRequiredAttributeUser');
			$u = new User;
			$at = AuthenticationType::getByHandle(Session::get('uRequiredAttributeUserAuthenticationType'));
		    Session::remove('uRequiredAttributeUserAuthenticationType');
			if (!$at) throw new \Exception(t("Invalid Authentication Type"));

			$ui = UserInfo::getByID($u->getUserID());
			$aks = UserAttributeKey::getRegistrationList();

			$unfilled = array_values(array_filter($aks, function($ak) use ($ui) {
				return $ak->isAttributeKeyRequiredOnRegister() && !is_object($ui->getAttributeValueObject($ak));
			}));

			foreach ($unfilled as $attribute) {
				$err = $attribute->validateAttributeForm();
				if ($err == false) {
					$this->error->add(t('The field "%s" is required', $attribute->getAttributeKeyDisplayName()));
				} elseif ($err instanceof \Concrete\Core\Error\Error) {
					$this->error->add($err);
				} else {
					$attribute->saveAttributeForm($ui);
				}
			}

			$this->finishAuthentication($at);
		} catch (\Exception $e) {
			$this->error->add($e->getMessage());
			$this->render('/login');
		}
	}

	public function chooseRedirect() {
		if (!$this->error) {
			$this->error = Loader::helper('validation/error');
		}
		
		$nh = Loader::helper('validation/numbers');
		$navigation = Loader::helper('navigation');
		$rUrl = false;

		$u = new User(); // added for the required registration attribute change above. We recalc the user and make sure they're still logged in
		if ($u->isRegistered()) {
			if ($u->config('NEWSFLOW_LAST_VIEWED') == 'FIRSTRUN') {
				$u->saveConfig('NEWSFLOW_LAST_VIEWED', 0);
			}
			do {
				// redirect to original destination
				if(Session::has('rcID')) {
					$rcID = Session::get('rcID');
					if ($nh->integer($rcID)) {
						$rc = Page::getByID($rcID);
					} elseif (strlen($rcID)) {
						$rcID = trim($rcID, '/');
						$rc = Page::getByPath('/' . $rcID);
					}
					if ($rc instanceof Page && !$rc->isError()) {
						$rUrl = $navigation->getLinkToCollection($rc);
						break;
					}		
				}
				
				// admin to dashboard?
				$dash = Page::getByPath("/dashboard", "RECENT");
				$dbp = new Permissions($dash);
				//should administrator be redirected to dashboard?  defaults to yes if not set.
				$adminToDash=intval(Config::get('LOGIN_ADMIN_TO_DASHBOARD'));
				if ($dbp->canRead() && $adminToDash) {
					$rUrl = $navigation->getLinkToCollection($rc);
					break;
				}
				
				//options set in dashboard/users/registration
				$login_redirect_mode=Config::get('LOGIN_REDIRECT');
				
				//redirect to user profile
				if ($login_redirect_mode=='PROFILE' && ENABLE_USER_PROFILES) {
					$rUrl = View::url('/profile',$u->getUserID());
					break;
				} 
				
				//redirect to custom page
				$login_redirect_cid = intval(Config::get('LOGIN_REDIRECT_CID'));
				if ($login_redirect_mode == 'CUSTOM' && $login_redirect_cid > 0) {
					$rc = Page::getByID($login_redirect_cid);
					if ($rc instanceof Page && !$rc->isError()) {
						$rUrl = $navigation->getLinkToCollection($rc);
						break;
					}
				}
				
				break;
			} while(false);
			
			if($rUrl) {
				$this->redirect($rUrl);
			} else {
				$this->redirect('/');
			}
		} else {
			$this->error->add(t('User is not registered. Check your authentication controller.'));
			$u->logout();
		}
	}

	public function logout($token = false) {
		if (Loader::helper('validation/token')->validate('logout', $token)) {
			$u = new User();
			$u->logout();
			$this->redirect('/');
		}
	}

	public function forward($cID = 0) {
		$nh = Loader::helper('validation/numbers');
		if ($nh->integer($cID) && intval($cID) > 0) {
			$this->set('rcID', intval($cID));
			Session::set('rcID', intval($cID));
		}
	}

	/* @TODO this functionality needs to be ported to the concrete5 auth type
	// responsible for validating a user's email address
	public function v($hash = '') {
		$ui = UserInfo::getByValidationHash($hash);
		if (is_object($ui)) {
			$ui->markValidated();
			$this->set('uEmail', $ui->getUserEmail());
			$this->set('validated', true);
		}
	}

	*/

}
