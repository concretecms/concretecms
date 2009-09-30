<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Todd Crowe <toddbcrowe@gmail.com>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for downloading and install marketplace packages
 * @package Helpers
 * @category Concrete
 * @author Todd Crowe <toddbcrowe@gmail.com>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));

class PackageHelper {

	public function get_remote_url($type, $remoteCID) {
		$item = $this->get_remote_item($type, $remoteCID);
		if (empty($item)) {
			return "";
		}

		$fileURL = $item->getRemoteFileURL();
		if (empty($fileURL)) {
			return "";
		}

		$authData = UserInfo::getAuthData();
		$fileURL .= "&auth_token={$authData['auth_token']}&auth_uname={$authData['auth_uname']}&auth_timestamp={$authData['auth_timestamp']}";
		return $fileURL;
	}
	
	public function prepare_remote_upgrade($type, $remoteCID, $handle) {
		$item = $this->get_remote_purchase($remoteCID);
		
		if (empty($item)) {
			return array(Package::E_PACKAGE_NOT_FOUND);
		}
		
		// backup the old package
		$pkg = Package::getByHandle($handle);
		$r = $pkg->backup();
		if (is_array($r)) {
			return $r;
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
		
		/*
		$tests = Package::testForInstall($item->getHandle(), false);
		if (is_array($tests)) {
			return $tests;
		} else {
			$p = Package::getByHandle($item->getHandle());
			try {
				$p->upgradeCoreData();
				$p->upgrade();
			} catch(Exception $e) {
				return array(Package::E_PACKAGE_INSTALL);
			}
		}
		*/
		
		return true;
	}

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

	private function download_remote_package($fileURL) {
		$fh = Loader::helper('file');
		$pkg = $fh->getContents($fileURL);
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

	private function get_remote_purchase($cID) {
		$helper = Loader::helper("concrete/marketplace/blocks");
		$list = $helper->getPurchasesList(false);
		foreach ($list as $item) {
			if ($cID == $item->getRemoteCollectionID()) {
				return $item;
			}
		}
	}
	
	private function get_remote_item($type, $remoteCID) {
		if (empty($remoteCID)) {
			return "";
		}

	    if ($type != 'theme') {
	    	$helper = Loader::helper('concrete/marketplace/blocks');
    		$list = $helper->getCombinedList();
		} else {
	    	$helper = Loader::helper('concrete/marketplace/themes');
    		$list = $helper->getPreviewableList();
		}
        foreach ($list as $item) {
			if ($remoteCID == $item->getRemoteCollectionID()) {
				break;
			}
		}
		return $item;
	}
}
