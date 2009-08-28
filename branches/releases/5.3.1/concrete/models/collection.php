<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Contains the collection object.
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A generic object that holds blocks and maps them to areas. 
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
	class Collection extends Object {
		
		var $cID;
		protected $attributes = array();
		/* version specific stuff */

		function loadVersionObject($cvID = "ACTIVE") {
			$cvID = CollectionVersion::getNumericalVersionID($this->getCollectionID(), $cvID);
			$this->vObj = CollectionVersion::get($this, $cvID);
			$vp = new Permissions($this->vObj);			
			return $vp;
		}
		
		function getVersionToModify() {
			// first, we check to see if the version we're modifying has the same
			// author uID associated with it as we currently have, and if it's inactive
			// If that's the case, then we just return the current collection + version object.

			$u = new User();
			$vObj = $this->getVersionObject();
			if ($this->isMasterCollection() || ($vObj->isNew())) {
				return $this;
			} else {
				// otherwise, we have to clone this version of the collection entirely,
				// and return that collection.

				$nc = $this->_cloneVersion($versionComments);
				return $nc;
			}
		}

		function _cloneVersion($versionComments) {
			// first, we run the version object's createNew() command, which returns a new
			// version object, which we can combine with our collection object, so we'll have
			// our original collection object ($this), and a new collection object, consisting
			// of our collection + the new version
			$vObj = $this->getVersionObject();
			$nvObj = $vObj->createNew($versionComments);
			$nc = Page::getByID($this->getCollectionID());
			$nc->vObj = $nvObj;
			// now that we have the original version object and the cloned version object,
			// we're going to select all the blocks that exist for this page, and we're going
			// to copy them to the next version
			// unless btIncludeAll is set -- as that gets included no matter what

			$db = Loader::db();
			$cID = $this->getCollectionID();
			$cvID = $vObj->getVersionID();
			$q = "select bID, arHandle from CollectionVersionBlocks where cID = '$cID' and cvID = '$cvID' and cbIncludeAll=0 order by cbDisplayOrder asc";
		//	echo $q;
			$r = $db->query($q);
			if ($r) {
				while ($row = $r->fetchRow()) {
					// now we loop through these, create block objects for all of them, and
					// duplicate them to our collection object (which is actually the same collection,
					// but different version)
					$b = Block::getByID($row['bID'], $this, $row['arHandle']);
					if (is_object($b)) {
						$b->alias($nc);
					}
				}
			}

			// now that we've duplicated all the blocks for the collection, we return the new
			// collection

			return $nc;
		}
		
		/* attribute stuff */
		
		public function getAttribute($akHandle) {
			if (is_object($this->vObj)) {
				return $this->vObj->getAttribute($akHandle);
			}
		}
		
		public function getCollectionAttributeValue($ak) {
			if (is_object($this->vObj)) {
				if (is_object($ak)) {
					return $this->vObj->getAttribute($ak->getCollectionAttributeKeyHandle());
				} else {
					return $this->vObj->getAttribute($ak);
				}
			}
		}
		
		/*
		
		function getCollectionAttributeValue($ak) {
			$db = Loader::db();
			if (is_object($ak)) {
				$v = array($this->getCollectionID(), $this->getVersionID(), $ak->getCollectionAttributeKeyID());
				$q = "select value from CollectionAttributeValues where cID = ? and cvID = ? and akID = ?";		
				$value = $db->GetOne($q, $v);
				$akType = $ak->getCollectionAttributeKeyType();
			} else if (is_string($ak)) {
				$db = Loader::db();
				$v = array($this->getCollectionID(), $this->getVersionID(), $ak);
				$q = "select cak.akType, cav.value from CollectionAttributeValues cav inner join CollectionAttributeKeys cak on cav.akID = cak.akID where cID = ? and cvID = ? and cak.akHandle = ?";
				$r = $db->getRow($q, $v);
				$value = $r['value'];
				$akType = $r['akType'];
			}
			$v = false;
			switch($akType) {
				case "IMAGE_FILE":
					if ($value > 0) {
						Loader::block('library_file');
						$v = LibraryFileBlockController::getFile($value);
					}
					break;
				default:
					$v = $value;
					break;
			}
			return $v;
		}
		
		public function getAttribute($akHandle) {
			return $this->getCollectionAttributeValue($akHandle);
		}
		*/
		
		
		public function setAttribute($akHandle, $value) {
			$db = Loader::db();
			$akID = $db->GetOne("select akID from CollectionAttributeKeys where akHandle = ?", array($akHandle));
			if ($akID > 0) {
				$db->Replace('CollectionAttributeValues', array(
					'cID' => $this->cID,
					'cvID' => $this->getVersionID(),
					'akID' => $akID,
					'value' => $value
				),
				array('cID', 'cvID', 'akID'), true);
			}
			
			$this->refreshCache();
		}
		
		// get's an array of collection attribute objects that are attached to this collection. Does not get values
		public function getSetCollectionAttributes() {
			$db = Loader::db();
			$akIDs = $db->GetCol("select akID from CollectionAttributeValues where cID = ? and cvID = ?", array($this->getCollectionID(), $this->getVersionID()));
			$attribs = array();
			foreach($akIDs as $akID) {
				$attribs[] = CollectionAttributeKey::getByID($akID);
			}
			return $attribs;
		}

		function addAttribute($ak, $value) {
			$db = Loader::db();
			$vo = $this->getVersionObject();
			$cvID = $vo->getVersionID();

			$v = array($this->getCollectionID(), $cvID, $ak->getCollectionAttributeKeyID());
			$db->query("delete from CollectionAttributeValues where cID = ? and cvID = ? and akID = ?", $v);

			$v3 = array($this->getCollectionID(), $cvID, $ak->getCollectionAttributeKeyID(), $value);
			$db->query("insert into CollectionAttributeValues (cID, cvID, akID, value) values (?, ?, ?, ?)", $v3);
			
			unset($v); unset($v3);
			
			// deal with arrays as values by storing them funky. we should probably just use serialize.
			
			if (is_array($value)) {
				$_sub = array();
				foreach($value as $sub) {
					$sub = trim($sub);
					if ($sub) {
						$_sub[] = $sub;
					}
				}
				if (count($_sub) > 0) { 
					$value = implode("[|]", $_sub);
				}
			}
			$this->refreshCache();
		}

		/* area stuff */
		
		function getArea($arHandle) {
			return Area::get($c, $arHandle);
		}

		/* aliased content */

		function hasAliasedContent() {
			$db = Loader::db();
			// aliased content is content on the particular page that is being
			// used elsewhere - but the content on the PAGE is the original version
			$v = array($this->cID);
			$q = "select bID from CollectionVersionBlocks where cID = ? and isOriginal = 1";
			$r = $db->query($q, $v);
			$bIDArray = array();
			if ($r) {
				while ($row = $r->fetchRow()) {
					$bIDArray[] = $row['bID'];
				}
				if (count($bIDArray) > 0) {
					$bIDList = implode(',', $bIDArray);
					$v2 = array($bIDList, $this->cID);
					$q2 = "select cID from CollectionVersionBlocks where bID in (?) and cID <> ? limit 1";
					$aliasedCID = $db->getOne($q2, $v2);
					if ($aliasedCID > 0) {
						return true;
					}
				}
			}
			return false;
		}


		/* basic CRUD */
		
		function getCollectionID() {
			return $this->cID;
		}

		function getCollectionDateLastModified($mask = null) {
			if ($mask == null) {
				return $this->cDateModified;
			} else {
				return date($mask, strtotime($this->cDateModified));
			}
		}


		function getVersionObject() {
			return $this->vObj;
		}

		function getCollectionHandle() {
			return $this->cHandle;
		}

		function getCollectionDateAdded($mask = null) {
			if ($mask == null) {
				return $this->cDateAdded;
			} else {
				return date($mask, strtotime($this->cDateAdded));
			}
		}

		function getVersionID() {
			// shortcut
			return $this->vObj->cvID;
		}

	function getCollectionAreaDisplayOrder($arHandle, $ignoreVersions = false) {
		// this function queries CollectionBlocks to grab the highest displayOrder value, then increments it, and returns
		// this is used to add new blocks to existing Pages/areas

		$db = Loader::db();
		$cID = $this->cID;
		$cvID = $this->vObj->cvID;
		if ($ignoreVersions) {
			$q = "select max(cbDisplayOrder) as cbdis from CollectionVersionBlocks where cID = '$cID' and arHandle='$arHandle'";
		} else {
			$q = "select max(cbDisplayOrder) as cbdis from CollectionVersionBlocks where cID = '$cID' and cvID = '{$cvID}' and arHandle='$arHandle'";
		}
		$r = $db->query($q);
		if ($r) {
			if ($r->numRows() > 0) {
				// then we know we got a value; we increment it and return
				$res = $r->fetchRow();
				$displayOrder = $res['cbdis'];
				if (is_null($displayOrder)) {
					return 0;
				}
				$displayOrder++;
				return $displayOrder;
			} else {
				// we didn't get anything, so we return a zero
				return 0;
			}
		}
	}

	function rescanDisplayOrder($areaName) {
		// this collection function fixes the display order properties for all the blocks within the collection/area. We select all the items
		// order by display order, and fix the sequence

		$db = Loader::db();
		$cID = $this->cID;
		$cvID = $this->vObj->cvID;
		$q = "select bID from CollectionVersionBlocks where cID = '$cID' and cvID = '{$cvID}' and arHandle='$arHandle' order by cbDisplayOrder asc";
		$r = $db->query($q);

		if ($r) {
			$displayOrder = 0;
			while ($row = $r->fetchRow()) {
				$q = "update CollectionVersionBlocks set cbDisplayOrder = '$displayOrder' where cID = '$cID' and cvID = '{$cvID}' and arHandle = '$arHandle' and bID = '{$row['bID']}'";
				$r2 = $db->query($q);
				$displayOrder++;
			}
			$r->free();
		}
	}
	

		/* new cleaned up API below */

		public static function getByID($cID, $version = 'RECENT') {
			$db = Loader::db(); 
			$q = "select Collections.cDateAdded, Collections.cDateModified, Collections.cID from Collections where cID = ?";
			$row = $db->getRow($q, array($cID));
			
			$c = new Collection;
			$c->setPropertiesFromArray($row);
			
			if ($version != false) {
				// we don't do this on the front page
				$c->loadVersionObject($version);
			}			

			return $c;
		}	
		
		/* This function is slightly misnamed: it should be getOrCreateByHandle($handle) but I wanted to keep it brief */
		
		public static function getByHandle($handle) {
			$db = Loader::db();
			
			// first we ensure that this does NOT appear in the Pages table. This is not a page. It is more basic than that 
			
			$r = $db->query("select Collections.cID, Pages.cID as pcID from Collections left join Pages on Collections.cID = Pages.cID where Collections.cHandle = ?", array($handle));
			if ($r->numRows() == 0) {

				// there is nothing in the collections table for this page, so we create and grab

				$data['handle'] = $handle;
				$cObj = Collection::add($data);

			} else {
				$row = $r->fetchRow();
				if ($row['cID'] > 0 && $row['pcID'] == null) {			

					// there is a collection, but it is not a page. so we grab it
					$cObj = Collection::getByID($row['cID']);
					
				}
			}
			
			if (isset($cObj)) {
				return $cObj;
			}
			
		}
		
		public function refreshCache() {
			$vo = $this->getVersionObject();
			Cache::delete('page', $this->getCollectionID());
			Cache::delete('page_path', $this->getCollectionID());
			if (is_object($vo)) {
				$vo->refreshCache();
			}
		}
		
		public function getBlocks($arHandle = false) {
			
			$db = Loader::db();
			
			$v = array($this->getCollectionID(), $this->getVersionID());
			if ($arHandle != false) {
				$v[] = $arHandle;
			}
			$q = "select Blocks.bID, CollectionVersionBlocks.arHandle ";
			$q .= "from CollectionVersionBlocks inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID) inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where CollectionVersionBlocks.cID = ? and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) ";
			if ($arHandle != false) {
				$q .= 'and CollectionVersionBlocks.arHandle = ? ';
			}
			$q .= "order by CollectionVersionBlocks.cbDisplayOrder asc";

			$r = $db->query($q, $v);
			$blocks = array();
			while ($row = $r->fetchRow()) {
				$ab = Block::getByID($row['bID'], $this, $row['arHandle']);
				if (is_object($ab)) {
					$blocks[] = $ab;
				}
			}
			$r->free();
			return $blocks;
		}
			
		public function addBlock($bt, $a, $data) {
			$db = Loader::db();
			
			// first we add the block to the system
			$nb = $bt->add($data, $this, $a);
			
			// now that we have a block, we add it to the collectionversions table
			
			$arHandle = (is_object($a)) ? $a->getAreaHandle() : $a;
			$cID = $this->getCollectionID();
			$vObj = $this->getVersionObject();
	
			if ($bt->includeAll()) {
				// normally, display order is dependant on a per area, per version basis. However, since this block
				// is not aliased across versions, then we want to get display order simply based on area, NOT based 
				// on area + version
				$newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder($arHandle, true); // second argument is "ignoreVersions"
			} else {
				$newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder($arHandle);
			}

			$v = array($cID, $vObj->getVersionID(), $nb->getBlockID(), $arHandle, $newBlockDisplayOrder, 1, $bt->includeAll());
			$q = "insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbIncludeAll) values (?, ?, ?, ?, ?, ?, ?)";

			$res = $db->Execute($q, $v);
			
			return Block::getByID($nb->getBlockID(), $this, $a);
		}
		
		public function add($data) {
			$db = Loader::db();
			$dh = Loader::helper('date');
			$cDate = $dh->getLocalDateTime(); 
			$cDatePublic = ($data['cDatePublic']) ? $data['cDatePublic'] : $cDate;
			
			if (isset($data['cID'])) {
				$res = $db->query("insert into Collections (cID, cHandle, cDateAdded, cDateModified) values (?, ?, ?, ?)", array($data['cID'], $data['handle'], $cDate, $cDate));
				$newCID = $data['cID'];
			} else {
				$res = $db->query("insert into Collections (cHandle, cDateAdded, cDateModified) values (?, ?, ?)", array($data['handle'], $cDate, $cDate));
				$newCID = $db->Insert_ID();
			}
			
			$cvIsApproved = (isset($data['cvIsApproved']) && $data['cvIsApproved'] == 0) ? 0 : 1;
			
			if ($res) {
				// now we add a pending version to the collectionversions table
				$v2 = array($newCID, 1, $data['name'], $data['handle'], $data['cDescription'], $cDatePublic, $cDate, VERSION_INITIAL_COMMENT, $data['uID'], $cvIsApproved);
				$q2 = "insert into CollectionVersions (cID, cvID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsApproved) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$r2 = $db->prepare($q2);
				$res2 = $db->execute($r2, $v2);
			}
			
			$nc = Collection::getByID($newCID);
			return $nc;
		}
		
		public function markModified() {
			// marks this collection as newly modified
			$db = Loader::db();
			$dh = Loader::helper('date');
			$cDateModified = $dh->getLocalDateTime();

			$v = array($cDateModified, $this->cID);
			$q = "update Collections set cDateModified = ? where cID = ?";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
		}
		
		public function delete() {
			if ($this->cID > 0) {
				$db = Loader::db();
	
				// First we delete all versions
				$vl = new VersionList($this);
				$vlArray = $vl->getVersionListArray();
		
				foreach($vlArray as $v) {
					$v->delete();
				}
		
				$cID = $this->getCollectionID();
		
				$q = "delete from CollectionAttributeValues where cID = {$cID}";
				$db->query($q);

				$q = "delete from Collections where cID = '{$cID}'";
				$r = $db->query($q);
			}
		}
		
		public function duplicate() {
			$db = Loader::db();
			$dh = Loader::helper('date');
			$cDate = $dh->getLocalDateTime();
			
			$v = array($cDate, $cDate, $this->cHandle);
			$r = $db->query("insert into Collections (cDateAdded, cDateModified, cHandle) values (?, ?, ?)", $v);
			$newCID = $db->Insert_ID();
			
			if ($r) {

				// first, we get the creation date of the active version in this collection
				//$q = "select cvDateCreated from CollectionVersions where cvIsApproved = 1 and cID = {$this->cID}";
				//$dcOriginal = $db->getOne($q);
				// now we create the query that will grab the versions we're going to copy
				
				$qv = "select * from CollectionVersions where cID = '{$this->cID}' order by cvDateCreated asc";
	
				// now we grab all of the current versions
				$rv = $db->query($qv);
				$cvList = array();
				while ($row = $rv->fetchRow()) {
					// insert
					$cvList[] = $row['cvID'];
					$cDate = date("Y-m-d H:i:s", strtotime($cDate) + 1);
					$vv = array($newCID, $row['cvID'], $row['cvName'], $row['cvHandle'], $row['cvDescription'], $row['cvDatePublic'], $cDate, $row['cvComments'], $row['cvAuthorUID'], $row['cvIsApproved']);
					$qv = "insert into CollectionVersions (cID, cvID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsApproved) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$db->query($qv, $vv);
				}
	
				// now we grab all the blocks we're going to need
				$cvList = implode(',',$cvList);
				$q = "select bID, cvID, arHandle, cbDisplayOrder, cbOverrideAreaPermissions, cbIncludeAll from CollectionVersionBlocks where cID = '{$this->cID}' and cvID in ({$cvList})";
				$r = $db->query($q);
				while ($row = $r->fetchRow()) {
					$v = array($newCID, $row['cvID'], $row['bID'], $row['arHandle'], $row['cbDisplayOrder'], 0, $row['cbOverrideAreaPermissions'], $row['cbIncludeAll']);
					$q = "insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions, cbIncludeAll) values (?, ?, ?, ?, ?, ?, ?, ?)";
					$db->query($q, $v);
					if ($row['cbOverrideAreaPermissions'] != 0) {
						$q2 = "select gID, uID, cbgPermissions from CollectionVersionBlockPermissions where cID = '{$this->cID}' and bID = '{$row['bID']}' and cvID = '{$row['cvID']}'";
						$r2 = $db->query($q2);
						while ($row2 = $r2->fetchRow()) {
							$v3 = array($newCID, $row['cvID'], $row['bID'], $row2['gID'], $row2['uID'], $row2['cbgPermissions']);
							$q3 = "insert into CollectionVersionBlockPermissions (cID, cvID, bID, gID, uID, cbgPermissions) values (?, ?, ?, ?, ?, ?)";
							$db->query($q3, $v3);
						}
					}
				}
	
				// duplicate any attributes belonging to the collection
				
				$v = array($this->getCollectionID());
				$q = "select akID, cvID, value from CollectionAttributeValues where cID = ?";
				$r = $db->query($q, $v);
				while ($row = $r->fetchRow()) {
					$v2 = array($row['akID'], $row['cvID'], $row['value'], $newCID);
					$db->query("insert into CollectionAttributeValues (akID, cvID, value, cID) values (?, ?, ?, ?)", $v2);
				}			
				return Collection::getByID($newCID);
			}
			
		}
		

	}

?>