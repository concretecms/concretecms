<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for validating users in Concrete
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
	defined('C5_EXECUTE') or die("Access Denied.");
	class ConcreteValidationHelper {
	
		/** 
		 * Checks whether a passed username is unique or if a user of this name already exists
		 * @param string $uName
		 * @return bool
		 */
		function isUniqueUsername($uName) {
			$db = Loader::db();
			$q = "select uID from Users where uName = ?";
			$r = $db->getOne($q, array($uName));
			if ($r) {
				return false;
			} else {
				return true;
			}
		}


		/**
		 * Checks whether a passed email address is unique
		 * @return bool
		 * @param string $uEmail
		 */
		function isUniqueEmail($uEmail) {
			$db = Loader::db();
			$q = "select uID from Users where uEmail = ?";
			$r = $db->getOne($q, array($uEmail));
			if ($r) {
				return false;
			} else {
				return true;
			}
		}

	
		/**
		 * Returns true if this is a valid pass. Valid passwords cannot contain
		 * ',",\ or whitespace. Also checks against the password length constant
		 */
		public function password($pass) {
			$pass = trim($pass);
			if (strlen($pass) < USER_PASSWORD_MINIMUM) {
				return false;
			}
			if (strlen($pass) > USER_PASSWORD_MAXIMUM) {
				return false;
			}
			
			$resp = preg_match('/[[:space:]]|\>|\<|\"|\'|\\\/i', $pass);
			if ($resp > 0) {
				return false;
			}
			return true;
		}
			
		/**
		 * Returns true if this is a valid username. 
		 * Valid usernames can only contain letters, numbers and optionally single spaces
		*/
		public function username($username) {
			$username = trim($username);
			if (strlen($username) < USER_USERNAME_MINIMUM) {
				return false;
			}
			if (strlen($username) > USER_USERNAME_MAXIMUM) {
				return false;
			}
			if(USER_USERNAME_ALLOW_SPACES) {
				$resp = preg_match("/[^A-Za-z0-9 ]/", $username);
			} else {
				$resp = preg_match("/[^A-Za-z0-9]/", $username);
			}

			if ($resp > 0) {
				return false;
			}
			
			return true;
		}
	
	}