<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_PageRequestView extends RequestView {
	
	protected $c; // the page object we're going to render.
	
	protected $themePaths = array();
	protected $themeHandle;
	protected $theme; // the page theme we're going to render the page in.
	protected $themePkgHandle;

	protected $template;
	

	public function setCollectionObject($page) {
		$this->c = $page;
	}

	public function getCollectionObject() {
		return $this->c;
	}

	public function setPageViewTheme($theme) {
		if (is_object($theme)) {
			$this->themeHandle = $theme->getPageThemeHandle();
			$this->theme = $theme;
		} else {
			$this->themeHandle = $theme;
		}
	}

	protected function setupPageViewThemeObject() {
		if (!is_object($this->theme) && ($this->themeHandle != VIEW_CORE_THEME && $this->themeHandle != 'dashboard')) {
			$this->theme = PageTheme::getByHandle($this->themeHandle);
			$this->themePkgHandle = $this->theme->getPackageHandle();
		}
	}

	protected function setupController() {
		if (!isset($this->controller)) {
			$this->controller = Loader::controller($this->c);
			$this->controller->setupAndRun();
		}
	}

	/** 
	 * Renders the page view
	 */
	public function executeRender() {

		// Our environment library
		$env = Environment::get();

		// The page that we're going to render.
		$c = $this->c;

		// This is the file we're going to ultimately include. The "outer" file.
		$file = false;

		// First the starting gun.
		Events::fire('on_start', $this);

		// Set the theme object that we should use for this requested page.
		// Only run setup if the theme is unset. Usually it will be but if we set it
		// programmatically we already have a theme.
		if (!isset($this->themeHandle)) {
			$this->setupPageViewTheme();
		}

		// Now we take the theme handle (which is a string) and we load our theme object into it.
		// We don't ALWAYS have an object, however. Core and Dashboard themes are just strings.
		$this->setupPageViewThemeObject();

		if ($c->getPageTemplateID() == 0 && $c->getCollectionFilename()) {
			// this is a single page. first, we check to see if a file for this particular collection handle
			// exists in the active theme. If it does, we use that as our file.
			$rec = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $c->getCollectionHandle() . '.php', $this->themePkgHandle);
			if ($rec->exists()) {
				$innerContentFile = false;
				$this->setPageViewTemplate($rec->file);
			} else {
				$innerContentFile = $env->getPath(DIRNAME_PAGES . '/' . trim($c->getCollectionFilename(), '/'), $this->themePkgHandle);
				$this->setPageViewTemplate(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle);
			}
		} else {
			// this is a page template in a theme.
			$innerContentFile = false;
			$pt = PageTemplate::getByID($c->getPageTemplateID());
			$this->setPageViewTemplate(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle);
		}

		$this->controller->on_before_render();
		extract($this->controller->getSets());
		extract($this->controller->getHelperObjects());

		// If we are in a single page or something else with an innerContent,
		// we include that and grab that info
		if ($innerContentFile) {
			ob_start();
			include($innerContentFile);
			$innerContent = ob_get_contents();
			ob_end_clean();
		}

		Events::fire('on_before_render', $this);
		if (file_exists($this->template)) {

			$cache = PageCache::getLibrary();
			$shouldAddToCache = $cache->shouldAddToCache($this);
			if ($shouldAddToCache) {
				$cache->outputCacheHeaders($this->c);
			}

			ob_start();
			include($this->template);
			$pageContent = ob_get_contents();
			ob_end_clean();

			$r = Request::get();
			$assets = $r->getRequiredAssetsToOutput();
			
			foreach($assets as $asset) {
				$this->addOutputAsset($asset);
			}			
			
			$pageContent = $this->replaceAssetPlaceholders($pageContent);

			// replace any empty placeholders
			$pageContent = $this->replaceEmptyAssetPlaceholders($pageContent);

			$ret = Events::fire('on_page_output', $pageContent);
			if($ret != '') {
				$pageContent = $ret;
			}

			print $pageContent;

			$cache = PageCache::getLibrary();
			if ($shouldAddToCache) {
				$cache->set($this->c, $pageContent);
			}

			require(DIR_BASE_CORE . '/startup/jobs.php');
			require(DIR_BASE_CORE . '/startup/shutdown.php');
			exit;

		} else {
			throw new Exception(t('File %s not found. All themes need default.php and view.php files in them. Consult concrete5 documentation on how to create these files.', $this->template));
		}

	}


}
