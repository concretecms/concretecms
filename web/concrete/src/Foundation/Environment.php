<?php
namespace Concrete\Core\Foundation;
/**
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
use Config;

/**
 * Useful functions for getting paths for concrete5 items.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */


class Environment {

	protected $coreOverrides = array();
	protected $corePackages = array();
	protected $coreOverridesByPackage = array();
	protected $overridesScanned = false;
	protected $cachedOverrides = array();
	protected $autoLoaded = false;

	public static function get() {
		static $env;
		if (!isset($env)) {
			if (file_exists(Config::get('concrete.cache.directory') . '/' . Config::get('concrete.cache.environment.file'))) {
				$r = @file_get_contents(Config::get('concrete.cache.directory') . '/' . Config::get('concrete.cache.environment.file'));
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

	public static function saveCachedEnvironmentObject() {
		if (!file_exists(Config::get('concrete.cache.directory') . '/' . Config::get('concrete.cache.environment.file'))) {
			$env = new Environment();
			$env->getOverrides();
			@file_put_contents(Config::get('concrete.cache.directory') . '/' . Config::get('concrete.cache.environment.file'), serialize($env));
		}
	}

	public function clearOverrideCache() {
		@unlink(Config::get('concrete.cache.directory') . '/' . Config::get('concrete.cache.environment.file'));
		$this->overridesScanned = false;
	}


	/**
	 * @access private
	 */
	protected $ignoreFiles = array('__MACOSX');

	public function reset() {
		$this->ignoreFiles = array('__MACOSX');
	}


	/**
	 * Builds a list of all overrides
	 */
	protected function getOverrides() {

		$check = array(
			DIR_FILES_BLOCK_TYPES, 
			DIR_FILES_CONTROLLERS, 
			DIR_FILES_ELEMENTS,
			DIR_APPLICATION . '/' . DIRNAME_ATTRIBUTES, 
			DIR_APPLICATION . '/' . DIRNAME_AUTHENTICATION,
			DIR_FILES_JOBS, 
			DIR_APPLICATION . '/' . DIRNAME_CSS, 
			DIR_APPLICATION . '/' . DIRNAME_JAVASCRIPT, 
			DIR_APPLICATION . '/' . DIRNAME_LANGUAGES,
			DIR_FILES_EMAIL_TEMPLATES, 
			DIR_FILES_CONTENT, 
			DIR_FILES_THEMES, 
			DIR_FILES_TOOLS, 
			DIR_APPLICATION . '/' . DIRNAME_PAGE_TYPES, 
			DIR_APPLICATION . '/' . DIRNAME_CLASSES);
		foreach($check as $loc) {
			if (is_dir($loc)) {
				$contents = $this->getDirectoryContents($loc, array(), true);
				foreach($contents as $f) {
					$item = str_replace(DIR_APPLICATION . '/', '', $f);
					$item = str_replace(DIR_BASE . '/', '', $item);
					$this->coreOverrides[] = $item;
				}
			}

		}

		if (is_dir(DIR_PACKAGES_CORE)) {
			$this->corePackages = $this->getDirectoryContents(DIR_PACKAGES_CORE);
		}

		$this->overridesScanned = true;
	}

	public function getDirectoryContents($dir, $ignoreFilesArray = array(), $recursive = false) {
		$ignoreFiles = array_merge($this->ignoreFiles, $ignoreFilesArray);
		$aDir = array();
		if (is_dir($dir)) {
			$handle = opendir($dir);
			while(($file = readdir($handle)) !== false) {
				if (substr($file, 0, 1) != '.' && (!in_array($file, $ignoreFiles))) {
					if (is_dir($dir. "/" . $file)) {
						if($recursive) {
							$aDir = array_merge($aDir, $this->getDirectoryContents($dir. "/" . $file, $ignoreFiles, $recursive));
							$file = $dir . "/" . $file;
						}
						$aDir[] = preg_replace("/\/\//si", "/", $file);
					} else {
						if ($recursive) {
							$file = $dir . "/" . $file;
						}
						$aDir[] = preg_replace("/\/\//si", "/", $file);
					}
				}
			}
			closedir($handle);
		}
		return $aDir;

	}

	public function overrideCoreByPackage($segment, $pkgOrHandle) {
		$pkgHandle = is_object($pkgOrHandle) ? $pkgOrHandle->getPackageHandle() : $pkgOrHandle;
		$this->coreOverridesByPackage[$segment] = $pkgHandle;
	}

	public function getRecord($segment, $pkgHandle = false) {
		if(is_object($pkgHandle)) {
			$pkgHandle = $pkgHandle->getPackageHandle();
		} else {
			$pkgHandle = (string)$pkgHandle;
		}

		if (!$this->overridesScanned) {
			$this->getOverrides();
		}

		if (isset($this->cachedOverrides[$segment][$pkgHandle])) {
			return $this->cachedOverrides[$segment][$pkgHandle];
		}

		$obj = new EnvironmentRecord();

		if (!in_array($segment, $this->coreOverrides) && !$pkgHandle && !array_key_exists($segment, $this->coreOverridesByPackage)) {
			$obj->file = DIR_BASE_CORE . '/' . $segment;
			$obj->url = ASSETS_URL . '/' . $segment;
			$obj->override = false;
			$this->cachedOverrides[$segment][''] = $obj;
			return $obj;
		}

		if (in_array($segment, $this->coreOverrides)) {
			$obj->file = DIR_APPLICATION . '/' . $segment;
			$obj->url = REL_DIR_APPLICATION . '/' . $segment;
			$obj->override = true;
			$this->cachedOverrides[$segment][''] = $obj;
			return $obj;
		}

		if (array_key_exists($segment, $this->coreOverridesByPackage)) {
			$pkgHandle = $this->coreOverridesByPackage[$segment];
		}

		if (!in_array($pkgHandle, $this->corePackages)) {
			$dirp = DIR_PACKAGES . '/' . $pkgHandle;
			$obj->url = DIR_REL . '/' . DIRNAME_PACKAGES. '/' . $pkgHandle . '/' . $segment;
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
	 * Bypasses overrides cache to get record
	 */
	public function getUncachedRecord($segment, $pkgHandle = false) {
		$obj = new EnvironmentRecord();
		if (is_object($pkgHandle)) {
			$pkgHandle = $pkgHandle->getPackageHandle();
		}
		$obj->override = false;
		if (file_exists(DIR_APPLICATION . '/' . $segment)) {
			$obj->file = DIR_APPLICATION . '/' . $segment;
			$obj->override = true;
			$obj->url = REL_DIR_APPLICATION . '/' . $segment;
		} else if ($pkgHandle) {
			$dirp1 = DIR_PACKAGES . '/' . $pkgHandle . '/' . $segment;
			$dirp2 = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . $segment;
			if (file_exists($dirp2)) {
				$obj->file = $dirp2;
				$obj->url = ASSETS_URL . '/' . DIRNAME_PACKAGES. '/' . $pkgHandle . '/' . $segment;
			} else if (file_exists($dirp1)) {
				$obj->file = $dirp1;
				$obj->url = DIR_REL . '/' . DIRNAME_PACKAGES. '/' . $pkgHandle . '/' . $segment;
			}
		} else {
			$obj->file = DIR_BASE_CORE . '/' . $segment;
			$obj->url = ASSETS_URL . '/' . $segment;
		}
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
