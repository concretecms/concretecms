<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * A helper that allows the creation of nonces/tokens, to protect against CSRF attacks.
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
class ValidationTokenHelper {

	const VALID_HASH_TIME_THRESHOLD = 86400; // 24 hours (in seconds)
	
	/** 
	 * For localization we can't just store this as a constant, unfortunately
	 */
	public function getErrorMessage() {
		return t("Invalid form token. Please reload this form and submit again.");
	}
	
	/**
	 * Generates a unique token for a given action. This is a token in the form of
	 * time:hash, where hash is md5(time:userID:action:salt)
	 * @param string table
	 * @param string key
	 * @param int length
	 */
	public function generate($action = '', $time = null) {
		$u = new User();
		$uID = $u->getUserID();
		if (!$uID) {
			$uID = 0;
		}
		if ($time == null) {
			$time = time();
		}
		$hash = $time . ':' . md5($time . ':' . $uID . ':' . $action . ':' . PASSWORD_SALT);
		return $hash;
	}
	
	/** 
	 * prints out a generated token as a hidden form field
	 */
	public function output($action = '', $return = false) {
		$hash = $this->generate($action);
		$token = '<input type="hidden" name="ccm_token" value="' . $hash . '" />';
		if (!$return) {
			print $token;
		} else {
			return $token;
		}
	}
	
	/** 
	 * returns a generated token as a query string variable
	 */
	public function getParameter($action = '') {
		$hash = $this->generate($action);
		return 'ccm_token=' . $hash;
	}
	
	
	
	/** 
	 * Validates against a given action. Basically, we check the passed hash to see if
	 * a. the hash is valid. That means it computes in the time:action:PASSWORD_SALT format
	 * b. the time included next to the hash is within the threshold.
	 * @param string $action
	 * @param string $token
	 */
	 public function validate($action = '', $token = null) {
		if ($token == null) {
			$token = $_REQUEST['ccm_token'];
		}
		$parts = explode(':', $token);
		if ($parts[0]) {
			$time = $parts[0];
			$hash = $parts[1];
			$compHash = $this->generate($action, $time);
			$now = time();
			
			if (substr($compHash, strpos($compHash, ':') + 1) == $hash) {
				$diff = $now - $time;
				//hash is only valid if $diff is less than VALID_HASH_TIME_RECORD
				return $diff <= ValidationTokenHelper::VALID_HASH_TIME_THRESHOLD;
			}
		}
	 	return false;
	 }
	 
	

}