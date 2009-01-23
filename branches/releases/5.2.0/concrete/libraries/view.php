<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

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
			foreach($this->themePaths as $lp => $layout) {
				if (preg_match('/^\\' . $lp . '(.*)/', $path)) {
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
				return REL_DIR_FILES_TOOLS . '/css/' . DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . $stylesheet . '?mode=preview';
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
		 * Function responsible for outputting header items
		 * @access private
		 */
		public function outputHeaderItems() {
			$items = array();
			if (is_array($this->headerItems['VIEW'])) {
				foreach($this->headerItems['VIEW'] as $hi) {
					if (!in_array($hi, $items)) {
						print $hi . "\n";
						$items[] = $hi;
					}
				}
			}
			if (is_array($this->headerItems['CONTROLLER'])) {
				foreach($this->headerItems['CONTROLLER'] as $hi) {
					if (!in_array($hi, $items)) {
						print $hi . "\n";
						$items[] = $hi;
					}
				}
			}
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
		 * @param $theme object
		 * @return void
		*/
		public function setThemeByPath($path, $theme) {
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
		 * @access public
		 * @param string $url
		 * @return boolean | void
		*/	
		public function section($url) {
			if (is_object($this->c)) {
				$cPath = $this->c->getCollectionPath();
				if (strpos($cPath, '/' . $url) !== false && strpos($cPath, '/' . $url) == 0) {
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
				$dispatcher = '/index.php';
			}
			if ($action == '/') {
				return DIR_REL . '/';
			}
			$_action = DIR_REL . $dispatcher. $action;
			// remove last / if it's on there
			if (substr($_action, strlen($_action) - 1, 1) == '/') {
				$_action = substr($_action, 0, strlen($_action) - 1);
			}
			
			if ($task != null) {
				$_action .= '/-/' . $task;
				$args = func_get_args();
				if (count($args) > 2) {
					for ($i = 2; $i < count($args); $i++){
						$_action .= '/' . $args[$i];
					}
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
			if (!isset($this->theme) || (!$this->theme)) {
				$this->setThemeForView(DIRNAME_THEMES_CORE, FILENAME_THEMES_ERROR . '.php', true);
				include($this->theme);	
			} else {
				Loader::element('error_fatal', array('innerContent' => $innerContent, 
					'titleContent' => $titleContent));
			}
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
				
				// Extract controller information from the view, and put it in the current context
				if (!isset($this->controller)) {
					$this->controller = Loader::controller($view);
					$this->controller->setupAndRun();
				}
				
				extract($this->controller->getSets());
				extract($this->controller->getHelperObjects());
				
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
					}
					$wrapTemplateInTheme = true;
					$themeFilename = $view . '.php';
				}
				
				
				if (is_object($this->c)) {
					$c = $this->c;
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
					$blocks = $view->getBlocks();
					foreach($blocks as $b1) {
						$btc = Loader::controller($b1);
						$btc->runTask('on_page_view', $view);
					}
				}
	
				// finally, we include the theme (which was set by setTheme and will automatically include innerContent)
				// disconnect from our db and exit
				if ($content != false) {
					include($content);
				}

				$innerContent = ob_get_contents();
				
				if (ob_get_level() == (OB_INITIAL_LEVEL + 1)) {
					ob_end_clean();
				}
				
				Events::fire('on_before_render', $this);
				
				include($this->theme);
				
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