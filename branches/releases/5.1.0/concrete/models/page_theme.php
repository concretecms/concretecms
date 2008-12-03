<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
*
* When activating a theme, any file within the theme is loaded into the system as a Page Theme File. At that point
* the file can then be used to create a new page type. 
* @package Pages
* @subpackage Themes
*/
class PageThemeFile {
	
	protected $filename;
	protected $type;
	
	/**
	 * Type of page corresponding to the view template (used by single pages in this theme). Typically that means this template file is "view.php"
	 */
	const TFTYPE_VIEW = 1;
	
	/**
	 * Type of page corresponding to the default page type. If a page type doesn't have a template in a particular theme, default is used. 
	 */
	const TFTYPE_DEFAULT = 2;

	/**
	 * If this is used to designate what type of template this is, this means it corresponds to a single page like "login.php"
	 */
	const TFTYPE_SINGLE_PAGE = 3;
	
	/**
	 * This is a template for a new page type - one that hasn't been previously created in the system.
	 */
	const TFTYPE_PAGE_TYPE_NEW = 4;
	
	/**
	 * This is a template for a page type that already exists in the system.
	 */
	const TFTYPE_PAGE_TYPE_EXISTING = 5;
	
	/** 
	 * Sets the filename of this object to the passed parameter.
	 * @params string $filename
	 * @return void
	 */
	public function setFilename($filename) { $this->filename = $filename;}
	
	/**
	 * Sets the type of file for this object to one of the constants.
	 * @params string $type
	 * @return void
	 */
	public function setType($type) { $this->type = $type; }
	
	
	/** 
	 * Gets the filename for this theme file object.
	 * @return string $filename
	 */
	public function getFilename() { return $this->filename;}
	
	/**
	 * Gets the type of file for this object.
	 * @return string $type
	 */
	public function getType() {return $this->type;}
	
	/**
	 * Returns just the part of the filename prior to the extension
	 * @return string $handle
	 */
	public function getHandle() {
		return substr($this->filename, 0, strpos($this->filename, '.'));
	}
	
}

/**
*
* A page's theme is a pointer to a directory containing templates, CSS files and optionally PHP includes, images and JavaScript files. 
* Themes inherit down the tree when a page is added, but can also be set at the site-wide level (thereby overriding any previous choices.) 
* @package Pages and Collections
* @subpackages Themes
*/
class PageTheme extends Object {

	protected $ptName;
	protected $ptID;
	protected $ptDescription;
	protected $ptDirectory;
	protected $ptThumbnail;
	protected $ptHandle;
	protected $ptURL;
	
	const E_THEME_INSTALLED = 1;
	const THEME_EXTENSION = ".php";
	const FILENAME_TYPOGRAPHY_CSS = "typography.css";	
	
	public static function getGlobalList() {
		return PageTheme::getList('pkgID > 0');
	}
	
	public static function getLocalList() {
		return PageTheme::getList('pkgID = 0');
	}
	
	public static function getList($where = null) {
		if ($where != null) {
			$where = ' where ' . $where;
		}
		
		$db = Loader::db();
		$r = $db->query("select ptID from PageThemes" . $where);
		$themes = array();
		while ($row = $r->fetchRow()) {
			$pl = PageTheme::getByID($row['ptID']);
			$themes[] = $pl;
		}
		return $themes;
	}
		
	public static function getAvailableThemes($filterInstalled = true) {
		// scans the directory for available themes. For those who don't want to go through
		// the hassle of uploading
		
		$db = Loader::db();
		$dh = Loader::helper('file');
		
		$themes = $dh->getDirectoryContents(DIR_FILES_THEMES);
		if ($filterInstalled) {
			// strip out themes we've already installed
			$handles = $db->GetCol("select ptHandle from PageThemes");
			$themesTemp = array();
			foreach($themes as $t) {
				if (!in_array($t, $handles)) {
					$themesTemp[] = $t;
				}
			}
			$themes = $themesTemp;
		}
		
		if (count($themes) > 0) {
			$themesTemp = array();
			// get theme objects from the file system
			foreach($themes as $t) {
				$th = PageTheme::getByFileHandle($t);
				$themesTemp[] = $th;
			}
			$themes = $themesTemp;
		}
		return $themes;
			
	}
	
	public static function getByFileHandle($handle, $dir = DIR_FILES_THEMES) {
		$dirt = $dir . '/' . $handle;
		if (is_dir($dirt)) {
			$res = PageTheme::getThemeNameAndDescription($dirt);
	
			$th = new PageTheme;
			$th->ptHandle = $handle;
			$th->ptDirectory = $dirt;
			$th->ptName = $res->ptName;
			$th->ptDescription = $res->ptDescription;	
			switch($dir) {
				case DIR_FILES_THEMES:
					$th->ptURL = BASE_URL . DIR_REL . '/' . DIRNAME_THEMES . '/' . $handle;
					break;
			}
			return $th;
		}
	}
	
	public function getByHandle($ptHandle) {
		$where = 'ptHandle = ?';
		$args = array($ptHandle);
		return PageTheme::populateThemeQuery($where, $args);
	}
	
	public function getByID($ptID) {
		$where = 'ptID = ?';
		$args = array($ptID);
		return PageTheme::populateThemeQuery($where, $args);
	}
	
	protected function populateThemeQuery($where, $args) {
		$db = Loader::db();
		$row = $db->GetRow("select ptID, ptHandle, ptDescription, pkgID, ptName from PageThemes where {$where}", $args);
		if ($row['ptID']) {
			$pl = new PageTheme;
			$pl->setPropertiesFromArray($row);
			$pkgHandle = $pl->getPackageHandle();
			
			if ($row['pkgID'] > 0) {
				if (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) {
					$pl->ptDirectory = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_THEMES . '/' . $row['ptHandle'];
					$url = BASE_URL . DIR_REL;
				} else {
					$pl->ptDirectory = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_THEMES . '/' . $row['ptHandle'];
					$url = ASSETS_URL;
				}
				$pl->ptURL = $url . '/' . DIRNAME_PACKAGES  . '/' . $pkgHandle . '/' . DIRNAME_THEMES . '/' . $row['ptHandle'];
			} else if (is_dir(DIR_FILES_THEMES . '/' . $row['ptHandle'])) {
				$pl->ptDirectory = DIR_FILES_THEMES . '/' . $row['ptHandle'];
				$pl->ptURL = BASE_URL . DIR_REL . '/' . DIRNAME_THEMES . '/' . $row['ptHandle'];
			} else {
				$pl->ptDirectory = DIR_FILES_THEMES_CORE . '/' . $row['ptHandle'];
				$pl->ptURL = ASSETS_URL . '/' . DIRNAME_THEMES . '/' . $row['ptHandle'];
			}
			return $pl;
		}
	}
	
	public function add($ptHandle, $pkg = null) {
		if (is_object($pkg)) {
			if (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle())) {
				$dir = DIR_PACKAGES . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $ptHandle;
			} else {
				$dir = DIR_PACKAGES_CORE . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $ptHandle;
			}
			$pkgID = $pkg->getPackageID();
		} else if (is_dir(DIR_FILES_THEMES . '/' . $ptHandle)) {
			$dir = DIR_FILES_THEMES . '/' . $ptHandle;
			$pkgID = 0;
		} else {
			$dir = DIR_FILES_THEMES_CORE . '/' . $ptHandle;
			$pkgID = 0;
		}
		$l = PageTheme::install($dir, $ptHandle, $pkgID);
		return $l;
	}
	
	// grabs all files in theme that are PHP based (or html if we go that route) and then
	// lists them out, by type, allowing people to install them as page type, etc...
	public function getFilesInTheme() {
		Loader::model('collection_types');
		Loader::model('single_page');
		
		$dh = Loader::helper('file');
		$ctlist = CollectionType::getList();
		$cts = array();
		foreach($ctlist as $ct) {
			$cts[] = $ct->getCollectionTypeHandle();
		}
		
		$filesTmp = $dh->getDirectoryContents($this->ptDirectory);
		foreach($filesTmp as $f) {
			if (strrchr($f, '.') == PageTheme::THEME_EXTENSION) {
				$fHandle = substr($f, 0, strpos($f, '.'));
				
				if ($f == FILENAME_THEMES_VIEW) {
					$type = PageThemeFile::TFTYPE_VIEW;
				} else if ($f == FILENAME_THEMES_DEFAULT) {
					$type = PageThemeFile::TFTYPE_DEFAULT;
				} else if (in_array($f, SinglePage::getThemeableCorePages())) {
					$type = PageThemeFile::TFTYPE_SINGLE_PAGE;
				} else if (in_array($fHandle, $cts)) {
					$type = PageThemeFile::TFTYPE_PAGE_TYPE_EXISTING;
				} else {
					$type = PageThemeFile::TFTYPE_PAGE_TYPE_NEW;
				}
				
				$pf = new PageThemeFile();
				$pf->setFilename($f);
				$pf->setType($type);
				$files[] = $pf;
			}
		}
		
		return $files;
	}
	
	private static function getThemeNameAndDescription($dir) {
		$res = new stdClass;
		$res->ptName = '(No Name)';
		$res->ptDescription = '(No Description)';
		if (file_exists($dir . '/' . FILENAME_THEMES_DESCRIPTION)) {
			$con = file($dir . '/' . FILENAME_THEMES_DESCRIPTION);
			$res->ptName = $con[0];
			$res->ptDescription = $con[1];	
		}
		return $res;
	}
	
	private function install($dir, $ptHandle, $pkgID) {
		if (is_dir($dir)) {
			$db = Loader::db();
			$cnt = $db->getOne("select count(ptID) from PageThemes where ptHandle = ?", array($ptHandle));
			if ($cnt > 0) {
				throw new Exception(PageTheme::E_THEME_INSTALLED);
			}
			$res = PageTheme::getThemeNameAndDescription($dir);
			$ptName = $res->ptName;
			$ptDescription = $res->ptDescription;
			$db->query("insert into PageThemes (ptHandle, ptName, ptDescription, pkgID) values (?, ?, ?, ?)", array($ptHandle, $ptName, $ptDescription, $pkgID));
			
			return PageTheme::getByID($db->Insert_ID());
		}
	}
	
	public function getThemeID() {return $this->ptID;}
	public function getThemeName() {return $this->ptName;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	public function getThemeHandle() {return $this->ptHandle;}
	public function getThemeDescription() {return $this->ptDescription;}
	public function getThemeDirectory() {return $this->ptDirectory;}
	public function getThemeURL() {return $this->ptURL;}
	public function getThemeEditorCSS() {return $this->ptURL . '/' . PageTheme::FILENAME_TYPOGRAPHY_CSS;}
	public function isUninstallable() {
		return ($this->ptDirectory != DIR_FILES_THEMES_CORE . '/' . $this->getThemeHandle());
	}
	public function getThemeThumbnail() {
		if (file_exists($this->ptDirectory . '/' . FILENAME_THEMES_THUMBNAIL)) {
			$src = $this->ptURL . '/' . FILENAME_THEMES_THUMBNAIL;
		} else {
			$src = ASSETS_URL_THEMES_NO_THUMBNAIL;
		}
		$h = Loader::helper('html');
		$img = $h->image($src, THEMES_THUMBNAIL_WIDTH, THEMES_THUMBNAIL_HEIGHT, array('class' => 'ccm-icon-theme'));
		return $img;
	}
	
	public function applyToSite() {
		// applies the current theme to the entire site by overriding the theme on the home page
		$db = Loader::db();
		$r = $db->query("update Pages left join Packages on Pages.pkgID = Packages.pkgID set Pages.ptID = ? where cIsTemplate = 0 and (Packages.pkgHandle <> 'core' or pkgHandle is null or Pages.ctID > 0)", array($this->ptID));
	}
	
	public function getSiteTheme() {
		// returns cLayout on the home collection
		$db = Loader::db();
		$r = $db->getOne("select ptID from Pages where cID = 1");
		return PageTheme::getByID($r);
	}
	
	public function uninstall() {
		$db = Loader::db();
		Loader::model('page_theme_archive');
		$pla = new PageThemeArchive($this->ptHandle);
		$pla->uninstall();
		$db->query("delete from PageThemes where ptID = ?", array($this->ptID));
		
	}

}