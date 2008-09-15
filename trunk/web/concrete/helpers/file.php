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
	public function getDirectoryContents($dir, $ignoreFilesArray = array()) {
		$this->ignoreFiles = array_merge($this->ignoreFiles, $ignoreFilesArray);
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
	
	
	/**
	 * Adds content to a new line in a file. If a file is not there it will be created
	 * @param string $filename
	 * @param string $content
	 */
	public function append($filename, $content) {
		file_put_contents($filename, $content, FILE_APPEND);
	}
	
	
	/**
	 * Just a consistency wrapper for file_get_contents
	 * Should use curl if it exists and fopen isn't allowed (thanks Remo)
	 * @param $filename
	 */
	public function getContents($file, $timeout = 5) {
		if (ini_get('allow_url_fopen')) {
			$ctx = stream_context_create(array( 
				'http' => array( 'timeout' => $timeout ) 
			)); 
			$contents = file_get_contents($file, 0, $ctx);
		} else if (function_exists('curl_init')) {
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $file);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			$contents = curl_exec($curl_handle);
			curl_close($curl_handle);
		} else {
			throw new Exception("Unable to retrieve remote file contents.");
		}
		return $contents;
	}
	
	/** 
	 * Removes contents of the file
	 * @param $filenamee
	 */
	public function clear($file) {
		file_put_contents($file, '');
	}
}

?>