<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_PathRequestView extends RequestView {

	protected $viewPath;
	protected $innerContentFile;

	protected $themeHandle;
	protected $themeObject;
	protected $themeRelativePath;
	protected $themeAbsolutePath;
	protected $themePkgHandle;


	public function getThemeDirectory() {return $this->themeAbsolutePath;}
	/**
	 * gets the relative theme path for use in templates
	 * @access public
	 * @return string $themePath
	*/
	public function getThemePath() { return $this->themeRelativePath; }
	public function getThemeHandle() {return $this->themeHandle;}
	
	protected function setInnerContentFile($innerContentFile) {
		$this->innerContentFile = $innerContentFile;
	}

	public function inc($file, $args = array()) {
		extract($args);
		extract($this->getScopeItems());
		$env = Environment::get();
		include($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $file, $this->themePkgHandle));
	}

	/**
	 * A shortcut to posting back to the current page with a task and optional parameters. Only works in the context of 
	 * @param string $action
	 * @param string $task
	 * @return string $url
	 */
	public function action($action) {
		$a = func_get_args();
		array_unshift($a, $this->viewPath);
		$ret = call_user_func_array(array($this, 'url'), $a);
		return $ret;
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
		$rl = Router::get();
		if ($this->controller->theme != false) {
			$this->setRequestViewTheme($this->controller->theme);
		} else {
			$tmpTheme = $rl->getThemeFromPath($this->viewPath);
			if ($tmpTheme) {
				$this->setRequestViewTheme($tmpTheme[0]);
			} else if (!$this->themeHandle) {
				if ($this->controller->theme != false) {
					$this->setRequestViewTheme($this->controller->theme);
				} else {
					$this->setRequestViewTheme(FILENAME_COLLECTION_DEFAULT_THEME);
				}
			}
		}

		if ($this->themeHandle != VIEW_CORE_THEME && $this->themeHandle != 'dashboard') {
			$this->themeObject = PageTheme::getByHandle($this->themeHandle);
			$this->themePkgHandle = $this->themeObject->getPackageHandle();
		}
		$this->themeAbsolutePath = $env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle);
		$this->themeRelativePath = $env->getURL(DIRNAME_THEMES . '/' . $this->themeHandle);
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

	protected function setupController() {
		if (!isset($this->controller)) {
			$this->controller = Loader::controller($this->viewPath);
		}
	}

	protected function runControllerTask() {
		$this->controller->setupAndRun();
	}

	public function setupRender() {
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

	public function startRender() {
		// First the starting gun.
		Events::fire('on_start', $this);
		parent::startRender();
	}

	protected function onBeforeGetContents() {
		Events::fire('on_before_render', $this);
		if ($this->themeHandle == VIEW_CORE_THEME) {
			$_pt = new ConcretePageTheme();
			$_pt->registerAssets();
		} else if (is_object($this->themeObject)) {
			$this->themeObject->registerAssets();
		}
	}

	public function renderViewContents($scopeItems) {
		extract($scopeItems);
		if ($this->innerContentFile) {
			ob_start();
			include($this->innerContentFile);
			$innerContent = ob_get_contents();
			ob_end_clean();
		}

		if (file_exists($this->template)) {
			ob_start();
			$this->onBeforeGetContents();
			include($this->template);
			$contents = ob_get_contents();
			$this->onAfterGetContents();
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
		parent::deliverRender($contents);
	}

	public function finishRender() {
		Events::fire('on_render_complete', $this);
		require(DIR_BASE_CORE . '/startup/jobs.php');
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}
}