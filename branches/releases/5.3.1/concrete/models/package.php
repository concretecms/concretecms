<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 *
 * Package-related classes.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @link http://www.concrete5.org
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 */

/**
 *
 * @access private
 *
 */
interface Installable {

	public function install();
	public function uninstall();

}

/**
 *
 * Groups and lists installed and available pages.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @link http://www.concrete5.org
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 */
class PackageList extends Object {
	
	protected $packages = array();
	
	public function add($pkg) {
		$this->packages[] = $pkg;
	}
	
	public function getPackages() {
		return $this->packages;
	}
	
	public static function getHandle($pkgID) {
		$pkgHandle = Cache::get('pkgHandle', $pkgID);
		if ($pkgHandle != false) {
			return $pkgHandle;
		}
		
		$pl = PackageList::get();
		$handle = null;
		$plitems = $pl->getPackages();
		
		foreach($plitems as $p) {
			if ($p->getPackageID() == $pkgID) {
				$handle = $p->getPackageHandle();
				break;
			}
		}

		Cache::set('pkgHandle', $pkgID, $handle);
		return $handle;
	}
	
	public static function get($pkgIsInstalled = 1) {
		
		$db = Loader::db();
		$r = $db->query("select pkgID, pkgName, pkgIsInstalled, pkgDescription, pkgVersion, pkgHandle, pkgDateInstalled from Packages where pkgIsInstalled = ? order by pkgID asc", array($pkgIsInstalled));
		$list = new PackageList();
		while ($row = $r->fetchRow()) {
			$pkg = new Package;
			$pkg->setPropertiesFromArray($row);
			$list->add($pkg);
		}

		return $list;
	}
	
}

/**
 *
 * Represents a package. A package is a grouping of Concrete functionality that can be "packaged" up and distributed
 * and easily installed in one spot.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @link http://www.concrete5.org
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 */
class Package extends Object {

	public function getPackageID() {return $this->pkgID;}
	public function getPackageName() {return t($this->pkgName);}
	public function getPackageDescription() {return t($this->pkgDescription);}
	public function getPackageHandle() {return $this->pkgHandle;}
	public function getPackageDateInstalled() {return $this->pkgDateInstalled;}
	public function getPackageVersion() {return $this->pkgVersion;}
	public function isPackageInstalled() { return $this->pkgIsInstalled;}
	
	protected $appVersionRequired = '5.0.0';
	
	const E_PACKAGE_NOT_FOUND = 1;
	const E_PACKAGE_INSTALLED = 2;
	const E_PACKAGE_VERSION = 3;
	const E_PACKAGE_DOWNLOAD = 4;
	const E_PACKAGE_SAVE = 5;
	const E_PACKAGE_UNZIP = 6;
	const E_PACKAGE_INSTALL = 7;

	protected $errorText = array();

	public function getApplicationVersionRequired() {
		return $this->appVersionRequired;
	}
	
	public static function installDB($xmlFile) {
		
		if (!file_exists($xmlFile)) {
			return false;
		}
		
		// currently this is just done from xml
		
		$db = Loader::db();

		// this sucks - but adodb generates errors at the beginning because it attempts
		// to find a table that doesn't exist! 
		
		$handler = $db->IgnoreErrors();
		if ($db->getDebug() == false) {
			ob_start();
		}
		
		$schema = $db->getADOSChema();		
		$sql = $schema->ParseSchema($xmlFile);
		
		$db->IgnoreErrors($handler);
		
		if (!$sql) {
			$result->message = $db->ErrorMsg();
			return $result;
		}

		$r = $schema->ExecuteSchema();


		if ($db->getDebug() == false) {
			$dbLayerErrorMessage = ob_get_contents();
			ob_end_clean();
		}
		
		$result = new stdClass;
		$result->result = false;
		
		if ($dbLayerErrorMessage != '') {
			$result->message = $dbLayerErrorMessage;
			return $result;
		} if (!$r) {
			$result->message = $db->ErrorMsg();
			return $result;
		}
		
		$result->result = true;
		
		$db->CacheFlush();
		return $result;
	
	}

	
	public function testForInstall($package) {
		// this is the pre-test routine that packages run through before they are installed. Any errors that come here
		// are to be returned in the form of an array so we can show the user. If it's all good we return true
		$db = Loader::db();
		$errors = array();
		
		$pkg = Loader::package($package);
		
		// Step 1 does that package exist ?
		if ((!is_dir(DIR_PACKAGES . '/' . $package) && (!is_dir(DIR_PACKAGES_CORE . '/' . $package))) || $package == '') {
			$errors[] = Package::E_PACKAGE_NOT_FOUND;
		} else if (!is_object($pkg)) {
			$errors[] = Package::E_PACKAGE_NOT_FOUND;
		}
		
		// Step 2 - check to see if the user has already installed a package w/this handle
		$cnt = $db->getOne("select count(*) from Packages where pkgHandle = ?", array($package));
		if ($cnt > 0) {
			$errors[] = Package::E_PACKAGE_INSTALLED;
		}
		
		if (count($errors) == 0) {
			// test minimum application version requirement
			if (version_compare(APP_VERSION, $pkg->getApplicationVersionRequired(), '<')) {
				$errors[] = array(Package::E_PACKAGE_VERSION, $pkg->getApplicationVersionRequired());
			}
		}
		
		if (count($errors) > 0) {
			return $errors;
		} else {
			return true;
		}
	}

	public function mapError($testResults) {
		$errorText[Package::E_PACKAGE_INSTALLED] = t("You've already installed that package.");
		$errorText[Package::E_PACKAGE_NOT_FOUND] = t("Invalid Package.");
		$errorText[Package::E_PACKAGE_VERSION] = t("This package requires Concrete version %s or greater.");
		$errorText[Package::E_PACKAGE_DOWNLOAD] = t("An error occured while downloading the package.");
		$errorText[Package::E_PACKAGE_SAVE] = t("Concrete was not able to save the package after download.");
		$errorText[Package::E_PACKAGE_UNZIP] = t('An error while trying to unzip the package.');
		$errorText[Package::E_PACKAGE_INSTALL] = t('An error while trying to install the package.');

		$testResultsText = array();
		foreach($testResults as $result) {
			if (is_array($result)) {
				$et = $errorText[$result[0]];
				array_shift($result);
				$testResultsText[] = vsprintf($et, $result);
			} else if (is_int($result)) {
				$testResultsText[] = $errorText[$result];
			} else if (!empty($result)) {
				$testResultsText[] = $result;
			}
		}
		return $testResultsText;
	}

	/*
	 * Returns a path to where the packages files are located.
	 * @access public
	 * @return string $path
	 */
	 
	public function getPackagePath() {
		$dirp = (is_dir(DIR_PACKAGES . '/' . $this->getPackageHandle())) ? DIR_PACKAGES : DIR_PACKAGES_CORE;
		$path = $dirp . '/' . $this->getPackageHandle();
		return $path;
	}
	
	
	public function getByID($pkgID) {
		$db = Loader::db();
		$row = $db->GetRow("select * from Packages where pkgID = ?", array($pkgID));
		if ($row) {
			$pkg = new Package;
			$pkg->setPropertiesFromArray($row);
			return $pkg;
		}
	}
	
	protected function install() {
		$db = Loader::db();
		$dh = Loader::helper('date');
		$v = array($this->getPackageName(), $this->getPackageDescription(), $this->getPackageVersion(), $this->getPackageHandle(), 1, $dh->getLocalDateTime());
		$db->query("insert into Packages (pkgName, pkgDescription, pkgVersion, pkgHandle, pkgIsInstalled, pkgDateInstalled) values (?, ?, ?, ?, ?, ?)", $v);
		
		$pkg = Package::getByID($db->Insert_ID());
		Package::installDB($pkg->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
		
		return $pkg;
	}
	
	public static function getInstalledHandles() {
		$db = Loader::db();
		return $db->GetCol("select pkgHandle from Packages");
	}

	public static function getInstalledList() {
		$db = Loader::db();
		$r = $db->query("select * from Packages where pkgIsInstalled = 1 order by pkgDateInstalled asc");
		$pkgArray = array();
		while ($row = $r->fetchRow()) {
			$pkg = new Package;
			$pkg->setPropertiesFromArray($row);
			$pkgArray[] = $pkg;
		}
		return $pkgArray;
	}
	
	public static function getAvailablePackages($filterInstalled = true) {
		$dh = Loader::helper('file');
		
		$packages = $dh->getDirectoryContents(DIR_PACKAGES);
		if ($filterInstalled) {
			$handles = self::getInstalledHandles();

			// strip out packages we've already installed
			$packagesTemp = array();
			foreach($packages as $p) {
				if (!in_array($p, $handles)) {
					$packagesTemp[] = $p;
				}
			}
			$packages = $packagesTemp;
		}
		
		if (count($packages) > 0) {
			$packagesTemp = array();
			// get package objects from the file system
			foreach($packages as $p) {
				$pkg = Loader::package($p);
                if (!empty($pkg)) {
				    $packagesTemp[] = $pkg;
                }
			}
			$packages = $packagesTemp;
		}
		return $packages;
	}
	

}
