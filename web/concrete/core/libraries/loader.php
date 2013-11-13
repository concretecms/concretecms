<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Core
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A wrapper for loading core files, libraries, applications and models. Whenever possible the loader class should be used because it will always know where to look for the proper files, in the proper order.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
 class Concrete5_Library_Loader {
		
		static $autoloadClasses = array();
		
		/** 
		 * Loads a library file, either from the site's files or from Concrete's
		 */
		public static function library($lib, $pkgHandle = null) {
			$env = Environment::get();
			require_once($env->getPath(DIRNAME_LIBRARIES . '/' . $lib . '.php', $pkgHandle));
		}

		/** 
		 * Loads a job file, either from the site's files or from Concrete's
		 */
		public static function job($job, $pkgHandle = null) {
			$env = Environment::get();
			require_once($env->getPath(DIRNAME_JOBS . '/' . $job . '.php', $pkgHandle));
		}

		/** 
		 * Loads a model from either an application, the site, or the core Concrete directory
		 */
		public static function model($mod, $pkgHandle = null) {
			$env = Environment::get();
			$r = self::legacyModel($mod);
			if (!$r) {
				require_once($env->getPath(DIRNAME_MODELS . '/' . $mod . '.php', $pkgHandle));
			}
		}
		
		protected static function legacyModel($model) {
			switch($model) {
				case 'collection_attributes':
				//case 'collection_types':
				case 'user_attributes':
				case 'file_attributes':
					return true;
					break;
				default:
					return false;
					break;
			}
		}
		
		/** 
		 * @access private
		 */
		public function packageElement($file, $pkgHandle, $args = null) {
			self::element($file, $args, $pkgHandle);
		}

		/** 
		 * Loads an element from C5 or the site
		 */
		public function element($_file, $args = null, $_pkgHandle= null) {
			if (is_array($args)) {
				$collisions = array_intersect(array('_file', '_pkgHandle'), array_keys($args));
				if ($collisions) {
					throw new Exception(t("Illegal variable name '%s' in element args.", implode(', ', $collisions)));
				}
				$collisions = null;
				extract($args);
			}

			include(Environment::get()->getPath(DIRNAME_ELEMENTS . '/' . $_file . '.php', $_pkgHandle));
		}

		 /**
		 * Loads a tool file from c5 or site
		 */
		public static function tool($file, $args = null, $pkgHandle= null) {
		   if (is_array($args)) {
			   extract($args);
		   }
			$env = Environment::get();
			require_once($env->getPath(DIRNAME_TOOLS . '/' . $file . '.php', $pkgHandle));
		}
		
		/** 
		 * Registers a component with concrete5's autoloader.
		 */
		public static function registerAutoload($classes) {
			foreach($classes as $class => $data) {	
				if (strpos($class, ',') > -1) {
					$subclasses = explode(',', $class);
					foreach($subclasses as $subclass) {
						self::$autoloadClasses[$subclass][$data[0]] = $data;
					}
				} else {
					self::$autoloadClasses[$class][$data[0]] = $data;
				}
			}				
		}
		
		protected static function getFileFromCorePath($type, $found) {
			$classes = self::$autoloadClasses;
			$cl = $classes[$found][$type];
			if ($cl) {
				$file = $cl[1];
			} else {
				$file = str_replace('_', '/', $found);
				$path = explode('/', $file);
				if (count($path) > 0) {
					$file = '';
					for ($i = 0; $i < count($path); $i++) {
						$p = $path[$i];
						$file .= Object::uncamelcase($p);
						if (($i + 1) < count($path)) {
							$file .= '/';
						}							
					}
				} else {
					$file = Object::uncamelcase($file);				
				}
			}
			return $file;
		}
		
		public static function autoloadCore($class) {
			if (stripos($class, $m = 'Concrete5_Model_') === 0) {
				$file = self::getFileFromCorePath('model', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_MODELS . '/' . $file . '.php');
			}
			elseif (stripos($class, $m = 'Concrete5_Library_') === 0) {
				$file = self::getFileFromCorePath('library', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_LIBRARIES . '/' . $file . '.php');
			}
			elseif (stripos($class, $m = 'Concrete5_Helper_') === 0) {
				$file = self::getFileFromCorePath('helper', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_HELPERS . '/' . $file . '.php');
			}
			elseif (stripos($class, $m = 'Concrete5_Controller_Block_') === 0) {
				$file = self::getFileFromCorePath('block_controller', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_BLOCKS . '/' . $file. '.php');
			}
			elseif (stripos($class, $m = 'Concrete5_Controller_PageType_') === 0) {
				$file = self::getFileFromCorePath('page_type_controller', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/' . $file. '.php');

			} elseif (preg_match('/^Concrete5_Controller_AuthenticationType_(.*)/i', $class, $m)) {
				$file = self::getFileFromCorePath('authentication_type_controller', $m[1]);
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_MODELS . '/' . DIRNAME_AUTHENTICATION . '/' . DIRNAME_AUTHENTICATION_TYPES . '/' . $file . '.php');
			} elseif (preg_match('/^Concrete5_Controller_AttributeType_(.*)/i', $class, $m)) {
				$file = self::getFileFromCorePath('attribute_type_controller', $m[1]);
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' . $file . '.php');
			}
			elseif (stripos($class, $m = 'Concrete5_Controller_Page_') === 0) {
				$file = self::getFileFromCorePath('page_controller', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGES . '/' . $file . '.php');
			}
			elseif (stripos($class, $m = 'Concrete5_Controller_Panel_') === 0) {
				$file = self::getFileFromCorePath('panel_controller', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PANELS . '/' . $file . '.php');
			}
			elseif (stripos($class, $m = 'Concrete5_Controller_') === 0) {
				$file = self::getFileFromCorePath('page_controller', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_CONTROLLERS . '/' . $file . '.php');
			}
			elseif (stripos($class, $m = 'Concrete5_Job_') === 0) {
				$file = self::getFileFromCorePath('job', substr($class, strlen($m)));
				require_once(DIR_BASE_CORE . '/' . DIRNAME_CORE_CLASSES . '/' . DIRNAME_JOBS . '/' . $file . '.php');
			}
		}
		
		/** 
		 * @private
		 */
		public static function autoload($class) {
			$classes = self::$autoloadClasses;
			$clx = $classes[$class];
			if (is_array($clx)) {
				$k = key($clx);
				$cl = $clx[$k];
				call_user_func_array(array(__CLASS__, $cl[0]), array($cl[1], $cl[2]));
			} else {
				if (strpos($class, 'BlockController') > 0) {
					$class = substr($class, 0, strpos($class, 'BlockController'));
					$handle = Object::uncamelcase($class);
					self::block($handle);
				} else if (strpos($class, 'PanelController') > 0) {
					$env = Environment::get();
					$class = substr($class, 0, strpos($class, 'PanelController'));
					$path = Object::uncamelcase($class);
					$path = $env->getPath(DIRNAME_CONTROLLERS . '/' . DIRNAME_PANELS . '/' . $path . '.php', $pkgHandle);
					require_once($path);
				} else if (strpos($class, 'Controller') > 0) {
					$env = Environment::get();
					$class = substr($class, 0, strpos($class, 'Controller'));
					$handle = Object::uncamelcase($class);
					$path = str_replace('_', '/', $handle);
					$path = $env->getPath(DIRNAME_CONTROLLERS . '/' . $path . '.php', $pkgHandle);
					require_once($path);
				} else if (strpos($class, 'AttributeType') > 0) {
					$class = substr($class, 0, strpos($class, 'AttributeType'));
					$handle = Object::uncamelcase($class);
					$at = AttributeType::getByHandle($handle);
				} else 	if (strpos($class, 'Helper') > 0) {
					$class = substr($class, 0, strpos($class, 'Helper'));
					$handle = Object::uncamelcase($class);
					$handle = preg_replace('/^site_/', '', $handle);
					self::helper($handle);
				}
			}
		}
		
		/** 
		 * Loads a block's controller/class into memory. 
		 * <code>
		 * <?php self::block('autonav'); ?>
		 * </code>
		 */
		public static function block($bl) {
			$db = self::db();
			$pkgHandle = $db->GetOne('select pkgHandle from Packages left join BlockTypes on BlockTypes.pkgID = Packages.pkgID where BlockTypes.btHandle = ?', array($bl));
			$env = Environment::get();
			require_once($env->getPath(DIRNAME_BLOCKS . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER, $pkgHandle));
		}
		
		/** 
		 * Loads the various files for the database abstraction layer. We would bundle these in with the db() method below but
		 * these need to be loaded before the models which need to be loaded before db() 
		 */
		public function database() {
			require(DIR_BASE_CORE . '/libraries/3rdparty/adodb/adodb.inc.php');
			require(DIR_BASE_CORE . '/libraries/3rdparty/adodb/adodb-exceptions.inc.php');
			require(DIR_BASE_CORE . '/libraries/3rdparty/adodb/adodb-active-record.inc.php');
			require(DIR_BASE_CORE . '/libraries/3rdparty/adodb/adodb-xmlschema03.inc.php');
			require(DIR_BASE_CORE . '/libraries/database.php');
		}
		
		/** 
		 * Returns the database object, or loads it if not yet created
		 * <code>
		 * <?php
		 * $db = Loader::db();
		 * $db->query($sql);
		 * </code>
		 */
		public static function db($server = null, $username = null, $password = null, $database = null, $create = false, $autoconnect = true) {
			static $_dba;
			if ((!isset($_dba) || $create) && ($autoconnect)) {
				if ($server == null && defined('DB_SERVER')) {	
					$dsn = DB_TYPE . '://' . DB_USERNAME . ':' . rawurlencode(DB_PASSWORD) . '@' . rawurlencode(DB_SERVER) . '/' . DB_DATABASE;
				} else if ($server) {
					$dsn = DB_TYPE . '://' . $username . ':' . rawurlencode($password) . '@' . rawurlencode($server) . '/' . $database;
				}

				if (isset($dsn) && $dsn) {
					$_dba = @NewADOConnection($dsn);
					if (is_object($_dba)) {
						$_dba->setFetchMode(ADODB_FETCH_ASSOC);
						if (DB_CHARSET != '') {
							$names = 'SET NAMES \'' . DB_CHARSET . '\'';
							if (DB_COLLATE != '') {
								$names .= ' COLLATE \'' . DB_COLLATE . '\'';
							}
							$_dba->Execute($names);
						}
						
						ADOdb_Active_Record::SetDatabaseAdapter($_dba);
					} else if (defined('DB_SERVER')) {
						$v = View::getInstance();
						$v->renderError(t('Unable to connect to database.'), t('A database error occurred while processing this request.'));
					}
				} else {
					return false;
				}
			}
			
			//$_dba->LogSQL(true);
			//global $ADODB_PERF_MIN;
			//$ADODB_PERF_MIN = 0;

			return $_dba;
		}
		
		/** 
		 * Loads a helper file. If the same helper file is contained in both the core concrete directory and the site's directory, it will load the site's first, which could then extend the core.
		 */
		public static function helper($file, $pkgHandle = false) {
		
			static $instances = array();

			$class = Object::camelcase($file) . "Helper";
			$siteclass = "Site" . Object::camelcase($file) . "Helper";

			if (array_key_exists($class, $instances)) {
            	$instance = $instances[$class];
			} else if (array_key_exists($siteclass, $instances)) {
            	$instance = $instances[$siteclass];
			} else {

				$env = Environment::get();
				$f1 = $env->getRecord(DIRNAME_HELPERS . '/' . $file . '.php', $pkgHandle);
				require_once($f1->file);
				if ($f1->override) {
					if (class_exists($siteclass, false)) {
						$class = $siteclass;
					}
				} else if ($pkgHandle) {
					$pkgclass = Object::camelcase($pkgHandle . '_' . $file) . "Helper";
					if (class_exists($pkgclass, false)) {
						$class = $pkgclass;
					}
				}


				$instance = new $class();
				if (!property_exists($instance, 'helperAlwaysCreateNewInstance') || $instance->helperAlwaysCreateNewInstance == false) {
		            $instances[$class] = $instance;
		        }
			}
			
			if(method_exists($instance,'reset')) {
				$instance->reset();
			}
			
			return $instance;
		}
		
		/**
		 * @access private
		 */
		public function package($pkgHandle) {
			// loads and instantiates the object
			$env = Environment::get();
			$path = $env->getPath(FILENAME_PACKAGE_CONTROLLER, $pkgHandle);
			if (file_exists($path)) {
				require_once($path);
			}
			$class = Object::camelcase($pkgHandle) . "Package";
			if (class_exists($class)) {
				$cl = new $class;
				return $cl;
			}
		}
		
		/**
		 * @access private
		 */
		public function startingPointPackage($pkgHandle) {
			// loads and instantiates the object
			$dir = (is_dir(DIR_STARTING_POINT_PACKAGES . '/' . $pkgHandle)) ? DIR_STARTING_POINT_PACKAGES : DIR_STARTING_POINT_PACKAGES_CORE;
			if (file_exists($dir . '/' . $pkgHandle . '/' . FILENAME_PACKAGE_CONTROLLER)) {
				require_once($dir . '/' . $pkgHandle . '/' . FILENAME_PACKAGE_CONTROLLER);
				$class = Object::camelcase($pkgHandle) . "StartingPointPackage";
				if (class_exists($class)) {
					$cl = new $class;
					return $cl;
				}
			}
		}
		

		/** 
		 * Gets the path to a particular page type controller
		 */
		public function pageTypeControllerPath($ctHandle) {			
			$ct = PageType::getByHandle($ctHandle);
			if (!is_object($ct)) {
				return false;
			}			
			$pkgHandle = $ct->getPackageHandle();
			$env = Environment::get();
			$path = $env->getPath(DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php', $pkgHandle);
			if (file_exists($path)) {
				return $patpkgHandleh;
			}
		}

		protected static function singlePageControllerPage($path, $pkgHandle) {			
			$env = Environment::get();
			$f1 = $env->getRecord(DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGES . $path . '/' . FILENAME_COLLECTION_CONTROLLER, $pkgHandle);
			$f2 = $env->getRecord(DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGES . $path . '.php', $pkgHandle);
			if ($f1->exists()) {
				return $f1->file;
			} else if ($f2->exists()) {
				return $f2->file;
			}
		}
		
		/** 
		 * Loads a controller object
		 */
		public function controller($mixed) {
			$env = Environment::get();
			if ($mixed instanceof Block || $mixed instanceof BlockType) {
				$class = Object::camelcase($mixed->getBlockTypeHandle()) . 'BlockController';
				return new $class($mixed);
			}
			if ($mixed instanceof Page) {
				$class = 'PageController';
				if ($mixed->getPageTypeID() > 0) {
					$ptHandle = $mixed->getPageTypeHandle();
					$path = self::pageTypeControllerPath($ptHandle, $mixed->getPackageHandle());
					if ($path) {
						require_once($path);
						$class = Object::camelcase($ptHandle) . 'PageTypeController';
					}
				} else if ($mixed->isGeneratedCollection()) {
					$file = $mixed->getCollectionFilename();
					if (strpos($file, '/' . FILENAME_COLLECTION_VIEW) !== false) {
						$path = substr($file, 0, strpos($file, '/'. FILENAME_COLLECTION_VIEW));
					} else {
						$path = substr($file, 0, strpos($file, '.php'));
					}
					$file = self::singlePageControllerPage($path, $mixed->getPackageHandle());
					if ($file) {
						require_once($file);
						$class = Object::camelcase($path) . 'PageController';
					}
				}
				return new $class($mixed);
			} else if (is_string($mixed)) {
				// now we test to see if this is, in fact, a page.
				// now, in one case loader::controller('/dashboard') we DON'T want to instantiate the 
				// controller, because it's only ever going to be extended by page controllers.
				// so we load it but we DON'T instantiate.
				if ($mixed != '/dashboard') {
					$page = Page::getByPath($mixed);
				}

				if (is_object($page) && !$page->isError()) {
					$class = Object::camelcase($mixed) . 'PageController';
					$pathPrefix = DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGES;
				} else {
					$class = Object::camelcase($mixed) . 'Controller';
					$pathPrefix = DIRNAME_CONTROLLERS;
				}

				$f1 = $env->getRecord($pathPrefix . $mixed . '/' . FILENAME_COLLECTION_CONTROLLER);
				$f2 = $env->getRecord($pathPrefix . $mixed . '.php');
				if ($f1->exists()) {
					require_once($f1->file);
				} else if ($f2->exists()) {
					require_once($f2->file);
				}
				// in this case, we don't return the object.
				// we don't autoload the class exists because we have some classes that
				// resolve to different classes than their paths.
				if (($f1->exists() || $f2->exists()) && class_exists($class, false) && $class != 'DashboardController') {
					if (is_object($page)) {
						return new $class($page);
					} else {
						return new $class($mixed);
					}
				}
			}
		}
	}
