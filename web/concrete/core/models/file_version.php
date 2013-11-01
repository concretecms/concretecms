<?

class Concrete5_Model_FileVersion extends Object {

	protected $numThumbnailLevels = 3;
	protected $attributes = array();

	// Update type constants
	const UT_REPLACE_FILE = 1;
	const UT_TITLE = 2;
	const UT_DESCRIPTION = 3;
	const UT_TAGS = 4;
	const UT_EXTENDED_ATTRIBUTE = 5;

	public function getFileID() {return $this->fID;}
	public function getFileVersionID() {return $this->fvID;}
	public function getPrefix() {return $this->fvPrefix;}
	public function getFileName() {return $this->fvFilename;}
	public function getTitle() {return $this->fvTitle;}
	public function getTags() {return $this->fvTags;}
	public function getDescription() {return $this->fvDescription;}
	public function isApproved() {return $this->fvIsApproved;}

	public function getGenericTypeText() {
		$to = $this->getTypeObject();
		return $to->getGenericTypeText( $to->getGenericType() );
	}

	/**
	 * returns the File object associated with this FileVersion object
	 * @return File
	 */
	public function getFile() {
		$fo = File::getByID($this->fID);
		return $fo;
	}

	//returns an array of tags, instead of a string
	public function getTagsList(){
		$tags=explode("\n",str_replace("\r","\n",trim($this->getTags())));
		$clean_tags=array();
		foreach($tags as $tag){
			if( strlen(trim($tag)) )
				$clean_tags[]=trim($tag);
		}
		return $clean_tags;
	}

	/**
	 * Gets an associative array of all attributes for a file version
	 */
	public function getAttributeList() {
		$db = Loader::db();
		$v = array($this->fID, $this->fvID);
		Loader::model('attribute/categories/file');
		$attributes = FileAttributeKey::getAttributes($this->fID, $this->fvID);
		return $attributes;
	}

	/**
	 * Gets an attribute for the file. If "nice mode" is set, we display it nicely
	 * for use in the file attributes table
	 */

	public function getAttribute($ak, $mode = false) {
		if (is_object($ak)) {
			$akHandle = $ak->getAttributeKeyHandle();
		} else {
			$akHandle = $ak;
		}

		if (!isset($this->attributes[$akHandle . $mode])) {
			$this->attributes[$akHandle . $mode] = false;
			$ak = FileAttributeKey::getByHandle($akHandle);
			if (is_object($ak)) {
				$av = $this->getAttributeValueObject($ak);
				if (is_object($av)) {
					$this->attributes[$akHandle . $mode] = $av->getValue($mode);
				}
			}
		}
		return $this->attributes[$akHandle . $mode];
	}


	public function getMimeType() {
		$h = Loader::helper('mime');
		$fh = Loader::helper('file');
		$ext = $fh->getExtension($this->fvFilename);
		return $h->mimeFromExtension($ext);
	}

	public function getSize() {
		return round($this->fvSize / 1024) . t('KB');
	}
	public function getFullSize() {
		return $this->fvSize;
	}
	public function getAuthorName() {
		return $this->fvAuthorName;
	}

	public function getAuthorUserID() {
		return $this->fvAuthorUID;
	}

	/**
	 * Gets the date a file version was added
	 * if user is specified, returns in the current user's timezone
	 * @param string $type (system || user)
	 * @return string date formated like: 2009-01-01 00:00:00
	*/
	function getDateAdded($type = 'system') {
		if(ENABLE_USER_TIMEZONES && $type == 'user') {
			$dh = Loader::helper('date');
			return $dh->getLocalDateTime($this->fvDateAdded);
		} else {
			return $this->fvDateAdded;
		}
	}

	public function getExtension() {
		return $this->fvExtension;
	}

	public function logVersionUpdate($updateTypeID, $updateTypeAttributeID = 0) {
		$db = Loader::db();
		$db->Execute('insert into FileVersionLog (fID, fvID, fvUpdateTypeID, fvUpdateTypeAttributeID) values (?, ?, ?, ?)', array(
			$this->getFileID(),
			$this->getFileVersionID(),
			$updateTypeID,
			$updateTypeAttributeID
		));
	}

	/**
	 * Takes the current value of the file version and makes a new one with the same values
	 */
	public function duplicate() {
		$f = File::getByID($this->fID);

		$dh = Loader::helper('date');
		$date = $dh->getSystemDateTime();
		$db = Loader::db();
		$fvID = $db->GetOne("select max(fvID) from FileVersions where fID = ?", array($this->fID));
		if ($fvID > 0) {
			$fvID++;
		}

		$data = $db->GetRow("select * from FileVersions where fID = ? and fvID = ?", array($this->fID, $this->fvID));
		$data['fvID'] = $fvID;
		$data['fvDateAdded'] = $date;
		$u = new User();
		$data['fvAuthorUID'] = $u->getUserID();

		// If This version is the approved version, we approve the new one.
		if ($this->isApproved()) {
			$data['fvIsApproved'] = 1;
		} else {
			$data['fvIsApproved'] = 0;
		}

		// build the field insert query
		$fields = '';
		$i = 0;
		$data2 = array();
		foreach($data as $key => $value) {
			if (!is_integer($key)) {
				$data2[$key] = $value;
			}
		}

		foreach($data2 as $key => $value) {
			$fields .= $key;
			$questions .= '?';
			if (($i + 1) < count($data2)) {
				$fields .= ',';
				$questions .= ',';
			}
			$i++;
		}

		$db->Execute("insert into FileVersions (" . $fields . ") values (" . $questions . ")", $data2);


		$this->deny();

		$r = $db->Execute('select fvID, akID, avID from FileAttributeValues where fID = ? and fvID = ?', array($this->getFileID(), $this->fvID));
		while ($row = $r->fetchRow()) {
			$db->Execute("insert into FileAttributeValues (fID, fvID, akID, avID) values (?, ?, ?, ?)", array(
				$this->fID,
				$fvID,
				$row['akID'],
				$row['avID']
			));
		}
		$fv2 = $f->getVersion($fvID);
		Events::fire('on_file_version_duplicate', $fv2);
		return $fv2;
	}


	public function getType() {
		$ftl = $this->getTypeObject();
		if (is_object($ftl)) {
			return $ftl->getName();
		}
	}

	public function getTypeObject() {
		$fh = Loader::helper('file');
		$ext = $fh->getExtension($this->fvFilename);

		$ftl = FileTypeList::getType($ext);
		return $ftl;
	}

	/**
	 * Returns an array containing human-readable descriptions of everything that happened in this version
	 */
	public function getVersionLogComments() {
		$updates = array();
		$db = Loader::db();
		$ga = $db->GetAll('select fvUpdateTypeID, fvUpdateTypeAttributeID from FileVersionLog where fID = ? and fvID = ? order by fvlID asc', array($this->getFileID(), $this->getFileVersionID()));
		foreach($ga as $a) {
			switch($a['fvUpdateTypeID']) {
				case FileVersion::UT_REPLACE_FILE:
					$updates[] = t('File');
					break;
				case FileVersion::UT_TITLE:
					$updates[] = t('Title');
					break;
				case FileVersion::UT_DESCRIPTION:
					$updates[] = t('Description');
					break;
				case FileVersion::UT_TAGS:
					$updates[] = t('Tags');
					break;
				case FileVersion::UT_EXTENDED_ATTRIBUTE:
					$val = $db->GetOne("select akName from AttributeKeys where akID = ?", array($a['fvUpdateTypeAttributeID']));
					if ($val != '') {
						$updates[] = $val;
					}
					break;
			}
		}
		$updates = array_unique($updates);
		$updates1 = array();
		foreach($updates as $val) {
			// normalize the keys
			$updates1[] = $val;
		}
		return $updates1;
	}

	public function updateTitle($title) {
		$db = Loader::db();
		$db->Execute("update FileVersions set fvTitle = ? where fID = ? and fvID = ?", array($title, $this->getFileID(), $this->getFileVersionID()));
		$this->logVersionUpdate(FileVersion::UT_TITLE);
		$this->fvTitle = $title;
		Events::fire('on_file_version_update_title', $this, $title);
		$fo = $this->getFile();
		$fo->refreshCache();
	}

	public function updateTags($tags) {
		$db = Loader::db();
		$tags = FileVersion::cleanTags($tags);
		$db->Execute("update FileVersions set fvTags = ? where fID = ? and fvID = ?", array($tags, $this->getFileID(), $this->getFileVersionID()));
		$this->logVersionUpdate(FileVersion::UT_TAGS);
		$this->fvTitle = $tags;
		Events::fire('on_file_version_update_tags', $this, $tags);
		$fo = $this->getFile();
		$fo->refreshCache();
	}


	public function updateDescription($descr) {
		$db = Loader::db();
		$db->Execute("update FileVersions set fvDescription = ? where fID = ? and fvID = ?", array($descr, $this->getFileID(), $this->getFileVersionID()));
		$this->logVersionUpdate(FileVersion::UT_DESCRIPTION);
		$this->fvTitle = $descr;
		Events::fire('on_file_version_update_description', $this, $descr);
		$fo = $this->getFile();
		$fo->refreshCache();
	}

	public function updateFile($filename, $prefix) {
		$db = Loader::db();
		$db->Execute("update FileVersions set fvFilename = ?, fvPrefix = ? where fID = ? and fvID = ?", array($filename, $prefix, $this->getFileID(), $this->getFileVersionID()));
		$this->logVersionUpdate(FileVersion::UT_REPLACE_FILE);
		$this->fvFilename = $filename;
		$this->fvPrefix = $prefix;

		$fo = $this->getFile();
		$fo->refreshCache();
	}


	public function approve() {
		$db = Loader::db();
		$db->Execute("update FileVersions set fvIsApproved = 0 where fID = ?", array($this->getFileID()));
		$db->Execute("update FileVersions set fvIsApproved = 1 where fID = ? and fvID = ?", array($this->getFileID(), $this->getFileVersionID()));

		Events::fire('on_file_version_approve', $this);
		$fo = $this->getFile();
		$fo->reindex();
		$fo->refreshCache();
	}


	public function deny() {
		$db = Loader::db();
		$db->Execute("update FileVersions set fvIsApproved = 0 where fID = ? and fvID = ?", array($this->getFileID(), $this->getFileVersionID()));
		Events::fire('on_file_version_deny', $this);
		$fo = $this->getFile();
		$fo->refreshCache();
	}


	public function setAttribute($ak, $value) {
		if (!is_object($ak)) {
			$ak = FileAttributeKey::getByHandle($ak);
		}
		$ak->setAttribute($this, $value);
		$fo = $this->getFile();
		$fo->refreshCache();
		$fo->reindex();
		unset($ak);
	}


	/**
	 * Removes a version of a file. Note, does NOT remove the file because we don't know where the file might elsewhere be used/referenced.
	 */
	public function delete() {
		if ($this->fvIsApproved == 1) {
			return false; // can only delete non-live files
		}

		$db = Loader::db();
		// now from the DB
		$db->Execute("delete from FileVersions where fID = ? and fvID = ?", array($this->fID, $this->fvID));
		$db->Execute("delete from FileAttributeValues where fID = ? and fvID = ?", array($this->fID, $this->fvID));
		$db->Execute("delete from FileVersionLog where fID = ? and fvID = ?", array($this->fID, $this->fvID));
	}


	/**
	 * Returns a full filesystem path to the file on disk.
	 */
	public function getPath() {
		$f = Loader::helper('concrete/file');
		if ($this->fslID > 0) {
			Loader::model('file_storage_location');
			$fsl = FileStorageLocation::getByID($this->fslID);
			$path = $f->mapSystemPath($this->fvPrefix, $this->fvFilename, false, $fsl->getDirectory());
		} else {
			$path = $f->getSystemPath($this->fvPrefix, $this->fvFilename);
		}
		return $path;
	}

	/**
	 * Returns a full URL to the file on disk
	 */
	public function getURL() {
		return BASE_URL . $this->getRelativePath();
	}

	/**
	 * Returns a URL that can be used to download the file. This passes through the download_file single page.
	 */
	public function getDownloadURL() {
		$c = Page::getCurrentPage();
		if($c instanceof Page) {
			$cID = $c->getCollectionID();
		} else {
			$cID = 0;
		}
		return BASE_URL . View::url('/download_file',$this->getFileID(),$cID);
	}
	
	/**
	 * Returns a url that can be used to download a file, will force the download of all file types, even if your browser can display them.
	 */
	public function getForceDownloadURL() {
		$c = Page::getCurrentPage();
		if($c instanceof Page) {
			$cID = $c->getCollectionID();
		} else {
			$cID = 0;
		}
		return BASE_URL . View::url('/download_file','force', $this->getFileID(),$cID);
	}
	

	public function getRelativePath($fullurl = false) {
		$f = Loader::helper('concrete/file');
		if ($this->fslID > 0) {
			$c = Page::getCurrentPage();
			if($c instanceof Page) {
				$cID = $c->getCollectionID();
			} else {
				$cID = 0;
			}
			$path = BASE_URL . View::url('/download_file', 'view_inline', $this->getFileID(),$cID);
		} else {
			if ($fullurl) {
				$path = BASE_URL . $f->getFileRelativePath($this->fvPrefix, $this->fvFilename );
			} else {
				$path = $f->getFileRelativePath($this->fvPrefix, $this->fvFilename );
			}
		}
		return $path;
	}

	public function getThumbnailPath($level) {
		$f = Loader::helper('concrete/file');
		$path = $f->getThumbnailSystemPath($this->fvPrefix, $this->fvFilename, $level);
		return $path;
	}

	public function getThumbnailSRC($level) {
		if ($this->{"fvHasThumbnail{$level}"}) {
			$f = Loader::helper('concrete/file');
			$path = $f->getThumbnailRelativePath($this->fvPrefix, $this->fvFilename, $level);
			return $path;
		}
	}

	public function hasThumbnail($level) {
		return $this->{"fvHasThumbnail{$level}"};
	}

	public function getThumbnail($level, $fullImageTag = true) {
		$html = Loader::helper('html');
		if ($this->{"fvHasThumbnail{$level}"}) {
			if ($fullImageTag) {
				return $html->image($this->getThumbnailSRC($level));
			} else {
				return $this->getThumbnailSRC($level);
			}
		} else {
			$ft = FileTypeList::getType($this->fvFilename);
			return $ft->getThumbnail($level, $fullImageTag);
		}
	}

	//
	public function refreshThumbnails($refreshCache = true) {
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

		if ($refreshCache) {
			$fo = $this->getFile();
			$fo->refreshCache();
		}
	}

	// update types
	const UT_NEW = 0;


	/**
	 * Responsible for taking a particular version of a file and rescanning all its attributes
	 * This will run any type-based import routines, and store those attributes, generate thumbnails,
	 * etc...
	 */
	public function refreshAttributes($firstRun = false) {
		$fh = Loader::helper('file');
		$ext = $fh->getExtension($this->fvFilename);
		$ftl = FileTypeList::getType($ext);
		$db = Loader::db();

		if (!file_exists($this->getPath())) {
			return File::F_ERROR_FILE_NOT_FOUND;
		}

		$size = filesize($this->getPath());

		$title = ($firstRun) ? $this->getFilename() : $this->getTitle();

		$db->Execute('update FileVersions set fvExtension = ?, fvType = ?, fvTitle = ?, fvSize = ? where fID = ? and fvID = ?',
			array($ext, $ftl->getGenericType(), $title, $size, $this->getFileID(), $this->getFileVersionID())
		);
		if (is_object($ftl)) {
			if ($ftl->getCustomImporter() != false) {
				Loader::library('file/inspector');

				$db->Execute('update FileVersions set fvGenericType = ? where fID = ? and fvID = ?',
					array($ftl->getGenericType(), $this->getFileID(), $this->getFileVersionID())
				);

				// we have a custom library script that handles this stuff
				$cl = $ftl->getCustomInspector();
				$cl->inspect($this);

			}
		}
		$this->refreshThumbnails(false);
		$f = $this->getFile();
		$f->refreshCache();
		$f->reindex();
	}

	public function createThumbnailDirectories(){
		$f = Loader::helper('concrete/file');
		for ($i = 1; $i <= $this->numThumbnailLevels; $i++) {
			$path = $f->getThumbnailSystemPath($this->fvPrefix, $this->fvFilename, $i, true);
		}
	}


	/**
	 * Checks current viewers for this type and returns true if there is a viewer for this type, false if not
	 */
	public function canView() {
		$to = $this->getTypeObject();
		if (is_object($to) && $to->getView() != '') {
			return true;
		}
		return false;
	}

	public function canEdit() {
		$to = $this->getTypeObject();
		if (is_object($to) && $to->getEditor() != '') {
			return true;
		}
		return false;
	}

	public function clearAttribute($ak) {
		$db = Loader::db();
		$cav = $this->getAttributeValueObject($ak);
		if (is_object($cav)) {
			$cav->delete();
		}
		$fo = $this->getFile();
		$fo->refreshCache();
		$fo->reindex();
	}

	public function getAttributeValueObject($ak, $createIfNotFound = false) {
		$db = Loader::db();
		$av = false;
		$v = array($this->getFileID(), $this->getFileVersionID(), $ak->getAttributeKeyID());
		$avID = $db->GetOne("select avID from FileAttributeValues where fID = ? and fvID = ? and akID = ?", $v);
		if ($avID > 0) {
			$av = FileAttributeValue::getByID($avID);
			if (is_object($av)) {
				$av->setFile($this->getFile());
				$av->setAttributeKey($ak);
			}
		}

		if ($createIfNotFound) {
			$cnt = 0;

			// Is this avID in use ?
			if (is_object($av)) {
				$cnt = $db->GetOne("select count(avID) from FileAttributeValues where avID = ?", $av->getAttributeValueID());
			}

			if ((!is_object($av)) || ($cnt > 1)) {
				$av = $ak->addAttributeValue();
			}
		}

		return $av;
	}

	//takes a string of comma or new line delimited tags, and puts them in the appropriate format
	public static function cleanTags($tagsStr){
		$tagsArray=explode("\n",str_replace(array("\r",","),"\n",$tagsStr));
		$cleanTags=array();
		foreach($tagsArray as $tag){
			if( !strlen(trim($tag)) ) continue;
			$cleanTags[]=trim($tag);
		}
		//the leading and trailing line break char is for searching: fvTag like %\ntag\n%
		return "\n".join("\n",$cleanTags)."\n";
	}
}
