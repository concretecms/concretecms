<?php
/**
 * Helper elements for dealing with errors in Concrete
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 	defined('C5_EXECUTE') or die(_("Access Denied."));
	class ValidationIpHelper {	
		/**
 		 * Checks if an IP has been banned
		 * @param type $ip
		 * @return boolean
		 */		
		public function check($ip=false){
			$ip = ($ip) ? $ip : $this->getRequestIP();
			$db = Loader::db();
			//do ip check
			$q = 'SELECT count(expires) as count
			FROM UserBannedIPs 
			WHERE 	
			(
				(ipFrom = ? AND ipTo = 0) 
				OR 
				(ipFrom <= ? AND ipTo >= ?)
			)
			AND (expires = 0 OR expires > UNIX_TIMESTAMP(now()))
			';
			$ip_as_long = ip2long($ip);
			$v = array($ip_as_long, $ip_as_long, $ip_as_long);
			
			$rs 	= $db->Execute($q,$v);
			$row 	= $rs->fetchRow();
			
			return ($row['count'] > 0) ? false : true;
		}
	
		protected function getRequestIP(){			
			if( array_key_exists ('HTTP_CLIENT_IP', $_SERVER ) && $_SERVER['HTTP_CLIENT_IP']){
				return $_SERVER['HTTP_CLIENT_IP'];
			}
			else if( array_key_exists ('HTTP_X_FORWARDED_FOR', $_SERVER ) && $_SERVER['HTTP_X_FORWARDED_FOR']){
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else{
				return $_SERVER['REMOTE_ADDR'];
			}
		}
		
		public function getErrorMessage() {
			return t("Unable to complete action: your IP address has been banned. Please contact the administrator of this site for more information.");
		}	
		
		public function logSignupRequest(){		
			if(Config::get('IP_BAN_LOCK_IP_ENABLE') == 1){
				$signupRequest = new SignupRequest();
				$signupRequest->ipFrom = ip2long($this->getRequestIP());
				$signupRequest->save();
			}
		}
		
		public function signupRequestThreshholdReached(){
			$threshold_attemps  = Config::get('IP_BAN_LOCK_IP_ATTEMPTS');
			$threshhold_seconds = Config::get('IP_BAN_LOCK_IP_TIME');
			
			$q = 'SELECT count(ipFrom) 
			FROM SignupRequests 
			WHERE ipFrom =  AND UNIX_TIMESTAMP(date_access) > (UNIX_TIMESTAMP(now()) - 1000);';
		}
		
	}

	class SignupRequest extends Model{}	
?>