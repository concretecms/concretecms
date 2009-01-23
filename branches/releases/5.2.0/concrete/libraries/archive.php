<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @package Utilities
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A generic class representing an archive in the system (most likely a zip archive.)
 *
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class Archive { 
	
	/**
	 * The directory where this archive will be unzipped
	 */
	protected $targetDirectory = "/dev/null";
	
	public function __construct() {
		$this->f = Loader::helper('file');
	}

	/**
	 * Moves an uploaded file to the tmp directory
	 * @param string $file
	 * @return string $directory
	 */
	protected function uploadZipToTemp($file) {
		if (!file_exists($file)) {
			throw new Exception(t('Could not transfer to temp directory - file not found.'));
		} else {
			$dir = time();
			copy($_FILES['archive']['tmp_name'], DIR_TMP . '/' . $dir . '.zip');
			return $dir;
		}
	}

	/**
	 * Unzips a file at a directory level by concatenating ".zip" onto the end of it.
	 * <code>
	 * 	unzip("/path/to/files/themes/mytheme") // will unzip "mytheme.zip"
	 * </code> 
	 * @param string $directory
	 * @return string $directory
	 */
	protected function unzip($directory) {
		$file = $directory . '.zip';
		$ret = @shell_exec(DIR_FILES_BIN_UNZIP . ' ' . DIR_TMP . '/' . $file . ' -d ' . DIR_TMP . '/' . $directory . '/');
		$files = $this->f->getDirectoryContents(DIR_TMP . '/' . $directory);
		if (count($files) == 0) {
			throw new Exception(t('There was an error unpacking your theme. Perhaps you have not uploaded a valid zip file, or you do not have zip installed.'));
		} else {
			return DIR_TMP . '/' . $directory;
		}
	}
	
	/**
	 * Returns either the directory (if the archive itself contains files at the first level) or the subdirectory if, like
	 * many archiving programs, we the zip archive is a directory, THEN a list of files.
	 * @param string $directory
	 * @return string $directory
	 */
	protected function getArchiveDirectory($dir) {
		// this is necessary to either get the current directory if there are files in it, or the subdirectory if,
		// like most archiving programs, the zip archive is a directory, THEN a list of files.
		$files = $this->f->getDirectoryContents($dir);
		
		// strip out items in directories that we know aren't valid
		
		if (count($files) == 1 && is_dir($dir . '/' . $files[0])) {
			return $dir . '/' . $files[0];
		} else {
			return $dir;
		}	
	}

	/**
	 * Installs a theme from the passed directory
	 * @todo This is theme-specific - it really ought to be moved to the page_theme_archive class, at least most it. 
	 * @param string $zipfile
	 * @return PageTheme $theme
	 */
	protected function install($file) {
		$directory = $this->uploadZipToTemp($file);
		$dir = $this->unzip($directory);
		$themeDirFull = $this->getArchiveDirectory($dir);
		$themeDir = substr(strrchr($themeDirFull, '/'), 1);
		if (file_exists($this->targetDirectory . '/' . $themeDir)) {
			throw new Exception(t('The directory %s already exists. Perhaps this item has already been installed.', $this->targetDirectory . '/' . $themeDir));
		} else {
			$r = @rename($themeDirFull, $this->targetDirectory . '/' . $themeDir);
			if (!$r) {
				throw new Exception('Unable to copy directory ' . $themeDir . ' to ' . $this->targetDirectory . '. Perhaps permissions are set incorrectly or the target directory does not exist.');
			} else {
				// now we install the bad boy
				$pl = PageTheme::addSiteTheme($themeDir);
				return $pl;
			}
		}
	}
}