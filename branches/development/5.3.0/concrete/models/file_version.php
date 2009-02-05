<?

class FileVersion extends Object {
	
	private $numThumbnailLevels = 3; 
	
	public function getFileID() {return $this->fID;}
	public function getFileVersionID() {return $this->fvID;}
	public function getPrefix() {return $this->fvPrefix;}
	public function getFileName() {return $this->fvFilename;}
	public function getTitle() {return $this->fvTitle;}
	public function getSize() {
		return round($this->fvSize / 1024) . t('KB');
	}
	public function getType() {
		$fh = Loader::helper('file');
		$ext = $fh->getExtension($this->fvFilename);
		
		$ftl = FileTypeList::getType($ext);
		if (is_object($ftl)) {
			return $ftl->getName();
		}
	}
	
	/** 
	 * Returns a full filesystem path to the file on disk.
	 */
	public function getPath() {
		$f = Loader::helper('concrete/file');
		$path = $f->getSystemPath($this->fvPrefix, $this->fvFilename);
		return $path;
	}
	
	public function getRelativePath() {
		$f = Loader::helper('concrete/file');
		$path = $f->getFileRelativePath($this->fvPrefix, $this->fvFilename );
		return $path;
	}	
	
	public function getThumbnailPath($level) {
		$f = Loader::helper('concrete/file');
		$path = $f->getThumbnailSystemPath($this->fvPrefix, $this->fvFilename, $level);
		return $path;
	}
	
	public function getThumbnailSRC($level) {
		eval('$hasThumbnail = $this->fvHasThumbnail' . $level . ';');
		if ($hasThumbnail) {
			$f = Loader::helper('concrete/file');
			$path = $f->getThumbnailRelativePath($this->fvPrefix, $this->fvFilename, $level);
			return $path;
		}
	}
	
	public function getThumbnail($level) {
		$html = Loader::helper('html');
		eval('$hasThumbnail = $this->fvHasThumbnail' . $level . ';');
		if ($hasThumbnail) {
			return $html->image($this->getThumbnailSRC(1));
		} else {
			eval('$width = AL_THUMBNAIL_WIDTH_LEVEL' . $level . ';');
			eval('$height = AL_THUMBNAIL_WIDTH_HEIGHT' . $level . ';');
			$fh = Loader::helper('file');
			$ext = $fh->getExtension($this->fvFilename);
			if (file_exists(DIR_AL_ICONS . '/' . $ext . '.png')) {
				$url = REL_DIR_AL_ICONS . '/' . $ext . '.png';
			} else {
				$url = AL_ICON_DEFAULT;
			}
			return '<img src="' . $url . '" class="ccm-generic-thumbnail" width="' . $width . '" height="' . $height . '" />';
		}
	}
	// 
	public function refreshThumbnails() {
		$db = Loader::db();
		$f = Loader::helper('concrete/file');
		for ($i = 1; $i <= $this->numThumbnailLevels; $i++) {
			$path = $f->getThumbnailSystemPath($this->fvPrefix, $this->fvFilename, $i);	
			$hasThumbnail = 0;
			if (file_exists($path)) {
				$hasThumbnail = 1;
			}
			$db->Execute("update FileVersions set fvHasThumbnail" . $i . "= ? where fID = ? and fvID = ?", array($hasThumbnail, $this->fID, $this->fvID));
		}
	}
	
	// update types
	const UT_NEW = 0;
	
	
	/** 
	 * Responsible for taking a particular version of a file and rescanning all its attributes
	 * This will run any type-based import routines, and store those attributes, generate thumbnails,
	 * etc...
	 */
	public function refreshAttributes() {
		$fh = Loader::helper('file');
		$ext = $fh->getExtension($this->fvFilename);
		$ftl = FileTypeList::getType($ext);
		$db = Loader::db();
		$size = filesize($this->getPath());
		$db->Execute('update FileVersions set fvTitle = ?, fvSize = ? where fID = ? and fvID = ?',
			array($this->getFilename(), $size, $this->getFileID(), $this->getFileVersionID())
		);
		if (is_object($ftl)) {
			if ($ftl->getCustomImporter() != false) {
				Loader::library('file/inspector');
				
				$db->Execute('update FileVersions set fvGenericType = ? where fID = ? and fvID = ?',
					array($ftl->getGenericType(), $this->getFileID(), $this->getFileVersionID())
				);
				
				// we have a custom library script that handles this stuff
				$script = 'file/types/' . $ftl->getCustomImporter();
				Loader::library($script);
				
				$class = Object::camelcase($ftl->getCustomImporter()) . 'FileTypeInspector';
				$cl = new $class;
				$cl->inspect($this);
				
			}
		}
	}

	public function createThumbnailDirectories(){
		$f = Loader::helper('concrete/file');
		for ($i = 1; $i <= $this->numThumbnailLevels; $i++) {
			$path = $f->getThumbnailSystemPath($this->fvPrefix, $this->fvFilename, $i, true);	
		}
	}
	
	public function setAttribute($akHandle, $value) {
		$db = Loader::db();
		$fakID = $db->GetOne("select fakID from FileAttributeKeys where akHandle = ?", array($akHandle));
		if ($fakID > 0) {
			$db->Replace('FileAttributeValues', array(
				'fID' => $this->fID,
				'fvID' => $this->getFileVersionID(),
				'fakID' => $fakID,
				'value' => $value
			),
			array('fID', 'fvID', 'fakID'), true);
		}
		
	}
	
	
}