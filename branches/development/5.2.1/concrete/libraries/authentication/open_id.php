<?
defined('C5_EXECUTE') or die(_("Access Denied."));

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
		
		private $error;
		
		const E_INVALID_OPENID = 1;
		const E_CONNECTION_ERROR = 2;
		const E_CANCEL = 3;
		const E_FAILURE = 4;
		
		public function isError() {
			return $this->error != false;
		}
		
		public function getError() {
			return $this->error;
		}
		
		public function getErrorMessage() {
			return $this->errorMsg;
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
				$this->error = OpenIDAuth::E_CANCEL;
				$this->errorMsg = $response->message;
			} else if ($response->status == Auth_OpenID_FAILURE) {
				$this->error = OpenIDAuth::E_FAILURE;
				$this->errorMsg = $response->message;
			} else if ($response->status == Auth_OpenID_SUCCESS) {
				$this->translate($response);
			}

		}
		
    	public function request($identifier) {
    		$response = $this->consumer->begin($identifier);
			if (!$response) {
				$this->error = OpenIDAuth::E_INVALID_OPENID;
			} else {
				$regRequest = Auth_OpenID_SRegRequest::build(
					// Required
					array('nickname'),
					// Optional
					array('fullname', 'email')
				);

				if ($regRequest) {
					$response->addExtension($regRequest);
				}
				
				$this->redirect = $response->redirectURL(BASE_URL . DIR_REL, $this->getReturnURL());
		
				// If the redirect URL can't be built, display an error
				// message.
				if (Auth_OpenID::isFailure($this->redirect)) {
					$this->error = OpenIDAuth::E_CONNECTION_ERROR;
					$this->errorMsg = $this->redirect->message;
				} else {
					// Send redirect.
					header("Location: ".$this->redirect);
					exit;
				}        
			}		
    	}
    	
    	/** 
    	 * Translates the response from the open id library to our internal tools, taking care of checking whether the user
    	 * account needs to be created, etc...
    	 */
    	private function translate($response) {
			$openid = $response->getDisplayIdentifier();
        	$id = htmlentities($openid);  
        	print $id;
        	$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
       		$sreg = $sreg_resp->contents();

			// so it gets complicated here. Step one, do we have an email address?
			if ($sreg['email'] != '') {
				// ok, we do. now, is it in use on the site already?
				$ui = UserInfo::getByEmail($sreg['email']);
				if (!is_object($ui)) {
					return false;
				}
			}			

		}		
	}