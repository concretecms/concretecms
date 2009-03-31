<?
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

	public function install_remote($type, $remoteCID=null, $install=false)
	{
		if (empty($remoteCID)) {
			return t('No package specified.');
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
		if (empty($item)) {
			return t('Not a recognized package.');
		}

		$fileURL = $item->getRemoteFileURL();
		if ($type == 'purchase') {
			$authData = UserInfo::getAuthData();
			$fileURL .= "&auth_token={$authData['auth_token']}&auth_uname={$authData['auth_uname']}&auth_timestamp={$authData['auth_timestamp']}";
		}
		$file = $this->download_remote_package($fileURL);
		if (empty($file)) {
			return t('Not a recognized package.');
		}

		try {
			Loader::model('package_archive');
			$am = new PackageArchive($item->getHandle());
			$am->install($file, true);
		} catch (Exception $e) {
			return t('An error while trying to unzip the package: ') . $e->getMessage();
		}

		if ($install) {
        	$tests = Package::testForInstall($item->getHandle());
			$errors = "";
        	if (is_array($tests)) {
				$errors .= "<ol>";
				foreach ($tests as $test) {
					$errors .= "<li>$test</li>";
				}
				$errors .= "</ol>";
				return $errors;
        	} else {
            	$p = Loader::package($item->getHandle());
            	try {
                	$p->install();
            	} catch(Exception $e) {
					return t('An error while trying to install the package: ') . $e->getMessage();
            	}
			}
        }

		return null;
	}

	private function download_remote_package($fileURL)
	{
		if (empty($fileURL)) return;

		$fh = Loader::helper('file');
		$pkg = $fh->getContents($fileURL);

		$file = time();
		// Use the same method as the Archive library to build a temporary file name.
		$tmpFile = $fh->getTemporaryDirectory() . '/' . $file . '.zip';
		$fp = fopen($tmpFile, "wb");
		fwrite($fp, $pkg);
		fclose($fp);

		return $file;
	}
}
