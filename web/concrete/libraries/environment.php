<?
/**
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Useful functions for getting paths for concrete5 items.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");

class Environment {

	protected $overrides;
	protected $changed = false;
	
	public static function get() {
		static $env;
		if (!isset($env)) {
			$env = new Environment();
		}
		return $env;
	}
	
	protected function parseOverrides() {
		$this->overrides = array();
		$db = Loader::db();
		$r = $db->Execute('select segment, pkgIdentifier, object from OverrideList');
		while ($row = $r->FetchRow()) {
			if ($row['pkgIdentifier']) {
				$this->overrides[$row['segment']][$row['pkgIdentifier']] = unserialize($row['object']);
			} else { 
				$this->overrides[$row['segment']] = unserialize($row['object']);
			}
		}
	}
	
	protected function pathRecordExists($segment, $pkgIdentifier) {
		if (!pkgIdentifier) {
			return isset($this->overrides[$segment][$pkgIdentifier]);
		} else {
			return isset($this->overrides[$segment]);
		}
	}

	protected function getCachedRecord($segment, $pkgIdentifier) {
		if (!pkgIdentifier) {
			return $this->overrides[$segment][$pkgIdentifier];
		} else {
			return $this->overrides[$segment];
		}
	}
	
	public function shutdown() {
		if ($this->changed) {
			$db = Loader::db();
			foreach($this->overrides as $key => $x) {
				if (is_array($x)) {
					foreach($x as $_x => $y) {
						$r = serialize($y);
						$db->Replace('OverrideList', array("id" => md5($key . $_x), "segment" => $key, "pkgIdentifier" => $_x, 'object' => serialize($y)), array('id'), true);
					}
				} else {
					$r = serialize($x);
					$db->Replace('OverrideList', array("id" => md5($key), "segment" => $key, 'object' => $r), array('id'), true);
				}			
			}
		}
	}
	
	protected function saveCachedRecord($obj) {
		if ($obj->requestedPkgIdentifier) {
			$this->overrides[$obj->requestedSegment][$obj->requestedPkgIdentifier] = $obj;
		} else {
			$this->overrides[$obj->requestedSegment] = $obj;
		}
	}
	
	protected function getRecord($segment, $pkgIdentifier = false) {
		
		if (!isset($this->overrides)) {
			$this->parseOverrides();
		}	
		
		if ($this->pathRecordExists($segment, $pkgIndentifier)) {
			return $this->getCachedRecord($segment, $pkgIdentifier);
		}
		$this->changed = true;
		
		if (file_exists(DIR_BASE . '/' . $segment)) {
			$file = DIR_BASE . '/' . $segment;
			$url = BASE_URL . DIR_REL . '/' . $segment;
		}
		
		if (!isset($file) && $pkgIdentifier != false) {
			if (Loader::helper('validation/numbers')->integer($pkgIdentifier)) {
				$pkgHandle = PackageList::getHandle($pkgIdentifier);
			} else {
				$pkgHandle = $pkgIdentifier;
			}
			
			$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
			if (file_exists($dirp . '/' . $segment)) {
				$file = $dirp . '/' . $segment;
				if (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) { 
					$url = BASE_URL . DIR_REL . '/' .DIRNAME_PACKAGES. '/' . $pkgHandle . '/' . $segment;
				} else {
					$url = ASSETS_URL . '/' . DIRNAME_PACKAGES. '/' . $pkgHandle . '/' . $segment;
				}
			}
		}
		
		if (!isset($file)) {
			if (file_exists(DIR_BASE_CORE . '/' . $segment)) {
				$file = DIR_BASE_CORE . '/' . $segment;
				$url = ASSETS_URL . '/' . $segment;
			}
		}
		
		if (isset($file)) {
			$obj = new stdClass;
			$obj->requestedPkgIdentifier = $pkgIdentifier;
			$obj->requestedSegment = $segment;
			$obj->file = $file;
			$obj->url = $url;
			$this->saveCachedRecord($obj);
			return $obj;
		} else {
			return false;
		}
	}
	
	/** 
	 * Returns a full path to the subpath segment. Returns false if not found
	 */
	public function getPath($subpath, $pkgIdentifier = false) {
		$r = $this->getRecord($subpath, $pkgIdentifier);
		return $r->file;	
		return false;
	}
	
	/** 
	 * Returns  a public URL to the subpath item. Returns false if not found
	 */
	public function getURL($subpath, $pkgIdentifier = false) {
		$r = $this->getRecord($subpath, $pkgIdentifier);
		return $r->url;		
		return false;
	}

}