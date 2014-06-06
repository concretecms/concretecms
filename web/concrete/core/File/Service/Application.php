<?

namespace Concrete\Core\File\Service;
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
     * @TODO Make this work again. It needs to respsect file storage locations and have a place
     * in the UI.
     */
    public function getIncomingDirectoryContents()
    {
		$incoming_file_information = array();
		
		if (is_dir(DIR_FILES_INCOMING)) {
		    if ($incoming_file_handle = opendir(DIR_FILES_INCOMING)) {
		        $cnt = 0;
				
				while (($file = readdir($incoming_file_handle)) !== false) {
					if (substr($file, 0, 1) == '.') {
						continue;
					}
					
					$current_file_stats = array();
					$current_file_stats = stat(DIR_FILES_INCOMING .'/'. $file);
					
					$incoming_file_information[$cnt]['name'] = $file;
					$incoming_file_information[$cnt]['size'] = $current_file_stats[7];
		        
					$cnt++;
				}
		        closedir($incoming_file_handle);
			}
		}			
	
		return $incoming_file_information;
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
		$arr = $this->unserializeUploadFileExtensions(UPLOAD_FILE_EXTENSIONS_ALLOWED);
		sort($arr);
		return $arr;
	}
}