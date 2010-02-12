<?

defined('C5_EXECUTE') or die(_("Access Denied."));

class Marketplace {
	
	public function isConnected() {
		$token = Config::get('MARKETPLACE_SITE_TOKEN');
		return $token != '';
	}
	
	public function generateSiteToken() {
		$fh = Loader::helper('file');
		$token = $fh->getContents(MARKETPLACE_URL_CONNECT_TOKEN_NEW);
		return $token;	
	}
	
	public function getSitePageURL() {
		$token = Config::get('MARKETPLACE_SITE_URL_TOKEN');
		return MARKETPLACE_BASE_URL_SITE_PAGE . '/' . $token;
	}

	public static function downloadRemoteFile($file) {
		$fh = Loader::helper('file');
		$pkg = $fh->getContents($file);
		if (empty($pkg)) {
			return Package::E_PACKAGE_DOWNLOAD;
		}

		$file = time();
		// Use the same method as the Archive library to build a temporary file name.
		$tmpFile = $fh->getTemporaryDirectory() . '/' . $file . '.zip';
		$fp = fopen($tmpFile, "wb");
		if ($fp) {
			fwrite($fp, $pkg);
			fclose($fp);
		} else {
			return Package::E_PACKAGE_SAVE;
		}
		
		return $file;
	}
	
	/*
	public function install_remote($type, $remoteCID=null, $install=false){
		$item = $this->get_remote_item($type, $remoteCID);
		if (empty($item)) {
			return array(Package::E_PACKAGE_NOT_FOUND);
		}

		$fileURL = $item->getRemoteFileURL();
		if (empty($fileURL)) {
			return array(Package::E_PACKAGE_NOT_FOUND);
		}

		$authData = UserInfo::getAuthData();
		$fileURL .= "&auth_token={$authData['auth_token']}&auth_uname={$authData['auth_uname']}&auth_timestamp={$authData['auth_timestamp']}";

		$file = $this->download_remote_package($fileURL);
		if (empty($file) || $file == Package::E_PACKAGE_DOWNLOAD) {
			return array(Package::E_PACKAGE_DOWNLOAD);
		} else if ($file == Package::E_PACKAGE_SAVE) {
			return array($file);
		}

		try {
			Loader::model('package_archive');
			$am = new PackageArchive($item->getHandle());
			$am->install($file, true);
		} catch (Exception $e) {
			return array($e->getMessage());
		}

		if ($install) {
        	$tests = Package::testForInstall($item->getHandle());
        	if (is_array($tests)) {
				return $tests;
        	} else {
            	$p = Loader::package($item->getHandle());
            	try {
                	$p->install();
            	} catch(Exception $e) {
					return array(Package::E_PACKAGE_INSTALL);
            	}
			}
        }

		return true;
	
	
	}
	
	
	}*/
	


	public function getAvailableMarketplaceItems($filterInstalled=true) {
		Loader::model('marketplace_remote_item');
		if (!function_exists('mb_detect_encoding')) {
			return array();
		}
		
		if (!is_array($addons)) {
			$fh = Loader::helper('file'); 
			if (!$fh) return array();

			// Retrieve the URL contents 
			$csToken = Config::get('MARKETPLACE_SITE_TOKEN');
			$url = MARKETPLACE_PURCHASES_LIST_WS."?csToken={$csToken}";
			$xml = $fh->getContents($url);

			$addons=array();
			if( $xml || strlen($xml) ) {
				// Parse the returned XML file
				$enc = mb_detect_encoding($xml);
				$xml = mb_convert_encoding($xml, 'UTF-8', $enc); 
				
				try {
					libxml_use_internal_errors(true);
					$xmlObj = new SimpleXMLElement($xml);
					foreach($xmlObj->addon as $addon){
						$mi = new MarketplaceRemoteItem();
						$mi->loadFromXML($addon);
						$mi->isPurchase(1);
						$remoteCID = $mi->getRemoteCollectionID();
						if (!empty($remoteCID)) {
							$addons[$mi->getHandle()] = $mi;
						}
					}
				} catch (Exception $e) {}
			}

		}

		if ($filterInstalled && is_array($addons)) {
			Loader::model('package');
			$handles = Package::getInstalledHandles();
			if (is_array($handles)) {
				$adlist = array();
				foreach($addons as $key=>$ad) {
					if (!in_array($ad->getHandle(), $handles)) {
						$adlist[$key] = $ad;
					}
				}
				$addons = $adlist;
			}
		}

		return $addons;
	}

}

?>