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

class Concrete5_Library_Environment {

	protected $coreOverrides = array();
	protected $corePackages = array();
	protected $coreOverridesByPackage = array();
	protected $overridesScanned = false;
	protected $cachedOverrides = array();
	protected $autoLoaded = false;
	
	public static function get() {
		static $env;
		if (!isset($env)) {
			if (ENABLE_OVERRIDE_CACHE) { 
				$r = Config::get('ENVIRONMENT_CACHE');
				if ($r) {
					$en = @unserialize($r);
					if ($en instanceof Environment) {
						$env = $en;
						$env->autoLoaded = true;
						return $env;
					}
				}
			}
			$env = new Environment();
		}
		return $env;
	}
	
	public function clearOverrideCache() {
		Config::clear("ENVIRONMENT_CACHE");
	}
	
	/** 
	 * Builds a list of all overrides
	 */
	protected function getOverrides() {
		$check = array(DIR_FILES_BLOCK_TYPES, DIR_FILES_CONTROLLERS, DIR_FILES_ELEMENTS, DIR_HELPERS, 
			DIR_FILES_JOBS, DIR_BASE . '/' . DIRNAME_CSS, DIR_BASE . '/' . DIRNAME_JAVASCRIPT, DIR_BASE . '/' . DIRNAME_LANGUAGES,
			DIR_LIBRARIES, DIR_FILES_EMAIL_TEMPLATES, DIR_MODELS, DIR_FILES_CONTENT, DIR_FILES_THEMES, DIR_FILES_TOOLS);
		foreach($check as $loc) {
			if (is_dir($loc)) {
				$rc = new RecursiveDirectoryIterator($loc, RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
				$rcd = new RecursiveIteratorIterator($rc);
				$rgx = new RegexIterator($rcd, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
				foreach ($rgx as $r) {
					foreach($r as $r1) {
						$this->coreOverrides[] = str_replace(DIR_BASE . '/', '', $r1);
					}
				}
			}
		}
		if (is_dir(DIR_PACKAGES_CORE)) { 
			$rc = new RecursiveDirectoryIterator(DIR_PACKAGES_CORE, RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
			foreach ($rc as $r) {
				if (substr($r->getFileName(), 0, 1) != '.') { 
					$this->corePackages[] = $r->getFileName();
				}
			}
		}
		$this->overridesScanned = true;
		if (ENABLE_OVERRIDE_CACHE && !$this->autoLoaded) {
			Config::save('ENVIRONMENT_CACHE', serialize($this));
		}		
	}
	
	public function overrideCoreByPackage($segment, $pkg) {
		$pkgHandle = $pkg->getPackageHandle();
		$this->coreOverridesByPackage[$segment] = $pkgHandle;	
	}
	
	public function getRecord($segment, $pkgHandle = false) {
		
		if (!$this->overridesScanned) {
			$this->getOverrides();
		}	
		
		if (isset($this->cachedOverrides[$segment][$pkgHandle])) {
			return $this->cachedOverrides[$segment][$pkgHandle];
		}
		
		$obj = new stdClass;

		if (!in_array($segment, $this->coreOverrides) && !$pkgHandle && !array_key_exists($segment, $this->coreOverridesByPackage)) {
			$obj->file = DIR_BASE_CORE . '/' . $segment;
			$obj->url = ASSETS_URL . '/' . $segment;
			$obj->override = false;
			$this->cachedOverrides[$segment][''] = $obj;
			return $obj;
		}
			
		if (in_array($segment, $this->coreOverrides)) {
			$obj->file = DIR_BASE . '/' . $segment;
			$obj->url = BASE_URL . DIR_REL . '/' . $segment;
			$obj->override = true;
			$this->cachedOverrides[$segment][''] = $obj;
			return $obj;
		} 

		if (array_key_exists($segment, $this->coreOverridesByPackage)) {
			$pkgHandle = $this->coreOverridesByPackage[$segment];
		}		

		if (!in_array($pkgHandle, $this->corePackages)) {
			$dirp = DIR_PACKAGES . '/' . $pkgHandle;		
			$obj->url = BASE_URL . DIR_REL . '/' . DIRNAME_PACKAGES. '/' . $pkgHandle . '/' . $segment;
		} else {
			$dirp = DIR_PACKAGES_CORE . '/' . $pkgHandle;
			$obj->url = ASSETS_URL . '/' . DIRNAME_PACKAGES. '/' . $pkgHandle . '/' . $segment;
		}
		$obj->file = $dirp . '/' . $segment;
		$obj->override = false;
		$this->cachedOverrides[$segment][$pkgHandle] = $obj;
		return $obj;		
	}
	
	/** 
	 * Returns a full path to the subpath segment. Returns false if not found
	 */
	public function getPath($subpath, $pkgIdentifier = false) {
		$r = $this->getRecord($subpath, $pkgIdentifier);
		return $r->file;	
	}
	
	/** 
	 * Returns  a public URL to the subpath item. Returns false if not found
	 */
	public function getURL($subpath, $pkgIdentifier = false) {
		$r = $this->getRecord($subpath, $pkgIdentifier);
		return $r->url;		
	}

}