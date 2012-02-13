<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Core
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A generic object that every front-end template (view) or page extends.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class View extends Object {
	
		private $viewPath;
		
		/**
		 * controller used by this particular view
		 * @access public
	     * @var object
		*/
		public $controller;
		
		
		/** 
		 * An array of items that get loaded into a page's header
		 */
		private $headerItems = array();

		/** 
		 * An array of items that get loaded into just before body close
		 */
		private $footerItems = array();

		/**
		 * themePaths holds the various hard coded paths to themes
		 * @access private
	     * @var array
		*/
		private $themePaths = array();	
	
		private $areLinksDisabled = false;
		
		/**
		 * editing mode is enabled or not
		 * @access private
	     * @var boolean
		*/	
		private $isEditingEnabled = true;
		
		// getInstance() grabs one instance of the view w/the singleton pattern
		public function getInstance() {
			static $instance;
			if (!isset($instance)) {
				$v = __CLASS__;
				$instance = new $v;
			}
			return $instance;
		}		
		
		
		/**
		 * This grabs the theme for a particular path, if one exists in the themePaths array 
		 * @access private
	     * @param string $path
		 * @return string $theme
		*/
		private function getThemeFromPath($path) {
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
		 * Returns a stylesheet found in a themes directory - but FIRST passes it through the tools CSS handler
		 * in order to make certain style attributes found inside editable
		 * @param string $stylesheet
		 */
		public function getStyleSheet($stylesheet) {
			if ($this->isPreview()) {
				return REL_DIR_FILES_TOOLS . '/css/' . DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . $stylesheet . '?mode=preview&time=' . time();
			} else {
				return REL_DIR_FILES_TOOLS . '/css/' . DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . $stylesheet;
			}
		}

		/** 
		 * Function responsible for adding header items within the context of a view.
		 * @access private
		 */

		public function addHeaderItem($item, $namespace = 'VIEW') {
			$this->headerItems[$namespace][] = $item;
		}
		
		/** 
		 * Function responsible for adding footer items within the context of a view.
		 * @access private
		 */
		public function addFooterItem($item, $namespace = 'VIEW') {
			$this->footerItems[$namespace][] = $item;
		}
		
		public function getHeaderItems() {
			$a1 = (is_array($this->headerItems['CORE'])) ? $this->headerItems['CORE'] : array();
			$a2 = (is_array($this->headerItems['VIEW'])) ? $this->headerItems['VIEW'] : array();
			$a3 = (is_array($this->headerItems['CONTROLLER'])) ? $this->headerItems['CONTROLLER'] : array();
			
			$items = array_merge($a1, $a2, $a3);
			if (version_compare(PHP_VERSION, '5.2.9', '<')) {
				$items = array_unique($items);
			} else {
				// stupid PHP
				$items = array_unique($items, SORT_STRING);
			}
			return $items;
		}
		
		public function getFooterItems() {
			$a1 = (is_array($this->footerItems['CORE'])) ? $this->footerItems['CORE'] : array();
			$a2 = (is_array($this->footerItems['VIEW'])) ? $this->footerItems['VIEW'] : array();
			$a3 = (is_array($this->footerItems['CONTROLLER'])) ? $this->footerItems['CONTROLLER'] : array();
			$a4 = (is_array($this->footerItems['SCRIPT'])) ? $this->footerItems['SCRIPT'] : array();
			
			$items = array_merge($a1, $a2, $a3, $a4);
			if (version_compare(PHP_VERSION, '5.2.9', '<')) {
				$items = array_unique($items);
			} else {
				// stupid PHP
				$items = array_unique($items, SORT_STRING);
			}
			
			// also strip out anything that was in the header
			$headerItems = $this->getHeaderItems();
			$retitems = array();
			foreach($items as $it) {
				if (!in_array($it, $headerItems)) {
					$retitems[] = $it;
				}
			}
			return $retitems;
		}
		
		/** 
		 * Function responsible for outputting header items
		 * @access private
		 */
		public function outputHeaderItems() {
			
			$items = $this->getHeaderItems();
			
			// Loop through all items
			// If it is a header output object, place each item in a separate array for its container directory
			// Otherwise, put it in the outputPost array
			
			$outputPost = array();
			$output = array();
			
			foreach($items as $hi) {
				print $hi; // caled on two seperate lines because of pre php 5.2 __toString issues
				print "\n";
			}			
			
		}
		
		/** 
		 * Function responsible for outputting footer items
		 * @access private
		 */
		public function outputFooterItems() {
			$items = $this->getFooterItems();
			
			foreach($items as $hi) {
				print $hi; // caled on two seperate lines because of pre php 5.2 __toString issues
				print "\n";
			}
		}

		public function field($fieldName) {
			return $this->controller->field($fieldName);
		}
		
		
		/** 
		 * @access private
		 */
		public function enablePreview() {
			$this->isPreview = true;
		}
		
		/** 
		 * @access private
		 */
		public function isPreview() {
			return $this->isPreview;
		}
		
		/** 
		 * @access private
		 */
		public function disableLinks() {
			$this->areLinksDisabled = true;
		}
		
		/** 
		 * @access private
		 */
		public function enableLinks() {
			$this->areLinksDisabled = false;
		}
		
		/** 
		 * @access private
		 */
		public function areLinksDisabled() {
			return $this->areLinksDisabled;
		}
		
		/** 
		 * Returns the path used to access this view
		 * @return string $viewPath
		 */
		private function getViewPath() {
			return $this->viewPath;
		}
		
		/** 
		 * Returns the handle of the currently active theme
		 */
		public function getThemeHandle() { return $this->ptHandle;}
		
		/**
		 * gets the theme include file for this particular view		
		 * @access public
		 * @return string $theme
		*/
		public function getTheme() { return $this->theme;}
	
	
		/**
		 * gets the relative theme path for use in templates
		 * @access public
		 * @return string $themePath
		*/
		public function getThemePath() { return $this->themePath; }


		/**
		 * set directory of current theme for use when loading an element
		 * @access public
		 * @param string $path
		*/
		public function setThemeDirectory($path) { $this->themeDir=$path; }

		/**
		 * get directory of current theme for use when loading an element
		 * @access public
		 * @return string $themeDir
		*/
		public function getThemeDirectory() {return $this->themeDir;}
		
	
		/**
		 * used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes
		 * @access public
		 * @param $path string
		 * @param $theme object, if null site theme is default
		 * @return void
		*/
		public function setThemeByPath($path, $theme = NULL) {
			if ($theme != VIEW_CORE_THEME && $theme != 'dashboard') { // this is a hack until we figure this code out.
				if (is_string($theme)) {
					$pageTheme = PageTheme::getByHandle($theme);
					if(is_object($pageTheme) && $pageTheme->getThemeHandle() == $theme) { // is it the theme that's been requested?
						$theme = $pageTheme;
					}
				}
			}
			$this->themePaths[$path] = $theme;
		}
		
		/**
		 * Returns the value of the item in the POST array.
		 * @access public
		 * @param $key
		 * @return void
		*/
		public function post($key) {
			return $this->controller->post($key);
		}

		
		/**
		 * gets the collection object for the current view
		 * @access public
		 * @return Collection Object $c
		*/
		public function getCollectionObject() {
			return $this->c;
		}
		
		/**
		 * sets the collection object for the current view
		 * @access public
		 * @return void
		*/
		public function setCollectionObject($c) {
			$this->c = $c;
		}


		/**
		 * Includes file from the current theme path. Similar to php's include().
		 * Files included with this function will have all variables set using $this->controller->set() in their local scope,
		 * As well as access to all that controller's helper objects.
		 * @access public
		 * @param string $file
		 * @param array $args
		 * @return void
		*/
		public function inc($file, $args = array()) {
			extract($args);
			if (isset($this->c)) {
				$c = $this->c;
			}
			extract($this->controller->getSets());
			extract($this->controller->getHelperObjects());
			include($this->themeDir . '/' . $file);
		}

	
		/**
		 * editing is enabled true | false
		 * @access private
		 * @return boolean
		*/		
		public function editingEnabled() {
			return $this->isEditingEnabled;
		}
		
	
		/**
		 * set's editing to disabled
		 * @access private
		 * @return void
		*/
		public function disableEditing() {
			$this->isEditingEnabled = false;
		}


		/**
		 * sets editing to enabled
		 * @access private
		 * @return void
		*/
		public function enableEditing() {
			$this->isEditingEnabled = true;
		}
		
	
	
	
		/**
		 * This is rarely used. We want to render another view
		 * but keep the current controller. Views should probably not
		 * auto-grab the controller anyway but whatever
		 * @access private
		 * @param object $cnt
		 * @return void
		*/
		public function setController($cnt) {
			$this->controller = $cnt;
		}


		/**
		 * checks the current view to see if you're in that page's "section" (top level)
		 * (with one exception: passing in the home page url ('' or '/') will always return false)
		 * @access public
		 * @param string $url
		 * @return boolean | void
		*/	
		public function section($url) {
			$cPath = Page::getCurrentPage()->getCollectionPath();
			if (!empty($cPath)) {
				$url = '/' . trim($url, '/');
				if (strpos($cPath, $url) !== false && strpos($cPath, $url) == 0) {
					return true;
				}
			}
		}
		
		
		/**
		 * url is a utility function that is used inside a view to setup urls w/tasks and parameters		
		 * @access public
		 * @param string $action
		 * @param string $task
		 * @return string $url
		*/	
		public function url($action, $task = null) {
			$dispatcher = '';
			if ((!URL_REWRITING_ALL) || !defined('URL_REWRITING_ALL')) {
				$dispatcher = '/' . DISPATCHER_FILENAME;
			}
			
			$action = trim($action, '/');
			if ($action == '') {
				return DIR_REL . '/';
			}
			
			// if a query string appears in this variable, then we just pass it through as is
			if (strpos($action, '?') > -1) {
				return DIR_REL . $dispatcher. '/' . $action;
			} else {
				$_action = DIR_REL . $dispatcher. '/' . $action . '/';
			}
			
			if ($task != null) {
				if (ENABLE_LEGACY_CONTROLLER_URLS) {
					$_action .= '-/' . $task;
				} else {
					$_action .= $task;			
				}
				$args = func_get_args();
				if (count($args) > 2) {
					for ($i = 2; $i < count($args); $i++){
						$_action .= '/' . $args[$i];
					}
				}
				
				if (strpos($_action, '?') === false) {
					$_action .= '/';
				}
			}
			
			return $_action;
		}
		
		/**
		 * A shortcut to posting back to the current page with a task and optional parameters. Only works in the context of 
		 * @param string $action
		 * @param string $task
		 * @return string $url
		 */
		public function action($action, $task = null) {
			$a = func_get_args();
			array_unshift($a, $this->viewPath);
			$ret = call_user_func_array(array($this, 'url'), $a);
			return $ret;
		}

		/**
		 * render's a fata error using the built-in view. This is currently only
		 * used when the database connection fails
		 * @access public
		 * @param string $title
		 * @param string $error
		 * @return void
		*/	
		public function renderError($title, $error, $errorObj = null) {
			$innerContent = $error;
			$titleContent = $title; 
			if (!isset($this) || (!$this)) {
				$v = new View();
				$v->setThemeForView(DIRNAME_THEMES_CORE, FILENAME_THEMES_ERROR . '.php', true);
				include($v->getTheme());	
				exit;
			}
			if (!isset($this->theme) || (!$this->theme) || (!file_exists($this->theme))) {
				$this->setThemeForView(DIRNAME_THEMES_CORE, FILENAME_THEMES_ERROR . '.php', true);
				include($this->theme);	
				exit;			
			} else {
				Loader::element('error_fatal', array('innerContent' => $innerContent, 
					'titleContent' => $titleContent));
			}
		}
		
		/** 
		 * @private 
		 */
		public static function defaultExceptionHandler($e) {
			View::renderError(t('An unexpected error occurred.'), $e->getMessage(), $e);
		}

		/**
		 * sets the current theme
		 * @access public
		 * @param string $theme
		 * @return void
		*/	
		public function setTheme($theme) {
			$this->themeOverride = $theme;
		}
		
		/**
		 * set theme takes either a text-based theme ("concrete" or "dashboard" or something)
		 * or a PageTheme object and sets information in the view about that theme. This is called internally
		 * and is always passed the correct item based on context
		 * 
		 * @access public
		 * @param PageTheme object $pl
		 * @param string $filename
		 * @param boolean $wrapTemplateInTheme
		 * @return void
		*/	
		private function setThemeForView($pl, $filename, $wrapTemplateInTheme = false) {
			// wrapTemplateInTheme gets set to true if we're passing the filename of a single page or page type file through 
			$pkgID = 0;
			if ($pl instanceof PageTheme) {
				$this->ptHandle = $pl->getThemeHandle();
				if ($pl->getPackageID() > 0) {
					if (is_dir(DIR_PACKAGES . '/' . $pl->getPackageHandle())) {
						$dirp = DIR_PACKAGES;
						$url = DIR_REL;
					} else {
						$dirp = DIR_PACKAGES_CORE;
						$url = ASSETS_URL;
					}
					$theme = $dirp . '/' . $pl->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $pl->getThemeHandle() . '/' . $filename;
					if (!file_exists($theme)) {
						if ($wrapTemplateInTheme) {
							$theme = $dirp . '/' . $pl->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $pl->getThemeHandle() . '/' . FILENAME_THEMES_VIEW;
						} else {
							$theme = $dirp . '/' . $pl->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $pl->getThemeHandle() . '/' . FILENAME_THEMES_DEFAULT;
						}
					}
					$themeDir = $dirp . '/' . $pl->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $pl->getThemeHandle();
					$themePath = $url . '/' . DIRNAME_PACKAGES . '/' . $pl->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $pl->getThemeHandle();
					$pkgID = $pl->getPackageID();
				} else {
					if (is_dir(DIR_FILES_THEMES . '/' . $pl->getThemeHandle())) {
						$dir = DIR_FILES_THEMES;
						$themePath = DIR_REL . '/' . DIRNAME_THEMES . '/' . $pl->getThemeHandle();
					} else {
						$dir = DIR_FILES_THEMES_CORE;
						$themePath = ASSETS_URL . '/' . DIRNAME_THEMES . '/' . $pl->getThemeHandle();
					}
					$theme = $dir . '/' . $pl->getThemeHandle() . '/' . $filename;
					if (!file_exists($theme)) {
						if ($wrapTemplateInTheme) {
							$theme = $dir . '/' . $pl->getThemeHandle() . '/' . FILENAME_THEMES_VIEW;
						} else {
							$theme = $dir . '/' . $pl->getThemeHandle() . '/' . FILENAME_THEMES_DEFAULT;
						}
					}
					$themeDir = $dir . '/' . $pl->getThemeHandle();
				}
			} else {
				$this->ptHandle = $pl;
				if (file_exists(DIR_FILES_THEMES . '/' . $pl . '/' . $filename)) {
					$themePath = DIR_REL . '/' . DIRNAME_THEMES . '/' . $pl;
					$theme = DIR_FILES_THEMES . "/" . $pl . '/' . $filename;
					$themeDir = DIR_FILES_THEMES . "/" . $pl;
				} else if (file_exists(DIR_FILES_THEMES . '/' . $pl . '/' . FILENAME_THEMES_VIEW)) {
					$themePath = DIR_REL . '/' . DIRNAME_THEMES . '/' . $pl;
					$theme = DIR_FILES_THEMES . "/" . $pl . '/' . FILENAME_THEMES_VIEW;
					$themeDir = DIR_FILES_THEMES . "/" . $pl;
				} else if (file_exists(DIR_FILES_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $pl . '.php')) {
					$theme = DIR_FILES_THEMES . '/' . DIRNAME_THEMES_CORE . "/" . $pl . '.php';
					$themeDir = DIR_FILES_THEMES . '/' . DIRNAME_THEMES_CORE;
				} else if (file_exists(DIR_FILES_THEMES_CORE . "/" . $pl . '/' . $filename)) {
					$themePath = ASSETS_URL . '/' . DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $pl;
					$theme = DIR_FILES_THEMES_CORE . "/" . $pl . '/' . $filename;
					$themeDir = DIR_FILES_THEMES_CORE . "/" . $pl;
				} else if (file_exists(DIR_FILES_THEMES_CORE . "/" . $pl . '/' . FILENAME_THEMES_VIEW)) {
					$themePath = ASSETS_URL . '/' . DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $pl;
					$theme = DIR_FILES_THEMES_CORE . "/" . $pl . '/' . FILENAME_THEMES_VIEW;
					$themeDir = DIR_FILES_THEMES_CORE . "/" . $pl;
				} else if (file_exists(DIR_FILES_THEMES_CORE_ADMIN . "/" . $pl . '.php')) {
					$theme = DIR_FILES_THEMES_CORE_ADMIN . "/" . $pl . '.php';
					$themeDir = DIR_FILES_THEMES_CORE_ADMIN;
				}
			}
			
			$this->theme = $theme;
			$this->themePath = $themePath;
			$this->themeDir = $themeDir;
			$this->themePkgID = $pkgID;
		}
		public function escape($text){
			Loader::helper('text');
			return TextHelper::sanitize($text);
		}
		/**
		 * render takes one argument - the item being rendered - and it can either be a path or a page object
		 * @access public
		 * @param string $view
		 * @param array $args
		 * @return void
		*/	
		public function render($view, $args = null) { 
			
			try {			
				if (is_array($args)) {
					extract($args);
				}
	
				// strip off a slash if there is one at the end
				if (is_string($view)) {
					if (substr($view, strlen($view) - 1) == '/') {
						$view = substr($view, 0, strlen($view) - 1);
					}
				}
				
				$wrapTemplateInTheme = false;

				Events::fire('on_start', $this);
				
				// Extract controller information from the view, and put it in the current context
				if (!isset($this->controller)) {
					$this->controller = Loader::controller($view);
					$this->controller->setupAndRun();
				}

				if ($this->controller->getRenderOverride() != '') {
				   $view = $this->controller->getRenderOverride();
				}
				
				// Determine which inner item to load, load it, and stick it in $innerContent
				$content = false;
								
				ob_start();			
				if ($view instanceof Page) {
					
					$viewPath = $view->getCollectionPath();
					$this->viewPath = $viewPath;
					
					$cFilename = $view->getCollectionFilename();
					$ctHandle = $view->getCollectionTypeHandle();
					$editMode = $view->isEditMode();
					$c = $view;
					$this->c = $c;
					
					// $view is a page. It can either be a SinglePage or just a Page, but we're not sure at this point, unfortunately
					if ($view->getCollectionTypeID() == 0 && $cFilename) {
						$wrapTemplateInTheme = true;
						if (file_exists(DIR_FILES_CONTENT. "{$cFilename}")) {
							$content = DIR_FILES_CONTENT. "{$cFilename}";
						} else if ($view->getPackageID() > 0) {
							$file1 = DIR_PACKAGES . '/' . $view->getPackageHandle() . '/'. DIRNAME_PAGES . $cFilename;
							$file2 = DIR_PACKAGES_CORE . '/' . $view->getPackageHandle() . '/'. DIRNAME_PAGES . $cFilename;
							if (file_exists($file1)) {
								$content = $file1;
							} else if (file_exists($file2)) {
								$content = $file2;
							}
						} else if (file_exists(DIR_FILES_CONTENT_REQUIRED . "{$cFilename}")) {
							$content = DIR_FILES_CONTENT_REQUIRED. "{$cFilename}";
						}
						
						$themeFilename = $c->getCollectionHandle() . '.php';
						
					} else {
						if (file_exists(DIR_BASE . '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php')) {
							$content = DIR_BASE . '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php';
							$wrapTemplateInTheme = true;
						} else if (file_exists(DIR_BASE_CORE. '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php')) {
							$content = DIR_BASE_CORE . '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php';
							$wrapTemplateInTheme = true;
						} else if ($view->getPackageID() > 0) {
							$file1 = DIR_PACKAGES . '/' . $view->getPackageHandle() . '/'. DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php';
							$file2 = DIR_PACKAGES_CORE . '/' . $view->getPackageHandle() . '/'. DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php';
							if (file_exists($file1)) {
								$content = $file1;
								$wrapTemplateInTheme = true;
							} else if (file_exists($file2)) {
								$content = $file2;
								$wrapTemplateInTheme = true;
							}
						}
						
						$themeFilename = $ctHandle . '.php';
					}
					
					
				} else if (is_string($view)) {
					
					// if we're passing a view but our render override is not null, that means that we're passing 
					// a new view from within a controller. If that's the case, then we DON'T override the viewPath, we want to keep it
					
					// In order to enable editable 404 pages, other editable pages that we render without actually visiting
					if (defined('DB_DATABASE') && $view == '/page_not_found') {
						$pp = Page::getByPath($view);
						if (!$pp->isError()) {
							$this->c = $pp;
						}
					}
					
					$viewPath = $view;
					if ($this->controller->getRenderOverride() != '' && $this->getCollectionObject() != null) {
						// we are INSIDE a collection renderring a view. Which means we want to keep the viewPath that of the collection
						$this->viewPath = $this->getCollectionObject()->getCollectionPath();
					}
					
					// we're just passing something like "/login" or whatever. This will typically just be 
					// internal Concrete stuff, but we also prepare for potentially having something in DIR_FILES_CONTENT (ie: the webroot)
					if (file_exists(DIR_FILES_CONTENT . "/{$view}/" . FILENAME_COLLECTION_VIEW)) {
						$content = DIR_FILES_CONTENT . "/{$view}/" . FILENAME_COLLECTION_VIEW;
					} else if (file_exists(DIR_FILES_CONTENT . "/{$view}.php")) {
						$content = DIR_FILES_CONTENT . "/{$view}.php";
					} else if (file_exists(DIR_FILES_CONTENT_REQUIRED . "/{$view}/" . FILENAME_COLLECTION_VIEW)) {
						$content = DIR_FILES_CONTENT_REQUIRED . "/{$view}/" . FILENAME_COLLECTION_VIEW;
					} else if (file_exists(DIR_FILES_CONTENT_REQUIRED . "/{$view}.php")) {
						$content = DIR_FILES_CONTENT_REQUIRED . "/{$view}.php";
					} else if ($this->getCollectionObject() != null && $this->getCollectionObject()->isGeneratedCollection() && $this->getCollectionObject()->getPackageID() > 0) {
						//This is a single_page associated with a package, so check the package views as well
						$pagePkgPath = Package::getByID($this->getCollectionObject()->getPackageID())->getPackagePath();
						if (file_exists($pagePkgPath . "/single_pages/{$view}/" . FILENAME_COLLECTION_VIEW)) {
							$content = $pagePkgPath . "/single_pages/{$view}/" . FILENAME_COLLECTION_VIEW;
						} else if (file_exists($pagePkgPath . "/single_pages/{$view}.php")) {
							$content = $pagePkgPath . "/single_pages/{$view}.php";
						}
					}
					$wrapTemplateInTheme = true;
					$themeFilename = $view . '.php';
				}
				
				
				if (is_object($this->c)) {
					$c = $this->c;
					if (defined('DB_DATABASE') && $view == '/page_not_found') {
						$view = $c;
						$req = Request::get();
						$req->setCurrentPage($c);
					}
				}
				
				// Determine which outer item/theme to load
				// obtain theme information for this collection
				if (isset($this->themeOverride)) {
					$theme = $this->themeOverride;
				} else if ($this->controller->theme != false) {
					$theme = $this->controller->theme;
				} else if (($tmpTheme = $this->getThemeFromPath($viewPath)) != false) {
					$theme = $tmpTheme;
				} else if (is_object($this->c) && ($tmpTheme = $this->c->getCollectionThemeObject()) != false) {
					$theme = $tmpTheme;
				} else {
					$theme = FILENAME_COLLECTION_DEFAULT_THEME;
				}		
				
				$this->setThemeForView($theme, $themeFilename, $wrapTemplateInTheme);

				// Now, if we're on an actual page, we retrieve all the blocks on the page
				// and store their view states in the local cache (for the page). That way
				// we can add header items and have them show up in the header BEFORE
				// the block itself is actually loaded 			
				
				if ($view instanceof Page) {
					$_pageBlocks = $view->getBlocks();
					$_pageBlocksGlobal = $view->getGlobalBlocks();
					$_pageBlocks = array_merge($_pageBlocks, $_pageBlocksGlobal);
					if ($view->supportsPageCache($_pageBlocks, $this->controller)) {
						$pageContent = $view->getFromPageCache();
						if ($pageContent != false) {
							Events::fire('on_before_render', $this);
							if (defined('APP_CHARSET')) {
								header("Content-Type: text/html; charset=" . APP_CHARSET);
							}
							print($pageContent);
							Events::fire('on_render_complete', $this);
							if (ob_get_level() == OB_INITIAL_LEVEL) {
		
								require(DIR_BASE_CORE . '/startup/shutdown.php');
								exit;
							}
							return;
						}
					}
					
					foreach($_pageBlocks as $b1) {
						$btc = $b1->getInstance();
						// now we inject any custom template CSS and JavaScript into the header
						if('Controller' != get_class($btc)){
							$btc->outputAutoHeaderItems();
						}
						$btc->runTask('on_page_view', array($view));
					}
					
					// do we have any custom menu plugins?
					$cp = new Permissions($view);
					if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) { 
						$ih = Loader::helper('concrete/interface/menu');
						$_interfaceItems = $ih->getPageHeaderMenuItems();
						foreach($_interfaceItems as $_im) {
							$_controller = $_im->getController();
							$_controller->outputAutoHeaderItems();
						}
						unset($_interfaceItems);
						unset($_im);
						unset($_controller);
					}
					unset($_interfaceItems);
					unset($_im);
					unset($_controller);
					
					
					// now, we output all the custom style records for the design tab in blocks/areas on the page
					$c = $this->getCollectionObject();
					$view->outputCustomStyleHeaderItems(); 				
				}
	
				// finally, we include the theme (which was set by setTheme and will automatically include innerContent)
				// disconnect from our db and exit

				$this->controller->on_before_render();
				extract($this->controller->getSets());
				extract($this->controller->getHelperObjects());

				if ($content != false) {
					include($content);
				}

				$innerContent = ob_get_contents();
				
				if (ob_get_level() > OB_INITIAL_LEVEL) {
					ob_end_clean();
				}
				
				Events::fire('on_before_render', $this);
				
				if (defined('APP_CHARSET')) {
					header("Content-Type: text/html; charset=" . APP_CHARSET);
				}
				
				if (file_exists($this->theme)) {
					
					ob_start();
					include($this->theme);
					$pageContent = ob_get_contents();
					ob_end_clean();
					
					$ret = Events::fire('on_page_output', $pageContent);
					if($ret != '') {
						print $ret;
					} else {
						print $pageContent;
					}
					
					if ($view instanceof Page) {
						if ($view->supportsPageCache($_pageBlocks, $this->controller)) {
							$view->addToPageCache($pageContent);
						}
					}
					
				} else {
					throw new Exception(t('File %s not found. All themes need default.php and view.php files in them. Consult concrete5 documentation on how to create these files.', $this->theme));
				}
				
				Events::fire('on_render_complete', $this);
				
				if (ob_get_level() == OB_INITIAL_LEVEL) {
	
					require(DIR_BASE_CORE . '/startup/shutdown.php');
					exit;
					
				}
				
			} catch(ADODB_Exception $e) {
				// if it's a database exception we go here.
				if (Config::get('SITE_DEBUG_LEVEL') == DEBUG_DISPLAY_ERRORS) {
					$this->renderError(t('An unexpected error occurred.'), $e->getMessage(), $e);		
				} else {
					$this->renderError(t('An unexpected error occurred.'), t('A database error occurred while processing this request.'), $e);
				}
				
				// log if setup to do so
				if (ENABLE_LOG_ERRORS) {
					$l = new Log(LOG_TYPE_EXCEPTIONS, true, true);
					$l->write(t('Exception Occurred: ') . $e->getMessage());
					$l->write($e->getTraceAsString());
					$l->close();
				}
			} catch (Exception $e) {
				$this->renderError(t('An unexpected error occurred.'), $e->getMessage(), $e);
				// log if setup to do so
				if (ENABLE_LOG_ERRORS) {
					$l = new Log(LOG_TYPE_EXCEPTIONS, true, true);
					$l->write(t('Exception Occurred: ') . $e->getMessage());
					$l->write($e->getTraceAsString());
					$l->close();
				}
			}

		}
		
	}
