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
	 * Returns any registered information we have for the particular file type, based on its registration
	 */
	public static function getType($ext) {
		$ftl = FileTypeList::getInstance();	
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
	
		

}