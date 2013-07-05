<?php
/**
 * Helper elements for dealing with errors in Concrete
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 	defined('C5_EXECUTE') or die("Access Denied.");
	class Concrete5_Helper_Validation_Ip {	
		/**
 		 * Checks if an IP has been banned
		 * @param type $ip  if 127.0.0.1 form (as opposed to int)
		 * @return boolean
		 */		
		public function check($ip=false,$extraParamString=false,$extraParamValues=array()) {
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
			
			if($extraParamString !== false){
				$q .= $extraParamString;
			}
			
			$ip_as_long = ip2long($ip);
			$v = array($ip_as_long, $ip_as_long, $ip_as_long);
			$v = array_merge($v,$extraParamValues);
			
			$rs 	= $db->Execute($q,$v);
			$row 	= $rs->fetchRow();
			
			return ($row['count'] > 0) ? false : true;
		}
	
		protected function checkForManualPermBan($ip=false){
			return $this->check($ip, ' AND isManual = ? AND expires = ? ',Array(1,0));
		}
		
		/** Checks if an IPv4 address belongs to a private network.
		* @param string $ip The IP address to check.
		* @return bool Returns true if $ip belongs to a private network, false if it's a public IP address.
		*/
		public function isPrivateIP($ip) {
			if(empty($ip)) {
				return false;
			}
			if(
				(strpos($ip, '10.') === 0)
				||
				(strpos($ip, '192.168.') === 0)
				||
				(preg_match('/^172\.(\d+)\./', $ip, $m) && (intval($m[1]) >= 16) && (intval($m[1]) <= 31))
			) {
				return true;
			}
			return false;
		}
		
		/** Returns the client IP address (or an empty string if it can't be found).
		* @return string
		*/
		public function getRequestIP() {
			$result = '';
			foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $index) {
				if(array_key_exists($index, $_SERVER) && is_string($_SERVER[$index])) {
					foreach(explode(',', $_SERVER[$index]) as $ip) {
						$ip = trim($ip);
						if(strlen($ip)) {
							if($this->isPrivateIP($ip)) {
								$result = $ip;
							}
							else {
								return $ip;
							}
						}
					}
				}
			}
			return $result;
		}
		
		public function getErrorMessage() {
			return t("Unable to complete action: your IP address has been banned. Please contact the administrator of this site for more information.");
		}	
		
		public function logSignupRequest($ignoreConfig=false) {		
			Loader::model('signup_request');
			if (Config::get('IP_BAN_LOCK_IP_ENABLE') == 1) {
				$signupRequest = new SignupRequest();
				$signupRequest->id = null;
				$signupRequest->date_access = null;
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

				//IP_BAN_LOCK_IP_HOW_LONG_MIN of 0 or undefined  means forever
				$timeOffset = Config::get('IP_BAN_LOCK_IP_HOW_LONG_MIN');
				$timeOffset = $timeOffset ? ($timeOffset * 60)  : 0;				
				$time 		= $timeOffset ? time() + $timeOffset : 0;
				
				$db	= Loader::db();				
				Loader::model('user_banned_ip');
				
				//delete before inserting .. catching a duplicate (1062) doesn't 
				//seem to be working in all enviornments.  If there's a permanant ban,
				//obey its setting
				if ($this->checkForManualPermBan(long2ip($ip), true)) {
					$db->StartTrans();
					//check if there's a manual ban
	
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
		
	}
?>
