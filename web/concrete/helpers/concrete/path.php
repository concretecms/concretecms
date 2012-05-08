<?
/**
 * @package Helpers
 * @subpackage Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Useful functions for getting paths for concrete5 items.
 * @subpackage Concrete
 * @package Helpers
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcretePathHelper {

	protected function mapPath($segment, $pkgIdentifier = false) {
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
			$obj->file = $file;
			$obj->url = $url;
			return $obj;
		} else {
			return false;
		}
	}
	
	/** 
	 * Returns a full path to the subpath segment. Returns false if not found
	 */
	public function getPath($subpath, $pkgIdentifier = false) {
		$r = $this->mapPath($subpath, $pkgIdentifier);
		return $r->file;	
		return false;
	}
	
	/** 
	 * Returns  a public URL to the subpath item. Returns false if not found
	 */
	public function getURL($subpath, $pkgIdentifier = false) {
		$r = $this->mapPath($subpath, $pkgIdentifier);
		return $r->url;		
		return false;
	}

}