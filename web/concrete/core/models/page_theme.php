<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 *
 * A page theme editable style object corresponds to a style in a stylesheet that is able to be manipulated through the dashboard.
 * @package Pages
 * @subpackage Themes
 */
class Concrete5_Model_PageThemeEditableStyle extends Object {
	
	const TSTYPE_COLOR = 1;
	const TSTYPE_FONT = 10;
	const TSTYPE_CUSTOM = 20;
	
	public function getHandle() {return $this->ptsHandle;}
	public function getOriginalValue() {return $this->ptsOriginalValue;}
	public function getValue() {return $this->ptsValue;}
	public function getProperty() {
		// the original property that the stylesheet defines, like background-color, etc...
		return $this->ptsProperty;
	}
	
	public function getType() {return $this->ptsType;}
	public function getName() {
		$h = Loader::helper('text');
		return $h->unhandle($this->ptsHandle);
	}
	
	public function __construct($value = '') {
		if ($value) {
			$this->ptsValue = trim($value);
			$this->ptsOriginalValue = trim($value);
			$this->ptsProperty = substr($this->ptsValue, 0, strpos($this->ptsValue, ':'));
			$this->ptsValue = substr($this->ptsValue, strpos($this->ptsValue, ':') + 1);
			$this->ptsValue = trim(str_replace(';', '', $this->ptsValue));
		}
	}
}

/** 
 * A class specifically for editable fonts
 */
class Concrete5_Model_PageThemeEditableStyleFont extends Concrete5_Model_PageThemeEditableStyle {
	
	public function getFamily() {return $this->family;}
	public function getSize() {return $this->size;}
	public function getWeight() {return $this->weight;}
	public function getStyle() {return $this->style;}
	
	public function __construct($value) {
		parent::__construct($value);
		
		// value is pretty rigid. Has to be "font: normal normal 18px Book Antiqua"
		// so font: $weight $
		
		$expl = explode(' ', $this->ptsValue);
		$this->style = trim($expl[0]);
		$this->weight = trim($expl[1]);
		$this->size = preg_replace('/[^0-9]/', '', trim($expl[2]));
		$this->family = trim($expl[3]);
		if (count($expl) > 4) {
			for ($i = 4; $i < count($expl); $i++) {
				$this->family .= ' ' . trim($expl[$i]);
			}
		}
		
	}
	
	public function getShortValue() {
		return $this->style . '|' . $this->weight . '|' . $this->size . '|' . $this->family;
	}
}


/**
*
* When activating a theme, any file within the theme is loaded into the system as a Page Theme File. At that point
* the file can then be used to create a new page type. 
* @package Pages
* @subpackage Themes
*/
class Concrete5_Model_PageThemeFile {
	
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
class Concrete5_Model_PageTheme extends Object {

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
	const FILENAME_EXTENSION_CSS = "css";
	
	public static function getGlobalList() {
		return PageTheme::getList('pkgID > 0');
	}
	
	public static function getLocalList() {
		return PageTheme::getList('pkgID = 0');
	}

	public static function getListByPackage($pkg) {
		return PageTheme::getList('pkgID = ' . $pkg->getPackageID());
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
		
	public static function getInstalledHandles() {
		$db = Loader::db();
		return $db->GetCol("select ptHandle from PageThemes");
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
				if (!empty($th)) {
					$themesTemp[] = $th;
				}
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
	
	/** 
	 * Looks into the current theme and outputs the contents of the stylesheet.
	 * This function will eventually check to see if a cached version is available, as well as tie the dynamic areas of the stylesheet to whatever they have been saved.
	 * @param string $file
	 */
	public function outputStyleSheet($file, $styles = false) {
		print $this->parseStyleSheet($file, $styles);
	}
	
	public function parseStyleSheet($file, $styles = false) {
		if (file_exists($this->getThemeDirectory() . '/' . $file)) {
			$fh = Loader::helper('file');
			$contents = $fh->getContents($this->getThemeDirectory() . '/' . $file);
			
			// replace all url( instances with url starting with path to theme
			$contents = preg_replace('/(url\(\')([^\)]*)/', '$1' . $this->getThemeURL() . '/$2', $contents);
         	$contents = preg_replace('/(url\(")([^\)]*)/', '$1' . $this->getThemeURL() . '/$2', $contents);
            $contents = preg_replace('/(url\((?![\'"]))([^\)]*)/', '$1' . $this->getThemeURL() . '/$2', $contents);
			$contents = str_replace('url(' . $this->getThemeURL() . '/data:image', 'url(data:image', $contents);

			
			// load up all tokens from the db for this stylesheet.
			// if a replacement style array is passed then we use that instead of the database (as is the case when previewing)
			if (!is_array($styles)) {			
				$db = Loader::db();
				$ptes = $db->GetAll("select ptsHandle, ptsValue, ptsType from PageThemeStyles where ptID = ?", $this->getThemeID());
				$styles = array();
				foreach($ptes as $p) {
					$pts = new PageThemeEditableStyle($p['ptsValue']);
					$pts->setPropertiesFromArray($p);
					$styles[] = $pts;
				}
			}

			$replacements = array();
			$searches = array();
			
			foreach($styles as $p) {
				if ($p->getType() == PageThemeEditableStyle::TSTYPE_CUSTOM) {
					$contents = preg_replace("/\/\*[\s]?customize_" . $p->getHandle() . "[\s]?\*\/(.*)\/\*[\s]?customize_" . $p->getHandle() . "[\s]?\*\//i", 
						"/* customize_" . $p->getHandle() . " */ " . $p->getValue() . " /* customize_" . $p->getHandle() . " */"
					, $contents);	
				} else {
					$contents = preg_replace("/\/\*[\s]?customize_" . $p->getHandle() . "[\s]?\*\/[\s]?" . $p->getProperty() . "(.*)\/\*[\s]?customize_" . $p->getHandle() . "[\s]?\*\//i", 
						"/* customize_" . $p->getHandle() . " */ " . $p->getValue() . " /* customize_" . $p->getHandle() . " */"
					, $contents);				
				}
			}

			return $contents;
		}
	}
	
	
	public function mergeStylesFromPost($post) {
		$values = array();
		$styles = $this->getEditableStylesList();
		foreach($styles as $sto) {
			foreach($sto as $st) {
				$ptes = new PageThemeEditableStyle();
				$ptes->ptsHandle = $st->getHandle();
				$ptes->ptsType = $st->getType();
				$ptes->ptsProperty = $st->getProperty();
				
				switch($st->getType()) {
					case PageThemeEditableStyle::TSTYPE_COLOR:
						if (isset($post['input_theme_style_' . $st->getHandle() . '_' . $st->getType()])) {
							$ptes->ptsValue = $ptes->getProperty() . ':' . $post['input_theme_style_' . $st->getHandle() . '_' . $st->getType()] . ';';
							$values[] = $ptes;
						}
						break;
					case PageThemeEditableStyle::TSTYPE_CUSTOM:
						if (isset($post['input_theme_style_' . $st->getHandle() . '_' . $st->getType()])) {
							$ptes->ptsValue = $post['input_theme_style_' . $st->getHandle() . '_' . $st->getType()];
							$values[] = $ptes;
						}
						break;
					case PageThemeEditableStyle::TSTYPE_FONT:
						if (isset($post['input_theme_style_' . $st->getHandle() . '_' . $st->getType()])) {
							$value = $post['input_theme_style_' . $st->getHandle() . '_' . $st->getType()];
							// now we transform it from it's post, which has pipes and separators and crap
							$fv = explode('|', $value);
							$ptes->ptsValue = $ptes->getProperty() . ':' . $fv[0] . ' ' . $fv[1] . ' ' . $fv[2] . 'px ' . $fv[3] . ';';
							$values[] = $ptes;
						}
						break;
				}
			}
		}
		
		return $values;
	}
	
	
	/** 
	 * Removes any custom styles by clearing them out of the database
	 * @return void
	 */
	public function reset() {
		$db = Loader::db();
		$db->Execute('delete from PageThemeStyles where ptID = ?', array($this->ptID));	
		
		// now we reset all cached css files in this theme
		$sheets = $this->getStyleSheets();
		foreach($sheets as $s) {
			Cache::delete($this->getThemeHandle(), $s);
		}
	}
	
	/** 
	 * Takes an associative array of pagethemeeditablestyle objects and saves it to the PageThemeStyles table
	 * @param array $styles
	 */
	public function saveEditableStyles($styles) {
		$db = Loader::db();
		foreach($styles as $ptes) {
			$db->Replace('PageThemeStyles', array(
				'ptID' => $this->getThemeID(),
				'ptsHandle' => $ptes->getHandle(),
				'ptsValue' => $ptes->getValue(),
				'ptsType' => $ptes->getType()
			),
			array('ptID', 'ptsHandle', 'ptsType'), true);
		}

		// now we reset all cached css files in this theme
		$sheets = $this->getStyleSheets();
		foreach($sheets as $s) {
			Cache::delete($this->getThemeHandle(), $s);
		}
	}
	
	private function getStyleSheets() {
		$fh = Loader::helper('file');
		$files = $fh->getDirectoryContents($this->getThemeDirectory());
		$sheets = array();
		foreach($files as $f) {
			$pos = strrpos($f, '.');
			$ext = substr($f, $pos + 1);
			if ($ext == PageTheme::FILENAME_EXTENSION_CSS) {
				$sheets[] = $f;
			}
		}
		return $sheets;
	}
	
	/** 
	 * Parses the style declaration found in the stylesheet to return the type of editable style
	 */
	private function getEditableStyleType($value) {
		// thx yamanoi
		if (preg_match('/^\s*font\s*:/',$value)) {
			return PageThemeEditableStyle::TSTYPE_FONT;
		}
		if (preg_match('/^\s*([a-z]+-)*color\s*:/',$value)) {
			return PageThemeEditableStyle::TSTYPE_COLOR;
		}
		return PageThemeEditableStyle::TSTYPE_CUSTOM;

	}
	
	/** 
	 * Retrieves an array of editable style objects from the current them. This is accomplished by locating all style sheets in the root of the theme, parsing all their contents
	 * @param string $file
	 * @return array
	 */
	public function getEditableStylesList() {
		$sheets = $this->getStyleSheets();

		$styles = array();
		foreach($sheets as $file) {		
			$ss = $this->parseStyleSheet($file);

			// match all tokens
			$matches = array();
			 
			//REGEX NEW LINE BUG!
			//currently this doesn't capture customize_ tags that span multiple lines
			//you can easily make the .* dot character match multiple lines by adding the "s" modifiers to the end of the expression...
			//but this introduces another bug where you can't have two tags with the same name within a style sheet
			//Any regex wizards out there wanting to give it a shot?
			preg_match_all("/\/\*[\s]?customize_(.*)[\s]?\*\/(.*)\/\*[\s]?customize_\\1[\s]?\*\//isU", $ss, $matches); 
	
			// the format of the $matches array is [1] = the handle of the editable style object, [2] = the value (which we need to trim)
			// handles are unique.
			$handles = $matches[1];
			$values = $matches[2];
			for($i = 0 ; $i < count($handles); $i++) {
				$type = $this->getEditableStyleType($values[$i]);
				if ($type == PageThemeEditableStyle::TSTYPE_FONT) {
					$pte = new PageThemeEditableStyleFont(trim($values[$i]));
				} else {
					$pte = new PageThemeEditableStyle(trim($values[$i]));
				}
				$pte->ptsHandle = trim($handles[$i]);
				$pte->ptsType = $type;
				
				// returns a nested associative array that's
				// $styles[$handle'][] = style 1, $styles[$handle'][] = 'style 2', etc...
				$styles[$pte->ptsHandle][] = $pte;
			}
		}
		return $styles;
	}
	
	/**
	 * @param string $ptHandle
	 * @return PageTheme
	 */
	public function getByHandle($ptHandle) {
		$pt = Cache::get('page_theme_by_handle', $ptHandle);
		if ($pt instanceof PageTheme) {
			return $pt;
		}
		
		$where = 'ptHandle = ?';
		$args = array($ptHandle);
		$pt = PageTheme::populateThemeQuery($where, $args);
		Cache::set('page_theme_by_handle', $ptHandle, $pt);
		return $pt;
	}
	
	/**
	 * @param int $ptID
	 * @return PageTheme
	 */
	public function getByID($ptID) {
		$pt = Cache::get('page_theme_by_id', $ptID);
		if ($pt instanceof PageTheme) {
			return $pt;
		}
		
		$where = 'ptID = ?';
		$args = array($ptID);
		$pt = PageTheme::populateThemeQuery($where, $args);
		Cache::set('page_theme_by_id', $ptID, $pt);
		return $pt;
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
		$res->ptName = t('(No Name)');
		$res->ptDescription = t('(No Description)');
		if (file_exists($dir . '/' . FILENAME_THEMES_DESCRIPTION)) {
			$con = file($dir . '/' . FILENAME_THEMES_DESCRIPTION);
			$res->ptName = trim($con[0]);
			$res->ptDescription = trim($con[1]);	
		}
		return $res;
	}
	
	public static function exportList($xml) {
		$nxml = $xml->addChild('themes');
		$list = PageTheme::getList();
		$pst = PageTheme::getSiteTheme();
		
		foreach($list as $pt) {
			$activated = 0;
			if ($pst->getThemeID() == $pt->getThemeID()) {
				$activated = 1;
			}
			$type = $nxml->addChild('theme');
			$type->addAttribute('handle', $pt->getThemeHandle());
			$type->addAttribute('package', $pt->getPackageHandle());
			$type->addAttribute('activated', $activated);
		}

	}
	
	protected function install($dir, $ptHandle, $pkgID) {
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

			$env = Environment::get();
			$env->clearOverrideCache();
			
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
		$db = Loader::db();

		$r = $db->query("update CollectionVersions inner join Pages on CollectionVersions.cID = Pages.cID left join Packages on Pages.pkgID = Packages.pkgID set CollectionVersions.ptID = ? where cIsTemplate = 0 and (Packages.pkgHandle <> 'core' or pkgHandle is null or CollectionVersions.ctID > 0)", array($this->ptID));
		Cache::flush();
	}
	
	public function getSiteTheme() {
		$c = Page::getByID(HOME_CID);
		return PageTheme::getByID($c->getCollectionThemeID());
	}
	
	public function uninstall() {
		$db = Loader::db();
		Loader::model('page_theme_archive');
		//$pla = new PageThemeArchive($this->ptHandle);
		//$pla->uninstall();
		$db->query("delete from PageThemes where ptID = ?", array($this->ptID));
		Cache::delete('page_theme_by_id', $this->ptID);
		Cache::delete('page_theme_by_handle', $this->ptHandle);
		$env = Environment::get();
		$env->clearOverrideCache();

	}

}
