<?

class File extends Object { 

	const CREATE_NEW_VERSION_THRESHOLD = 300; // in seconds (5 minutes)
	const F_ERROR_INVALID_FILE = 1;
	
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
	public function getVersionToModify() {
		$u = new User();
		$createNew = false;
		
		$fv = $this->getRecentVersion();
		
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
				
		if ($createNew) {
			$fv2 = $fv->duplicate();
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
		$db->Execute('insert into Files (fDateAdded) values (?)', array($date));
		
		$fID = $db->Insert_ID();
		
		$f = File::getByID($fID);
		
		$fv = $f->addVersion($filename, $prefix, $data);
		
		return $fv;
	}
	
	public function addVersion($filename, $prefix, $data = array()) {
		$u = new User();
		$uID = (isset($data['uID'])) ? $data['uID'] : $u->getUserID();

		$fvTitle = (isset($data['fvTitle'])) ? $data['fvTitle'] : '';
		$fvDescription = (isset($data['fvDescription'])) ? $data['fvDescription'] : '';
		$fvTags = (isset($data['fvTags'])) ? $data['fvTags'] : '';
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
		$r = $db->GetAll('select fvFilename, fvPrefix from FileVersions where fID = ?', array($this->fID));
		$h = Loader::helper('concrete/file');
		foreach($r as $val) {
			$path = $h->getSystemPath($val['fvPrefix'], $val['fvFilename']);
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
		$fv->setPropertiesFromArray($row);
		$fv->populateAttributes();
		
		return $fv;
	}


}