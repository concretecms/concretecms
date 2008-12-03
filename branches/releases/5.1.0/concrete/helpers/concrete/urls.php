<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
class ConcreteUrlsHelper {

	/** 
	 * Gets a full URL to an icon for a particular application
	 */
	public function getPackageIconURL($pkg) {
		if (file_exists($pkg->getPackagePath() . '/' . FILENAME_BLOCK_ICON)) {
			return $this->getPackageURL($pkg) . '/' . FILENAME_BLOCK_ICON;
		} else {
			return BLOCK_TYPE_GENERIC_ICON;
		}
	}
	
	public function getPackageURL($pkg) {
		if (is_dir(DIR_PACKAGES_CORE . '/' . $pkg->getPackageHandle())) {
			return ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkg->getPackageHandle();
		} else {
			return BASE_URL . DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkg->getPackageHandle();
		}
	}
	
	/** 
	 * Gets a URL to reference a script in the tools directory
	 * @param $string $tool
	 * @param $string $pkgHandle
	 */
	public function getToolsURL($tool, $pkgHandle = null) {
		if ($pkgHandle != null) {
			$url = (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) ? BASE_URL . DIR_REL : ASSETS_URL; 
			$url = REL_DIR_FILES_TOOLS_PACKAGES . '/' . $pkgHandle . '/' . $tool;
			return $url;
		} else {
			if (file_exists(DIR_BASE . '/' . DIRNAME_TOOLS . '/' . $tool . '.php')) {
				return REL_DIR_FILES_TOOLS . '/' . $tool;
			} else {
				return REL_DIR_FILES_TOOLS_REQUIRED . '/' . $tool;
			}
		}
	}
	
	/** 
	 * Gets a full URL to an icon for a particular block type
	 * @param BlockType $bt
	 * @return string
	 */
	public function getBlockTypeIconURL($bt) {
		if (file_exists($bt->getBlockTypePath() . '/' . FILENAME_BLOCK_ICON)) {
			return $this->getBlockTypeAssetsURL($bt) . '/' . FILENAME_BLOCK_ICON;		
		} else {
			return BLOCK_TYPE_GENERIC_ICON;
		}
	}
	
	/** 
	 * Gets a full URL to the directory containing all of a block's items, including JavaScript, tools, icons, etc...
	 * @param BlockType $bt
	 * @return string $url
	 */
	public function getBlockTypeAssetsURL($bt) {
		if ($bt->getPackageID() > 0) {
			$db = Loader::db();
			$h = $bt->getPackageHandle();
			$url = (is_dir(DIR_PACKAGES . '/' . $h)) ? BASE_URL . DIR_REL : ASSETS_URL; 
			$url = $url . '/' . DIRNAME_PACKAGES . '/' . $h . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle();		
		} else if (is_dir(DIR_FILES_BLOCK_TYPES_CORE . '/' . $bt->getBlockTypeHandle())) {
			$url = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle();
		} else {
			$url = BASE_URL . DIR_REL . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle();
		}
		
		return $url;
	}
	
	/** 
	 * Gets a full URL to a block's JavaScript file (if one exists)
	 * @param BlockType $bt
	 * @return string $url
	 */
	public function getBlockTypeJavaScriptURL($bt) {
		if (file_exists($bt->getBlockTypePath() . '/auto.js')) {
			return $this->getBlockTypeAssetsURL($bt) . '/auto.js';
		}
	}

	/** 
	 * Gets a full URL to a block's tools directory
	 * @param BlockType $bt
	 * @return string $url
	 */
	public function getBlockTypeToolsURL($bt) {
		return REL_DIR_FILES_TOOLS_BLOCKS . '/' . $bt->getBlockTypeHandle();
	}

	
}