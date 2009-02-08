<?

class File extends Object { 

	public function getPath() {
		$fv = $this->getVersion();
		return $fv->getPath();
	}
	
	public function getByID($fID) {
		Loader::model('file_set');
		$db = Loader::db();
		$f = new File();
		$row = $db->GetRow("SELECT Files.*, FileVersions.fvID,
		(fs.fsType = ?) as isStarred
		FROM Files LEFT JOIN FileVersions on Files.fID = FileVersions.fID 
		LEFT JOIN FileSetFiles fsf on Files.fID = fsf.fID
		LEFT JOIN FileSets fs on fsf.fsID = fs.fsID			
		WHERE Files.fID = ?", array(FileSet::TYPE_STARRED,$fID));
		$f->setPropertiesFromArray($row);
		
		return $f;
	}
	
	public function getDateAdded() {
		return $this->fDateAdded;
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
		
		$db = Loader::db();
		$dh = Loader::helper('date');
		$date = $dh->getLocalDateTime();
		
		$fvID = $db->GetOne("select max(fvID) from FileVersions where fID = ?", array($this->fID));
		if ($fvID > 0) {
			$fvID++;
		} else {
			$fvID = 1;
		}
		
		$db->Execute('insert into FileVersions (fID, fvID, fvFilename, fvPrefix, fvDateAdded, fvIsApproved, fvApproverUID, fvAuthorUID, fvActivateDateTime) 
		values (?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
			$this->fID, 
			$fvID,
			$filename,
			$prefix, 
			$date,
			1, 
			$uID, 
			$uID, 
			$date));
			
		$fv = $this->getVersion($fvID);
		return $fv;
	}
	
	public function getActiveVersion() {
		return $this->getVersion();
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
		return $fv;
		
	}


}