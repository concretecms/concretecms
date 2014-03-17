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
		 * Returns true if this is a valid password. 
		 */
		public function password($pass) {
			if (strlen($pass) < USER_PASSWORD_MINIMUM) {
				return false;
			}
			if (strlen($pass) > USER_PASSWORD_MAXIMUM) {
				return false;
			}
			
			return true;
		}
			
		/**
		 * Returns true if this is a valid username. 
		 * Valid usernames can only contain letters, numbers, dots (only in the middle), underscores (only in the middle) and optionally single spaces
		 * @return bool
		*/
		public function username($username) {
			$username = trim($username);
			if (strlen($username) < USER_USERNAME_MINIMUM) {
				return false;
			}
			if (strlen($username) > USER_USERNAME_MAXIMUM) {
				return false;
			}
			$rxBoundary = '[A-Za-z0-9]';
			if(USER_USERNAME_ALLOW_SPACES) {
				$rxMiddle = '[A-Za-z0-9_. ]';
			}
			else {
				$rxMiddle = '[A-Za-z0-9_.]';
			}
			if(strlen($username) < 3) {
				if(!preg_match('/^' . $rxBoundary . '+$/', $username)) {
					return false;
				}
			}
			else {
				if(!preg_match('/^' . $rxBoundary  . $rxMiddle . '+'. $rxBoundary . '$/', $username)) {
					return false;
				}
			}
			return true;
		}
	
	}
