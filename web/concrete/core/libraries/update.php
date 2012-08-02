<?

defined('C5_EXECUTE') or die("Access Denied.");

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
class Concrete5_Library_Update {

	public function getLatestAvailableVersionNumber() {
		if (defined('MULTI_SITE') && MULTI_SITE == 1) {
			$updates = Update::getLocalAvailableUpdates();
			$multiSiteVersion = 0;
			foreach($updates as $up) {
				if (version_compare($up->getUpdateVersion(), $multiSiteVersion, '>')) {
					$multiSiteVersion = $up->getUpdateVersion();
				}	
			}
			Config::save('APP_VERSION_LATEST', $multiSiteVersion);
			return $multiSiteVersion;
		}
		
		$d = Loader::helper('date');
		// first, we check session
		$queryWS = false;
		Cache::disableCache();
		$vNum = Config::get('APP_VERSION_LATEST', true);
		Cache::enableCache();
		if (is_object($vNum)) {
			$seconds = strtotime($vNum->timestamp);
			$version = $vNum->value;
			if (is_object($version)) {
				$versionNum = $version->version;
			} else {
				$versionNum = $version;
			}
			$diff = time() - $seconds;
			if ($diff > APP_VERSION_LATEST_THRESHOLD) {
				// we grab a new value from the service
				$queryWS = true;
			}
		} else {
			$queryWS = true;
		}
		
		if ($queryWS) {
			Loader::library('marketplace');
			$mi = Marketplace::getInstance();
			if ($mi->isConnected()) {
				Marketplace::checkPackageUpdates();
			}
			$update = Update::getLatestAvailableUpdate();
			$versionNum = $update->version;
			
			if ($versionNum) {
				Config::save('APP_VERSION_LATEST', $versionNum);
				if (version_compare($versionNum, APP_VERSION, '>')) {
					Loader::model('system_notification');
					SystemNotification::add(SystemNotification::SN_TYPE_CORE_UPDATE, t('A new version of concrete5 is now available.'), '', $update->notes, View::url('/dashboard/system/backup_restore/update'));
				}		
			} else {
				// we don't know so we're going to assume we're it
				Config::save('APP_VERSION_LATEST', APP_VERSION);
			}
		}
		
		return $versionNum;
	}
	
	public function getApplicationUpdateInformation() {
		$r = Cache::get('APP_UPDATE_INFO', false);
		if (!is_object($r)) {
			$r = Update::getLatestAvailableUpdate();
		}
		return $r;
	}
		
	protected function getLatestAvailableUpdate() {
		$obj = new stdClass;
		$obj->notes = false;
		$obj->url = false;
		$obj->date = false;
		
		if (function_exists('curl_init')) {
			$curl_handle = @curl_init();

			// Check to see if there are proxy settings
			if (Config::get('HTTP_PROXY_HOST') != null) {
				@curl_setopt($curl_handle, CURLOPT_PROXY, Config::get('HTTP_PROXY_HOST'));
				@curl_setopt($curl_handle, CURLOPT_PROXYPORT, Config::get('HTTP_PROXY_PORT'));

				// Check if there is a username/password to access the proxy
				if (Config::get('HTTP_PROXY_USER') != null) {
					@curl_setopt($curl_handle, CURLOPT_PROXYUSERPWD, Config::get('HTTP_PROXY_USER') . ':' . Config::get('HTTP_PROXY_PWD'));
				}
			}

			@curl_setopt($curl_handle, CURLOPT_URL, APP_VERSION_LATEST_WS);
			@curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			@curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			@curl_setopt($curl_handle, CURLOPT_POST, true);
			@curl_setopt($curl_handle, CURLOPT_POSTFIELDS, 'LOCALE=' . ACTIVE_LOCALE . '&BASE_URL_FULL=' . BASE_URL . '/' . DIR_REL . '&APP_VERSION=' . APP_VERSION);
			$resp = @curl_exec($curl_handle);
			
			$xml = @simplexml_load_string($resp);
			if ($xml === false) {
				// invalid. That means it's old and it's just the version
				$obj->version = trim($resp);
			} else {
				$obj = new stdClass;
				$obj->version = (string) $xml->version;
				$obj->notes = (string) $xml->notes;
				$obj->url = (string) $xml->url;
				$obj->date = (string) $xml->date;
			}		

			Cache::set('APP_UPDATE_INFO', false, $obj);

		} else {
			$obj->version = APP_VERSION;
		}
		
		return $obj;
	}

	/** 
	 * Looks in the designated updates location for all directories, ascertains what
	 * version they represent, and finds all versions greater than the currently installed version of
	 * concrete5
	 */
	public function getLocalAvailableUpdates() {
		$fh = Loader::helper('file');
		$updates = array();
		$contents = @$fh->getDirectoryContents(DIR_APP_UPDATES);
		foreach($contents as $con) {
			if (is_dir(DIR_APP_UPDATES . '/' . $con)) {
				$obj = ApplicationUpdate::get($con);
				if (is_object($obj)) {
					if (version_compare($obj->getUpdateVersion(), APP_VERSION, '>')) {
						$updates[] = $obj;
					}
				}
			}				
		}
		return $updates;
	}


}
