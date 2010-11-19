<?php 
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUserHelper {

	function getOnlineNow($uo, $showSpacer = true) {
		$ul = 0;
		if (is_object($uo)) {
			// user object
			$ul = $uo->getLastOnline();
		} else if (is_numeric($uo)) {
			$db = Loader::db();
			$ul = $db->getOne("select uLastOnline from Users where uID = {$uo}");
		}

		$online = (time() - $ul) <= ONLINE_NOW_TIMEOUT;			
		
		if ($online) {
			
			return ONLINE_NOW_SRC_ON;
		} else {
			if ($showSpacer) {
				return ONLINE_NOW_SRC_OFF;
			}
			
		}
	}
	
	public function validNewPassword( $password, $errorObj=NULL){
			
		$valc = Loader::helper('concrete/validation');	
			
		if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
			if($errorObj) 
				$errorObj->add( t('A password must be between %s and %s characters', USER_PASSWORD_MINIMUM, USER_PASSWORD_MAXIMUM) );
			$invalid=1;
		}
			
		if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
			if($errorObj) 
				$errorObj->add( t('A password may not contain ", \', >, <, or any spaces.') );
			$invalid=1;
		}
		
		if($invalid) return false;
		
		return true;
	}
}
