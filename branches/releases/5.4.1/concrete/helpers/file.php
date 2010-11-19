<?php 
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

defined('C5_EXECUTE') or die("Access Denied.");
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
		return substr($txt->unhandle($filename), 0, strrpos($filename, '.'));
	}
	
	/** 
	 * Recursively copies all items in the source directory to the target directory
	 * @param string $source
	 * @param string $target
	 */
	public function copyAll($source, $target, $mode = 0777) {
		if (is_dir($source)) {
			@mkdir($target, $mode);
			@chmod($target, $mode);
			
			$d = dir($source);
			while (FALSE !== ($entry = $d->read())) {
				if ( $entry == '.' || $entry == '..' || substr($entry, 0, 1) == '.') {
					continue;
				}
			
				$Entry = $source . '/' . $entry;            
				if (is_dir($Entry)) {
					$this->copyAll($Entry, $target . '/' . $entry, $mode);
					continue;
				}
				
				copy($Entry, $target . '/' . $entry);
				chmod($target . '/' . $entry, $mode);
			}
			
			$d->close();
		} else {
			copy($source, $target);
			chmod($target, $mode);
		}
	}
	
	/** 
	 * Takes a path to a file and sends it to the browser, streaming it, and closing the HTTP connection afterwards. Basically a force download method
	 */
	public function forceDownload($file) {
		
		header('Content-type: application/octet-stream');
		$filename = basename($file);
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Content-Length: ' . filesize($file));
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Transfer-Encoding: binary");
		
		// This code isn't ready yet. It will allow us to no longer force download
		
		/*
		$h = Loader::helper('mime');
		$mimeType = $h->mimeFromExtension($this->getExtension($file));
		header('Content-type: ' . $mimeType);
		*/
		
	
		$buffer = '';
		$chunk = 1024*1024;
		$handle = fopen($file, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunk);
			print $buffer;
		}
		
		fclose($handle);
		exit;		
	}
	
	/** 
	 * Returns the full path to the temporary directory
	 */
	public function getTemporaryDirectory() {
		if (!is_dir(DIR_TMP)) {
			mkdir(DIR_TMP, 0777);
			touch(DIR_TMP . '/index.html');
		}
		return DIR_TMP;
		
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
		$url = @parse_url($file);
		if (isset($url['scheme']) && isset($url['host'])) {
			if (ini_get('allow_url_fopen')) {
				$ctx = stream_context_create(array( 
					'http' => array( 'timeout' => $timeout ) 
				)); 
				if ($contents = @file_get_contents($file, 0, $ctx)) {
					return $contents;
				}
			}
			
			if (function_exists('curl_init')) {
				$curl_handle = curl_init();
				curl_setopt($curl_handle, CURLOPT_URL, $file);
				curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $timeout);
				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
				$contents = curl_exec($curl_handle);
				$http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
				if ($http_code == 404) {	
					return false;
				}
				
				return $contents;
			}
		} else {
			if ($contents = @file_get_contents($file)) {
				return $contents;
			}
		}
		
		return false;
	}
	
	/** 
	 * Removes contents of the file
	 * @param $filename
	 */
	public function clear($file) {
		file_put_contents($file, '');
	}
	
	
	/** 
	 * Cleans up a filename and returns the cleaned up version
	 */
	public function sanitize($file) {
		// $file = preg_replace("/[^0-9A-Z_a-z-.\s]/","", $file); // pre 5.4.1 allowed spaces
		$file = preg_replace(array("/[\s]/","/[^0-9A-Z_a-z-.]/"),array("_",""), $file);
		return trim($file);
	}
	
	/** 
	* Returns the extension for a file name
	* @param $filename
	*/
	public function getExtension($filename) {
		$extension = end(explode(".",$filename));
		return $extension;
	}
	
	/** 
	 * Takes a path and replaces the files extension in that path with the specified extension
	 */
	public function replaceExtension($filename, $extension) {
		$newFileName = substr($filename, 0, strrpos($filename, '.')) . '.' . $extension;
		return $newFileName;
	}
		
}
?>