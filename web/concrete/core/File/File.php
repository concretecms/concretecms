<?
namespace Concrete\Core\File;
use Carbon\Carbon;
use FileSet;
use Loader;
use CacheLocal;
use User;
use Events;
use Page;
use Database;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Attribute\Key as AttributeKey;
use \Concrete\Core\File\StorageLocation\StorageLocation;
use FileAttributeKey;
use PermissionKey;

/**
 * @Entity
 * @Table(name="Files")
 */

class File implements \Concrete\Core\Permission\ObjectInterface {

    /**
     * Create a new version every 300 seconds.
     */
    const CREATE_NEW_VERSION_THRESHOLD = 300;


    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $fID;

    /**
     * @Column(type="datetime")
     */
    protected $fDateAdded = null;

    /**
     * @Column(type="string")
     */
    protected $fPassword;

    /**
     * @Column(type="boolean")
     */
    protected $fOverrideSetPermissions = false;

    /**
     * Originally placed on which page.
     * @Column(columnDefinition="integer unsigned")
     */
    protected $ocID = 0;

    /**
     * @Column(columnDefinition="integer unsigned")
     */
    protected $uID = 0;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\File\StorageLocation\StorageLocation")
     * @JoinColumn(name="fslID", referencedColumnName="fslID")
     **/
    protected $storageLocation;


    /**
	 * returns a file object for the given file ID
	 * @param int $fID
	 * @return File
	 */
    public static function getByID($fID)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->find('\Concrete\Core\File\File', $fID);
    }

	/** 
	 * For all methods that file does not implement, we pass through to the currently active file version object 
	 */
	public function __call($nm, $a) {
		$fv = $this->getApprovedVersion();
		return call_user_func_array(array($fv, $nm), $a);
	}

	public function getPermissionResponseClassName() {
		return '\\Concrete\\Core\\Permission\\Response\\FileResponse';
	}

	public function getPermissionAssignmentClassName() {
		return '\\Concrete\\Core\\Permission\\Assignment\\FileAssignment';	
	}
	public function getPermissionObjectKeyCategoryHandle() {
		return 'file';
	}

	public function getPermissionObjectIdentifier() {
		return $this->getFileID();
	}

    public function getPassword() {
		return $this->fPassword;
	}
	
	public function getStorageLocationID() {
		return $this->locationn->getID();
	}

    /**
     * @return \Concrete\Core\File\StorageLocation\StorageLocation
     */
    public function getFileStorageLocationObject()
    {
        return $this->storageLocation;
    }

	public function reindex() {

		return;
		
		$attribs = FileAttributeKey::getAttributes($this->getFileID(), $this->getFileVersionID(), 'getSearchIndexValue');
		$db = Loader::db();

		$db->Execute('delete from FileSearchIndexAttributes where fID = ?', array($this->getFileID()));
		$searchableAttributes = array('fID' => $this->getFileID());
		$rs = $db->Execute('select * from FileSearchIndexAttributes where fID = -1');
		AttributeKey::reindex('FileSearchIndexAttributes', $searchableAttributes, $attribs, $rs);
	}

	public static function getRelativePathFromID($fID) {
		$path = CacheLocal::getEntry('file_relative_path', $fID);
		if ($path != false) {
			return $path;
		}
		
		$f = static::getByID($fID);
		$path = $f->getRelativePath();
		
		CacheLocal::set('file_relative_path', $fID, $path);
		return $path;
	}

    protected function save()
    {
        $em = Database::get()->getEntityManager();
        $em->persist($this)->flush();
    }

	public function setFileStorageLocation(StorageLocation $newLocation)
    {
        $fh = Loader::helper('concrete/file');
        $currentLocation = $this->getFileStorageLocationObject();
        $currentFilesystem = $currentLocation->getFileSystemObject();

        $newFileSystem = $newLocation->getFileSystemObject();

        $list = $this->getVersionList();
        foreach($list as $fv) {
            $contents = $fv->getFileContents();
            $newFileSystem->put($fh->prefix($fv->getPrefix(), $fv->getFilename()), $contents);
            $currentFilesystem->delete($fh->prefix($fv->getPrefix(), $fv->getFilename()));
        }

        $this->location = $newLocation;
        $this->save();
	}

	public function setPassword($pw) {

		$fe = new \Concrete\Core\File\Event\FileWithPassword($this);
		$fe->setFilePassword($pw);
		Events::dispatch('on_file_set_password', $fe);

        $this->fPassword = $pw;
        $this->save();
	}
	
	public function setOriginalPage($ocID) {
		if ($ocID < 1) {
			return false;
		}
        $this->ocID = $ocID;
        $this->save();
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
        $this->fOverrideSetPermissions = (bool) $fOverrideSetPermissions;
        $this->save();
		if ($fOverrideSetPermissions) {
			$permissions = PermissionKey::getList('file');
			foreach($permissions as $pk) { 
				$pk->setPermissionObject($this);
				$pk->copyFromFileSetToFile();
			}	
		}
	}
	

	public function getUserID() {
		return $this->uID;
	}
	
	public function setUserID($uID) {
		$this->uID = $uID;
        $this->save();
	}
	
	public function getFileSets() {
		$db = Loader::db();
		$fsIDs = $db->Execute("select fsID from FileSetFiles where fID = ?", array($this->getFileID()));
		$filesets = array();
		while ($row = $fsIDs->FetchRow()) {
			$filesets[] = FileSet::getByID($row['fsID']);
		}
		return $filesets;
	}
	
	public function isStarred($u = false) {
		if (!$u) {
			$u = new User();
		}
		$db = Loader::db();
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

        $em = Database::get()->getEntityManager();
        $nf = $em->copy($this);
        $nf->fDateAdded = new Carbon($date);
        $em->flush();

        $fIDNew = $nf->getFileID();

		$versions = $db->GetAll('select * from FileVersions where fID = ?', $this->fID);
		foreach($versions as $fileversion) {
			$fileversion['fID'] = $fIDNew;
			$fileversion['fvActivateDatetime'] = $date;
			$fileversion['fvDateAdded'] = $date;
			$r2 = $db->insert('FileVersions', $fileversion);
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
		$nf = static::getByID($fIDNew);

		$fe = new \Concrete\Core\File\Event\DuplicateFile($this);
		$fe->setNewFileObject($nf);
		Events::dispatch('on_file_duplicate', $fe);

		return $nf;		
	}
	
	public static function add($filename, $prefix, $data = array(), $fsl = false) {
		$db = Loader::db();
		$dh = Loader::helper('date');
		$date = $dh->getSystemDateTime(); 

        if (!is_object($fsl)) {
            $fsl = StorageLocation::getDefault();
        }

		$uID = 0;
		$u = new User();
		if (isset($data['uID'])) {
			$uID = $data['uID'];
		} else if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}

        $f = new self;
        $f->uID = $uID;
        $f->storageLocation = $fsl;
        $f->fDateAdded = new Carbon($date);
        $em = $db->getEntityManager();
        $em->persist($f);
        $em->flush();

		$fv = $f->addVersion($filename, $prefix, $data);

		$fve = new \Concrete\Core\File\Event\FileVersion($fv);
		Events::dispatch('on_file_add', $fve);
		
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
		$fvTags = (isset($data['fvTags'])) ? Version::cleanTags($data['fvTags']) : '';
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

		$fve = new \Concrete\Core\File\Event\FileVersion($fv);
		Events::dispatch('on_file_version_add', $fve);

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
		
		// fire an on_page_delete event
		$fve = new \Concrete\Core\File\Event\DeleteFile($this);
		$fve = Events::dispatch('on_file_delete', $fve);
		if (!$fve->proceed()) {
			return false;
		}

        $versions = $this->getVersionList();
        foreach($versions as $fv) {
            $fv->delete();
        }

		// now from the DB
        $em = $db->getEntityManager();
        $em->remove($this);
		$db->Execute("delete from FileSetFiles where fID = ?", array($this->fID));
		$db->Execute("delete from FileSearchIndexAttributes where fID = ?", array($this->fID));
		$db->Execute("delete from DownloadStatistics where fID = ?", array($this->fID));
		$db->Execute("delete from FilePermissionAssignments where fID = ?", array($this->fID));		
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
	 * @return Version
	 */
	public function getVersion($fvID = null) {

		if ($fvID == null) {
			$fvID = $this->fvID; // approved version
		}
		$fv = CacheLocal::getEntry('file', $this->getFileID() . ':' . $fvID);
		if ($fv === -1) {
			return false;
		}
		if ($fv) {
			return $fv;
		}

		$db = Loader::db();
		$row = $db->GetRow("select * from FileVersions where fvID = ? and fID = ?", array($fvID, $this->fID));
		$row['fvAuthorName'] = $db->GetOne("select uName from Users where uID = ?", array($row['fvAuthorUID']));

        $fv = false;
        if ($row['fvID']) {
            $fv = new Version();
            $row['fslID'] = $this->fslID;
            $fv->setPropertiesFromArray($row);
        }

		CacheLocal::set('file', $this->getFileID() . ':' . $fvID, $fv);
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
			$limitString = 'limit ' . intval($limit);
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

		$fve = new \Concrete\Core\File\Event\FileAccess($fv);
		Events::dispatch('on_file_download', $fve);

		$db = Loader::db();
		$db->Execute('insert into DownloadStatistics (fID, fvID, uID, rcID) values (?, ?, ?, ?)',  array( $this->fID, intval($fvID), $uID, $rcID ) );		
	}
}
