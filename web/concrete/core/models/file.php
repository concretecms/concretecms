<?

Loader::model('file_version');

class Concrete5_Model_File extends Object { 

	const CREATE_NEW_VERSION_THRESHOLD = 300; // in seconds (5 minutes)
	const F_ERROR_INVALID_FILE = 1;
	const F_ERROR_FILE_NOT_FOUND = 2;
	
	/**
	 * returns a file object for the given file ID
	 * @param int $fID
	 * @return File
	 */
	public function getByID($fID) {
		$f = Cache::get('file_approved', $fID);
		if (is_object($f)) {
			return $f;
		}
		
		Loader::model('file_set');
		$db = Loader::db();
		$f = new File();
		$row = $db->GetRow("SELECT Files.*, FileVersions.fvID
		FROM Files LEFT JOIN FileVersions on Files.fID = FileVersions.fID and FileVersions.fvIsApproved = 1
		WHERE Files.fID = ?", array($fID));
		if ($row['fID'] == $fID) {
			$f->setPropertiesFromArray($row);
			Cache::set('file_approved', $fID, $f);
		} else {
			$f->error = File::F_ERROR_INVALID_FILE;
		}
		return $f;
	}	
	
	/** 
	 * For all methods that file does not implement, we pass through to the currently active file version object 
	 */
	public function __call($nm, $a) {
		$fv = $this->getApprovedVersion();
		return call_user_func_array(array($fv, $nm), $a);
	}

	public function getPermissionObjectIdentifier() {
		return $this->getFileID();
	}

	public function getPath() {
		$fv = $this->getVersion();
		return $fv->getPath();
	}

	public function getPassword() {
		return $this->fPassword;
	}
	
	public function getStorageLocationID() {
		return $this->fslID;
	}
	
	public function refreshCache() {
		$db = Loader::db();
		Cache::delete('file_relative_path', $this->getFileID());
		Cache::delete('file_approved', $this->getFileID());
		$r = $db->GetCol('select fvID from FileVersions where fID = ?', array($this->getFileID()));
		foreach($r as $fvID) {
			Cache::delete('file_version_' . $this->getFileID(), $fvID);	
		}
	}
	
	public function reindex() {
		Loader::model('attribute/categories/file');
		$attribs = FileAttributeKey::getAttributes($this->getFileID(), $this->getFileVersionID(), 'getSearchIndexValue');
		$db = Loader::db();

		$db->Execute('delete from FileSearchIndexAttributes where fID = ?', array($this->getFileID()));
		$searchableAttributes = array('fID' => $this->getFileID());
		$rs = $db->Execute('select * from FileSearchIndexAttributes where fID = -1');
		AttributeKey::reindex('FileSearchIndexAttributes', $searchableAttributes, $attribs, $rs);
	}

	public static function getRelativePathFromID($fID) {
		$path = Cache::get('file_relative_path', $fID);
		if ($path != false) {
			return $path;
		}
		
		$f = File::getByID($fID);
		$path = $f->getRelativePath();
		
		Cache::set('file_relative_path', $fID, $path);
		return $path;
	}

	
	public function setStorageLocation($item) {
		if ($item == 0) {
			// set to default
			$itemID = 0;
			$path = DIR_FILES_UPLOADED;
		} else {
			$itemID = $item->getID();
			$path = $item->getDirectory();
		}
		
		if ($itemID != $this->getStorageLocationID()) {
			// retrieve all versions of a file and move its stuff
			$list = $this->getVersionList();
			$fh = Loader::helper('concrete/file');
			foreach($list as $fv) {
				$newPath = $fh->mapSystemPath($fv->getPrefix(), $fv->getFileName(), true, $path);
				$currPath = $fv->getPath();
				rename($currPath, $newPath);
			}			
			$db = Loader::db();
			$db->Execute('update Files set fslID = ? where fID = ?', array($itemID, $this->fID));
		}
		$this->refreshCache();
	}
	
	public function setPassword($pw) {
		Events::fire('on_file_set_password', $this, $pw);
		$db = Loader::db();
		$db->Execute("update Files set fPassword = ? where fID = ?", array($pw, $this->getFileID()));
		$this->fPassword = $pw;
		$this->refreshCache();
	}
	
	public function setOriginalPage($ocID) {
		if ($ocID < 1) {
			return false;
		}
		
		$db = Loader::db();
		$db->Execute("update Files set ocID = ? where fID = ?", array($ocID, $this->getFileID()));
		$this->refreshCache();
	}
	
	public function getOriginalPageObject() {
		if ($this->ocID > 0) {
			$c = Page::getByID($this->ocID);
			if (is_object($c) && !$c->isError()) {
				return $c;
			}
		}
	}
	
	public function overrideFileSetPermissions() {
		return $this->fOverrideSetPermissions;
	}
	
	public function resetPermissions($fOverrideSetPermissions = 0) {
		$db = Loader::db();
		$db->Execute("delete from FilePermissionAssignments where fID = ?", array($this->fID));
		$db->Execute("update Files set fOverrideSetPermissions = ? where fID = ?", array($fOverrideSetPermissions, $this->fID));
		if ($fOverrideSetPermissions) {
			$permissions = PermissionKey::getList('file');
			foreach($permissions as $pk) { 
				$pk->setPermissionObject($this);
				$pk->copyFromFileSetToFile();
			}	
		}
		$this->refreshCache();
	}
	
	public function setPermissions($obj, $canRead, $canSearch, $canWrite, $canAdmin) {
		$fID = $this->fID;
		$uID = 0;
		$gID = 0;
		$db = Loader::db();
		if (is_a($obj, 'UserInfo')) {
			$uID = $obj->getUserID();
		} else {
			$gID = $obj->getGroupID();
		}
		
		if ($canRead < 1) {
			$canRead = 0;
		}
		
		if ($canSearch < 1) {
			$canSearch = 0;
		}
		
		if ($canWrite < 1) {
			$canWrite = 0;
		}
		
		if ($canAdmin < 1) {
			$canAdmin = 0;
		}
		
		$db->Replace('FilePermissions', array(
			'fID' => $fID,
			'uID' => $uID, 
			'gID' => $gID,
			'canRead' => $canRead,
			'canSearch' => $canSearch,
			'canWrite' => $canWrite,
			'canAdmin' => $canAdmin
		), 
		array('fID', 'gID', 'uID'), true);
		$this->refreshCache();
		
	}
	
	public function getUserID() {
		return $this->uID;
	}
	
	public function setUserID($uID) {
		$this->uID = $uID;
		$db = Loader::db();
		$db->Execute("update Files set uID = ? where fID = ?", array($uID, $this->fID));
	}
	
	public function getFileSets() {
		$db = Loader::db();
		Loader::model('file_set');
		$fsIDs = $db->Execute("select fsID from FileSetFiles where fID = ?", array($this->getFileID()));
		$filesets = array();
		foreach($fsIDs as $fsID) {
			$filesets[] = FileSet::getByID($fsID);
		}
		return $filesets;
	}
	
	public function isStarred($u = false) {
		if (!$u) {
			$u = new User();
		}
		$db = Loader::db();
		Loader::model('file_set');
		$r = $db->GetOne("select fsfID from FileSetFiles fsf inner join FileSets fs on fs.fsID = fsf.fsID where fsf.fID = ? and fs.uID = ? and fs.fsType = ?",
			array($this->getFileID(), $u->getUserID(), FileSet::TYPE_STARRED));
		return $r > 0;
	}
	
	public function getDateAdded() {
		return $this->fDateAdded;
	}
	
	/** 
	 * Returns a file version object that is to be written to. Computes whether we can use the current most recent version, OR a new one should be created
	 */
	public function getVersionToModify($forceCreateNew = false) {
		$u = new User();
		$createNew = false;
		
		$fv = $this->getRecentVersion();
		$fav = $this->getApprovedVersion();
		
		// first test. Does the user ID of the most recent version match ours? If not, then we create new
		if ($u->getUserID() != $fv->getAuthorUserID()) {
			$createNew = true;
		}
		
		// second test. If the date the version was added is older than File::CREATE_NEW_VERSION_THRESHOLD, we create new
		$unixTime = strtotime($fv->getDateAdded());
		$diff = time() - $unixTime;
		if ($diff > File::CREATE_NEW_VERSION_THRESHOLD) {
			$createNew = true;
		}
		
		if ($forceCreateNew) {
			$createNew = true;
		}
		
		if ($createNew) {
			$fv2 = $fv->duplicate();
			
			// Are the recent and active versions the same? If so, we approve this new version we just made
			if ($fv->getFileVersionID() == $fav->getFileVersionID()) {
				$fv2->approve();
			}
			return $fv2;
		} else {
			return $fv;
		}
	}
	
	public function getFileID() { return $this->fID;}
	
	public function duplicate() {
		$dh = Loader::helper('date');
		$db = Loader::db();
		$date = $dh->getSystemDateTime(); 

		$far = new ADODB_Active_Record('Files');
		$far->Load('fID=?', array($this->fID));
		
		$far2 = clone $far;
		$far2->fID = null;
		$far2->fDateAdded = $date;
		$far2->Insert();
		$fIDNew = $db->Insert_ID();

		$fvIDs = $db->GetCol('select fvID from FileVersions where fID = ?', $this->fID);
		foreach($fvIDs as $fvID) {
			$farv = new ADODB_Active_Record('FileVersions');
			$farv->Load('fID=? and fvID = ?', array($this->fID, $fvID));
	
			$farv2 = clone $farv;
			$farv2->fID = $fIDNew;
			$farv2->fvActivateDatetime = $date;
			$farv2->fvDateAdded = $date;
			$farv2->Insert();
		}		

		$r = $db->Execute('select fvID, akID, avID from FileAttributeValues where fID = ?', array($this->getFileID()));
		while ($row = $r->fetchRow()) {
			$db->Execute("insert into FileAttributeValues (fID, fvID, akID, avID) values (?, ?, ?, ?)", array(
				$fIDNew, 
				$row['fvID'],
				$row['akID'], 
				$row['avID']
			));
		}

		$v = array($this->fID);
		$q = "select fID, paID, pkID from FilePermissionAssignments where fID = ?";
		$r = $db->query($q, $v);
		while($row = $r->fetchRow()) {
			$v = array($fIDNew, $row['paID'], $row['pkID']);
			$q = "insert into FilePermissionAssignments (fID, paID, pkID) values (?, ?, ?)";
			$db->query($q, $v);
		}
		
		// return the new file object
		return File::getByID($fIDNew);
		
	}
	
	public static function add($filename, $prefix, $data = array()) {
		$db = Loader::db();
		$dh = Loader::helper('date');
		$date = $dh->getSystemDateTime(); 
		
		$uID = 0;
		$u = new User();
		if (isset($data['uID'])) {
			$uID = $data['uID'];
		} else if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}
		
		$db->Execute('insert into Files (fDateAdded, uID) values (?, ?)', array($date, $uID));
		
		$fID = $db->Insert_ID();
		
		$f = File::getByID($fID);
		
		$fv = $f->addVersion($filename, $prefix, $data);
		Events::fire('on_file_add', $f, $fv);
		
		$entities = $u->getUserAccessEntityObjects();
		$hasUploader = false;
		foreach($entities as $obj) {
			if ($obj instanceof FileUploaderPermissionAccessEntity) {
				$hasUploader = true;
			}
		}
		if (!$hasUploader) {
			$u->refreshUserGroups();
		}
		return $fv;
	}
	
	public function addVersion($filename, $prefix, $data = array()) {
		$u = new User();
		$uID = (isset($data['uID']) && $data['uID'] > 0) ? $data['uID'] : $u->getUserID();
		
		if ($uID < 1) {
			$uID = 0;
		}
		
		$fvTitle = (isset($data['fvTitle'])) ? $data['fvTitle'] : '';
		$fvDescription = (isset($data['fvDescription'])) ? $data['fvDescription'] : '';
		$fvTags = (isset($data['fvTags'])) ? FileVersion::cleanTags($data['fvTags']) : '';
		$fvIsApproved = (isset($data['fvIsApproved'])) ? $data['fvIsApproved'] : '1';

		$db = Loader::db();
		$dh = Loader::helper('date');
		$date = $dh->getSystemDateTime();
		
		$fvID = $db->GetOne("select max(fvID) from FileVersions where fID = ?", array($this->fID));
		if ($fvID > 0) {
			$fvID++;
		} else {
			$fvID = 1;
		}
		
		$db->Execute('insert into FileVersions (fID, fvID, fvFilename, fvPrefix, fvDateAdded, fvIsApproved, fvApproverUID, fvAuthorUID, fvActivateDateTime, fvTitle, fvDescription, fvTags, fvExtension) 
		values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
			$this->fID, 
			$fvID,
			$filename,
			$prefix, 
			$date,
			$fvIsApproved, 
			$uID, 
			$uID, 
			$date,
			$fvTitle,
			$fvDescription, 
			$fvTags,
			''));;
			
		$fv = $this->getVersion($fvID);
		Events::fire('on_file_version_add', $fv);
		return $fv;
	}
	
	public function getApprovedVersion() {
		return $this->getVersion();
	}
	
	public function inFileSet($fs) {
		$db = Loader::db();
		$r = $db->GetOne("select fsfID from FileSetFiles where fID = ? and fsID = ?", array($this->getFileID(), $fs->getFileSetID()));
		return $r > 0;
	}
	
	/** 
	 * Removes a file, including all of its versions
	 */
	public function delete() {
		// first, we remove all files from the drive
		$db = Loader::db();
		$pathbase = false;
		$r = $db->GetAll('select fvFilename, fvPrefix from FileVersions where fID = ?', array($this->fID));
		$h = Loader::helper('concrete/file');
		Loader::model('file_storage_location');
		if ($this->getStorageLocationID() > 0) {
			$fsl = FileStorageLocation::getByID($this->getStorageLocationID());
			$pathbase = $fsl->getDirectory();
		}
		foreach($r as $val) {
			
			// Now, we make sure this file isn't referenced by something else. If it is we don't delete the file from the drive
			$cnt = $db->GetOne('select count(*) as total from FileVersions where fID <> ? and fvFilename = ? and fvPrefix = ?', array(
				$this->fID,
				$val['fvFilename'],
				$val['fvPrefix']
			));
			if ($cnt == 0) {
				if ($pathbase != false) {
					$path = $h->mapSystemPath($val['fvPrefix'], $val['fvFilename'], false, $pathbase);
				} else {
					$path = $h->mapSystemPath($val['fvPrefix'], $val['fvFilename'], false);
				}
				$t1 = $h->getThumbnailSystemPath($val['fvPrefix'], $val['fvFilename'], 1);
				$t2 = $h->getThumbnailSystemPath($val['fvPrefix'], $val['fvFilename'], 2);
				$t3 = $h->getThumbnailSystemPath($val['fvPrefix'], $val['fvFilename'], 3);
				if (file_exists($path)) {
					unlink($path);
				}
				if (file_exists($t1)) {
					unlink($t1);
				}
				if (file_exists($t2)) {
					unlink($t2);
				}
				if (file_exists($t3)) {
					unlink($t3);
				}
			}
		}
		
		// now from the DB
		$db->Execute("delete from Files where fID = ?", array($this->fID));
		$db->Execute("delete from FileVersions where fID = ?", array($this->fID));
		$db->Execute("delete from FileAttributeValues where fID = ?", array($this->fID));
		$db->Execute("delete from FileSetFiles where fID = ?", array($this->fID));
		$db->Execute("delete from FileVersionLog where fID = ?", array($this->fID));
		$this->refreshCache();
	}
	

	/**
	 * returns the most recent FileVersion object
	 * @return FileVersion
	 */
	public function getRecentVersion() {
		$db = Loader::db();
		$fvID = $db->GetOne("select fvID from FileVersions where fID = ? order by fvID desc", array($this->fID));
		return $this->getVersion($fvID);
	}
	
	/**
	 * returns the FileVersion object for the provided fvID
	 * if none provided returns the approved version
	 * @param int $fvID
	 * @return FileVersion
	 */
	public function getVersion($fvID = null) {
		if ($fvID == null) {
			$fvID = $this->fvID; // approved version
		}
		
		$fv = Cache::get('file_version_' . $this->getFileID(), $fvID);
		if (is_object($fv)) {
			return $fv;
		}
		
		$db = Loader::db();
		$row = $db->GetRow("select * from FileVersions where fvID = ? and fID = ?", array($fvID, $this->fID));
		$row['fvAuthorName'] = $db->GetOne("select uName from Users where uID = ?", array($row['fvAuthorUID']));
		
		$fv = new FileVersion();
		$row['fslID'] = $this->fslID;
		$fv->setPropertiesFromArray($row);
		$fv->populateAttributes();
		
		Cache::set('file_version_' . $this->getFileID(), $fvID, $fv);		
		return $fv;
	}
	
	/** 
	 * Returns an array of all FileVersion objects owned by this file
	 */
	public function getVersionList() {
		$db = Loader::db();
		$r = $db->Execute("select fvID from FileVersions where fID = ? order by fvDateAdded desc", array($this->getFileID()));
		$files = array();
		while ($row = $r->FetchRow()) {
			$files[] = $this->getVersion($row['fvID']);
		}
		return $files;
	}
	
	public function getTotalDownloads() {
		$db = Loader::db();
		return $db->GetOne('select count(*) from DownloadStatistics where fID = ?', array($this->getFileID()));
	}
	
	public function getDownloadStatistics($limit = 20){
		$db = Loader::db();
		$limitString = '';
		if ($limit != false) {
			$limitString = 'limit ' . $limit;
		}
		
		if (is_object($this) && $this instanceof File) { 
			return $db->getAll("SELECT * FROM DownloadStatistics WHERE fID = ? ORDER BY timestamp desc {$limitString}", array($this->getFileID()));
		} else {
			return $db->getAll("SELECT * FROM DownloadStatistics ORDER BY timestamp desc {$limitString}");
		}
	}	
	
	/**
	 * Tracks File Download, takes the cID of the page that the file was downloaded from 
	 * @param int $rcID
	 * @return void
	 */
	public function trackDownload($rcID=NULL){ 
		$u = new User();
		$uID = intval( $u->getUserID() );
		$fv = $this->getVersion();
		$fvID = $fv->getFileVersionID();
		if(!isset($rcID) || !is_numeric($rcID)) {
			$rcID = 0;
		}
		Events::fire('on_file_download', $fv, $u);
		$db = Loader::db();
		$db->Execute('insert into DownloadStatistics (fID, fvID, uID, rcID) values (?, ?, ?, ?)',  array( $this->fID, intval($fvID), $uID, $rcID ) );		
	}
}
