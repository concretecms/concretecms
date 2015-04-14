<?php

namespace Concrete\Core\Updater;

use Core;
use Exception;

class Archive
{
    /**
     * The directory where this archive will be unzipped.
     *
     * @var string
     */
    protected $targetDirectory = "/dev/null";

    /**
     * File helper instance.
     *
     * @var \Concrete\Core\File\Service\File
     */
    protected $f;

    public function __construct()
    {
        $this->f = Core::make('helper/file');
    }

    /**
     * Moves an uploaded file to the tmp directory.
     *
     * @param string $file The full path of the file to copy
     *
     * @return string Return the base name of the file copied to the temporary directory.
     */
    protected function uploadZipToTemp($file)
    {
        if (!file_exists($file)) {
            throw new Exception(t('Could not transfer to temp directory - file not found.'));
        } else {
            $dir = time();
            copy($file, $this->f->getTemporaryDirectory() . '/'. $dir . '.zip');

            return $dir;
        }
    }

    /**
     * Unzips a file at a directory level by concatenating ".zip" onto the end of it.
     * <code>
     * 	unzip("/path/to/files/themes/mytheme") // will unzip "mytheme.zip"
     * </code>.
     *
     * @param string $directory The base name of the file (without extension, assumed to be in the the temporary directory).
     *
     * @return string Return the full path of the extracted directory.
     */
    protected function unzip($directory)
    {
        $file = $directory . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($this->f->getTemporaryDirectory() . '/' . $file) === true) {
            $zip->extractTo($this->f->getTemporaryDirectory() . '/' . $directory . '/');
            $zip->close();

            return $this->f->getTemporaryDirectory() . '/' . $directory;
        }
    }

    /**
     * Returns either the directory (if the archive itself contains files at the first level) or the subdirectory if, like
     * many archiving programs, we the zip archive is a directory, THEN a list of files.
     *
     * @param string $dir The full path of the directory to be analyzed.
     *
     * @return string Returns the final directory containing the files to be used.
     */
    protected function getArchiveDirectory($dir)
    {
        $files = $this->f->getDirectoryContents($dir);

        // strip out items in directories that we know aren't valid

        if (count($files) == 1 && is_dir($dir . '/' . $files[0])) {
            return $dir . '/' . $files[0];
        } else {
            return $dir;
        }
    }

    /**
     * Installs a zip file from the passed directory.
     *
     * @todo This is theme-specific - it really ought to be moved to the page_theme_archive class, at least most it.
     *
     * @param string $zipfile
     * @param bool $inplace Set to false if $file should be moved to the temporary directory before working on it, set to true if it's already in the temp directory.
     *
     * @return string Returns the base directory into which the zipfile was unzipped
     */
    protected function install($file, $inplace = false)
    {
        if (!$inplace) {
            $directory = $this->uploadZipToTemp($file);
        } else {
            $directory = $file;
        }
        $dir = $this->unzip($directory);
        $dirFull = $this->getArchiveDirectory($dir);
        $dirBase = substr(strrchr($dirFull, '/'), 1);
        if (file_exists($this->targetDirectory . '/' . $dirBase)) {
            throw new Exception(t('The directory %s already exists. Perhaps this item has already been installed.', $this->targetDirectory . '/' . $dirBase));
        } else {
            $this->f->copyAll($dirFull, $this->targetDirectory . '/' . $dirBase);
            if (!is_dir($this->targetDirectory . '/' . $dirBase)) {
                throw new Exception(t('Unable to copy directory %s to %s. Perhaps permissions are set incorrectly or the target directory does not exist.', $dirBase, $this->targetDirectory));
            }
        }

        return $dirBase;
    }
}
