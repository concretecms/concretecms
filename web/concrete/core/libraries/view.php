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
	class Concrete5_Library_View extends Object {
	
		protected $viewPath;
		protected $pkgHandle;
		protected $disableContentInclude = false;
		
		/**
		 * controller used by this particular view
		 * @access public
	     * @var object
		*/
		public $controller;
		
		
		protected $outputAssets = array();


		/** 
		 * An array of items that get loaded into a page's header
		 */
		/*
		private $headerItems = array();

		private $footerItems = array();

		*/

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
		public static function getInstance() {
			static $instance;
			if (!isset($instance)) {
				$instance = new View();
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
			}
			$pt = PageTheme::getByHandle($this->getThemeHandle());
			$file = $this->getThemePath() . '/' . $stylesheet;
			$cacheFile = DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle() . '/' . $stylesheet;
			$env = Environment::get();
			$themeRec = $env->getUncachedRecord(DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . $stylesheet, $pt->getPackageHandle());
			if (file_exists($cacheFile) && $themeRec->exists()) {
				if (filemtime($cacheFile) > filemtime($themeRec->file)) {
					return REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle() . '/' . $stylesheet;
				}
			}
			if ($themeRec->exists()) {
				$themeFile = $themeRec->file;
				if (!file_exists(DIR_FILES_CACHE . '/' . DIRNAME_CSS)) {
					@mkdir(DIR_FILES_CACHE . '/' . DIRNAME_CSS);
				}
				if (!file_exists(DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle())) {
					@mkdir(DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle());
				}
				$fh = Loader::helper('file');
				$stat = filemtime($themeFile);
				if (!file_exists(dirname($cacheFile))) {
					@mkdir(dirname($cacheFile), DIRECTORY_PERMISSIONS_MODE, true);
				}
				$style = $pt->parseStyleSheet($stylesheet);
				$r = @file_put_contents($cacheFile, $style);
				if ($r) {
					return REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle() . '/' . $stylesheet;
				} else {
					return $this->getThemePath() . '/' . $stylesheet;
				}
			}
		}

		/** 
		 * Function responsible for adding header items within the context of a view.
		 * @access private
		 */

		public function addHeaderItem($item) {
			$this->outputAssets[Asset::ASSET_POSITION_HEADER]['unweighted'][] = $item;
		}
		
		/** 
		 * Function responsible for adding footer items within the context of a view.
		 * @access private
		 */
		public function addFooterItem($item) {
			$this->outputAssets[Asset::ASSET_POSITION_FOOTER]['unweighted'][] = $item;
		}
		


		/** 
		 * Function responsible for outputting header items
		 * @access private
		 */
		public function outputHeaderItems() {
			print '<!--ccm:assets:' . Asset::ASSET_POSITION_HEADER . '//-->';
		}
		
		/** 
		 * Function responsible for outputting footer items
		 * @access private
		 */
		public function outputFooterItems() {
			print '<!--ccm:assets:' . Asset::ASSET_POSITION_FOOTER . '//-->';
		}

		protected function field($fieldName) {
			return $this->controller->field($fieldName);
		}
		
		public function addOutputAsset(Asset $asset) {
			if ($asset->getAssetWeight() > 0) {
				$this->outputAssets[$asset->getAssetPosition()]['weighted'][] = $asset;
			} else {
				$this->outputAssets[$asset->getAssetPosition()]['unweighted'][] = $asset;
			}
		}

		protected function sortAssetsByWeightDescending($assetA, $assetB) {
			$weightA = $assetA->getAssetWeight();
			$weightB = $assetB->getAssetWeight();

			if ($weightA == $weightB) {
				return 0;
			}

			return $weightA < $weightB ? 1 : -1;
		}

		protected function sortAssetsByPostProcessDescending($assetA, $assetB) {
			$ppA = ($assetA instanceof Asset && $assetA->assetSupportsPostProcessing());
			$ppB = ($assetB instanceof Asset && $assetB->assetSupportsPostProcessing());
			if ($ppA && $ppB) {
				return 0;
			}
			if ($ppA && !$ppB) {
				return -1;
			}

			if (!$ppA && $ppB) {
				return 1;
			}
			if (!$ppA && !$ppB) {
				return 0;
			}
		}

		protected function postProcessAssets($assets) {
			$c = Page::getCurrentPage();
			if (!is_object($c) || !ENABLE_ASSET_CACHE) {
				return $assets;
			}
			// goes through all assets in this list, creating new URLs and post-processing them where possible.
			$segment = 0;
			$subassets[$segment] = array();
			for ($i = 0; $i < count($assets); $i++) {
				$asset = $assets[$i];
				$nextasset = $assets[$i+1];
				$subassets[$segment][] = $asset;
				if ($asset instanceof Asset && $nextasset instanceof Asset) {
					if ($asset->getAssetType() != $nextasset->getAssetType()) {
						$segment++;
					} else if (!$asset->assetSupportsPostProcessing() || !$nextasset->assetSupportsPostProcessing()) {
						$segment++;
					}
				} else {
					$segment++;
				}
			}

			// now we have a sub assets array with different segments split by post process and non-post-process
			$return = array();
			foreach($subassets as $segment => $assets) {
				if ($assets[0] instanceof Asset && $assets[0]->assetSupportsPostProcessing()) {
					// this entire segment can be post processed together
					$class = Loader::helper('text')->camelcase($assets[0]->getAssetType()) . 'Asset';
					$assets = call_user_func(array($class, 'postprocess'), $assets);
				}
				$return = array_merge($return, $assets);
			}
			return $return;
		}

		protected function replaceEmptyAssetPlaceholders($pageContent) {
			foreach(array('<!--ccm:assets:' . Asset::ASSET_POSITION_HEADER . '//-->', '<!--ccm:assets:' . Asset::ASSET_POSITION_FOOTER . '//-->') as $comment) {
				$pageContent = str_replace($comment, '', $pageContent);
			}
			return $pageContent;
		}

		protected function replaceAssetPlaceholders($pageContent) {
			$outputItems = array();
			foreach($this->outputAssets as $position => $assets) {
				$output = '';
				if (is_array($assets['weighted'])) {
					$weightedAssets = $assets['weighted'];
					usort($weightedAssets, array($this, 'sortAssetsByWeightDescending'));
					$transformed = $this->postProcessAssets($weightedAssets);
					foreach($transformed as $item) {
						$itemstring = (string) $item;
						if (!in_array($itemstring, $outputItems)) {
							$output .= $this->outputAssetIntoView($item);
							$outputItems[] = $itemstring;
						}
					}
				}
				if (is_array($assets['unweighted'])) {
					// now the unweighted
					$unweightedAssets = $assets['unweighted'];
					usort($unweightedAssets, array($this, 'sortAssetsByPostProcessDescending'));
					$transformed = $this->postProcessAssets($unweightedAssets);
					foreach($transformed as $item) {
						$itemstring = (string) $item;
						if (!in_array($itemstring, $outputItems)) {
							$output .= $this->outputAssetIntoView($item);
							$outputItems[] = $itemstring;
						}
					}
				}
				$pageContent = str_replace('<!--ccm:assets:' . $position . '//-->', $output, $pageContent);
			}
			return $pageContent;				
		}
		
		protected function outputAssetIntoView($item) {
			return $item . "\n";			
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
		public function setThemeByPath($path, $theme = NULL, $wrapper = FILENAME_THEMES_VIEW) {
			if ($theme != VIEW_CORE_THEME && $theme != 'dashboard') { // this is a hack until we figure this code out.
				if (is_string($theme)) {
					$pageTheme = PageTheme::getByHandle($theme);
					if(is_object($pageTheme) && $pageTheme->getThemeHandle() == $theme) { // is it the theme that's been requested?
						$theme = $pageTheme;
					}
				}
			}
			$this->themePaths[$path] = array($theme, $wrapper);
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
			$env = Environment::get();
			include($env->getPath(DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . $file, $this->pkgHandle));
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

		public function checkMobileView() {
			if(isset($_COOKIE['ccmDisableMobileView']) && $_COOKIE['ccmDisableMobileView'] == true) {
				define('MOBILE_THEME_IS_ACTIVE', false);
				return false; // break out if we've said we don't want the mobile theme
			}
			
			$page = Page::getCurrentPage();
			if($page instanceof Page && $page->isAdminArea()) {
				define('MOBILE_THEME_IS_ACTIVE', false);
				return false; // no mobile theme for the dashboard
			}
			
			Loader::library('3rdparty/mobile_detect');
			$md = new Mobile_Detect();
			if ($md->isMobile()) {
				$themeId = Config::get('MOBILE_THEME_ID');
				if ($themeId > 0) {
					$mobileTheme = PageTheme::getByID($themeId);
					if($mobileTheme instanceof PageTheme) {
						define('MOBILE_THEME_IS_ACTIVE',true);
						// we have to grab the instance of the view
						// since on_page_view doesn't give it to us
						$this->setTheme($mobileTheme);
					}
				}
			}
			
			if (!defined('MOBILE_THEME_IS_ACTIVE')) {
				define('MOBILE_THEME_IS_ACTIVE', false);
			}
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
			header('HTTP/1.1 500 Internal Server Error');
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
		 * @param boolean $outerFileWrapper
		 * @return void
		*/	
		private function setThemeForView($pl, $filename, $outerFileWrapper = false) {
			// outerFileWrapper gets set to true if we're passing the filename of a single page or page type file through 
			$pkgID = 0;
			$env = Environment::get();
			if ($pl instanceof PageTheme) {
				$this->ptHandle = $pl->getThemeHandle();
				if ($pl->getPackageID() > 0) {
					$pkgID = $pl->getPackageID();
					$this->pkgHandle = $pl->getPackageHandle();
				}
			
				$rec = $env->getRecord(DIRNAME_THEMES . '/' . $pl->getThemeHandle() . '/' . $filename, $this->pkgHandle);
				if (!$rec->exists()) {
					if ($outerFileWrapper) {
						$theme = $env->getPath(DIRNAME_THEMES . '/' . $pl->getThemeHandle() . '/' . $outerFileWrapper, $this->pkgHandle);
					} else {
						$theme = $env->getPath(DIRNAME_THEMES . '/' . $pl->getThemeHandle() . '/' . FILENAME_THEMES_DEFAULT, $this->pkgHandle);
					}
				} else {
					$theme = $rec->file;
					$this->disableContentInclude = true;
				}
				
				$themeDir = str_replace('/' . FILENAME_THEMES_DEFAULT, '', $env->getPath(DIRNAME_THEMES . '/' . $pl->getThemeHandle() . '/' . FILENAME_THEMES_DEFAULT, $this->pkgHandle));
				$themePath = str_replace('/' . FILENAME_THEMES_DEFAULT, '', $env->getURL(DIRNAME_THEMES . '/' . $pl->getThemeHandle() . '/' . FILENAME_THEMES_DEFAULT, $this->pkgHandle));
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
		
			if (is_array($args)) {
				extract($args);
			}

			// strip off a slash if there is one at the end
			if (is_string($view)) {
				if (substr($view, strlen($view) - 1) == '/') {
					$view = substr($view, 0, strlen($view) - 1);
				}
			}
			
			$outerFileWrapper = false;
			$dsh = Loader::helper('concrete/dashboard');
			$wrapTemplateInTheme = false;

			$this->checkMobileView();
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
				/*
				$_pageBlocks = $view->getBlocks();

				if (!$dsh->inDashboard()) {
					$_pageBlocksGlobal = $view->getGlobalBlocks();
					$_pageBlocks = array_merge($_pageBlocks, $_pageBlocksGlobal);
				}
				*/

				// do we have any custom menu plugins?
				$cp = new Permissions($view);
				if ($cp->canViewToolbar()) { 
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
				
				$viewPath = $view->getCollectionPath();
				$this->viewPath = $viewPath;
				
				$cFilename = $view->getCollectionFilename();
				$ctHandle = $view->getCollectionTypeHandle();
				$editMode = $view->isEditMode();
				$c = $view;
				$this->c = $c;
				
				$env = Environment::get();
				// $view is a page. It can either be a SinglePage or just a Page, but we're not sure at this point, unfortunately
				if ($view->getCollectionTypeID() == 0 && $cFilename) {
					$outerFileWrapper = FILENAME_THEMES_VIEW;
					$cFilename = trim($cFilename, '/');
					$content = $env->getPath(DIRNAME_PAGES . '/' . $cFilename, $view->getPackageHandle());
					$themeFilename = $c->getCollectionHandle() . '.php';						
				} else {
					$rec = $env->getRecord(DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php', $view->getPackageHandle());
					if ($rec->exists()) {
						$outerFileWrapper = FILENAME_THEMES_VIEW;
						$content = $rec->file;
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
				$outerFileWrapper = FILENAME_THEMES_VIEW;
				$themeFilename = $view . '.php';
			}
			
			
			if (is_object($this->c)) {
				$c = $this->c;
				if (defined('DB_DATABASE') && ($view == '/page_not_found' || $view == '/login')) {
					$view = $c;
					$req = Request::get();
					$req->setCurrentPage($c);
					/*
					$_pageBlocks = $view->getBlocks();
					$_pageBlocksGlobal = $view->getGlobalBlocks();
					$_pageBlocks = array_merge($_pageBlocks, $_pageBlocksGlobal);
					*/
				}
			}
			
			/*
			if (is_array($_pageBlocks)) {
				foreach($_pageBlocks as $b1) {
					$b1p = new Permissions($b1);
					if ($b1p->canRead()) { 
						$btc = $b1->getInstance();
						// now we inject any custom template CSS and JavaScript into the header
						if('Controller' != get_class($btc)){
							$btc->outputAutoHeaderItems();
						}
						//$btc->runTask('on_page_view', array($view));
					}
				}
			}		
			*/	
			
			// Determine which outer item/theme to load
			// obtain theme information for this collection
			if (isset($this->themeOverride)) {
				$theme = $this->themeOverride;
			} else if ($this->controller->theme != false) {
				$theme = $this->controller->theme;
			} else if (($tmpTheme = $this->getThemeFromPath($viewPath)) != false) {
				$theme = $tmpTheme[0];
				$outerFileWrapper = $tmpTheme[1];
			} else if (is_object($this->c) && ($tmpTheme = $this->c->getCollectionThemeObject()) != false) {
				$theme = $tmpTheme;
			} else {
				$theme = FILENAME_COLLECTION_DEFAULT_THEME;
			}		
			
			$this->setThemeForView($theme, $themeFilename, $outerFileWrapper);


			// finally, we include the theme (which was set by setTheme and will automatically include innerContent)
			// disconnect from our db and exit

			$this->controller->on_before_render();
			extract($this->controller->getSets());
			extract($this->controller->getHelperObjects());

			if ($content != false && (!$this->disableContentInclude)) {
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
				
				$cache = PageCache::getLibrary();
				$shouldAddToCache = $cache->shouldAddToCache($this);
				if ($shouldAddToCache) {
					$cache->outputCacheHeaders($c);
				}

				ob_start();
				include($this->theme);
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
					$cache->set($c, $pageContent);
				}

			
			} else {
				throw new Exception(t('File %s not found. All themes need default.php and view.php files in them. Consult concrete5 documentation on how to create these files.', $this->theme));
			}
			
			Events::fire('on_render_complete', $this);
			
			if (ob_get_level() == OB_INITIAL_LEVEL) {
				require(DIR_BASE_CORE . '/startup/jobs.php');
				require(DIR_BASE_CORE . '/startup/shutdown.php');
				exit;
			}
			
		}
	}
