<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 *
 * Groups and lists installed and available pages.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @link http://www.concrete5.org
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 */
class Concrete5_Model_PackageList extends Object {
	
	protected $packages = array();
	
	public function add($pkg) {
		$this->packages[] = $pkg;
	}
	
	public function getPackages() {
		return $this->packages;
	}
	
	public static function export($xml) {
		$packages = PackageList::get()->getPackages();
		$pkgs = $xml->addChild("packages");
		foreach($packages as $pkg) {
			$node = $pkgs->addChild('package');
			$node->addAttribute('handle', $pkg->getPackageHandle());
		}
	}
	
	public static function getHandle($pkgID) {
		if ($pkgID < 1) {
			return false;
		}
		$packageList = CacheLocal::getEntry('packageHandleList', false);
		if (is_array($packageList)) {
			return $packageList[$pkgID];
		}
		
		$packageList = array();
		$db = Loader::db();
		$r = $db->Execute('select pkgID, pkgHandle from Packages where pkgIsInstalled = 1');
		while ($row = $r->FetchRow()) {
			$packageList[$row['pkgID']] = $row['pkgHandle'];
		}
		
		CacheLocal::set('packageHandleList', false, $packageList);
		return $packageList[$pkgID];
	}
	
	public static function refreshCache() {
		CacheLocal::delete('packageHandleList', false);
		CacheLocal::delete('pkgList', 1);
		CacheLocal::delete('pkgList', 0);
	}
	
	public static function get($pkgIsInstalled = 1) {
		$pkgList = CacheLocal::getEntry('pkgList', $pkgIsInstalled);
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
		
		CacheLocal::set('pkgList', $pkgIsInstalled, $list);

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
class Concrete5_Model_Package extends Object {

	protected $DIR_PACKAGES_CORE = DIR_PACKAGES_CORE;
	protected $DIR_PACKAGES = DIR_PACKAGES;
	protected $REL_DIR_PACKAGES_CORE = REL_DIR_PACKAGES_CORE;
	protected $REL_DIR_PACKAGES = REL_DIR_PACKAGES;
	
	public function getRelativePath() {
		$dirp = (is_dir($this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? $this->REL_DIR_PACKAGES : $this->REL_DIR_PACKAGES_CORE;
		return $dirp . '/' . $this->pkgHandle;
	}
	
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
	public function isPackageInstalled() { return $this->pkgIsInstalled;}

	public function getChangelogContents() {
		if (file_exists($this->getPackagePath() . '/CHANGELOG')) {
			$contents = Loader::helper('file')->getContents($this->getPackagePath() . '/CHANGELOG');
			return nl2br(Loader::helper('text')->entities($contents));
		}
		return '';
	}
	
	/**
	 * Returns the currently installed package version.
	 * NOTE: This function only returns a value if getLocalUpgradeablePackages() has been called first!
	 */
	public function getPackageCurrentlyInstalledVersion() {
		return $this->pkgCurrentVersion;
	}
	
	protected $appVersionRequired = '5.0.0';
	protected $pkgAllowsFullContentSwap = false;
	
	const E_PACKAGE_NOT_FOUND = 1;
	const E_PACKAGE_INSTALLED = 2;
	const E_PACKAGE_VERSION = 3;
	const E_PACKAGE_DOWNLOAD = 4;
	const E_PACKAGE_SAVE = 5;
	const E_PACKAGE_UNZIP = 6;
	const E_PACKAGE_INSTALL = 7;
	const E_PACKAGE_MIGRATE_BACKUP = 8;
	const E_PACKAGE_INVALID_APP_VERSION = 20;

	protected $errorText = array();

	public function getApplicationVersionRequired() {
		return $this->appVersionRequired;
	}
	
	public function hasInstallNotes() {
		return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install.php');
	}

	public function hasInstallPostScreen() {
		return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install_post.php');
	}
	
	public function allowsFullContentSwap() {
		return $this->pkgAllowsFullContentSwap;
	}
	
	public function showInstallOptionsScreen() {
		return $this->hasInstallNotes() || $this->allowsFullContentSwap();
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
		if (Database::getDebug() == false) {
			ob_start();
		}
		
		$schema = Database::getADOSChema();		
		$sql = $schema->ParseSchema($xmlFile);
		
		$db->IgnoreErrors($handler);
		
		if (!$sql) {
			$result->message = $db->ErrorMsg();
			return $result;
		}

		$r = $schema->ExecuteSchema();


		if (Database::getDebug() == false) {
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
	
	/**
	 * Loads package translation files into zend translate 
	 * @param string $locale
	 * @param string $key
	 * @return void
	*/
	public function setupPackageLocalization($locale = NULL, $key = NULL) {
		$translate = Localization::getTranslate();
		if (is_object($translate)) {
			$path = $this->getPackagePath() . '/' . DIRNAME_LANGUAGES;
			if(!isset($locale) || !strlen($locale)) {
				$locale = ACTIVE_LOCALE;
			}
			
			if(!isset($key)) {
				$key = $locale;
			}
			
			if (file_exists($path . '/' . $locale . '/LC_MESSAGES/messages.mo')) {
				$translate->addTranslation($path . '/' . $locale . '/LC_MESSAGES/messages.mo', $key);
			}
		}
	}
	
	/** 
	 * Returns an array of package items (e.g. blocks, themes)
	 */
	public function getPackageItems() {
		$items = array();
		Loader::model('single_page');
		Loader::library('mail/importer');
		Loader::model('job');
		Loader::model('collection_types');
		Loader::model('system/captcha/library');
		Loader::model('system/antispam/library');
		$items['attribute_categories'] = AttributeKeyCategory::getListByPackage($this);
		$items['permission_categories'] = PermissionKeyCategory::getListByPackage($this);
		$items['permission_access_entity_types'] = PermissionAccessEntityType::getListByPackage($this);
		$items['attribute_keys'] = AttributeKey::getListByPackage($this);
		$items['attribute_sets'] = AttributeSet::getListByPackage($this);
		$items['group_sets'] = GroupSet::getListByPackage($this);
		$items['page_types'] = CollectionType::getListByPackage($this);
		$items['mail_importers'] = MailImporter::getListByPackage($this);
		$items['configuration_values'] = Config::getListByPackage($this);
		$items['block_types'] = BlockTypeList::getByPackage($this);
		$items['page_themes'] = PageTheme::getListByPackage($this);
		$items['permissions'] = PermissionKey::getListByPackage($this);
		$items['single_pages'] = SinglePage::getListByPackage($this);
		$items['attribute_types'] = AttributeType::getListByPackage($this);		
		$items['captcha_libraries'] = SystemCaptchaLibrary::getListByPackage($this);		
		$items['antispam_libraries'] = SystemAntispamLibrary::getListByPackage($this);		
		$items['jobs'] = Job::getListByPackage($this);		
		$items['workflow_types'] = WorkflowType::getListByPackage($this);		
		ksort($items);
		return $items;
	}
	
	public static function getItemName($item) {
		$txt = Loader::helper('text');
		Loader::model('single_page');
		if ($item instanceof BlockType) {
			return t($item->getBlockTypeName());
		} else if ($item instanceof PageTheme) {
			return $item->getThemeName();
		} else if ($item instanceof CollectionType) {
			return $item->getCollectionTypeName();
		} else if ($item instanceof MailImporter) {
			return $item->getMailImporterName();		
		} else if ($item instanceof SinglePage) {
			return $item->getCollectionPath();
		} else if ($item instanceof AttributeType) {
			return tc('AttributeTypeName', $item->getAttributeTypeName());
		} else if ($item instanceof PermissionAccessEntityType) {
			return tc('PermissionAccessEntityTypeName', $item->getAccessEntityTypeName());
		} else if ($item instanceof PermissionKeyCategory) {
			return $txt->unhandle($item->getPermissionKeyCategoryHandle());
		} else if ($item instanceof AttributeKeyCategory) {
			return $txt->unhandle($item->getAttributeKeyCategoryHandle());
		} else if ($item instanceof AttributeSet) {
			$at = AttributeKeyCategory::getByID($item->getAttributeSetKeyCategoryID());
			return t('%s (%s)', tc('AttributeSetName', $item->getAttributeSetName()), $txt->unhandle($at->getAttributeKeyCategoryHandle()));
		} else if ($item instanceof GroupSet) {
			return $item->getGroupSetNAme();
		} else if (is_a($item, 'AttributeKey')) {
			$akc = AttributeKeyCategory::getByID($item->getAttributeKeyCategoryID());
			return t(' %s (%s)', $txt->unhandle($item->getAttributeKeyHandle()), $txt->unhandle($akc->getAttributeKeyCategoryHandle()));
		} else if ($item instanceof ConfigValue) {
			return ucwords(strtolower($txt->unhandle($item->key)));
		} else if ($item instanceof SystemAntispamLibrary) {
			return $item->getSystemAntispamLibraryName();
		} else if (is_a($item, 'PermissionKey')) {
			return tc('PermissionKeyName', $item->getPermissionKeyName());			
		} else if (is_a($item, 'Job')) {
			return $item->getJobName();
		} else if (is_a($item, 'WorkflowType')) {
			return $item->getWorkflowTypeName();
		}
	}

	/** 
	 * Uninstalls the package. Removes any blocks, themes, or pages associated with the package.
	 */
	public function uninstall() {
		$db = Loader::db();		
		
		$items = $this->getPackageItems();

		foreach($items as $k => $array) {
			if (!is_array($array)) {
				continue;
			}
			
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
						case 'SystemAntispamLibrary':
							$item->delete();
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
						case 'AttributeKeyCategory':
						case 'PermissionKeyCategory':
						case 'AttributeSet':
						case 'GroupSet':
						case 'AttributeType':
						case 'WorkflowType':
						case 'PermissionKey':
						case 'PermissionAccessEntityType':
							$item->delete();
							break;
						default:
							if(method_exists($item, 'delete')) {
								$item->delete();
							} elseif(method_exists($item, 'uninstall')) {
								$item->uninstall();
							}
							break;
					}
				}
			}
		}
		$db->Execute("delete from Packages where pkgID = ?", array($this->pkgID));
	}
	
	protected function validateClearSiteContents($options) {
		$u = new User();
		if ($u->isSuperUser()) { 
			// this can ONLY be used through the post. We will use the token to ensure that
			$valt = Loader::helper('validation/token');
			if ($valt->validate('install_options_selected', $options['ccm_token'])) {
				return true;	
			}
		}
		return false;
	}
	
	public function swapContent($options) {
		if ($this->validateClearSiteContents($options)) { 
			Loader::model("page_list");
			Loader::model("file_list");
			Loader::model("stack/list");

			$pl = new PageList();
			$pages = $pl->get();
			foreach($pages as $c) {
				$c->delete();
			}
			
			$fl = new FileList();
			$files = $fl->get();
			foreach($files as $f) {
				$f->delete();
			}
			
			// clear stacks
			$sl = new StackList();
			foreach($sl->get() as $c) {
				$c->delete();
			}
			
			$home = Page::getByID(HOME_CID);
			$blocks = $home->getBlocks();
			foreach($blocks as $b) {
				$b->deleteBlock();
			}
			
			$pageTypes = CollectionType::getList();
			foreach($pageTypes as $ct) {
				$ct->delete();
			}
			
			// now we add in any files that this package has
			if (is_dir($this->getPackagePath() . '/content_files')) {
				Loader::library('file/importer');
				$fh = new FileImporter();
				$contents = Loader::helper('file')->getDirectoryContents($this->getPackagePath() . '/content_files');
		
				foreach($contents as $filename) {
					$f = $fh->import($this->getPackagePath() . '/content_files/' . $filename, $filename);
				}
			}	
			
			// now we parse the content.xml if it exists.
			Loader::library('content/importer');
			$ci = new ContentImporter();
			$ci->importContentFile($this->getPackagePath() . '/content.xml');

		}
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
		$errorText[Package::E_PACKAGE_VERSION] = t("This package requires concrete5 version %s or greater.");
		$errorText[Package::E_PACKAGE_DOWNLOAD] = t("An error occurred while downloading the package.");
		$errorText[Package::E_PACKAGE_SAVE] = t("concrete5 was not able to save the package after download.");
		$errorText[Package::E_PACKAGE_UNZIP] = t('An error occurred while trying to unzip the package.');
		$errorText[Package::E_PACKAGE_INSTALL] = t('An error occurred while trying to install the package.');
		$errorText[Package::E_PACKAGE_MIGRATE_BACKUP] = t('Unable to backup old package directory to %s', DIR_FILES_TRASH);
		$errorText[Package::E_PACKAGE_INVALID_APP_VERSION] = t('This package isn\'t currently available for this version of concrete5. Please contact the maintainer of this package for assistance.');
		
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
		$dirp = (is_dir($this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? $this->DIR_PACKAGES : $this->DIR_PACKAGES_CORE;
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
	public function install() {
		PackageList::refreshCache();
		$db = Loader::db();
		$dh = Loader::helper('date');
		$v = array($this->getPackageName(), $this->getPackageDescription(), $this->getPackageVersion(), $this->getPackageHandle(), 1, $dh->getSystemDateTime());
		$db->query("insert into Packages (pkgName, pkgDescription, pkgVersion, pkgHandle, pkgIsInstalled, pkgDateInstalled) values (?, ?, ?, ?, ?, ?)", $v);
		
		$pkg = Package::getByID($db->Insert_ID());
		Package::installDB($pkg->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
		$env = Environment::get();
		$env->clearOverrideCache();
		return $pkg;
	}
	
	public function updateAvailableVersionNumber($vNum) {
		$db = Loader::db();
		$v = array($vNum, $this->getPackageID());
		$db->query("update Packages set pkgAvailableVersion = ? where pkgID = ?", $v);
	}
	
	public function upgradeCoreData() {
		$db = Loader::db();
		$p1 = Loader::package($this->getPackageHandle());
		$v = array($p1->getPackageName(), $p1->getPackageDescription(), $p1->getPackageVersion(), $this->getPackageID());
		$db->query("update Packages set pkgName = ?, pkgDescription = ?, pkgVersion = ? where pkgID = ?", $v);
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
