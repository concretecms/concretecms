<?php 

defined('C5_EXECUTE') or die("Access Denied.");

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
		if ($pkgID < 1) {
			return false;
		}
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
	
	public static function refreshCache() {
		Cache::delete('pkgList', 1);
		Cache::delete('pkgList', 0);
	}
	
	public static function get($pkgIsInstalled = 1) {
		$pkgList = Cache::get('pkgList', $pkgIsInstalled);
		if ($pkgList != false) {
			return $pkgList;
		}
		
		$db = Loader::db();
		$r = $db->query("select pkgID, pkgName, pkgIsInstalled, pkgDescription, pkgVersion, pkgHandle, pkgDateInstalled from Packages where pkgIsInstalled = ? order by pkgID asc", array($pkgIsInstalled));
		$list = new PackageList();
		while ($row = $r->fetchRow()) {
			$pkg = new Package;
			$pkg->setPropertiesFromArray($row);
			$list->add($pkg);
		}
		
		Cache::set('pkgList', $pkgIsInstalled, $list);

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
	
	/**
	 * Gets the date the package was added to the system, 
	 * if user is specified, returns in the current user's timezone
	 * @param string $type (system || user)
	 * @return string date formated like: 2009-01-01 00:00:00 
	*/
	function getPackageDateInstalled($type = 'system') {
		if(ENABLE_USER_TIMEZONES && $type == 'user') {
			$dh = Loader::helper('date');
			return $dh->getLocalDateTime($this->pkgDateInstalled);
		} else {
			return $this->pkgDateInstalled;
		}
	}
	
	public function getPackageVersion() {return $this->pkgVersion;}
	public function getPackageVersionUpdateAvailable() {return $this->pkgAvailableVersion;}
	public function getPackageCurrentlyInstalledVersion() {return $this->pkgCurrentVersion;}
	public function isPackageInstalled() { return $this->pkgIsInstalled;}
	
	protected $appVersionRequired = '5.0.0';
	
	const E_PACKAGE_NOT_FOUND = 1;
	const E_PACKAGE_INSTALLED = 2;
	const E_PACKAGE_VERSION = 3;
	const E_PACKAGE_DOWNLOAD = 4;
	const E_PACKAGE_SAVE = 5;
	const E_PACKAGE_UNZIP = 6;
	const E_PACKAGE_INSTALL = 7;
	const E_PACKAGE_MIGRATE_BACKUP = 8;

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
	
	public function setupPackageLocalization() {
		$translate = Localization::getTranslate();
		if (is_object($translate)) {
			$path = $this->getPackagePath() . '/' . DIRNAME_LANGUAGES;
			if (file_exists($path . '/' . LOCALE . '/LC_MESSAGES/messages.mo')) {
				$translate->addTranslation($path . '/' . LOCALE . '/LC_MESSAGES/messages.mo', LOCALE);
			}
		}
	}
	
	/** 
	 * Returns an array of package items (e.g. blocks, themes)
	 */
	public function getPackageItems() {
		$items = array();
		Loader::model('single_page');
		Loader::model('dashboard/homepage');
		Loader::library('mail/importer');
		Loader::model('job');
		Loader::model('collection_types');
		$items['attribute_categories'] = AttributeKeyCategory::getListByPackage($this);
		$items['attribute_keys'] = AttributeKey::getListByPackage($this);
		$items['attribute_sets'] = AttributeSet::getListByPackage($this);
		$items['page_types'] = CollectionType::getListByPackage($this);
		$items['mail_importers'] = MailImporter::getListByPackage($this);
		$items['dashboard_modules'] = DashboardHomepageView::getModules($this);
		$items['configuration_values'] = Config::getListByPackage($this);
		$items['block_types'] = BlockTypeList::getByPackage($this);
		$items['page_themes'] = PageTheme::getListByPackage($this);
		$tp = new TaskPermissionList();		
		$items['task_permissions'] = $tp->populatePackagePermissions($this);
		$items['single_pages'] = SinglePage::getListByPackage($this);
		$items['attribute_types'] = AttributeType::getListByPackage($this);		
		$items['jobs'] = Job::getListByPackage($this);		
		ksort($items);
		return $items;
	}
	
	public static function getItemName($item) {
		$txt = Loader::helper('text');
		Loader::model('single_page');
		Loader::model('dashboard/homepage');
		if ($item instanceof BlockType) {
			return $item->getBlockTypeName();
		} else if ($item instanceof PageTheme) {
			return $item->getThemeName();
		} else if ($item instanceof CollectionType) {
			return $item->getCollectionTypeName();
		} else if ($item instanceof MailImporter) {
			return $item->getMailImporterName();		
		} else if ($item instanceof SinglePage) {
			return $item->getCollectionPath();
		} else if ($item instanceof AttributeType) {
			return $item->getAttributeTypeName();
		} else if ($item instanceof AttributeKeyCategory) {
			return $txt->unhandle($item->getAttributeKeyCategoryHandle());
		} else if ($item instanceof AttributeSet) {
			$at = AttributeKeyCategory::getByID($item->getAttributeSetKeyCategoryID());
			return t('%s (%s)', $item->getAttributeSetName(), $txt->unhandle($at->getAttributeKeyCategoryHandle()));
		} else if (is_a($item, 'AttributeKey')) {
			$akc = AttributeKeyCategory::getByID($item->getAttributeKeyCategoryID());
			return t(' %s (%s)', $txt->unhandle($item->getAttributeKeyHandle()), $txt->unhandle($akc->getAttributeKeyCategoryHandle()));
		} else if ($item instanceof ConfigValue) {
			return ucwords(strtolower($txt->unhandle($item->key)));
		} else if ($item instanceof DashboardHomepage) {
			return t('%s (%s)', $item->dbhDisplayName, $txt->unhandle($item->dbhModule));
		} else if (is_a($item, 'TaskPermission')) {
			return $item->getTaskPermissionName();			
		} else if (is_a($item, 'Job')) {
			return $item->getJobName();
		}
	}

	/** 
	 * Uninstalls the package. Removes any blocks, themes, or pages associated with the package.
	 */
	public function uninstall() {
		$db = Loader::db();		
		
		$items = $this->getPackageItems();

		foreach($items as $k => $array) {
			foreach($array as $item) {
				if (is_a($item, 'Job')) {
					$item->uninstall();
				} else if (is_a($item, 'AttributeKey') || is_a($item, 'MailImporter')) {
					$item->delete();
				} else {
					switch(get_class($item)) {
						case 'BlockType':
							$item->delete();	
							break;
						case 'PageTheme':
							$item->uninstall();	
							break;
						case 'SinglePage':
							@$item->delete(); // we suppress errors because sometimes the wrapper pages can delete first.
							break;
						case 'CollectionType':
							$item->delete();
							break;
						case 'MailImporter':
							$item->delete();
							break;
						case 'ConfigValue':
							$co = new Config();
							$co->setPackageObject($this);
							$co->clear($item->key);
							break;
						case 'DashboardHomepage':
							$item->Delete();
							break;
						case 'AttributeKeyCategory':
						case 'AttributeSet':
						case 'AttributeType':
						case 'TaskPermission':
							$item->delete();
							break;
					}
				}
			}
		}
		$db->Execute("delete from Packages where pkgID = ?", array($this->pkgID));
		PackageList::refreshCache();
	}
	
	public function testForInstall($package, $testForAlreadyInstalled = true) {
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
		if ($testForAlreadyInstalled) {
			$cnt = $db->getOne("select count(*) from Packages where pkgHandle = ?", array($package));
			if ($cnt > 0) {
				$errors[] = Package::E_PACKAGE_INSTALLED;
			}
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
		$errorText[Package::E_PACKAGE_DOWNLOAD] = t("An error occurred while downloading the package.");
		$errorText[Package::E_PACKAGE_SAVE] = t("Concrete was not able to save the package after download.");
		$errorText[Package::E_PACKAGE_UNZIP] = t('An error occurred while trying to unzip the package.');
		$errorText[Package::E_PACKAGE_INSTALL] = t('An error occurred while trying to install the package.');
		$errorText[Package::E_PACKAGE_MIGRATE_BACKUP] = t('Unable to backup old package directory to %s', DIR_FILES_TRASH);

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
	
	
	/**
	 * returns a Package object for the given package id, null if not found
	 * @param int $pkgID
	 * @return Package
	 */
	public function getByID($pkgID) {
		$db = Loader::db();
		$row = $db->GetRow("select * from Packages where pkgID = ?", array($pkgID));
		if ($row) {
			$pkg = Loader::package($row['pkgHandle']);
			if (is_object($pkg)) {
				$pkg->setPropertiesFromArray($row);
				return $pkg;
			}
		}
	}

	/**
	 * returns a Package object for the given package handle, null if not found
	 * @param string $pkgHandle
	 * @return Package
	 */
	public function getByHandle($pkgHandle) {
		$db = Loader::db();
		$row = $db->GetRow("select * from Packages where pkgHandle = ?", array($pkgHandle));
		if ($row) {
			$pkg = Loader::package($row['pkgHandle']);
			if (is_object($pkg)) {
				$pkg->setPropertiesFromArray($row);
			}
			return $pkg;
		}
	}
	
	/**
	 * @return Package
	 */
	protected function install() {
		$db = Loader::db();
		$dh = Loader::helper('date');
		$v = array($this->getPackageName(), $this->getPackageDescription(), $this->getPackageVersion(), $this->getPackageHandle(), 1, $dh->getSystemDateTime());
		$db->query("insert into Packages (pkgName, pkgDescription, pkgVersion, pkgHandle, pkgIsInstalled, pkgDateInstalled) values (?, ?, ?, ?, ?, ?)", $v);
		
		$pkg = Package::getByID($db->Insert_ID());
		Package::installDB($pkg->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
		PackageList::refreshCache();
		
		return $pkg;
	}
	
	public function updateAvailableVersionNumber($vNum) {
		$db = Loader::db();
		$v = array($vNum, $this->getPackageID());
		$db->query("update Packages set pkgAvailableVersion = ? where pkgID = ?", $v);
		PackageList::refreshCache();
	}
	
	public function upgradeCoreData() {
		$db = Loader::db();
		$p1 = Loader::package($this->getPackageHandle());
		$v = array($p1->getPackageName(), $p1->getPackageDescription(), $p1->getPackageVersion(), $this->getPackageID());
		$db->query("update Packages set pkgName = ?, pkgDescription = ?, pkgVersion = ? where pkgID = ?", $v);
		PackageList::refreshCache();
	}
	
	public function upgrade() {
		Package::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB);		
		// now we refresh all blocks
		$items = $this->getPackageItems();
		if (is_array($items['block_types'])) {
			foreach($items['block_types'] as $item) {
				$item->refresh();
			}
		}
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
	
	/** 
	 * Returns an array of packages that have newer versions in the local packages directory
	 * than those which are in the Packages table. This means they're ready to be upgraded
	 */
	public static function getLocalUpgradeablePackages() {
		$packages = Package::getAvailablePackages(false);
		$upgradeables = array();
		$db = Loader::db();
		foreach($packages as $p) {
			$row = $db->GetRow("select pkgID, pkgVersion from Packages where pkgHandle = ? and pkgIsInstalled = 1", array($p->getPackageHandle()));
			if ($row['pkgID'] > 0) { 
				if (version_compare($p->getPackageVersion(), $row['pkgVersion'], '>')) {
					$p->pkgCurrentVersion = $row['pkgVersion'];
					$upgradeables[] = $p;
				}		
			}
		}
		return $upgradeables;		
	}

	public static function getRemotelyUpgradeablePackages() {
		$packages = Package::getInstalledList();
		$upgradeables = array();
		$db = Loader::db();
		foreach($packages as $p) {
			if (version_compare($p->getPackageVersion(), $p->getPackageVersionUpdateAvailable(), '<')) {
				$upgradeables[] = $p;
			}
		}
		return $upgradeables;		
	}	
	
	public function backup() {
		// you can only backup root level packages.
		// Need to figure something else out for core level
		if ($this->pkgHandle != '' && is_dir(DIR_PACKAGES . '/' . $this->pkgHandle)) {
			$ret = @rename(DIR_PACKAGES . '/' . $this->pkgHandle, DIR_FILES_TRASH . '/' . $this->pkgHandle . '_' . date('YmdHis'));
			if (!$ret) {
				return array(Package::E_PACKAGE_MIGRATE_BACKUP);
			}
		}
	}


	public function config($cfKey, $getFullObject = false) {
		$co = new Config();
		$co->setPackageObject($this);
		return $co->get($cfKey, $getFullObject);
	}
	
	public function saveConfig($cfKey, $value) {
		$co = new Config();
		$co->setPackageObject($this);
		return $co->save($cfKey, $value);
	}

	public function clearConfig($cfKey) {
		$co = new Config();
		$co->setPackageObject($this);
		return $co->clear($cfKey);
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
