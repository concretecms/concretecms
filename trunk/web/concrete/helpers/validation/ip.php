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
				(ipFrom = '.ip2long($ip).' AND ipTo = 0) 
				OR 
				(ipFrom <= '.ip2long($ip).' AND ipTo >= '.ip2long($ip).')
			)
			AND (expires = 0 OR expires > UNIX_TIMESTAMP(now()))
			';
			
			$rs 	= $db->Execute($q);
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
	}
	
?>