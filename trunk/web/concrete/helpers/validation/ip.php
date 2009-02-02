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
		
		public function logSignupRequest($ignoreConfig=false) {		
			Loader::model('signup_request');
			if (Config::get('IP_BAN_LOCK_IP_ENABLE') == 1) {
				$signupRequest = new SignupRequest();
				$signupRequest->ipFrom = ip2long($this->getRequestIP());
				$signupRequest->save();
			}
		}
		
		public function signupRequestThreshholdReached($ignoreConfig=false) {
			if ($ignoreConfig || Config::get('IP_BAN_LOCK_IP_ENABLE') == 1) {		
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
		}
		
		public function createIPBan($ip=false,$ignoreConfig=false) {
			if ($ignoreConfig || Config::get('IP_BAN_LOCK_IP_ENABLE') == 1) {		
				$ip = ($ip) ? $ip : $this->getRequestIP();			
				$ip = ip2long($ip);
				
				//$time = time() + (60 * 60 * 24 * 1);     //ban for a day
				$timeOffset = Config::get('IP_BAN_LOCK_IP_HOW_LONG') ? Config::get('IP_BAN_LOCK_IP_HOW_LONG') : (60 * 60 * 24 * 1);
				$time 		= time() + $timeOffset;
				$db	= Loader::db();				
				Loader::model('user_banned_ip');
				
				//delete before inserting .. catching a duplicate (1062) doesn't 
				//seem to be working in all enviornments
				$db->StartTrans();
				$q 	= 'DELETE FROM UserBannedIPs WHERE ipFrom = ? AND ipTo = 0 AND isManual = 0';
				$v  = Array($ip,0);				
				$db->execute($q,$v);
				
				$q	=  'INSERT INTO UserBannedIPs (ipFrom,ipTo,banCode,expires,isManual) ';
				$q  .= 'VALUES (?,?,?,?,?)';				
				$v  = array($ip,0,UserBannedIp::IP_BAN_CODE_REGISTRATION_THROTTLE,$time,0);
				$db->execute($q,$v);

				$db->CompleteTrans();				
			}
		}
		
	}
?>