<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* @access private
*
*/
class PermissionsCache {

	static $permCache = array();
	
	function exists($obj) {
		$identifier = PermissionsCache::getIdentifier($obj);
		return (isset(PermissionsCache::$permCache[$identifier]));
	}
	
	function getIdentifier($obj) {
		if (is_a($obj, "Page")) {
			$id = $obj->getPermissionsCollectionID();
			$prefix = 'page';
		} else if (is_a($obj, "Block")) {
			$id = $obj->getBlockID();
			$prefix = 'block';
		} else if (is_a($obj, "CollectionVersion")) {
			$id = $obj->getVersionID();
			$prefix = 'collection_version';
		} else if (is_a($obj, "Area")) {
			$id = $obj->getAreaID();
			$prefix = 'area';
		} else if (is_a($obj, 'FileSet')) {
			$id = $obj->getFileSetID();
			$prefix = 'fileset';
		} else if (is_a($obj, 'File')) {
			$id = $obj->getFileID();
			$prefix = 'file';
		}
		
		$identifier = $prefix . ':' . $id;
		return $identifier;
	}
	
	function getObject($obj) {
		$identifier = PermissionsCache::getIdentifier($obj);
		return PermissionsCache::$permCache[$identifier];
	}
	
	function add($originalObj, $obj) {
		
		// I don't know if globals is the best way to do this or not, but it seems better than hitting the database a million times
		$identifier = PermissionsCache::getIdentifier($originalObj);
		PermissionsCache::$permCache[$identifier] = $obj;

	}

}

/**
*
* @access private
*
*/
class PermissionsProxy {
	/* this class basically just gets a permissions object by checking what type of object this is */
	/* we have this class because Iwant to cache the permissions objects for performance reasons, and we need a centralized place to check that */
	
	private static function getNewOrCached($unknownObj, $class) {
		if (PermissionsCache::exists($unknownObj)) {
			$po = PermissionsCache::getObject($unknownObj);
		} else {
			$po = new $class($unknownObj);
		}
		
		return $po;
	}
	
	private static function getAreaPermissions($unknownObj) {
		// this is split out because it's so f'ing complicated
		$cObj = $unknownObj->getAreaCollectionObject();
		if ($unknownObj->overrideCollectionPermissions()) {
			$po = PermissionsProxy::getNewOrCached($unknownObj, 'AreaPermissions');			
		} else {
			if ($unknownObj->getAreaCollectionInheritID() > 0) {
				$areac = Page::getByID($unknownObj->getAreaCollectionInheritID());
				$inheritArea = Area::get($areac, $unknownObj->getAreaHandle());
				if ($inheritArea->overrideCollectionPermissions()) {
					$po = PermissionsProxy::getNewOrCached($inheritArea, 'AreaPermissions');			
				}
			}
		}
		
		if (!isset($po)) {						
			// otherwise we grab the collection permissions for this page
			$po = PermissionsProxy::getNewOrCached($cObj, 'CollectionPermissions');
		}
		
		return $po;
	}
	
	public function get($unknownObj) {
		if (is_a($unknownObj, 'File')) {
			if (!$unknownObj->overrideFileSetPermissions()) {				
				$po = PermissionsProxy::getNewOrCached($unknownObj, 'FileSetPermissions');
			} else {
				$po = PermissionsProxy::getNewOrCached($unknownObj, 'FilePermissions');
			}		
		} else if (is_a($unknownObj, 'FileSet')) {
			if ($unknownObj->overrideGlobalPermissions()) {
				$po = PermissionsProxy::getNewOrCached($unknownObj, 'FileSetPermissions');
			} else {
				$gs = FileSet::getGlobal();
				$po = PermissionsProxy::getNewOrCached($gs, 'FileSetPermissions');
			}
		} else if (is_a($unknownObj, 'Page')) {
			$po = PermissionsProxy::getNewOrCached($unknownObj, 'CollectionPermissions');
		} else if (is_a($unknownObj, 'Block')) {
			$aObj = $unknownObj->getBlockAreaObject();
			if (!is_object($aObj)) {
				$cObj = $unknownObj->getBlockCollectionObject();
				$po = PermissionsProxy::getNewOrCached($cObj, 'CollectionPermissions');
			} else if (!$unknownObj->overrideAreaPermissions()) {
				$po = PermissionsProxy::getAreaPermissions($aObj);
			} else {
				$po = PermissionsProxy::getNewOrCached($unknownObj, 'BlockPermissions');
			}
		} else if (is_a($unknownObj, 'CollectionVersion')) {
			$po = new VersionPermissions($unknownObj);
		} else if (is_a($unknownObj, 'Area')) {
			$po = PermissionsProxy::getAreaPermissions($unknownObj);
		} 
		
		return $po;
		
	}

}

/**
*
* The permissions object is checked to see if a logged-in user has access to a bit of content.
* @package Permissions
*
*/
class Permissions extends Object {
	
	/** 
	*
	* @access private
	*
	*/
	public $permissionSet;

	/**
	*
	* @access private
	*
	*/
	public $permError;

	/**
	*
	* @access private
	*
	*/
	public $originalObj;

	/**
	*
	* @access private
	*
	*/
	public $addCollectionTypes = array();

	/**
	*
	* @access private
	*
	*/
	public $addBlockTypes = array();
	
	/**
	*
	* oUID is the object (be it collection, block, etc...) uID - for example, if a block
	* is created by uID 3, the oUID = 3. We use this when calculating write permissions
	* @access private
	*
	*/
	public $oUID;
	
	/**
	*
	* The constructor for the permissions object, a Collection, Version, Area or Block object is passed
	* and a special sub-object is returned, which can then be checked against to see the if the user can 
	* view it, edit it, etc...
	* @param mixed $unknownObj
	*
	*/
	function Permissions(&$unknownObj) {
		// The permissions object is given another, and it basically checks to see if the logged-in
		// user has given permissions for that object, whatever it may be
		
		$this->u = new User();
		
		$this->originalObj = $unknownObj;
		/*	
		if ($unknownObj->getError()) {
			// inherit the error from the object
			$this->permError = $unknownObj->getError();
		}*/
		
		if (PermissionsCache::exists($unknownObj)) {
			$po = PermissionsCache::getObject($unknownObj);
			$this->loadPermissionSet($po);
			return;
		}
		
		$po = PermissionsProxy::get($unknownObj);
		$this->loadPermissionSet($po);
		
		PermissionsCache::add($unknownObj, $po);
	}
	
	function getError() {
		return $this->error;
	}
	
	function isError() {
		return $this->error != '';
	}
	
	function getOriginalObject() {
		return $this->originalObj;
	}
	
	public function populateAllPageTypes() {
		$ca = new Cache();
		$db = Loader::db();
		$q = "select ctID from PageTypes";
		$r = $db->query($q);
		while($row = $r->fetchRow()) {
			$this->addCollectionTypes[] = $row['ctID'];
		}
	}
	
	public function populateAllBlockTypes() {
		$ca = new Cache();
		$blockTypes = $ca->get('blockTypeList', false);
		if (is_array($blockTypes)) {
			$this->addBlockTypes = $blockTypes;
		} else {
			$db = Loader::db();
			$q = "select btID from BlockTypes";
			$r = $db->query($q);
			while($row = $r->fetchRow()) {
				$this->addBlockTypes[] = $row['btID'];
			}
			$ca->set('blockTypeList', false, $this->addBlockTypes);
		}
	}
	
	function mergePermissions($permissions) {
		// given an array of different permission sets, we merge them (additive) into one set, and return
		// first we concatenate all the permissions together
		$i = 0;
		foreach ($permissions as $value) {
			$permissionSet .= ($i != 0) ? ':' : '';
			$permissionSet .= $value;
			$i++;
		}
		
		// then we explode the whole string
		$permissionSetArray = @explode(':', $permissionSet);

		// now we remove the duplicate values
		$permissionSetUnique = array_unique($permissionSetArray);
		
		// now we re-output to a string
		$i = 0;
		$permissionSet = '';
		foreach ($permissionSetUnique as $value) {
			$permissionSet .= ($i != 0) ? ':' : '';
			$permissionSet .= $value;
			$i++;
		}
		
		return $permissionSet;
	}
	
	function canAddSubContent() {
		return (count($this->addCollectionTypes) > 0);
	}
	
	function canAddSubCollection($ct) {
		$ctID = $ct->getCollectionTypeID();
		return (in_array($ctID, $this->addCollectionTypes));
	}
	
	function canRead() {
		return strpos($this->permissionSet, 'r') > -1;
	}
	
	function canReadVersions() {
		return strpos($this->permissionSet, 'rv') > -1;
	}
		
	function canWrite() {
		return strpos($this->permissionSet, 'wa') > -1;
	}
	
	function disableWrite() {
		// strip out all write permissions
		$this->permissionSet = str_replace(array('wa:', 'db:'), '', $this->permissionSet);
		$this->permissionSet = str_replace(array('wa','db'), '', $this->permissionSet);
	}
		
	function canDeleteBlock() {
		return strpos($this->permissionSet, 'db') > -1;
	}
	
	function canDeleteCollection() {
		return strpos($this->permissionSet, 'dc') > -1;
	}
	
	function canApproveCollection() {
		if (PERMISSIONS_MODEL != 'simple') {
			return strpos($this->permissionSet, 'av') > -1;
		} else {
			return strpos($this->permissionSet, 'wa') > -1;
		}
	}
	
	function canAddBlocks() {
		return count($this->addBlockTypes) > 0;
	}
	
	function getAddBlockTypes() {
		return $this->addBlockTypes;
	}
	
	function canAddBlock($bt) {
		$btID = $bt->getBlockTypeID();
		return (in_array($btID, $this->addBlockTypes));
	}

	public function canAddStack($stack) {
		$blocks = $stack->getBlocks();
		foreach($blocks as $b) {
			if (!in_array($b->getBlockTypeID(), $this->addBlockTypes)) {
				return false;
			}
		}		
		return true;
	}
	function canAddFiles() {
		return count($this->permissions['canAddFileTypes']) > 0;
	}
	
	function canAddFileType($ext) {
		$ext = strtolower($ext);
		return (in_array($ext, $this->permissions['canAddFileTypes']));
	}
	
	function canAddFile($f) {
		return ($this->canAddFileType($f->getExtension()));
	}
	
	function canAdminBlock() {
		$oObj = $this->getOriginalObject();
		$c = (is_a($oObj, 'Area')) ? $oObj->getAreaCollectionObject() : $oObj->getBlockCollectionObject();
		$c->loadVersionObject('RECENT');		
		$cp = new Permissions($c);
		return $cp->canAdminPage();
	}

	function canAdminPage() {
		return $this->canAdmin();
	}
	
	function canAdmin() {
		return strpos($this->permissionSet, 'adm') > -1;
	}
	
	
	public function canAccessFileManager() {
		return $this->permissions['canSearch'];
	}
	
	/** 
	 * Hack until there is a better way
	 */
	public function canDeleteFileSet() {
		$fs = $this->originalObj;
		$u = new User();
		if ($fs->getFileSetType() == FileSet::TYPE_PRIVATE && $fs->getFileSetUserID() == $u->getUserID()) {
			return true;
		}
		$c = Page::getByPath('/dashboard/files/sets');
		if (is_object($c) && !$c->isError()) {
			$cp = new Permissions($c);
			return $cp->canRead();
		}
	}

	public function getFileSearchLevel() {
		return $this->permissions['canSearch'];
	}
	
	public function canSearchFiles() {
		return $this->permissions['canSearch'];
	}
	public function getFileReadLevel() {
		return $this->permissions['canRead'];
	}
	public function getFileWriteLevel() {
		return $this->permissions['canWrite'];
	}
	public function getFileAdminLevel() {
		return $this->permissions['canAdmin'];
	}
	public function getFileAddLevel() {
		return $this->permissions['canAdd'];
	}
	public function getAllowedFileExtensions() {
		return $this->permissions['canAddFileTypes'];
	}
	
	function buildPermissionsFromArray($ar) {
		$str = '';
		//print_r($ar);
		if ($ar['canRead'] == 1) {
			$str .= 'r:';
		}
		if ($ar['canWrite'] == 1) {
			$str .= 'wa:';
		}
		if ($ar['canApproveVersions'] == 1) {
			$str .= 'av:';
		}
		if ($ar['canReadVersions'] == 1) {
			$str .= 'rv:';
		}
		if ($ar['canDelete'] == 1) {
			$str .= 'dc:';
		}
		if ($ar['canAdmin'] == 1) {
			$str .= 'adm:';
		}
		
		
		if ($str != '') {
			// chop off the trailing colon
			$str = substr($str, 0, strlen($str) -1);
		}
		
		return $str;
	}
	
	function loadPermissionSet($pObj) {
		$this->permissionSet = $pObj->permissionSet;
		$this->permissions = $pObj->permissions;
		$this->oUID = $pObj->oUID;
		$this->addBlockTypes = $pObj->addBlockTypes;
		$this->addCollectionTypes = $pObj->addCollectionTypes;
		if ($pObj->permError) {
			$this->loadError($pObj->permError);
		}
	}
}

/**
*
* A specific permissions object for a collection (page)
* @package Permissions
*
*/
class CollectionPermissions extends Permissions {
	public $addCTIDs = array();
	
	function CollectionPermissions(&$cObj) {
		$u = new User();

		$this->originalObj = &$cObj;
		$this->oUID = $cObj->uID;
		
		$adm = $u->isSuperUser();
		if ($cObj->getError() == COLLECTION_INIT) {
			// The collection object in question represents the first page of a site not yet created
			// basically this is only important because it means the admin user cannot add any pages
			// to this collection. He/she may only edit the existing first page - can't add pages to
			// a collection that doesn't really exist
			
			if ($adm) {
				//$this->permissionSet = 'r:wa:ab:av:cp';
				$this->permissionSet = 'r:rv:wa:ab:av:adm'; // cp is redundant - we just check to see if is superuser
			} else {
				$this->permError = COLLECTION_FORBIDDEN;
			}
		} else {
			if ($adm) {
				/*
				$cv = $cObj->getVersionObject();
				if (is_object($cv)) {
					$this->permissionSet = ($cv->isMostRecent()) ? 'r:rv:wa:av:cp:dc:adm:db' : 'r:rv';
				} else {
					$this->permissionSet = 'r:rv:wa:av:cp:dc:db:adm';
				}
				*/

				$this->permissionSet = 'r:rv:wa:av:cp:dc:db:adm';
				$this->populateAllPageTypes();
				$this->populateAllBlockTypes();

			} else {
				// a bunch of database group permission stuff
				$this->permissionSet = $this->setGroupAccess($cObj, $u);
				if ((!$this->canRead())&& (!$cObj->getCollectionPointerExternalLink() != '')) {
					$this->permError = COLLECTION_FORBIDDEN;
				}
			}
		}
		
		if ($cObj->isMasterCollection()) {
			$canEditMaster = TaskPermission::getByHandle('access_page_defaults')->can();
			if ($canEditMaster && $_SESSION['mcEditID'] == $cObj->getCollectionID()) {
				$this->permissionSet = 'r:rv:wa:av:cp:dc:db:adm';
				$this->permError = false;
			} else {
				$this->permError = COLLECTION_FORBIDDEN;
			}
		}

		return $this;
	}

	function setGroupAccess(&$cObj, &$u) {

		if (!$u->isRegistered()) {
			$perms = Cache::get('page_permission_set_guest', $cObj->getCollectionID());
		}
		
		if ($perms == false) {
			$db = Loader::db();
			$groups = $u->getUserGroups();
			
			// now we get collection type permissions for all the groups that this user is in
			
			$inStr = '(';
			$i = 0;
			foreach ($groups as $key => $value) {
				$inStr .= ($i != 0) ? ', ' : '';
				$inStr .= $key;
				$i++;
			}
			$inStr .= ')';
			
			$_uID = ($u->getUserID() > 0) ? " or uID = " . $u->getUserID() : "";
			$_cID = $cObj->getPermissionsCollectionID();
		
			$q = "select cgPermissions, cgStartDate, cgEndDate, gID from PagePermissions where cID = '{$_cID}' and (gID in $inStr $_uID)";
	
			$r = $db->query($q);
			$groupSetAdditional = false;
			$canWriteToPage = false;
			$permissions = array();
			if ($r) {
				while ($row = $r->fetchRow()) {
					$dh = Loader::helper('date');
					$time = strtotime($dh->getSystemDateTime());
					if ((!$row['cgStartDate'] && !$row['cgEndDate']) || ($row['cgStartDate'] && !$row['cgEndDate'] && $time >= strtotime($row['cgStartDate'])) 
						 || (!$row['cgStartDate'] && $row['cgEndDate'] && $time <= strtotime($row['cgEndDate']))
						 || ($row['cgStartDate'] && $row['cgEndDate'] && $time >= strtotime($row['cgStartDate']) && $time <= strtotime($row['cgEndDate']))) {
							$permissions[] = $row['cgPermissions'];
							if (strpos($row['cgPermissions'], 'wa') !== false && (!$canWriteToPage)) {
								$canWriteToPage = true; // once this is set it can't be unset
							}
							
							//if ($row['gID'] != GUEST_GROUP_ID && $row['gID'] != REGISTERED_GROUP_ID) {
							if ($row['gID'] != GUEST_GROUP_ID) {
								$groupSetAdditional = true;
								if (PERMISSIONS_MODEL != 'simple') {
									$q2 = "select ctID from PagePermissionPageTypes where cID = '{$_cID}' and (gID in $inStr $_uID)";
									$r2 = $db->query($q2);
									while($row2 = $r2->fetchRow()) {
										$this->addCollectionTypes[] = $row2['ctID'];
									}
					
								}
							}
					}
				}
				$r->free();
			}
			
			if ($cObj->isExternalLink()) {
				// then whether the person can delete/write to this page ACTUALLY dependent on whether the PARENT collection
				// is writable
				$cParentCollection = Page::getByID($cObj->getCollectionParentID(), "RECENT");
				$cp2 = new Permissions($cParentCollection);
				if ($cp2->canWrite()) {
					$permissions[] = 'dc:wa';
				}
			}
			
			if ($canWriteToPage) {
				if (PERMISSIONS_MODEL == 'simple') {
					$this->populateAllPageTypes();
					// we add delete block to the permission set, since for some reason it's a separate permissions call than delete collection (which we should've already added)
					$permissions[] = "db";
				}
				$this->populateAllBlockTypes();
				// the block types directive above may be overridden by area-specific permissions
			}
			
			$perms = $this->mergePermissions($permissions);
			if (!$u->isRegistered()) {
				Cache::set('page_permission_set_guest', $cObj->getCollectionID(), $perms);
			}
		}
		
		$cv = $cObj->getVersionObject();
		if (is_object($cv)) {
			// if it's not the most recent, the only thing the user could do would be read
			if (!$cv->isMostRecent()) {
				$this->disableWrite();
				return $perms;
			} else {
				return $perms;
			}
		} else {
			return $perms;
		}
	}
}

/**
*
* A specific permissions object belonging to an area.
* @package Permissions
*
*/
class AreaPermissions extends Permissions {
	public $addBTIDs = array();
	
	function AreaPermissions($aObj) {
		$u = new User();

		$nc = $aObj->getAreaCollectionObject();
		$cv = $nc->getVersionObject();
		$this->originalObj = &$aObj;
		
		$adm = $u->isSuperUser();
		
		if ($adm) {
			if (is_object($cv)) {
				$this->permissionSet = ($cv->isMostRecent()) ? 'r:rb:wa:db' : 'r';
			} else {
				$this->permissionSet = 'r:rb:wa:db';
			}
			
			$this->populateAllBlockTypes();
		} else {
			$this->permissionSet = $this->setGroupAccess($aObj, $u);
		}
		
		return $this;
	}
	
	function setGroupAccess(&$aObj, &$u) {
		$db = Loader::db();
		$groups = $u->getUserGroups();
		$groupIDs = array();
		foreach($groups as $key => $value) {
			$groupIDs[] = $key;
		}
		$nc = $aObj->getAreaCollectionObject();
		$_cp = new Permissions($nc);
		
		// now we get collection type permissions for all the groups that this user is in
		
		$inStr = '(' . implode(', ', $groupIDs) . ')';
		$_uID = ($u->getUserID() > -1) ? " or uID = " . $u->getUserID() : "";
		
		$v = array($aObj->getCollectionID(), $aObj->getAreaHandle());
		$q = "select agPermissions from AreaGroups where cID = ? and arHandle = ? and (gID in $inStr $_uID)";
		$r = $db->query($q, $v);
		
		$permissions = array();
		if ($r) {
			while ($row = $r->fetchRow()) {
				$permissions[] = $row['agPermissions'];
			}
		}
		
		$r->free();
		
		$q2 = "select btID from AreaGroupBlockTypes where cID = ? and arHandle = ? and (gID in $inStr $_uID)";
		$r2 = $db->query($q2, $v);
		while($row2 = $r2->fetchRow()) {
			$this->addBlockTypes[] = $row2['btID'];
		}

		$r2->free();

		$perms = $this->mergePermissions($permissions);
		$cv = $nc->getVersionObject();
		
		if (is_object($cv)) {
			// if it's not the most recent, the only thing the user could do would be read
			if (!$cv->isMostRecent()) {
				$this->disableWrite();
				return $perms;
			} else {
				return $perms;
			}
		} else {
			return $perms;
		}
	}
}

/**
*
* A specific permissions object belonging to a block (which is only ever used in the advanced permissions model).
* @package Permissions
*
*/
class BlockPermissions extends Permissions {

	function BlockPermissions(&$bObj) {
		$u = new User();
		
		$this->oUID = $bObj->uID;
		$nc = $bObj->getBlockCollectionObject();
		$cv = $nc->getVersionObject();
		
		$adm = $u->isSuperUser();
		
		if ($adm) {
			$this->permissionSet = ($cv->isMostRecent()) ? 'r:wa:db:adm' : 'r';
		} else {
			$this->permissionSet = $this->setGroupAccess($bObj, $u, $cv);
		}
		
		return $this;
	}
	
	function setGroupAccess(&$bObj, &$u, &$cv) {
		$db = Loader::db();
		$groups = $u->getUserGroups();

		// now we get permissions for this block for all the groups that this user is in
		
		$inStr = '(';
		$i = 0;
		foreach ($groups as $key => $value) {
			$inStr .= ($i != 0) ? ', ' : '';
			$inStr .= $key;
			$i++;
		}
		$inStr .= ')';
		
		$cID = $bObj->getBlockCollectionID();
		$bID = $bObj->getBlockID();
		$cvID = $cv->getVersionID();
		$_uID = ($u->getUserID() > -1) ? " or uID = " . $u->getUserID() : "";
		
		$q = "select cbgPermissions from CollectionVersionBlockPermissions where cID = '$cID' and bID = '$bID' and cvID = '$cvID' and (gID in $inStr $_uID)";
		$r = $db->query($q);
		
		$permissions = array();
		if ($r) {
			while ($row = $r->fetchRow()) {
				$permissions[] = $row['cbgPermissions'];
			}
			$r->free();
		}
		
		$perms =  $this->mergePermissions($permissions);
		// if it's not the most recent, the only thing the user could do would be read
		if (!$cv->isMostRecent() && strpos($perms, 'r') > -1) {
			return 'r';
		} else {
			return $perms;
		}
	}

}

/**
*
* A specific permissions object belonging to a version of a collection.
* @package Permissions
*
*/
class VersionPermissions extends Permissions {

	function VersionPermissions(&$vObj) {
		if (!$vObj->getVersionID()) {
			$this->permError = ($vObj->canWrite()) ? COLLECTION_NOT_FOUND : COLLECTION_FORBIDDEN;
		} else if (!$vObj->isMostRecent()) {
			$this->permError = VERSION_NOT_RECENT;
		}

		return $this;
	}

}


/** 
 * Permissions for file sets
 */
class FileSetPermissions extends Permissions {

	public function __construct($fObj) {
		// This object can either be a file set, or a file
		// if it's a file, we grab permissions from all sets associated with that file		
		
		$u = new User();
		
		$adm = $u->isSuperUser();
		
		if ($adm) {
			$this->permissions['canSearch'] = FilePermissions::PTYPE_ALL;
			$this->permissions['canRead'] = FilePermissions::PTYPE_ALL;
			$this->permissions['canWrite'] = FilePermissions::PTYPE_ALL;
			$this->permissions['canAdmin'] = FilePermissions::PTYPE_ALL;
			$this->permissions['canAdd'] = FilePermissions::PTYPE_ALL;
		} else {
			$this->permissions = $this->setGroupAccess($fObj, $u);
		}

		if ($this->permissions['canRead'] == FilePermissions::PTYPE_ALL || (is_a($fObj, 'File') && $fObj->getUserID() == $u->getUserID() && $this->permissions['canRead'] == FilePermissions::PTYPE_MINE)) {
			$this->permissionSet .= 'r:';
		}
		if ($this->permissions['canWrite'] == FilePermissions::PTYPE_ALL || (is_a($fObj, 'File') && $fObj->getUserID() == $u->getUserID() && $this->permissions['canWrite'] == FilePermissions::PTYPE_MINE)) {
			$this->permissionSet .= 'wa:';
		}
		if ($this->permissions['canAdmin'] == FilePermissions::PTYPE_ALL || (is_a($fObj, 'File') && $fObj->getUserID() == $u->getUserID() && $this->permissions['canAdmin'] == FilePermissions::PTYPE_MINE)) {
			$this->permissionSet .= 'adm:';
		}
		
		if ($this->permissions['canAdd'] == FilePermissions::PTYPE_ALL) {
			$ch = Loader::helper('concrete/file');
			
			$this->permissions['canAddFileTypes'] = $ch->getAllowedFileExtensions();		
		}
		
	}
	
	/** 
	 * Returns an array of file set IDs that override the global, with the relevant permission set.
	 */
	public function getOverriddenSets($pcolumn = 'canRead', $ptype = FilePermissions::PTYPE_ALL) {
		$db = Loader::db();
		$u = new User();

		$groups = $u->getUserGroups();
		$inStr = '(' . implode(',', array_keys($groups)) . ')';
		$_uID = ($u->getUserID() > -1) ? " or FileSetPermissions.uID = " . $u->getUserID() : "";
		
		$q = "select max({$pcolumn}) as {$pcolumn}, FileSets.fsID from FileSetPermissions inner join FileSets on (FileSets.fsID = FileSetPermissions.fsID) where (gID in $inStr $_uID) and fsOverrideGlobalPermissions = 1 group by fsID";
		$r = $db->query($q);
		$sets = array();
		while($row = $r->fetchRow()) {
			if ($row[$pcolumn] == $ptype) {
				$sets[] = $row['fsID'];
			}
		}
		return $sets;		
	}
	
	function setGroupAccess($fs, $u) {
		
		
		$db = Loader::db();
		$groups = $u->getUserGroups();
		$inStr = '(' . implode(',', array_keys($groups)) . ')';

		if (is_a($fs, 'FileSet')) {
			$fsIDStr = "fsID = " .  $fs->getFileSetID();
		} else if (is_a($fs, 'File')) {
			$f = $fs->getFile();
			$sets = $f->getFileSets();
			
			// we only include sets in this list that are setup to override the global permissions
			$setIDs = array();
			foreach($sets as $fs) {
				if ($fs->overrideGlobalPermissions()) {
					$setIDs[] = $fs->getFileSetID();
				}
			}
			
			if (count($setIDs) == 0) {
				$setIDs[] = 0; // global file set
			}
			
			$fsIDStr = 'fsID in (' . implode(',', $setIDs) . ')';
		}
		$_uID = ($u->getUserID() > -1) ? " or uID = " . $u->getUserID() : "";
		
		$q = "select max(canAdmin) as canAdmin, max(canSearch) as canSearch, max(canRead) as canRead, max(canWrite) as canWrite, max(canAdd) as canAdd from FileSetPermissions where {$fsIDStr} and (gID in $inStr $_uID)";
		$p = $db->GetRow($q);
		
		if ($p['canAdd'] == FilePermissions::PTYPE_CUSTOM) {
			$q = "select extension from FilePermissionFileTypes where {$fsIDStr} and (gID in $inStr $_uID)";
			$p['canAddFileTypes'] = $db->GetCol($q);
		}
		
		return $p;
		
	}
	
}

/** 
 * A specific permissions object belonging to files 
 * @package Permissions
 */
class FilePermissions extends Permissions {

	const PTYPE_NONE = 0;
	const PTYPE_MINE = 3;
	const PTYPE_ALL = 10;
	const PTYPE_CUSTOM = 7;
	
	public function __construct($f = null) {
		
		if ($f == null) {
			return false;
		}
		
		$u = new User();
		
		$adm = $u->isSuperUser();
		
		if ($adm) {
			$this->permissions['canRead'] = FilePermissions::PTYPE_ALL;
			$this->permissions['canWrite'] = FilePermissions::PTYPE_ALL;
			$this->permissions['canAdmin'] = FilePermissions::PTYPE_ALL;
		} else {
			$db = Loader::db();
			$groups = $u->getUserGroups();
			
			$inStr = '(' . implode(',', array_keys($groups)) . ')';
			$_uID = ($u->getUserID() > -1) ? " or uID = " . $u->getUserID() : "";
			$fID = $f->getFileID();
			$p = $db->GetRow("select max(canAdmin) as canAdmin, max(canRead) as canRead, max(canSearch) as canSearch, max(canWrite) as canWrite from FilePermissions where fID = {$fID} and (gID in $inStr $_uID)");
			$this->permissions = $p;
		}
	
		if ($this->permissions['canRead'] == FilePermissions::PTYPE_ALL) {
			$this->permissionSet .= 'r:';
		}
		if ($this->permissions['canSearch'] == FilePermissions::PTYPE_ALL) {
			$this->permissionSet .= 'sch:';
		}
		if ($this->permissions['canWrite'] == FilePermissions::PTYPE_ALL) {
			$this->permissionSet .= 'wa:';
		}
		if ($this->permissions['canAdmin'] == FilePermissions::PTYPE_ALL) {
			$this->permissionSet .= 'adm:';
		}
	}
	
	public static function getGlobal() {
		Loader::model('file_set');
		$fs = FileSet::getGlobal();			
		$fsp = new Permissions($fs);
		return $fsp;
	}
	

}