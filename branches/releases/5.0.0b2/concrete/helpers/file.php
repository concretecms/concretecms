<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for working with files and directories.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class FileHelper {

	/**
	 * @access private
	 */
	protected $ignoreFiles = array('__MACOSX', DIRNAME_CONTROLLERS);
	
	/** 
	 * Returns the contents of a directory in an array.
	 * @param string $directory
	 * @return array
	 */
	public function getDirectoryContents($dir) {
		$aDir = array();
		if (is_dir($dir)) {
			$handle = opendir($dir);
			while(($file = readdir($handle)) !== false) {
				if (substr($file, 0, 1) != '.' && (!in_array($file, $this->ignoreFiles))) {
					$aDir[] = $file;
				}
			}
		}
		return $aDir;
	}
	
	/** 
	 * Removes the extension of a filename, uncamelcases it.
	 * @param string $filename
	 * @return string
	 */	
	public function unfilename($filename) {
		// removes the extension and makes it look nice
		$txt = Loader::helper('text');
		return substr($txt->uncamelcase($filename), 0, strrpos($filename, '.'));
	}
	
}

?>