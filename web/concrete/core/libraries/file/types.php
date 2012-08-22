<?

defined('C5_EXECUTE') or die("Access Denied.");

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
class Concrete5_Library_FileTypeList {
	
	public function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}
		return $instance;
	}
	
	protected $types = array();
	protected $importerAttributes = array();
	
	public function define($extension, $name, $type, $customImporter = false, $inlineFileViewer = false, $editor = false, $pkgHandle = false) {
		$ext = explode(',', $extension);
		foreach($ext as $e) {
			$ft = new FileType();
			$ft->name = $name;
			$ft->extension = $e;
			$ft->customImporter = $customImporter;
			$ft->editor = $editor;
			$ft->type = strtolower($type);
			$ft->view = $inlineFileViewer;
			$ft->pkgHandle = $pkgHandle;
			$this->types[$e] = $ft;
		}
	}
	
	public function defineImporterAttribute($akHandle, $akName, $akType, $akIsEditable) {
		$obj = new stdClass;
		$obj->akHandle = $akHandle;
		$obj->akName = $akName;
		$obj->akType = $akType;
		$obj->akIsEditable = $akIsEditable;		
		$this->importerAttributes[$akHandle] = $obj;
	}
	
	public function getImporterAttribute($akHandle) {
		$ftl = FileTypeList::getInstance();
		return $ftl->importerAttributes[$akHandle];
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
		$ext = strtolower($ext);
		if (is_object($ftl->types[$ext])) {
			return $ftl->types[$ext];
		} else {
			$ft = new FileType(); // generic
			return $ft;
		}
	}
	
		
}

class Concrete5_Library_FileType {

	// File Type Constants
	const T_IMAGE = 1;
	const T_VIDEO = 2;
	const T_TEXT = 3;
	const T_AUDIO = 4;
	const T_DOCUMENT = 5;
	const T_APPLICATION = 6;
	const T_UNKNOWN = 99;
	
	public $pkgHandle = false;
	
	public function __construct() {
		$this->type = FileType::T_UNKNOWN;
		$this->name = $this->mapGenericTypeText($this->type);
	}
	
	public function getPackageHandle() {return $this->pkgHandle;}
	public function getName() {return $this->name;}
	public function getExtension() {return $this->extension;}
	public function getCustomImporter() {return $this->customImporter;}
	public function getGenericType() {return $this->type;}
	public function getView() {return $this->view;}	
	public function getEditor() { return $this->editor;}
	
	protected function mapGenericTypeText($type) {
		switch($type) {
			case FileType::T_IMAGE:
				return t('Image');
				break;
			case FileType::T_VIDEO:
				return t('Video');
				break;
			case FileType::T_TEXT:
				return t('Text');
				break;
			case FileType::T_AUDIO:
				return t('Audio');
				break;
			case FileType::T_DOCUMENT:
				return t('Document');
				break;
			case FileType::T_APPLICATION:
				return t('Application');
				break;
			case FileType::T_UNKNOWN:
				return t('File');
				break;

		}
	}
	
	public function getGenericTypeText($type) {
		if ($type > 0) {
			return FileType::mapGenericTypeText($type);
		} else if (!empty($this->type)) {
			return FileType::mapGenericTypeText($this->type);		
		}
	}
	
	public function getCustomInspector() {
		$script = 'file/types/' . $this->getCustomImporter();
		if ($this->pkgHandle != false) {
			Loader::library($script, $this->pkgHandle);
		} else {
			Loader::library($script);
		}
		$class = Object::camelcase($this->getCustomImporter()) . 'FileTypeInspector';
		$cl = new $class;
		return $cl;
	}
	
	/** 
	 * Returns a thumbnail for this type of file
	 */
	public function getThumbnail($level, $fullImageTag = true) {
		$width = constant("AL_THUMBNAIL_WIDTH_LEVEL{$level}");
		$height = constant("AL_THUMBNAIL_HEIGHT_LEVEL{$level}");
		if (file_exists(DIR_AL_ICONS . '/' . $this->extension . '.png')) {
			$url = REL_DIR_AL_ICONS . '/' . $this->extension . '.png';
		} else {
			$url = AL_ICON_DEFAULT;
		}
		if ($fullImageTag == true) {
			return '<img src="' . $url . '" class="ccm-generic-thumbnail" width="' . $width . '" height="' . $height . '" />';
		} else {
			return $url;
		}
	}
	
		

}