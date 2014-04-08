<?
namespace Concrete\Core\Page\View;
use Loader;
use View;
use Environment;
use \Concrete\Core\Page\Template as PageTemplate;
use Permissions;
use \Concrete\Core\Cache\Page\PageCache;

class PageView extends View {

	protected $c; // page
	protected $cp;
	protected $pTemplateID;
	protected $customStyleMap;

	public function getPageObject() {
		return $this->c;
	}

	protected function constructView($page) {
		$this->c = $page;
		parent::constructView($page->getCollectionPath());
		if (!isset($this->pTemplateID)) {
			$this->pTemplateID = $this->c->getPageTemplateID();
		}
		if (!isset($this->pThemeID)) {
			$this->pThemeID = $this->c->getPageTemplateID();
		}
	}

	public function getScopeItems() {
		$items = parent::getScopeItems();
		$items['c'] = $this->c;
		return $items;
	}

	/** 
	 * Called from previewing functions, this lets us override the page's template with one of our own choosing
	 */
	public function setCustomPageTemplate(PageTemplate $pt) {
		$this->pTemplateID = $pt->getPageTemplateID();
	}

	/** 
	 * Called from previewing functions, this lets us override the page's theme with one of our own choosing
	 */
	public function setCustomPageTheme(PageTheme $pt) {
		$this->themeHandle = $pt->getThemeHandle();
	}

	public function setupRender() {
		$this->loadViewThemeObject();
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
			$pt = PageTemplate::getByID($this->pTemplateID);
			$rec = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle);
			if ($rec->exists()) {
				$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle));
			} else {
				$rec = $env->getRecord(DIRNAME_PAGE_TYPES . '/' . $this->c->getPageTypeHandle() . '.php', $this->themePkgHandle);
				if ($rec->exists()) {
					$this->setInnerContentFile($env->getPath(DIRNAME_PAGE_TYPES . '/' . $this->c->getPageTypeHandle() . '.php', $this->themePkgHandle));
					$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));
				} else {
					$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_DEFAULT, $this->themePkgHandle));
				}
			}
		}
	}

	public function startRender() {
		parent::startRender();
		$this->c->outputCustomStyleHeaderItems();
		// do we have any custom menu plugins?
		$cp = new Permissions($this->c);
		$this->cp = $cp;
		if ($cp->canViewToolbar()) { 
			$dh = Loader::helper('concrete/dashboard');
			if (!$dh->inDashboard() && $this->c->getCollectionPath() != '/page_not_found' && $this->c->isActive() && !$this->c->isMasterCollection()) {
				$u = new User();
				$u->markPreviousFrontendPage($this->c);
			}
			$ih = Loader::helper('concrete/ui/menu');
			$interfaceItems = $ih->getPageHeaderMenuItems();
			foreach($interfaceItems as $item) {
				$controller = $item->getController();
				$controller->outputAutoHeaderItems();
			}
		}
	}

	public function finishRender($contents) {
		parent::finishRender($contents);
		$cache = PageCache::getLibrary();
		$shouldAddToCache = $cache->shouldAddToCache($this);
		if ($shouldAddToCache) {
			$cache->outputCacheHeaders($this->c);
			$cache->set($this->c, $contents);
		}
		return $contents;
	}

	/** 
	 * Takes an array of original stylesheets => temporary preview stylesheets. Used by theme customizer
	 */
	public function setCustomStyleMap($styleMap) {
		$this->customStyleMap = $styleMap;
	}

	/** 
	 * Returns a stylesheet found in a themes directory - but FIRST passes it through the tools CSS handler
	 * in order to make certain style attributes found inside editable
	 * @param string $stylesheet
	 */
	public function getStyleSheet($stylesheet) {
		if (isset($this->customStyleMap) && array_key_exists($stylesheet, $this->customStyleMap)) {
			// this is used by preview
			return REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle . '/' . $this->customStyleMap[$stylesheet];
		}
		if ($this->cp->canViewPageVersions() && $this->c->hasPageThemeCustomizations()) {
			return URL::to('/system/css/page', $this->c->getCollectionID(), $this->c->getVersionID(), $stylesheet);
		}
		$env = Environment::get();
		$themeRec = $env->getUncachedRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $stylesheet, $this->themePkgHandle);
		$themeRecExists = $themeRec->exists();

		$pageCacheFile = DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle . '/' . $this->c->getCollectionID() . '/' . $stylesheet;
		if (file_exists($pageCacheFile) && $themeRecExists) {
			if (filemtime($pageCacheFile) > filemtime($pageCacheFile->file)) {
				return REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle . '/' . $this->c->getCollectionID() . '/' . $stylesheet;
			}
		}

		$cacheFile = DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->themeHandle . '/' . $stylesheet;
		if (file_exists($cacheFile) && $themeRecExists) {
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