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
		$vNum = Config::get('APP_VERSION_LATEST', true);
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
			$f = Loader::helper('file');
			$version = $f->getContents(APP_VERSION_LATEST_WS, 3);
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