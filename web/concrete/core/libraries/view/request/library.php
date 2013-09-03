<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_RequestView extends View {

	protected $controller;
	protected $viewPath;
	protected $innerContentFile;

	protected $themeHandle;
	protected $themeObject;
	protected $themeRelativePath;
	protected $themeAbsolutePath;
	protected $themePkgHandle;

	private static $instance;

	public function getThemeDirectory() {return $this->themeAbsolutePath;}

	public static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	protected function setInnerContentFile($innerContentFile) {
		$this->innerContentFile = $innerContentFile;
	}


	public function setRequestViewTheme($theme) {
		if (is_object($theme)) {
			$this->themeHandle = $theme->getPageThemeHandle();
		} else {
			$this->themeHandle = $theme;
		}
	}

	/** 
	 * Load all the theme-related variables for which theme to use for this request.
	 */
	protected function loadRequestViewThemeObject() {
		$env = Environment::get();
		if ($this->controller->theme != false) {
			$this->setRequestViewTheme($this->controller->theme);
		} else if (($tmpTheme = $this->getThemeFromPath($this->viewPath)) != false) {
			$this->setRequestViewTheme($tmpTheme[0]);
		} else {
			$this->setRequestViewTheme(FILENAME_COLLECTION_DEFAULT_THEME);
		}

		if ($this->themeHandle != VIEW_CORE_THEME) {
			$this->themeObject = PageTheme::getByHandle($this->themeHandle);
			$this->themePkgHandle = $this->theme->getPackageHandle();
		}

		$this->themeAbsolutePath = $env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle);
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
		$this->loadRequestViewThemeObject();

		$env = Environment::get();
		$this->setInnerContentFile($env->getPath(DIRNAME_PAGES . '/' . trim($this->viewPath, '/') . '.php', $this->themePkgHandle));
		if (file_exists(DIR_FILES_THEMES_CORE . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php')) {
			$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php'));
		} else {
			$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));
		}
	}

	public function getViewContents() {
		Events::fire('on_before_render', $this);
		$this->controller->on_before_render();
		extract($this->controller->getSets());
		extract($this->controller->getHelperObjects());

		if ($this->innerContentFile) {
			ob_start();
			include($this->innerContentFile);
			$innerContent = ob_get_contents();
			ob_end_clean();
		}

		if (file_exists($this->template)) {
			ob_start();
			include($this->template);
			$contents = ob_get_contents();
			ob_end_clean();

			return $contents;

		} else {
			throw new Exception(t('File %s not found. All themes need default.php and view.php files in them. Consult concrete5 documentation on how to create these files.', $this->template));
		}
	}

	public function deliverRender($contents) {
		$ret = Events::fire('on_page_output', $contents);
		if($ret != '') {
			$contents = $ret;
		}
		print $contents;
	}

	public function finishRender() {
		Events::fire('on_render_complete', $this);
		require(DIR_BASE_CORE . '/startup/jobs.php');
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}
}