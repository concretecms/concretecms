<?php 

class File extends Object { 

	const CREATE_NEW_VERSION_THRESHOLD = 300; // in seconds (5 minutes)
	const F_ERROR_INVALID_FILE = 1;
	const F_ERROR_FILE_NOT_FOUND = 2;
	
	public function getByID($fID) {
		Loader::model('file_set');
		$db = Loader::db();
		$f = new File();
		$row = $db->GetRow("SELECT Files.*, FileVersions.fvID
		FROM Files LEFT JOIN FileVersions on Files.fID = FileVersions.fID and FileVersions.fvIsApproved = 1
		WHERE Files.fID = ?", array($fID));
		if ($row['fID'] == $fID) {
			$f->setPropertiesFromArray($row);
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
		Cache::delete('file_relative_path', $this->getFileID());
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
	}
	
	public function setPassword($pw) {
		$db = Loader::db();
		$db->Execute("update Files set fPassword = ? where fID = ?", array($pw, $this->getFileID()));
		$this->fPassword = $pw;
	}
	
	public function overrideFileSetPermissions() {
		return $this->fOverrideSetPermissions;
	}
	
	public function resetPermissions($fOverrideSetPermissions = 0) {
		$db = Loader::db();
		$db->Execute("delete from FilePermissions where fID = ?", array($this->fID));
		$db->Execute("update Files set fOverrideSetPermissions = ? where fID = ?", array($fOverrideSetPermissions, $this->fID));
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
	
	public static function add($filename, $prefix, $data = array()) {
		$db = Loader::db();
		$dh = Loader::helper('date');
		$date = $dh->getLocalDateTime(); 
		
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
		$fvTags = (isset($data['fvTags'])) ? self::cleanTags($data['fvTags']) : '';
		$fvIsApproved = (isset($data['fvIsApproved'])) ? $data['fvIsApproved'] : '1';

		$db = Loader::db();
		$dh = Loader::helper('date');
		$date = $dh->getLocalDateTime();
		
		$fvID = $db->GetOne("select max(fvID) from FileVersions where fID = ?", array($this->fID));
		if ($fvID > 0) {
			$fvID++;
		} else {
			$fvID = 1;
		}
		
		$db->Execute('insert into FileVersions (fID, fvID, fvFilename, fvPrefix, fvDateAdded, fvIsApproved, fvApproverUID, fvAuthorUID, fvActivateDateTime, fvTitle, fvDescription, fvTags) 
		values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
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
			$fvTags));
			
		$fv = $this->getVersion($fvID);
		return $fv;
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
		
		// now from the DB
		$db->Execute("delete from Files where fID = ?", array($this->fID));
		$db->Execute("delete from FileVersions where fID = ?", array($this->fID));
		$db->Execute("delete from FileAttributeValues where fID = ?", array($this->fID));
		$db->Execute("delete from FileSetFiles where fID = ?", array($this->fID));
		$db->Execute("delete from FileVersionLog where fID = ?", array($this->fID));			
	}
	

	public function getRecentVersion() {
		$db = Loader::db();
		$fvID = $db->GetOne("select fvID from FileVersions where fID = ? order by fvID desc", array($this->fID));
		return $this->getVersion($fvID);
	}
	
	public function getVersion($fvID = null) {
		if ($fvID == null) {
			$fvID = $this->fvID; // approved version
		}
		$db = Loader::db();
		$row = $db->GetRow("select * from FileVersions where fvID = ? and fID = ?", array($fvID, $this->fID));
		$row['fvAuthorName'] = $db->GetOne("select uName from Users where uID = ?", array($row['fvAuthorUID']));
		
		$fv = new FileVersion();
		$row['fslID'] = $this->fslID;
		$fv->setPropertiesFromArray($row);
		$fv->populateAttributes();
		
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
	
	public function getDownloadStatistics(){
		$db = Loader::db();
		return $db->getAll("SELECT * FROM DownloadStatistics WHERE fID = ? ORDER BY timestamp desc", array($this->getFileID()));
	}
	
	public function trackDownload(){ 
		$u = new User();
		$uID = intval( $u->getUserID() );
		$fv = $this->getVersion();
		$fvID = $fv->getFileVersionID();
		
		$db = Loader::db();
		$db->Execute('insert into DownloadStatistics (fID, fvID, uID) values (?, ?, ?)',  array( $this->fID, intval($fvID), $uID ) );		
	}
}
