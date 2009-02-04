<?

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('file');
Loader::model('file_version');

/**
 * @package Core
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**

 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class FileImporter {
	
	const E_FILE_INVALID_EXTENSION = 1;
	const E_FILE_INVALID = 2; // pointer is invalid file, is a directory, etc...
	
	
	private function generatePrefix() {
		$prefix = rand(10, 99) . time();
		return $prefix;	
	}
	
	private function storeFile($prefix, $pointer, $filename) {
		// assumes prefix are 12 digits
		$fi = Loader::helper('concrete/file');
		$path = $fi->getSystemPath($prefix, $filename, true);
		copy($pointer, $path);
	}
	
	/** 
	 * Imports a local file into the system. The file must be added to this path
	 * somehow. That's what happens in tools/files/importers/.
	 * If a $fr (FileRecord) object is passed, we assign the newly imported FileVersion
	 * object to that File. If not, we make a new filerecord.
	 */
	public function import($pointer, $filename, $fr = false) {
		
		
		$fh = Loader::helper('validation/file');
		$fi = Loader::helper('file');
		$filename = $fi->sanitize($filename);
		
		// test if file is valid, else return FileImporter::E_FILE_INVALID
		if (!$fh->file($pointer)) {
			return FileImporter::E_FILE_INVALID;
		}
		
		if (!$fh->extension($filename)) {
			return FileImporter::E_FILE_INVALID_EXTENSION;
		}

		
		$prefix = $this->generatePrefix();
		
		// do save in the FileVersions table
		
		// move file to correct area in the filesystem based on prefix
		$this->storeFile($prefix, $pointer, $filename);
		
		if (!($fr instanceof File)) {
			// we have to create a new file object for this file version
			$fv = File::add($filename, $prefix);
		} else {
			$fv = $fr->addVersion($filename, $prefix);
		}

		// finally, we scan our file version object for its metadata
		$fv->refreshAttributes();
		
		return $fv;
	}
	
	

}
