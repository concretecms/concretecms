<?php 
/**
 * @package Blocks
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. <http://www.concrete5.org>
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * The library file block is an internal block type used by external block types to reference files. Basically, any file in Concrete maps to an instance of the library file block.
 *
 * @package Blocks
 * @access private
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. <http://www.concrete5.org>
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	defined('C5_EXECUTE') or die("Access Denied.");
	class LibraryFileBlockController extends BlockController {
	 
		protected $btIsInternal = 1;
		protected $btTable = 'btFile';
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Files added to the asset library");
		}
		
		public function getBlockTypeName() {
			return t("Library File");
		}		

		function getFile($fID) {
			Loader::model('file');
			$mf = new File();

			$file_obj = $mf->getByID($fID);
			$fileversion_obj = $file_obj->getVersion();
				
			$bf = new LibraryFileBlockController;

			$ftype = FileTypeList::getType($fileversion_obj->getExtension());
			$this->generictype = strtolower($ftype->getGenericTypeText($ftype->getGenericType()));
			$this->filename = $fileversion_obj->getFileName();
			$this->type = $fileversion_obj->getType();
			$this->url = $fileversion_obj->getURL();
			$this->filepath = $fileversion_obj->getPath();
			$this->relpath = $fileversion_obj->getRelativePath();
			$this->origfilename = $fileversion_obj->getRelativePath();
			$this->filesize = $fileversion_obj->getFullSize();
			
			$len = strlen(REL_DIR_FILES_UPLOADED);
			$fh = Loader::helper('concrete/file');
			
			$bf->bID 			= $fileversion_obj->getFileID();
			$bf->generictype 	= $this->generictype;
			$bf->type 			= $this->type;
			$bf->url 			= $this->url;
			$bf->filepath  		= $this->filepath;
			$bf->relpath  		= $this->relpath;
			$bf->filename 		= substr($this->relpath, $len); // for backwards compatibility this must include the prefixes
			$bf->filesize		= $this->filesize;
			return $bf;
		}

		function getIcon($type, $generictype, $getSRC = true) {
			$file = '';
			if (file_exists(DIR_AL_ICONS . '/type_' . $type . '.png')) {
				$file = REL_DIR_AL_ICONS . '/type_' . $type . '.png';
			} else if (file_exists(DIR_AL_ICONS . '/generic_' . $generictype . '.png')) {
				$file = REL_DIR_AL_ICONS . '/generic_' . $generictype . '.png';				
			} else {
				$file = REL_DIR_AL_ICONS . '/generic_file.png';	
			}
			
			if (!$getSRC) {
				return $file;
			} else {
				return '<img class="ccm-al-icon" src="' . $file . '" width="' . AL_ICON_WIDTH . '" height="' . AL_ICON_HEIGHT . '" />';
			}
		}
		
		function sanitizeTitle($filename, $maxLength = 60) {
			// remove all numbers from the front of a file
			$st = preg_replace("/^[0-9]+/", "", $filename);
			if (strlen($st) > $maxLength) {
				$st = substr($st, 0, $maxLength) . '...';
			}
			return $st;
		}
		
		/**
		 * Gets the original filename of an uploaded file.
	     * @author Tony Trupp <tony@concrete5.org>
	     * return string $filename
	     */
		function getOrigfilename() {return $this->origfilename;}
		function getOriginalFilename() {return $this->origfilename;}

		function getFilename() {return $this->filename;}
		function getFileID() {return $this->bID;}
		function getURL() {return $this->url;}
		function getType() {return $this->type;}
		function getGenericType() {return $this->generictype;}

		public function getFilePath() {
			return $this->filepath;
		}

		public function getFileRelativePath() {
			return $this->relpath;
		}

		public function getFileFullURL() {
			return BASE_URL . $this->url;
		}
		
		
		/*
		 * Returns the dimensions of the file object (assumed to be an image, movie or flash file) as an array
		 * Array keys: 0 = width, 1 = height
		 * @return array $dimensions
		 */
		function getDimensions() {
			$r = @getimagesize($this->filepath);
			if ($r) {
				return $r;
			}
		}
		
		/**
		 * Goes through all files without an original filename and makes one by using the sanitizeTitle() function
		 * @return void
		 */
		function populateOriginalFilenames() {
			$db = Loader::db();
			$r = $db->Execute("select bID, filename from btFile where origfilename = '' or origfilename is null");
			while ($row = $r->FetchRow()) {
				$origfilename = $this->sanitizeTitle($row['filename']);
				$db->Execute("update btFile set origfilename = ? where bID = ?", array($origfilename, $row['bID']));
			}
		}
		
		/**
	  	 * Creates a new image given an original path, a new path, a target width and height.
		 * @params string $originalPath, string $newpath, int $width, int $height
		 * @return void
		 */		
		public function createImage($originalPath, $newPath, $width, $height) {
			$fi = Loader::helper('image');
			$fi->create($originalPath, $newPath, $width, $height);
		}

		/** 
		 * Gets the system thumbnail for a given file object. This function returns a relative path
		 * @return string $path
		 */
		function getThumbnailRelativePath($filename = null) {
			if (!$filename) {
				$db = Loader::db();
				$q = "select filename from btFile where bID = '{$this->bID}'";
				$filename = $db->getOne($q);
				if ($filename) {
					$newFileName = substr($filename, 0, strrpos($filename, '.')) . '.jpg';
					return REL_DIR_FILES_UPLOADED_THUMBNAILS . '/' . $newFileName;
				}
			} else {
				$newFileName = substr($filename, 0, strrpos($filename, '.')) . '.jpg';
				return REL_DIR_FILES_UPLOADED_THUMBNAILS . '/' . $newFileName;
			}
		}

		/** 
		 * Gets the system thumbnail for a given file object. This function returns the absolute path
		 * @return string $path
		 */
		function getThumbnailAbsolutePath($filename = null) {
			
		}
		
		/** 
		 * Returns a thumbnail for the current file object. If a width and height are specified, attempts to create or retrieve a thumbnail of that size specifically
		 * Returns an object with the following properties: src, width, height, alt
		 * @param int $maxWidth
		 * @param int $maxHeight
		 * @return object $thumbnail
		 */
		public function getThumbnail($maxWidth = null, $maxHeight = null) {
			$thumb = new stdClass;
			if ($maxWidth == null && $maxHeight == null) {
				$src = $this->getThumbnailRelativePath();
				$abspath = $this->getThumbnailAbsolutePath();
			} else if ($this->filename != '' && is_int($maxWidth) && is_int($maxHeight)) {
				// then we check to see if a file with these dimensions exists
				$pi = pathinfo($this->filename);
				$filename = $pi['filename'] . '_' . $maxWidth . 'x' . $maxHeight . '.jpg';
				if (!file_exists(DIR_FILES_CACHE . '/' . $filename)) {
					LibraryFileBlockController::createImage(DIR_FILES_UPLOADED . '/' . $this->filename, DIR_FILES_CACHE . '/' . $filename, $maxWidth, $maxHeight);
				}
				$src = REL_DIR_FILES_CACHE . '/' . $filename;
				$abspath = DIR_FILES_CACHE . '/' . $filename;
			}
			
			if (isset($abspath) && file_exists($abspath)) {			
				$thumb->src = $src;
				$dimensions = getimagesize($abspath);
				$thumb->width = $dimensions[0];
				$thumb->height = $dimensions[1];
				$thumb->alt = $this->getOriginalFilename();
				return $thumb;
			}				
		}
		
		/** 
		 * Prints out an image string for the current thumbnail, using the getThumbnail method
		 */
		public function outputThumbnail($maxWidth = null, $maxHeight = null) {
			$thumb = $this->getThumbnail($maxWidth, $maxHeight);
			if (is_object($thumb)) {
				print '<img class="ccm-output-thumbnail" alt="' . htmlentities($thumb->alt, ENT_QUOTES, APP_CHARSET) . '" src="' . $thumb->src . '" width="' . $thumb->width . '" height="' . $thumb->height . '" />';
			}
		}

		function getFileSize() {
			return $this->filesize;
		}

		function delete() {
			$db = Loader::db();
			$bID = $this->bID;
			
			// first, we remove the image

			$q = "select filename from btFile where bID = '$bID'";
			$filename = $db->getOne($q);

			if ($filename) {

				if (file_exists(DIR_FILES_UPLOADED . '/' . $filename)) {
					unlink(DIR_FILES_UPLOADED . '/' . $filename);
				}
				if (file_exists(DIR_FILES_UPLOADED_THUMBNAILS . '/' . $filename)) {
					unlink(DIR_FILES_UPLOADED_THUMBNAILS . '/' . $filename);
				}
			}

			// now, finally, we remove the instance from the btContentFile table

			$q = "delete from btFile where bID = '$bID'";
			$r = $db->query($q);
		}
		
		function sanitizeAndCopy($pointer, $filename) {
			$prefix = rand(1000, 10000) . time();
			$filename = preg_replace(array("/[^0-9A-Za-z-.]/","/[\s]/"),"", $filename);
			$filename = $prefix . $filename;
			$path = DIR_FILES_UPLOADED . '/' . $filename;
			copy($pointer, $path);
			return $filename;
		}
		
		function createThumbnail($existingFile) {
			if (file_exists(DIR_FILES_UPLOADED . '/' . $existingFile)) {	
				$thumbnailFileName = substr($existingFile, 0, strrpos($existingFile, '.')) . '.jpg';
				$thumbnailFilePath = DIR_FILES_UPLOADED_THUMBNAILS . '/' . $thumbnailFileName;
				LibraryFileBlockController::createImage(DIR_FILES_UPLOADED . '/' . $existingFile, $thumbnailFilePath, AL_THUMBNAIL_WIDTH, AL_THUMBNAIL_HEIGHT);
			}
		}
		
		function duplicate($newBID) {
			// nothing
		}

		
		function save($data) {
			Loader::library("file/importer");


			if (file_exists($data['file'])) {
				$fi = new FileImporter();
           		$resp = $fi->import($data['file'], $data['name']);

				$lbc = new LibraryFileBlockController();
				return $lbc->getFile($resp->getFileID());				
			}
		}

/*
		function save_OLD($data) {
			
			if (file_exists($data['file'])) {
				// copy the file into the files directory
				$filename = LibraryFileBlockController::sanitizeAndCopy($data['file'], $data['name']);
				$bo = $this->getBlockObject();
				$bo->updateBlockName($filename);
				
				$size = @getimagesize(DIR_FILES_UPLOADED . '/' . $filename);
				
				// TODO: extend these out to more useful file types
				if ($size) {
					$generictype = "image";
					$type = '';
					LibraryFileBlockController::createThumbnail($filename);
					switch($size[2]) {
						case IMAGETYPE_PNG:
							$type = 'png';
							break;
						case IMAGETYPE_JPEG:
							$type = 'jpg';
							break;
						case IMAGETYPE_GIF:
							$type = 'gif';
							break;
					}							
				} else {
					$generictype = "file";
					$type = '';
					$ext = substr(strrchr($filename, '.'), 1);
					$type = $ext;	
					
				}
				
				$db = Loader::db();
				$origfilename= LibraryFileBlockController::sanitizeTitle($filename, 12);
				$v = array($filename, $origfilename, $type, $generictype, $bo->getBlockID());

				$r = $db->query("insert into btFile (filename,origfilename, type, generictype, bID) values (?, ?, ?, ?, ?)", $v);
				
				$bf = LibraryFileBlockController::getFile($bo->getBlockID());
				$ret = Events::fire('on_file_upload', $bf);
			}
		}
		*/	
	}

?>
