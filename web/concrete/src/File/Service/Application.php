<?php

namespace Concrete\Core\File\Service;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Config;
use Loader;
class Application {

    public function prefix($prefix, $filename)
    {
        $apr = str_split($prefix, 4);
        return sprintf('/%s/%s/%s/%s', $apr[0], $apr[1], $apr[2], $filename);
    }

    public function getThumbnailFilePath($prefix, $filename, $level)
    {
        switch($level) {
            case 2:
                $base = REL_DIR_FILES_THUMBNAILS_LEVEL2;
                break;
            case 3:
                $base = REL_DIR_FILES_THUMBNAILS_LEVEL3;
                break;
            default: // level 1
                $base = REL_DIR_FILES_THUMBNAILS;
                break;
        }

        $hi = Loader::helper('file');
        $filename = $hi->replaceExtension($filename, 'jpg');
        return $base . $this->prefix($prefix, $filename);
    }

    /**
     * @return array
     */
    public function getIncomingDirectoryContents()
    {
        $incoming_file_information = array();
        $fs = StorageLocation::getDefault()->getFileSystemObject();
        $items = $fs->listContents(REL_DIR_FILES_INCOMING);
        return $items;
    }

    /**
     * Serializes an array of strings into format suitable for multi-uploader
     *
     * example for format:
     * '*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.avi;*.mp4;*.mp3;*.qt;*.ppt;*.kml'
     * @param array $types
     * @return string
     */
    public function serializeUploadFileExtensions($types){
        $serialized = '';
        $types = preg_replace('{[^a-z0-9]}i','',$types);
        foreach ($types as $type) {
            $serialized .= '*.'.$type.';';
        }
        //removing trailing ; unclear if multiupload will choke on that or not
        $serialized = substr ($serialized, 0, strlen($serialized)-1);
        return $serialized;
    }

    /**
     * Unserializes an array of strings from format suitable for multi-uploader
     *
     * example for format:
     * '*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.avi;*.mp4;*.mp3;*.qt;*.ppt;*.kml'
     * @param string $types
     * @return array
     */
    public function unSerializeUploadFileExtensions($types){
        //split by semi-colon
        $types = preg_split('{;}',$types,null,PREG_SPLIT_NO_EMPTY);
        $types = preg_replace('{[^a-z0-9]}i','',$types);
        return $types;
    }

    /**
     * Returns an array of all allowed file extensions within the system
     */
    public function getAllowedFileExtensions() {
        $arr = $this->unserializeUploadFileExtensions(Config::get('concrete.upload.extensions'));
        sort($arr);
        return $arr;
    }
	
    /** 
     * Given a file's prefix and its name, returns a path to the.
     * Can optionally create the path if this is the first time doing this operation for this version.
     */
    public function getSystemPath($prefix, $filename, $createDirectories = false) {
        return $this->mapSystemPath($prefix, $filename, $createDirectories);
    }

    public function mapSystemPath($prefix, $filename, $createDirectories = false, $base = DIR_FILES_UPLOADED_STANDARD) {
        if ($prefix == null) {
            $path = $base . '/' . $filename;
        } else {
            $d1 = substr($prefix, 0, 4);
            $d2 = substr($prefix, 4, 4);
            $d3 = substr($prefix, 8);

            if ($createDirectories) { 
                if (!is_dir($base)) { 
                    @mkdir($base, DIRECTORY_PERMISSIONS_MODE, TRUE); 
                    @chmod($base, DIRECTORY_PERMISSIONS_MODE); 
                    @touch($base . '/' . $d1 . '/index.html');
                }
                if (!is_dir($base . '/' . $d1)) { 
                    @mkdir($base . '/' . $d1, DIRECTORY_PERMISSIONS_MODE, TRUE); 
                    @chmod($base . '/' . $d1, DIRECTORY_PERMISSIONS_MODE); 
                    @touch($base . '/' . $d1 . '/index.html');
                }
                if (!is_dir($base . '/' . $d1 . '/' . $d2)) { 
                    @mkdir($base . '/' . $d1 . '/' . $d2, DIRECTORY_PERMISSIONS_MODE, TRUE); 
                    @chmod($base . '/' . $d1 . '/' . $d2, DIRECTORY_PERMISSIONS_MODE); 
                    @touch($base . '/' . $d1 . '/' . $d2 . '/index.html');
                }
                if (!is_dir($base . '/' . $d1 . '/' . $d2 . '/' . $d3)) { 
                    @mkdir($base . '/' . $d1 . '/' . $d2 . '/' . $d3, DIRECTORY_PERMISSIONS_MODE, TRUE); 
                    @chmod($base . '/' . $d1 . '/' . $d2 . '/' . $d3, DIRECTORY_PERMISSIONS_MODE); 
                    @touch($base . '/' . $d1 . '/' . $d2 . '/' . $d3 . '/index.html');
                }
            }

            $path = $base . '/' . $d1 . '/' . $d2 . '/' . $d3 . '/' . $filename;
        }
        return $path;
    }

}
