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
		$pl = PackageList::get();
		$handle = null;
		$plitems = $pl->getPackages();
		foreach($plitems as $p) {
			if ($p->getPackageID() == $pkgID) {
				$handle = $p->getPackageHandle();
				break;
			}
		}
		return $handle;
	}
	
	public static function get() {
		static $list;
		if (!isset($list)) {
			$db = Loader::db();
			$r = $db->query("select pkgID, pkgName, pkgDescription, pkgHandle, pkgDateInstalled from Packages order by pkgID asc");
			$list = new PackageList();
			while ($row = $r->fetchRow()) {
				$pkg = new Package;
				$pkg->setPropertiesFromArray($row);
				$list->add($pkg);
			}
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
	public function getPackageName() {return $this->pkgName;}
	public function getPackageDescription() {return $this->pkgDescription;}
	public function getPackageHandle() {return $this->pkgHandle;}
	public function getPackageDateInstalled() {return $this->pkgDateInstalled;}
	
	const E_PACKAGE_NOT_FOUND = 1;
	const E_PACKAGE_INSTALLED = 2;
	
	public static function installDB($xmlFile) {
		
		if (!file_exists($xmlFile)) {
			return false;
		}
		
		// currently this is just done from xml
		
		$db = Loader::db();

		// this sucks - but adodb generates errors at the beginning because it attempts
		// to find a table that doesn't exist! 
		
		$handler = $db->IgnoreErrors();
		
		$schema = $db->getADOSChema();		
		$sql = $schema->ParseSchema($xmlFile);
		
		$db->IgnoreErrors($handler);
		
		if (!$sql) {
			$result->message = $db->ErrorMsg();
			return $result;
		}

		$r = $schema->ExecuteSchema();
		
		$result = new stdClass;
		$result->result = false;
		if (!$r) {
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
		
		// Step 1 does that package exist ?
		if ((!is_dir(DIR_PACKAGES . '/' . $package) && (!is_dir(DIR_PACKAGES_CORE . '/' . $package))) || $package == '') {
			$errors[] = E_PACKAGE_NOT_FOUND;
		}
		
		// Step 2 - check to see if the user has already installed a package w/this handle
		$cnt = $db->getOne("select count(*) from Packages where pkgHandle = ?", array($package));
		if ($cnt > 0) {
			$errors[] = E_PACKAGE_INSTALLED;
		}
		
		if (count($errors) > 0) {
			return $errors;
		} else {
			return true;
		}
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
		$v = array($this->pkgName, $this->pkgDescription, $this->pkgHandle, 1, $dh->getLocalDateTime());
		$db->query("insert into Packages (pkgName, pkgDescription, pkgHandle, pkgIsInstalled, pkgDateInstalled) values (?, ?, ?, ?, ?)", $v);
		
		$pkg = Package::getByID($db->Insert_ID());
		return $pkg;
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
		$db = Loader::db();
		$dh = Loader::helper('file');
		
		$packages = $dh->getDirectoryContents(DIR_PACKAGES);
		if ($filterInstalled) {
			// strip out packages we've already installed
			$handles = $db->GetCol("select pkgHandle from Packages");
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
				$packagesTemp[] = $pkg;
			}
			$packages = $packagesTemp;
		}
		return $packages;
	}
	

}