<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_PageRequestView extends PathRequestView {

	protected $c; // page

	public function getPageObject() {
		return $this->c;
	}

	/** 
	 * Begin the render
	 */
	public function start($page) {
		$this->c = $page;
		parent::start($page->getCollectionPath());
	}

	public function getScopeItems() {
		$items = parent::getScopeItems();
		$items['c'] = $this->c;
		return $items;
	}

	protected function setupController() {
		if (!isset($this->controller)) {
			$this->controller = Loader::controller($this->c);
		}
	}

	protected function loadRequestViewThemeObject() {
		$theme = $this->c->getCollectionThemeObject();
		if (is_object($theme)) {
			$this->themeHandle = $theme->getThemeHandle();
		}
		parent::loadRequestViewThemeObject();
	}

	public function setupRender() {
		$this->loadRequestViewThemeObject();
		$env = Environment::get();
		if ($this->c->getPageTypeID() == 0 && $this->c->getCollectionFilename()) {
			$cFilename = trim($this->c->getCollectionFilename(), '/');
			// if we have this exact template in the theme, we use that as the outer wrapper and we don't do an inner content file
			$r = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $cFilename);
			if ($r->exists()) {
				$this->setViewTemplate($r->file);
			} else {
				if (file_exists(DIR_FILES_THEMES_CORE . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php')) {
					$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php'));
				} else {
					$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));
				}
				$this->setInnerContentFile($env->getPath(DIRNAME_PAGES . '/' . $cFilename, $this->c->getPackageHandle()));
			}
		} else {
			$pt = PageTemplate::getByID($this->c->getPageTemplateID());
			$rec = $env->getRecord(DIRNAME_PAGE_TYPES . '/' . $this->c->getPageTypeHandle() . '.php', $this->themePkgHandle);
			if ($rec->exists()) {
				$this->setInnerContentFile($env->getPath(DIRNAME_PAGES . '/' . $cFilename, $this->themePkgHandle));
				$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));
			} else {
				$rec = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle);
				if ($rec->exists()) {
					$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle));
				} else {
					$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_DEFAULT, $this->themePkgHandle));
				}
			}
		}

	}

	public function deliverRender($contents) {
		$cache = PageCache::getLibrary();
		$shouldAddToCache = $cache->shouldAddToCache($this);
		if ($shouldAddToCache) {
			$cache->outputCacheHeaders($this->c);
			$cache->set($this->c, $contents);
		}
		print $contents;
	}

	/** 
	 * Returns a stylesheet found in a themes directory - but FIRST passes it through the tools CSS handler
	 * in order to make certain style attributes found inside editable
	 * @param string $stylesheet
	 */
	public function getStyleSheet($stylesheet) {
		$file = $this->getThemePath() . '/' . $stylesheet;
		$cacheFile = DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle . '/' . $stylesheet;
		$env = Environment::get();
		$themeRec = $env->getUncachedRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $stylesheet, $this->themePkgHandle);
		if (file_exists($cacheFile) && $themeRec->exists()) {
			if (filemtime($cacheFile) > filemtime($themeRec->file)) {
				return REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle . '/' . $stylesheet;
			}
		}
		if ($themeRec->exists()) {
			$themeFile = $themeRec->file;
			if (!file_exists(DIR_FILES_CACHE . '/' . DIRNAME_CSS)) {
				@mkdir(DIR_FILES_CACHE . '/' . DIRNAME_CSS);
			}
			if (!file_exists(DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle)) {
				@mkdir(DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle);
			}
			$fh = Loader::helper('file');
			$stat = filemtime($themeFile);
			if (!file_exists(dirname($cacheFile))) {
				@mkdir(dirname($cacheFile), DIRECTORY_PERMISSIONS_MODE, true);
			}
			$style = $this->themeObject->parseStyleSheet($stylesheet);
			$r = @file_put_contents($cacheFile, $style);
			if ($r) {
				return REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle . '/' . $stylesheet;
			} else {
				return $this->getThemePath() . '/' . $stylesheet;
			}
		}
	}

	/** 
	 * @deprecated
	 */
	public function getCollectionObject() {return $this->getPageObject();}
	public function section($url) {
		if (!empty($this->viewPath)) {
			$url = '/' . trim($url, '/');
			if (strpos($this->viewPath, $url) !== false && strpos($this->viewPath, $url) == 0) {
				return true;
			}
		}
	}

}