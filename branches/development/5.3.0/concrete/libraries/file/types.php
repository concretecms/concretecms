<?

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @package Core
 * @subpackage Files
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**

 * @package Core
 * @subpackage Files
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class FileTypeList {
	
	public function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}
		return $instance;
	}
	
	private $types = array();
	
	public function define($extension, $name, $type, $customImporter = false) {
		$ext = explode(',', $extension);
		foreach($ext as $e) {
			$ft = new FileType();
			$ft->name = $name;
			$ft->extension = $e;
			$ft->customImporter = $customImporter;
			$ft->type = $type;
			$this->types[$e] = $ft;
		}
	}
	
	/** 
	 * Can take an extension or a filename
	 * Returns any registered information we have for the particular file type, based on its registration
	 */
	public static function getType($ext) {
		$ftl = FileTypeList::getInstance();	
		if (strpos($ext, '.') !== false) {
			// filename
			$h = Loader::helper('file');
			$ext = $h->getExtension($ext);
		}
		return $ftl->types[$ext];
	}
	
		
}

class FileType {

	// File Type Constants
	const T_IMAGE = 1;
	const T_VIDEO = 2;
	const T_TEXT = 3;
	const T_AUDIO = 4;
	const T_DOCUMENT = 5;
	const T_APPLICATION = 6;

	public function getName() {return $this->name;}
	public function getExtension() {return $this->extension;}
	public function getCustomImporter() {return $this->customImporter;}
	public function getGenericType() {return $this->type;}

	/** 
	 * Returns a thumbnail for this type of file
	 */
	public function getThumbnail($level) {
		eval('$width = AL_THUMBNAIL_WIDTH_LEVEL' . $level . ';');
		eval('$height = AL_THUMBNAIL_WIDTH_HEIGHT' . $level . ';');
		if (file_exists(DIR_AL_ICONS . '/' . $this->extension . '.png')) {
			$url = REL_DIR_AL_ICONS . '/' . $this->extension . '.png';
		} else {
			$url = AL_ICON_DEFAULT;
		}
		return '<img src="' . $url . '" class="ccm-generic-thumbnail" width="' . $width . '" height="' . $height . '" />';
	}
	
		

}