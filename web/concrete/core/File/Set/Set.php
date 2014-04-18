<?php
namespace Concrete\Core\File\Set;
use Loader;
use Events;
use User;
use File as ConcreteFile;
use \Concrete\Core\Permission\Key\FileSetKey as FileSetPermissionKey;
use \Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use \Concrete\Core\Permission\Access\Entity\GroupCombinationEntity as GroupCombinationPermissionAccessEntity;
use \Concrete\Core\Permission\Access\Entity\UserEntity as UserPermissionAccessEntity;
use PermissionKey;
use Permissions;
use PermissionAccess;

class Set implements \Concrete\Core\Permission\ObjectInterface {
	const TYPE_PRIVATE 	= 0;
	const TYPE_PUBLIC 	= 1;
	const TYPE_STARRED 	= 2;
	const TYPE_SAVED_SEARCH = 3;
	protected $fileSetFiles;

	/** 
	 * Returns an object mapping to the global file set, fsID = 0.
	 * This is really only used for permissions mapping
	 */
	 
	public static function getGlobal() {
		$fs = new static;
		$fs->fsID = 0;
		return $fs;
	}
	
	public function getFileSetUserID() {return $this->uID;}
	public function getFileSetType() {return $this->fsType;}
	
	public function getSavedSearches() {
		$db = Loader::db();
		$sets = array();
		$u = new User();
		$r = $db->Execute('select * from FileSets where fsType = ? and uID = ? order by fsName asc', array(Set::TYPE_SAVED_SEARCH, $u->getUserID()));
		while ($row = $r->FetchRow()) {
			$fs = new static();
			$fs = array_to_object($fs, $row);
			$sets[] = $fs;
		}
		return $sets;
	}

	public function getPermissionResponseClassName() {
		return '\\Concrete\\Core\\Permission\\Response\\FileSetResponse';
	}

	public function getPermissionAssignmentClassName() {
		return '\\Concrete\\Core\\Permission\\Assignment\\FileSetAssignment';	
	}
	public function getPermissionObjectKeyCategoryHandle() {
		return 'file_set';
	}

	public function getPermissionObjectIdentifier() {
		return $this->getFileSetID();
	}
	
	public function getMySets($u = false) {
		if ($u == false) {
			$u = new User();
		}
		$db = Loader::db();
		$sets = array();
		$r = $db->Execute('select * from FileSets where fsType = ? or (fsType in (?, ?) and uID = ?) order by fsName asc', array(static::TYPE_PUBLIC, static::TYPE_STARRED, static::TYPE_PRIVATE, $u->getUserID()));
		while ($row = $r->FetchRow()) {
			$fs = new static();
			$fs = array_to_object($fs, $row);
			$fsp = new Permissions($fs);
			if ($fsp->canSearchFiles()) {
				$sets[] = $fs;
			}
		}
		return $sets;
	}
	
	public function updateFileSetDisplayOrder($files) {
		$db = Loader::db();
		$db->Execute('update FileSetFiles set fsDisplayOrder = 0 where fsID = ?', $this->getFileSetID());
		$i = 0;
		if (is_array($files)) { 
			foreach($files as $fID) {
				$db->Execute('update FileSetFiles set fsDisplayOrder = ? where fsID = ? and fID = ?', array($i, $this->getFileSetID(), $fID));
				$i++;
			}
		}
	}
	
	/**
	 * Get a file set object by a file set's id
	 * @param int $fsID
	 * @return FileSet
	 */
	public static function getByID($fsID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from FileSets where fsID = ?', array($fsID));
		if (is_array($row)) {
			$fs = new static();
			$fs = array_to_object($fs, $row);
			if ($row['fsType'] == static::TYPE_SAVED_SEARCH) {
				$row2 = $db->GetRow('select fsSearchRequest, fsResultColumns from FileSetSavedSearches where fsID = ?', array($fsID));
				$fs->fsSearchRequest = @unserialize($row2['fsSearchRequest']);
				$fs->fsResultColumns = @unserialize($row2['fsResultColumns']);
			}
			return $fs;
		}
	}
	
	/**
	 * Get a file set object by a file name
	 * @param string $fsName
	 * @return FileSet
	 */
	public static function getByName($fsName) {
		$db = Loader::db();
		$row = $db->GetRow('select * from FileSets where fsName = ?', array($fsName));
		if (is_array($row) && count($row)) {
			$fs = new static();
			$fs = array_to_object($fs, $row);
			return $fs;
		}
	}			
	
	public function getFileSetID() {
		if ($this->fsID) {
			return $this->fsID;
		}
		return 0;
	}
	public function overrideGlobalPermissions() {return $this->fsOverrideGlobalPermissions;}
	
	public function getFileSetName() {return $this->fsName;}	
	
	/**
	 * Creats a new fileset if set doesn't exists
	 *
	 * If we find a multiple groups with the same properties,
	 * we return an array containing each group
	 * @param string $fs_name
	 * @param int $fs_type
	 * @param int $fs_uid
	 * @return Mixed 
	 *
	 * Dev Note: This will create duplicate sets with the same name if a set exists owned by another user!!! 
	 */		
	public static function createAndGetSet($fs_name, $fs_type, $fs_uid=false) {
		if (!$fs_uid) {
			$u = new User();
			$fs_uid = $u->uID;
		}

		$db = Loader::db();
		$criteria = array($fs_name,$fs_type,$fs_uid);
		$fsID = $db->GetOne('select fsID from FileSets where fsName=? AND fsType=? and uID=?', $criteria);
		if ($fsID > 0) {
			return static::getByID($fsID);
		} else {
			$fs = static::add($fs_name, 0, $fs_uid, $fs_type);		
			return $fs;
		}			
	}

	/**
	 * Adds a file set
	 */
	public static function add($setName, $fsOverrideGlobalPermissions = 0, $u = false, $type = Set::TYPE_PUBLIC) {
		if (is_object($u) && $u->isRegistered()) {
			$uID = $u->getUserID();
		} else if ($u) {
			$uID = $u;
		} else {
			$uID = 0;
		}

		$db = Loader::db();
		$db->insert("FileSets", array('fsType' => $type, 'fsOverrideGlobalPermissions' => $fsOverrideGlobalPermissions, 'uID' => $uID, 'fsName' => $setName));
		$fsID = $db->lastInsertId();
		$fs = static::getByID($fsID);

		$fe = new \Concrete\Core\File\Event\FileSet($fs);
		Events::dispatch('on_file_set_add', $fe);

		return $fs;

	}

	/**
	 * Updates a file set.
	 */
	public function update($setName, $fsOverrideGlobalPermissions = 0) {
		$db = Loader::db();
		$db->update('FileSets', array('fsName' => $setName, 'fsOverrideGlobalPermissions' => $fsOverrideGlobalPermissions), array('fsID' => $this->fsID));
		return static::getByID($this->fsID);
	}

	/**
	* Adds the file to the set
	* @param type $fID  //accepts an ID or a File object
	* @return object
	*/		
	public function addFileToSet($f_id) {
		if (is_object($f_id)) {
			$f_id = $f_id->getFileID();
		}			
		$file_set_file = File::createAndGetFile($f_id,$this->fsID);

		$fe = new \Concrete\Core\File\Event\FileSetFile($file_set_file);
		Events::dispatch('on_file_added_to_set', $fe);

		return $file_set_file;
	}
	
	public function getSavedSearchRequest() {
		return $this->fsSearchRequest;
	}
	
	public function getSavedSearchColumns() {
		return $this->fsResultColumns;
	}
	public function removeFileFromSet($f_id){

		if (is_object($f_id)) {
			$f_id = $f_id->fID;
		}

		$file_set_file = File::createAndGetFile($f_id,$this->fsID);

		$db = Loader::db();
		$db->Execute('DELETE FROM FileSetFiles 
		WHERE fID = ? 
		AND   fsID = ?', array($f_id, $this->getFileSetID()));

		$fe = new \Concrete\Core\File\Event\FileSetFile($file_set_file);
		Events::dispatch('on_file_removed_from_set', $fe);

	}

	/**
	* Get a list of files asociated with this set
	*
	* Can obsolete this when we get version of ADOdB with one/many support
	* @return type $var_name
	*/		
	private function populateFiles() {
		$this->fileSetFiles = File::getFileSetFiles($this);		
	}
	
	public function hasFileID($f_id){
		if (!is_array($this->fileSetFiles)) {
			$this->populateFiles();
		}			
		foreach ($this->fileSetFiles as $file) {
			if($file->fID == $f_id){
				return true;
			}
		}
	}

	/**
	 * Returns an array of File objects from the current set
	 * @return array
	 */
	public function getFiles() {
		if (!$this->fileSetFiles) { $this->populateFiles();	}
		$files = array();
		foreach ($this->fileSetFiles as $file) {
			$files[] = ConcreteFile::getByID($file->fID);
		}
		return $files;
	}

	/**
	 * Static method to return an array of File objects by the set id
	 * @param  int $fsID
	 * @return array
	 */
	public static function getFilesBySetID($fsID) {
		if (intval($fsID) > 0) {
			$fileset = self::getByID($fsID);
			if ($fileset instanceof FileSet) {
				return $fileset->getFiles();
			}
		}
	}

	/**
	 * Static method to return an array of File objects by the set name
	 * @param  string $fsName
	 * @return array
	 */
	public static function getFilesBySetName($fsName) {
		if (!empty($fsName)) {
			$fileset = self::getByName($fsName);
			if ($fileset instanceof \Concrete\Core\File\Set\Set) {
				return $fileset->getFiles();
			}
		}
	}
	
	public function delete() {
		$db = Loader::db();
		$db->delete('FileSets', array('fsID' => $this->fsID));
		$db->Execute('delete from FileSetSavedSearches where fsID = ?', array($this->fsID));
	}
	
	public function resetPermissions() {
		$db = Loader::db();
		$db->Execute('delete from FileSetPermissionAssignments where fsID = ?', array($this->fsID));
	}
	
	public function acquireBaseFileSetPermissions() {
		$this->resetPermissions();

		$db = Loader::db();

		$q = "select fsID, paID, pkID from FileSetPermissionAssignments where fsID = 0";
		$r = $db->query($q);
		while($row = $r->fetchRow()) {
			$v = array($this->fsID, $row['paID'], $row['pkID']);
			$q = "insert into FileSetPermissionAssignments (fsID, paID, pkID) values (?, ?, ?)";
			$db->query($q, $v);
		}

	}
	
	public function assignPermissions($userOrGroup, $permissions = array(), $accessType = FileSetPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		if ($this->fsID > 0) { 
			$db->Execute("update FileSets set fsOverrideGlobalPermissions = 1 where fsID = ?", array($this->fsID));
			$this->fsOverrideGlobalPermissions = true;
		}
		
		if (is_array($userOrGroup)) { 
			$pe = GroupCombinationPermissionAccessEntity::getOrCreate($userOrGroup);
			// group combination
		} else if ($userOrGroup instanceof User || $userOrGroup instanceof UserInfo) { 
			$pe = UserPermissionAccessEntity::getOrCreate($userOrGroup);
		} else { 
			// group;
			$pe = GroupPermissionAccessEntity::getOrCreate($userOrGroup);
		}
		
		foreach($permissions as $pkHandle) { 
			$pk = PermissionKey::getByHandle($pkHandle);
			$pk->setPermissionObject($this);
			$pa = $pk->getPermissionAccessObject();
			if (!is_object($pa)) {
				$pa = PermissionAccess::create($pk);
			} else if ($pa->isPermissionAccessInUse()) {
				$pa = $pa->duplicate();
			}
			$pa->addListItem($pe, false, $accessType);
			$pt = $pk->getPermissionAssignmentObject();
			$pt->assignPermissionAccess($pa);
		}
	}


}
	
