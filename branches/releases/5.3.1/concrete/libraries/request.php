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
 * An object that represents a particular request to the Concrete-powered website. The request object then determines what is being requested, based on the path, and presents itself to the rest of the dispatcher (which loads the page, etc...)
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Request {

	private $requestPath;
	private $task;
	private $params;
	private $includeType;
	private $filename;
	private $cID;
	private $cPath;
	
	// parses the current request and returns an 
	// object with tasks, tools, etc... defined in them
	// for use in the dispatcher
	// Thanks to Code Igniter for some of this code (in terms of getenv(), etc...)
	
	private static function parsePathFromRequest($var) {
		$path = (isset($_SERVER[$var])) ? $_SERVER[$var] : @getenv($var);
		$replace[] = DIR_REL . '/' . DISPATCHER_FILENAME;
		if (DIR_REL != '') {
			$replace[] = DIR_REL . '/';
		}
		$path = str_replace($replace, '', $path);
		$path = trim($path, '/');
		return $path;
	}
	
	public function __construct($path) {
		$this->requestPath = $path;
		$this->parse();
	}
	
	/** 
	 * Gets a request object for the current request. Parses PATH_INFO as necessary.
	 * @return Request
	 */
	public static function get() {
		static $req;
		if (!isset($req)) {
			$path = Request::parsePathFromRequest('ORIG_PATH_INFO');
			if (!$path) {
				$path = Request::parsePathFromRequest('PATH_INFO');
			}
			if (!$path) {
				$path = Request::parsePathFromRequest('SCRIPT_NAME');
			}
			$req = new Request($path);
		}
		return $req;
	}
	
	private function parse() {
		
		$path = $this->requestPath;
		
		if ($_REQUEST['cID'] && intval($_REQUEST['cID']) > 0) {
			$this->cID = $_REQUEST['cID'];
		} else {
			$this->cID = HOME_CID;
		}
		// home page w/param and task
		
		if (preg_match("/^\-\/(.[^\/]*)\/(.*)/i", $path, $matches)) {
			$this->task = $matches[1];
			$this->params = $matches[2];
			return;
		}

		// home page w/just task
		if (preg_match("/^\-\/(.[^\/]*)/i", $path, $matches)) {
			$this->task = $matches[1];
			return;
		}

		// path + task + params
		if (preg_match("/^(.[^\.]*)\/\-\/(.[^\/]*)\/(.*)/i", $path, $matches)) {
			$this->cPath = $matches[1];
			$this->task = $matches[2];
			$this->params = $matches[3];
			return;
		}
		
		// path + task
		if (preg_match("/^(.[^\.]*)\/\-\/(.[^\/]*)/i", $path, $matches)) {
			$this->cPath = $matches[1];
			$this->task = $matches[2];
			return;
		}

		// tools

		if (preg_match("/^tools\/blocks\/(.[^\/]*)\/(.[^\.]*).php|^tools\/blocks\/(.[^\/]*)\/(.[^\.]*)/i", $path, $matches)) {
			if (isset($matches[4])) {
				$this->filename = $matches[4] . '.php';
				$this->btHandle = $matches[3];
			} else {
				$this->filename = $matches[2] . '.php';
				$this->btHandle = $matches[1];
			}
			$this->includeType = 'BLOCK_TOOL';
			return;
		}

		// theme-based css
		if (preg_match("/^tools\/css\/themes\/(.[^\/]*)\/(.[^\.]*).css/i", $path, $matches)) {
			$this->filename = 'css.php';
			$this->includeType = 'CONCRETE_TOOL';
			$this->auxData = new stdClass;
			$this->auxData->theme = $matches[1];
			$this->auxData->file = $matches[2] . '.css';
			
			return;
		}

		if (preg_match("/^tools\/packages\/(.[^\/]*)\/(.[^\.]*).php|^tools\/packages\/(.[^\/]*)\/(.[^\.]*)/i", $path, $matches)) {
			if (isset($matches[4])) {
				$this->filename = $matches[4] . '.php';
				$this->pkgHandle = $matches[3];
			} else {
				$this->filename = $matches[2] . '.php';
				$this->pkgHandle = $matches[1];
			}
			$this->includeType = 'PACKAGE_TOOL';
			return;
		}

		if (preg_match("/^tools\/required\/(.[^\.]*).php|^tools\/required\/(.[^\.]*)/i", $path, $matches)) {
			if (isset($matches[2])) {
				$this->filename = $matches[2] . '.php';
			} else {
				$this->filename = $matches[1] . '.php';
			}
			$this->includeType = 'CONCRETE_TOOL';
			return;
		}

		if (preg_match("/^tools\/(.[^\.\/]*).php|^tools\/(.[^\.\/]*)/i", $path, $matches)) {
			if (isset($matches[2])) {
				$this->filename = $matches[2] . '.php';
			} else {
				$this->filename = $matches[1] . '.php';
			}
			$this->includeType = 'TOOL';
			return;
		}

		
		// just path
		if (preg_match("/^(.[^\.]*)/i", $path, $matches)) {
			$this->cPath = $matches[1];
			return;
		}

	}
	
	/** 
	 * Gets the path of the current request
	 */
	public function getRequestPath() {
		return $this->requestPath;
	}

	/** 
	 * Gets the current collection path as contained in the current request 
	 */
	public function getRequestCollectionPath() {
		// I think the regexps take care of the trimming for us but just to be sure..
		$cPath = trim($this->cPath, '/');
		if ($cPath != '') {
			return '/' . $cPath;
		}
		return '';
	}

	/** 
	 * Gets page ID of the current request 
	 * @return int
	 */
	public function getRequestCollectionID() {
		return $this->cID;
	}
	
	/** 
	 * Gets the current MVC task of the request
	 * @return string
	 */
	public function getRequestTask() {
		return $this->task;
	}

	/** 
	 * Gets the array of parameters for this current MVC task
	 */
	public function getRequestTaskParameters() {
		return $this->params;
	}
	
	/** 
	 * Returns whether this request wants to include a file (typically a tool)
	 * @return bool
	 */
	public function isIncludeRequest() {
		return $this->includeType != null;
	}
	
	/** 
	 * Gets the include type of the current request
	 */
	public function getIncludeType() {
		return $this->includeType;
	}

	/** 
	 * If the current request wants to include a file, this returns the filename it wants to include
	 */
	public function getFilename() {
		return $this->filename;
	}
	
	/** 
	 * Gets the block requested by the current request
	 */
	public function getBlock() {
		return $this->btHandle;
	}
	
	/** 
	 * Auxiliary data is anything that the request specifies that doesn't really fit inside the request object, but gets passed along anyway
	 */
	public function getAuxiliaryData() {
		return $this->auxData;
	}
	
	/** 
	 * Gets the package requested by the current request
	 */
	public function getPackageHandle() {
		return $this->pkgHandle;
	}
}