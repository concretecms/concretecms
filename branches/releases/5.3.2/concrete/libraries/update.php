<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Update {
	
	public function getLatestAvailableVersionNumber() {
		$d = Loader::helper('date');
		
		// first, we check session
		$queryWS = false;
		Cache::disableCache();
		$vNum = Config::get('APP_VERSION_LATEST', true);
		Cache::enableCache();
		if (is_object($vNum)) {
			$seconds = strtotime($vNum->timestamp);
			$version = $vNum->value;
			$diff = time() - $seconds;
			if ($diff > APP_VERSION_LATEST_THRESHOLD) {
				// we grab a new value from the service
				$queryWS = true;
			}
		} else {
			$queryWS = true;
		}
		
		if ($queryWS) {
			
			if (function_exists('curl_init')) {
				$curl_handle = @curl_init();
				@curl_setopt($curl_handle, CURLOPT_URL, APP_VERSION_LATEST_WS);
				@curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
				@curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
				@curl_setopt($curl_handle, CURLOPT_POST, true);
				@curl_setopt($curl_handle, CURLOPT_POSTFIELDS, 'BASE_URL_FULL=' . BASE_URL . '/' . DIR_REL . '&APP_VERSION=' . APP_VERSION);
				$version = @curl_exec($curl_handle);
			} else {
				$version = APP_VERSION;
			}
			
			if ($version) {
				Config::save('APP_VERSION_LATEST', $version);
			} else {
				// we don't know so we're going to assume we're it
				Config::save('APP_VERSION_LATEST', APP_VERSION);
			}		
		}
		
		return $version;
	}



}