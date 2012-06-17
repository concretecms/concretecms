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
 
 class Loader {
		
		static $autoloadClasses = array();
		
		/** 
		 * Loads a library file, either from the site's files or from Concrete's
		 */
		public function library($lib, $pkgHandle = null) {
			$env = Environment::get();
			require_once($env->getPath(DIRNAME_LIBRARIES . '/' . $lib . '.php', $pkgHandle));
		}

		/** 
		 * Loads a model from either an application, the site, or the core Concrete directory
		 */
		public function model($mod, $pkgHandle = null) {
			$env = Environment::get();
			$r = Loader::legacyModel($mod);
			if (!$r) {
				require_once($env->getPath(DIRNAME_MODELS . '/' . $mod . '.php', $pkgHandle));
			}
		}
		
		protected function legacyModel($model) {
			switch($model) {
				case 'collection_attributes':
					Loader::model('attribute/categories/collection');
					return true;
					break;
				case 'user_attributes':
					Loader::model('attribute/categories/user');
					return true;
					break;
				case 'file_attributes':
					Loader::model('attribute/categories/file');
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
			Loader::element($file, $args, $pkgHandle);
		}

		/** 
		 * Loads an element from C5 or the site
		 */
		public function element($file, $args = null, $pkgHandle= null) {
			if (is_array($args)) {
				extract($args);
			}

			$env = Environment::get();
			include($env->getPath(DIRNAME_ELEMENTS . '/' . $file . '.php', $pkgHandle));
		}

		 /**
		 * Loads a tool file from c5 or site
		 */
		public function tool($file, $args = null, $pkgHandle= null) {
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
						Loader::$autoloadClasses[$subclass] = $data;
					}
				} else {
					Loader::$autoloadClasses[$class] = $data;
				}
			}				
		}
		
		/** 
		 * @private
		 */
		public static function autoload($class) {
			$classes = Loader::$autoloadClasses;
			$cl = $classes[$class];
			if ($cl) {
				call_user_func_array(array('Loader', $cl[0]), array($cl[1], $cl[2]));
			} else {
				/* lets handle some things slightly more dynamically */
				$txt = Loader::helper('text');
				if (strpos($class, 'BlockController') > 0) {
					$class = substr($class, 0, strpos($class, 'BlockController'));
					$handle = $txt->uncamelcase($class);
					Loader::block($handle);
				} else if (strpos($class, 'AttributeType') > 0) {
					$class = substr($class, 0, strpos($class, 'AttributeType'));
					$handle = $txt->uncamelcase($class);
					$at = AttributeType::getByHandle($handle);
				} else 	if (strpos($class, 'Helper') > 0) {
					$class = substr($class, 0, strpos($class, 'Helper'));
					$handle = $txt->uncamelcase($class);
					$handle = preg_replace('/^site_/', '', $handle);
					Loader::helper($handle);
				}
			}
		}
		
		/** 
		 * Loads a block's controller/class into memory. 
		 * <code>
		 * <?php Loader::block('autonav'); ?>
		 * </code>
		 */
		public function block($bl) {
			if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER)) {
				require_once(DIR_FILES_BLOCK_TYPES . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER);
			} else if (file_exists(DIR_FILES_BLOCK_TYPES_CORE . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER)) {
				require_once(DIR_FILES_BLOCK_TYPES_CORE . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER);
			} else {
				// we haven't found it anywhere so we need to try applications
				// this is last because it's kind of a performance drain to run all the time
				// but that will be less of a problem when we cache the block types request
				$bt = BlockType::getByHandle($bl);
				if (is_object($bt)) { 
					$pkg = $bt->getPackageHandle();
					
					if (file_exists(DIR_PACKAGES . '/' . $pkg . '/' . DIRNAME_BLOCKS . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER)) {
						require_once(DIR_PACKAGES . '/' . $pkg . '/' . DIRNAME_BLOCKS . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER);		
					} else if (file_exists(DIR_PACKAGES_CORE . '/' . $pkg . '/' . DIRNAME_BLOCKS . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER)) {
						require_once(DIR_PACKAGES_CORE . '/' . $pkg . '/' . DIRNAME_BLOCKS . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER);
					}
				}
			}
		}
		
		/** 
		 * Loads the various files for the database abstraction layer. We would bundle these in with the db() method below but
		 * these need to be loaded before the models which need to be loaded before db() 
		 */
		public function database() {
			require_once(DIR_BASE_CORE . '/libraries/3rdparty/adodb/adodb.inc.php');
			require_once(DIR_BASE_CORE . '/libraries/3rdparty/adodb/adodb-exceptions.inc.php');
			require_once(DIR_BASE_CORE . '/libraries/3rdparty/adodb/adodb-active-record.inc.php');
			require_once(DIR_BASE_CORE . '/libraries/3rdparty/adodb/adodb-xmlschema03.inc.php');
			require_once(DIR_BASE_CORE . '/libraries/database.php');
		}
		
		/** 
		 * Returns the database object, or loads it if not yet created
		 * <code>
		 * <?php
		 * $db = Loader::db();
		 * $db->query($sql);
		 * </code>
		 */
		public function db($server = null, $username = null, $password = null, $database = null, $create = false, $autoconnect = true) {
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
			
			return $_dba;
		}
		
		/** 
		 * Loads a helper file. If the same helper file is contained in both the core concrete directory and the site's directory, it will load the site's first, which could then extend the core.
		 */
		public function helper($file, $pkgHandle = false) {
		
			static $instances = array();
			$class = false;		
			
			if ($pkgHandle != false) {
				$class = Object::camelcase($pkgHandle . '_' . $file) . "Helper";
				$dir = (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) ? DIR_PACKAGES : DIR_PACKAGES_CORE;
				require_once($dir . '/' . $pkgHandle . '/' . DIRNAME_HELPERS . '/' . $file . '.php');
				if (!class_exists($class, false)) {
					$class = Object::camelcase($file) . "Helper";
				}
			} else if (file_exists(DIR_HELPERS . '/' . $file . '.php')) {
				// first we check if there's an object of the SAME kind in the core. If so, then we load the core first, then, we load the second one (site)
				// and we hope the second one EXTENDS the first
				if (file_exists(DIR_HELPERS_CORE . '/' . $file . '.php')) {
					$class = "Site" . Object::camelcase($file) . "Helper";
				} else {
					$class = Object::camelcase($file) . "Helper";
				}
			} else {
				$class = Object::camelcase($file) . "Helper";					
			}
			
			if (array_key_exists($class, $instances)) {
            	$instance = $instances[$class];
            } else {
				if ($pkgHandle != false) {
					// already handled by code above.
				} else if (file_exists(DIR_HELPERS . '/' . $file . '.php')) {
					// first we check if there's an object of the SAME kind in the core. If so, then we load the core first, then, we load the second one (site)
					// and we hope the second one EXTENDS the first
					if (file_exists(DIR_HELPERS_CORE . '/' . $file . '.php')) {
						require_once(DIR_HELPERS_CORE . '/' . $file . '.php');
						require_once(DIR_HELPERS . '/' . $file . '.php');
					} else {
						require_once(DIR_HELPERS . '/' . $file . '.php');
					}
				} else {
					require_once(DIR_HELPERS_CORE . '/' . $file . '.php');
				}

	            $instances[$class] = new $class();
    	        $instance = $instances[$class];
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
			$dir = (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) ? DIR_PACKAGES : DIR_PACKAGES_CORE;
			if (file_exists($dir . '/' . $pkgHandle . '/' . FILENAME_PACKAGE_CONTROLLER)) {
				require_once($dir . '/' . $pkgHandle . '/' . FILENAME_PACKAGE_CONTROLLER);
				$class = Object::camelcase($pkgHandle) . "Package";
				if (class_exists($class)) {
					$cl = new $class;
					return $cl;
				}
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
			
			Loader::model('collection_types');
			$ct = CollectionType::getByHandle($ctHandle);
			if (!is_object($ct)) {
				return false;
			}
			$pkgHandle = $ct->getPackageHandle();

			if ($pkgHandle != '') {
				$packageDir = (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) ? DIR_PACKAGES : DIR_PACKAGES_CORE;
			}

			if (file_exists(DIR_FILES_CONTROLLERS . "/" . DIRNAME_PAGE_TYPES . "/{$ctHandle}.php")) {
				$path = DIR_FILES_CONTROLLERS . "/" . DIRNAME_PAGE_TYPES . "/{$ctHandle}.php";
			} else if (isset($packageDir) && (file_exists($packageDir . '/' . $pkgHandle . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php'))) {
				$path = $packageDir . '/' . $pkgHandle . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php';
			} else if (file_exists(DIR_FILES_CONTROLLERS_REQUIRED . "/" . DIRNAME_PAGE_TYPES . "/{$ctHandle}.php")) {
				$path = DIR_FILES_CONTROLLERS_REQUIRED . "/" . DIRNAME_PAGE_TYPES . "/{$ctHandle}.php";
			}
			
			return $path;
		}
		/** 
		 * Loads a controller for either a page or view
		 */
		public function controller($item) {
			
			$include = false;
			
			if (is_string($item)) {
				$db = Loader::db();
				if (is_object($db)) {
					try {
						$_item = Page::getByPath($item);
						if ($_item->isError()) {
							$path = $item;
						} else {
							$item = $_item;
						}
					} catch(Exception $e) {
						$path = $item;
					}
				} else {
					$path = $item;
				}
			}
			
			if ($item instanceof Page) {
				$c = $item;
				if ($c->getCollectionTypeID() > 0) {					
					$ctHandle = $c->getCollectionTypeHandle();
					$path = Loader::pageTypeControllerPath($ctHandle, $item->getPackageHandle());
					if ($path != false) {
						require_once($path);
						$class = Object::camelcase($ctHandle) . 'PageTypeController';
					}
				} else if ($c->isGeneratedCollection()) {
					$file = $c->getCollectionFilename();
					if ($file != '') {
						// strip off PHP suffix for the $path variable, which needs it gone
						if (strpos($file, '/' . FILENAME_COLLECTION_VIEW) !== false) {
							$path = substr($file, 0, strpos($file, '/'. FILENAME_COLLECTION_VIEW));
						} else {
							$path = substr($file, 0, strpos($file, '.php'));
						}
					}
				}
			} else if ($item instanceof Block || $item instanceof BlockType) {
				if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $item->getBlockTypeHandle() . '/' . FILENAME_BLOCK_CONTROLLER)) {
					require_once(DIR_FILES_BLOCK_TYPES . "/" . $item->getBlockTypeHandle() . "/" . FILENAME_BLOCK_CONTROLLER);
				} else if (file_exists(DIR_FILES_BLOCK_TYPES_CORE . '/' . $item->getBlockTypeHandle() . '/' . FILENAME_BLOCK_CONTROLLER)) {
					require_once(DIR_FILES_BLOCK_TYPES_CORE . "/" . $item->getBlockTypeHandle() . "/" . FILENAME_BLOCK_CONTROLLER);
				} else if ($item->getPackageID() > 0 && file_exists(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $item->getBlockTypeHandle() . '/' . FILENAME_BLOCK_CONTROLLER)) {
					require_once(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $item->getBlockTypeHandle() . '/' . FILENAME_BLOCK_CONTROLLER);
				} else if ($item->getPackageID() > 0 && file_exists(DIR_PACKAGES_CORE . '/' . $item->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $item->getBlockTypeHandle() . '/' . FILENAME_BLOCK_CONTROLLER)) {
					require_once(DIR_PACKAGES_CORE . '/' . $item->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $item->getBlockTypeHandle() . '/' . FILENAME_BLOCK_CONTROLLER);
				} 
				$class = Object::camelcase($item->getBlockTypeHandle()) . 'BlockController';
				if (class_exists($class) && $item instanceof BlockType) {
					$controller = new $class($item);
				}
				
				if ($item instanceof Block) {
					$c = $item->getBlockCollectionObject();
				}
				
			}
			
			$controllerFile = $path . '.php';

			if ($path != '') {
				if (file_exists(DIR_FILES_CONTROLLERS . $controllerFile)) {
					require_once(DIR_FILES_CONTROLLERS . $controllerFile);
					$include = true;
				} else if (file_exists(DIR_FILES_CONTROLLERS . $path . '/' . FILENAME_COLLECTION_CONTROLLER)) {
					require_once(DIR_FILES_CONTROLLERS . $path . '/' . FILENAME_COLLECTION_CONTROLLER);
					$include = true;
				} else if (is_object($item)) {
					if ($item->getPackageID() > 0 && (file_exists(DIR_FILES_CONTROLLERS . $controllerFile))) {
						require_once(DIR_FILES_CONTROLLERS . $controllerFile);
						$include = true;
					} else if ($item->getPackageID() > 0 && (file_exists(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $controllerFile))) {
						require_once(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $controllerFile);
						$include = true;
					} else if ($item->getPackageID() > 0 && (file_exists(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $path . '/'. FILENAME_COLLECTION_CONTROLLER))) {
						require_once(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $path . '/'. FILENAME_COLLECTION_CONTROLLER);
						$include = true;
					} else if ($item->getPackageID() > 0 && (file_exists(DIR_PACKAGES_CORE . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $controllerFile))) {
						require_once(DIR_PACKAGES_CORE . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $controllerFile);
						$include = true;
					} else if ($item->getPackageID() > 0 && (file_exists(DIR_PACKAGES_CORE . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $path . '/'. FILENAME_COLLECTION_CONTROLLER))) {
						require_once(DIR_PACKAGES_CORE . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $path . '/'. FILENAME_COLLECTION_CONTROLLER);
						$include = true;
					}
				}
				
				if (!$include) {
					if (file_exists(DIR_FILES_CONTROLLERS_REQUIRED . $controllerFile)) {
						require_once(DIR_FILES_CONTROLLERS_REQUIRED . $controllerFile);
						$include = true;
					} else if (file_exists(DIR_FILES_CONTROLLERS_REQUIRED . $path . '/' . FILENAME_COLLECTION_CONTROLLER)) {
						require_once(DIR_FILES_CONTROLLERS_REQUIRED . $path . '/' . FILENAME_COLLECTION_CONTROLLER);
						$include = true;
					}
				}
					
				if ($include) {
					$class = Object::camelcase($path) . 'Controller';
				}
			}
			
			if (!isset($controller)) {
				if ($class && class_exists($class)) {
					// now we get just the filename for this guy, so we can extrapolate
					// what our controller is named
					$controller = new $class($item);
				} else {
					$controller = new Controller($item);
				}
			}
			
			if (isset($c) && is_object($c)) {
				$controller->setCollectionObject($c);
			}
			
			return $controller;
		}

	}
