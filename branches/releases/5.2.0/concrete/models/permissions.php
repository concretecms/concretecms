<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

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
			$id = $obj->getCollectionID();
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
		if (is_a($unknownObj, 'Page')) {
			$po = PermissionsProxy::getNewOrCached($unknownObj, 'CollectionPermissions');
		} else if (is_a($unknownObj, 'Block')) {
			$aObj = $unknownObj->getBlockAreaObject();
			if (!$unknownObj->overrideAreaPermissions()) {
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
	var $permissionSet;

	/**
	*
	* @access private
	*
	*/
	var $permError;

	/**
	*
	* @access private
	*
	*/
	var $originalObj;

	/**
	*
	* @access private
	*
	*/
	var $addCollectionTypes = array();

	/**
	*
	* @access private
	*
	*/
	var $addBlockTypes = array();
	
	/**
	*
	* oUID is the object (be it collection, block, etc...) uID - for example, if a block
	* is created by uID 3, the oUID = 3. We use this when calculating write permissions
	* @access private
	*
	*/
	var $oUID;
	
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
	
	function canAdminBlock() {
		$oObj = $this->getOriginalObject();
		$c = (is_a($oObj, 'Area')) ? $oObj->getAreaCollectionObject() : $oObj->getBlockCollectionObject();
		$c->loadVersionObject('RECENT');		
		$cp = new Permissions($c);
		return $cp->canAdminPage();
	}

	function canAdminPage() {
		return strpos($this->permissionSet, 'adm') > -1;
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
	var $addCTIDs = array();
	
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
				
				$db = Loader::db();
				$q = "select ctID from PageTypes";
				$r = $db->querycache(10, $q);
				while($row = $r->fetchRow()) {
					$this->addCollectionTypes[] = $row['ctID'];
				}
				
				$db = Loader::db();
				$q = "select btID from BlockTypes where btIsInternal = 0";
				$r = $db->querycache(10, $q);
				while($row = $r->fetchRow()) {
					$this->addBlockTypes[] = $row['btID'];
				}
			} else {
				// a bunch of database group permission stuff
				$this->permissionSet = $this->setGroupAccess($cObj, $u);
				if ((!$this->canRead())&& (!$cObj->getCollectionPointerExternalLink() != '')) {
					$this->permError = COLLECTION_FORBIDDEN;
				}
			}
		}
		
		if ($cObj->isMasterCollection()) {
			if (!$adm || ($_SESSION['mcEditID'] != $cObj->getCollectionID())) {
				$this->permError = COLLECTION_FORBIDDEN;
			}
		}

		return $this;
	}

	function setGroupAccess(&$cObj, &$u) {
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
		
		$_uID = ($u->getUserID() > -1) ? " or uID = " . $u->getUserID() : "";
		$_cID = $cObj->getPermissionsCollectionID();
	
		$q = "select cgPermissions, cgStartDate, cgEndDate, gID from PagePermissions where cID = '{$_cID}' and (gID in $inStr $_uID)";

		$r = $db->query($q);
		$groupSetAdditional = false;
		$canWriteToPage = false;
		$permissions = array();
		if ($r) {
			while ($row = $r->fetchRow()) {
				$dh = Loader::helper('date');
				$time = strtotime($dh->getLocalDateTime());
				if ((!$row['cgStartDate'] && !$row['cgEndDate']) || ($row['cgStartDate'] && !$row['cgEndDate'] && $time >= strtotime($row['cgStartDate'])) 
					 || (!$row['cgStartDate'] && $row['cgEndDate'] && $time <= strtotime($row['cgEndDate']))
					 || ($row['cgStartDate'] && $row['cgEndDate'] && $time >= strtotime($row['cgStartDate']) && $time <= strtotime($row['cgEndDate']))) {
						$permissions[] = $row['cgPermissions'];
						if (strpos($row['cgPermissions'], 'wa') !== false && (!$canWriteToPage)) {
							$canWriteToPage = true; // once this is set it can't be unset
						}
						
						if ($row['gID'] != GUEST_GROUP_ID && $row['gID'] != REGISTERED_GROUP_ID) {
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
				// we allow all pages to be added as subpages
				$q = "select ctID from PageTypes";
				$r = $db->querycache(10, $q);
				while($row = $r->fetchRow()) {
					$this->addCollectionTypes[] = $row['ctID'];
				}
				
				// we add delete block to the permission set, since for some reason it's a separate permissions call than delete collection (which we should've already added)
				$permissions[] = "db";
			}
			$q = "select btID from BlockTypes where btIsInternal = 0";
				$r = $db->querycache(10, $q);
			while($row = $r->fetchRow()) {
				$this->addBlockTypes[] = $row['btID'];
			}
			// the block types directive above may be overridden by area-specific permissions
		}
		
		$perms = $this->mergePermissions($permissions);
		
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
	var $addBTIDs = array();
	
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
			
			$db = Loader::db();
			$q = "select btID from BlockTypes where btIsInternal = 0";
			$r = $db->querycache(10, $q);
			while($row = $r->fetchRow()) {
				$this->addBlockTypes[] = $row['btID'];
			}
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
			$this->permissionSet = ($cv->isMostRecent()) ? 'r:wa:db' : 'r';
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