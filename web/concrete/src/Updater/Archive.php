<?php
namespace Concrete\Core\Updater;
use Loader;
use Marketplace;
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
		$fh = Loader::helper('file');
		if (!file_exists($file)) {
			throw new Exception(t('Could not transfer to temp directory - file not found.'));
		} else {
			$dir = time();
			copy($file, $fh->getTemporaryDirectory() . '/'. $dir . '.zip');
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
		$fh = Loader::helper('file');
        $zip = new \ZipArchive;
        if ($zip->open($fh->getTemporaryDirectory() . '/' . $file) === TRUE) {
            $zip->extractTo($fh->getTemporaryDirectory() . '/' . $directory . '/');
            $zip->close();
            return $fh->getTemporaryDirectory() . '/' . $directory;
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
	 * Installs a zip file from the passed directory
	 * @todo This is theme-specific - it really ought to be moved to the page_theme_archive class, at least most it.
	 * @param string $zipfile
	 * @return base directory into which the zipfile was unzipped
	 */
	protected function install($file, $inplace=false) {
		if (!$inplace) {
			$directory = $this->uploadZipToTemp($file);
		} else {
			$directory = $file;
		}
		$dir = $this->unzip($directory);
		$fh = Loader::helper('file');
		$dirFull = $this->getArchiveDirectory($dir);
		$dirBase = substr(strrchr($dirFull, '/'), 1);
		if (file_exists($this->targetDirectory . '/' . $dirBase)) {
			throw new \Exception(t('The directory %s already exists. Perhaps this item has already been installed.', $this->targetDirectory . '/' . $dirBase));
		} else {
			$f = $fh->copyAll($dirFull, $this->targetDirectory . '/' . $dirBase);
			if (!is_dir($this->targetDirectory . '/' . $dirBase)) {
				throw new \Exception(t('Unable to copy directory %s to %s. Perhaps permissions are set incorrectly or the target directory does not exist.',$dirBase,$this->targetDirectory));
			}
		}
		return $dirBase;
	}
}
