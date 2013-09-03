<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_RequestView extends View {

	protected $controller;
	protected $viewPath;
	protected $innerContentFile;

	private static $instance;

	public static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	protected function setInnerContentFile($innerContentFile) {
		$this->innerContentFile = $innerContentFile;
	}

	/** 
	 * Determine which outer theme to load for this page
	 */
	protected function setupRequestViewTheme() {
		$env = Environment::get();
		if ($this->controller->theme != false) {
			$this->setRequestViewTheme($this->controller->theme);
		} else if (($tmpTheme = $this->getThemeFromPath($this->viewPath)) != false) {
			$this->setRequestViewTheme($tmpTheme[0]);
			if ($tmpTheme[0] != VIEW_CORE_THEME && $tmpTheme[0] != 'dashboard') {
				$pt = PageTheme::getByHandle($tmpTheme[0]);
			}
			if (is_object($pt)) {
				$template = $env->getPath(DIRNAME_THEMES . '/' . $pt->getPageThemeHandle() . '/' . $tmpTheme[1], $pt->getPackageHandle());
			} else {
				$template = $env->getPath(DIRNAME_THEMES . '/' . $tmpTheme[0] . '/' . $tmpTheme[1]);
			}
			$this->setViewTemplate($template);
		} else if (is_object($this->c) && ($tmpTheme = $this->c->getCollectionThemeObject()) != false) {
			$this->setRequestViewTheme($tmpTheme);
		} else {
			$this->setRequestViewTheme(FILENAME_COLLECTION_DEFAULT_THEME);
		}
	}

	public function setRequestViewTheme($theme) {
		if (is_object($theme)) {
			$this->themeHandle = $theme->getPageThemeHandle();
			$this->theme = $theme;
		} else {
			$this->themeHandle = $theme;
		}
	}

	protected function loadRequestViewThemeObject() {
		if (!is_object($this->theme) && ($this->themeHandle != VIEW_CORE_THEME && $this->themeHandle != 'dashboard')) {
			$this->theme = PageTheme::getByHandle($this->themeHandle);
			$this->themePkgHandle = $this->theme->getPackageHandle();
		}
	}

	/**
	 * This grabs the theme for a particular path, if one exists in the themePaths array 
	 * @access private
     * @param string $path
	 * @return string $theme
	*/
	protected function getThemeFromPath($path) {
		// there's probably a more efficient way to do this
		$theme = false;
		$txt = Loader::helper('text');
		foreach($this->themePaths as $lp => $layout) {
			if ($txt->fnmatch($lp, $path)) {
				$theme = $layout;
				break;
			}
		}
		return $theme;
	}


	/** 
	 * Begin the render
	 */
	public function start($path) {
		if (substr($path, strlen($path) - 1) == '/') {
			$path = substr($path, 0, strlen($path) - 1);
		}
		$this->viewPath = $path;
	}

	/**
	 * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes
	 * @access public
	 * @param $path string
	 * @param $theme object, if null site theme is default
	 * @return void
	*/
	public function setThemeByPath($path, $theme = NULL, $wrapper = FILENAME_THEMES_VIEW) {
		$this->themePaths[$path] = array($theme, $wrapper);
	}

	protected function setupController() {
		if (!isset($this->controller)) {
			$this->controller = Loader::controller($this->viewPath);
			$this->controller->setupAndRun();
		}
	}

	public function startRender() {

		// First the starting gun.
		Events::fire('on_start', $this);

		// Set the theme object that we should use for this requested page.
		// Only run setup if the theme is unset. Usually it will be but if we set it
		// programmatically we already have a theme.
		if (!isset($this->themeHandle)) {
			$this->setupRequestViewTheme();
		}

		// Now we take the theme handle (which is a string) and we load our theme object into it.
		// We don't ALWAYS have an object, however. Core and Dashboard themes are just strings.
		$this->loadRequestViewThemeObject();

		$env = Environment::get();
		$this->setInnerContentFile($env->getPath(DIRNAME_PAGES . '/' . $this->viewPath, $this->themePkgHandle));
		$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));

	}

	public function executeRender() {

		Events::fire('on_before_render', $this);
		if ($innerContentFile) {
			ob_start();
			include($this->innerContentFile);
			$innerContent = ob_get_contents();
			ob_end_clean();
		}

		print $this->getViewContents();
	}
}