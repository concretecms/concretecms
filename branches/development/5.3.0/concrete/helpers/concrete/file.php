<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Internal functions useful for working with files.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

	defined('C5_EXECUTE') or die(_("Access Denied."));
	class ConcreteFileHelper {
	
		/** 
		 * Given a file's prefix and its name, returns a path to the.
		 * Can optionally create the path if this is the first time doing this operation for this version.
		 */
		public function getSystemPath($prefix, $filename, $createDirectories = false) {
			return $this->mapSystemPath($prefix, $filename, $createDirectories);
		}
		
		public function getThumbnailSystemPath($prefix, $filename, $level, $createDirectories = false) {
			switch($level) {
				case 2:
					$base = DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2;
					break;
				case 3:
					$base = DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3;
					break;
				default: // level 1
					$base = DIR_FILES_UPLOADED_THUMBNAILS;
					break;
			}
			
			$hi = Loader::helper('file');
			$filename = $hi->replaceExtension($filename, 'jpg');
			return $this->mapSystemPath($prefix, $filename, $createDirectories, $base);
		}
		
		public function getThumbnailRelativePath($prefix, $filename, $level) {
			switch($level) {
				case 2:
					$base = REL_DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2;
					break;
				case 3:
					$base = REL_DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3;
					break;
				default: // level 1
					$base = REL_DIR_FILES_UPLOADED_THUMBNAILS;
					break;
			}
			
			$hi = Loader::helper('file');
			$filename = $hi->replaceExtension($filename, 'jpg');
			return $this->mapSystemPath($prefix, $filename, $createDirectories, $base);
		}
		
		private function mapSystemPath($prefix, $filename, $createDirectories = false, $base = DIR_FILES_UPLOADED) {
			$d1 = substr($prefix, 0, 4);
			$d2 = substr($prefix, 4, 4);
			$d3 = substr($prefix, 8);
			
			if ($createDirectories) {
				if (!is_dir($base . '/' . $d1 . '/' . $d2 . '/' . $d3)) {
					mkdir($base . '/' . $d1 . '/' . $d2 . '/' . $d3, 0777, TRUE);
				}
			}
			
			$path = $base . '/' . $d1 . '/' . $d2 . '/' . $d3 . '/' . $filename;
			return $path;
		}
	

	
	}
	
?>