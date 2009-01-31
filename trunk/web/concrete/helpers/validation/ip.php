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
		public function check($ip=false) {
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
	
		protected function getRequestIP() {			
			if ( array_key_exists ('HTTP_CLIENT_IP', $_SERVER ) && $_SERVER['HTTP_CLIENT_IP']){
				return $_SERVER['HTTP_CLIENT_IP'];
			}
			else if ( array_key_exists ('HTTP_X_FORWARDED_FOR', $_SERVER ) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else{
				return $_SERVER['REMOTE_ADDR'];
			}
		}
		
		public function getErrorMessage() {
			return t("Unable to complete action: your IP address has been banned. Please contact the administrator of this site for more information.");
		}	
		
		public function logSignupRequest() {		
			if (Config::get('IP_BAN_LOCK_IP_ENABLE') == 1) {
				$signupRequest = new SignupRequest();
				$signupRequest->ipFrom = ip2long($this->getRequestIP());
				$signupRequest->save();
			}
		}
		
		public function signupRequestThreshholdReached() {
			$db = Loader::db();
			$threshold_attempts  = Config::get('IP_BAN_LOCK_IP_ATTEMPTS');
			$threshhold_seconds = Config::get('IP_BAN_LOCK_IP_TIME');
			$ip = ip2long($this->getRequestIP());			
			$q = 'SELECT count(ipFrom) as count
			FROM SignupRequests 
			WHERE ipFrom = ? 
			AND UNIX_TIMESTAMP(date_access) > (UNIX_TIMESTAMP(now()) - ?)';			
			$v = Array($ip, $threshhold_seconds);
			
			$rs = $db->execute($q,$v);			
			$row = $rs->fetchRow();
			if ($row['count'] >= $threshold_attempts) {
				return true;
			}
			else{
				return false;
			}
		}
		
		public function createIPBan($ip=false) {
			$ip = ($ip) ? $ip : $this->getRequestIP();			
			$ip = ip2long($ip);
			$time = time() + (60 * 60 * 24 * 1);     //ban for a day
			Loader::model('user_banned_ip');
			$ban = new UserBannedIP();
			$ban->ipFrom 	= $ip;
			$ban->ipTo 		= 0;
			$ban->banCode	= UserBannedIp::IP_BAN_CODE_REGISTRATION_THROTTLE;
			$ban->expires	= $time;
			$ban->isManual	= 0;
			try{
				$ban->save();
			}
			catch (Exception $e) {
				//AdoDB active record has problems with no primary key tables
				//if a duplicate key, update the expired 
				//if (strpos ( $e->getMsg(), string needle [, int offset] )
				if ($e->getCode() == 1062) {		//1602 is duplicate entry key
					$db = Loader::db();
					
					$q = 'UPDATE UserBannedIPs 
					SET 
					expires = ?
					WHERE 
					(ipFrom = ? AND ipTo = 0)
					AND
					NOT (expires = 0)';
					
					$time 	= time() + (60 * 60 * 24 * 1);
					$ip		= $ip;
					$v = array($time,$ip);					
					$db->execute($q,$v);
				}
			}
		}
		
	}

	class SignupRequest extends Model{}	
?>