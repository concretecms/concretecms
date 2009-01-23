<?php 
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
	defined('C5_EXECUTE') or die(_("Access Denied."));
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

	}