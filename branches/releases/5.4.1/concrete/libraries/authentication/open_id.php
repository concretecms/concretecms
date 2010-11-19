<?php 
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
/**
 * A class that facilitates working with OpenID
 *
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class OpenIDAuth  {
		
		private $store;
		private $consumer;
		private $returnTo;
		private $errorMsg;
		public $redirect;
		
		private $response;
		
		const E_INVALID_OPENID = 1;
		const E_CONNECTION_ERROR = 2;
		const E_CANCEL = 3;
		const E_FAILURE = 4;
		const E_REGISTRATION_EMAIL_INCOMPLETE = 5;	
		const E_REGISTRATION_EMAIL_EXISTS = 6;
	
		/** 
		 * Successful authentication. New user created in system. uID is the message
		 */
		const S_USER_CREATED = 10;
		const S_USER_AUTHENTICATED = 11;
		
		public function getResponse() {
			return $this->response;
		}
		
		/** 
		 * Returns TRUE if open ID login is enabled for this site
		 */
		public static function isEnabled() {
			return ENABLE_OPENID_AUTHENTICATION;
		}
		
		public function __construct() {
			Loader::library('3rdparty/Auth/OpenID/Consumer');
			Loader::library('3rdparty/Auth/OpenID/FileStore');
			Loader::library('3rdparty/Auth/OpenID/SReg');
			Loader::library('3rdparty/Auth/OpenID/PAPE');
			
			$this->store = new Auth_OpenID_FileStore(DIR_FILES_CACHE . '/openid.store');
			$this->consumer = new Auth_OpenID_Consumer($this->store);			
			
			$this->response = new stdClass;
		}
		
		public function reinstatePreviousRequest() {
			$preq = unserialize($_SESSION['uOpenIDPreviousPostArray']);
			if (is_array($preq)) {
				foreach($preq as $key => $value) {
					$_POST[$key] = $value;
					$_REQUEST[$key] = $value;
				}
			}
		}
		
		public function getReturnURL() {
			return $this->returnTo;
		}
		
		public function setReturnURL($url) {
			$this->returnTo = $url;
		}
		
		public function complete() {
			$response = $this->consumer->complete($this->returnTo);
			if ($response->status == Auth_OpenID_CANCEL) {
				$this->response->code = OpenIDAuth::E_CANCEL;
				$this->response->message = $response->message;
			} else if ($response->status == Auth_OpenID_FAILURE) {
				$this->response->code = OpenIDAuth::E_FAILURE;
				$this->response->message = $response->message;
			} else if ($response->status == Auth_OpenID_SUCCESS) {
				$this->translate($response);
			}

		}
		
    	public function request($identifier) {
    		$_SESSION['uOpenIDPreviousPostArray'] = serialize($_POST);
    		$response = $this->consumer->begin($identifier);
			if (!$response) {
				$this->response->code = OpenIDAuth::E_INVALID_OPENID;
			} else {
				if (is_object($response->endpoint) && $response->endpoint->claimed_id != '') {
					$id = $response->endpoint->claimed_id;
				} else {
					$id = $identifier;
				}
				
				// now we have an identifier. If we ALREADY have this openid in our system, we don't setup our request 
				// to ask for the meta fields
				
				$ui = UserInfo::getByOpenID($id);
				
				if (!is_object($ui)) {
					$regRequest = Auth_OpenID_SRegRequest::build(
						// Required
						array('nickname'),
						// Optional
						array('fullname', 'email')
					);
	
					if ($regRequest) {
						$response->addExtension($regRequest);
					}
				}
				
				$this->redirect = $response->redirectURL(BASE_URL . DIR_REL, $this->getReturnURL());
		
				// If the redirect URL can't be built, display an error
				// message.
				if (Auth_OpenID::isFailure($this->redirect)) {
					$this->response->code = OpenIDAuth::E_CONNECTION_ERROR;
					$this->response->message = $this->redirect->message;
				} else {
					// Send redirect.
					header("Location: ".$this->redirect);
					exit;
				}        
			}		
    	}
		
		/** 
		 * Creates a uID suitable for storing in the uName column
		 * doesn't need to map directly because we're always quering the uOpenID column when signing in as open ID
		 */
    	private function createUserID($openID) {
    		$uOpenID = str_replace(array('http://', 'https://'), '', $openID);
    		$uOpenID = trim($uOpenID);
    		$uOpenID = trim($uOpenID, '/');
    		$uOpenID = strtolower($uOpenID);
    		
    		// now we check to see if the user already exists with this name. HIGHLY unlikely
			$v = Loader::helper('validation/identifier');
			$uOpenIDRet = $v->generateFromBase($uOpenID, 'Users', 'uName');
			return $uOpenIDRet;
		}
		
    	public function registerUser($openID, $email) {
    		$v = Loader::helper('validation/identifier');
    		$pass = $v->getString(10);
    		
    		$data['uName'] = $this->createUserID($openID);
    		$data['uPassword'] = $pass;
    		$data['uEmail'] = $email;
    		$data['uIsValidated'] = 1;
    		$ui = UserInfo::add($data);
    		
    		if (is_object($ui)) {
				$this->linkUser($openID, $ui);
				return $ui;
    		}    		
    	}
    	
    	public function linkUser($openID, $ui) {
			$db = Loader::db();
			$db->Execute('insert into UserOpenIDs (uOpenID, uID) values (?, ?)', array($openID, $ui->getUserID()));
    	}
    	
    	/** 
    	 * Translates the response from the open id library to our internal tools, taking care of checking whether the user
    	 * account needs to be created, etc...
    	 */
    	private function translate($response) {
			$openid = $response->getDisplayIdentifier();
        	$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
       		$sreg = $sreg_resp->contents();
       		$val = Loader::helper('validation/strings');
       		
       		$ui = UserInfo::getByOpenID($openid);
			
			// There are a number of cases here.
			// Case 1: There is NO user on the site here that matches this open ID.
			if (!is_object($ui)) {
				// Ok, no user. Now, did an email address come BACK with this request from the openid server?
				if ($val->email($sreg['email'])) {
					// if so, does it belong to an existing user on the site ?
					$ui = UserInfo::getByEmail($sreg['email']);
					if (is_object($ui)) {
						$this->response->code = OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS;
						$this->response->user = $ui->getUserID();
						$this->response->openid = $openid;
					} else {
						// best possible case, really: we are a new user with an email address that is not mapped to 
						// an existing account. We register the new account here, and pass back information to the calling page
						// saying that we've done so
						$ui = $this->registerUser($openid, $sreg['email']);	
						$this->response->code = OpenIDAuth::S_USER_CREATED;
						$this->response->message = $ui->getUserID();
					}
				} else {
					$this->response->code = OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE;
					$this->response->message = $openid;
				}
			} else {
				// Ok, there IS a user on the site who matches the open ID. That means we're all good
				$this->response->code = OpenIDAuth::S_USER_AUTHENTICATED;
				$this->response->message = $ui->getUserID();
				$this->response->openid = $openid;
			}
		}		
	}