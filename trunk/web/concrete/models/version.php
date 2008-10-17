<?
	defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * Contains the collection version object.
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that maps to versions of collections. Each page in concrete is a _collection_ of blocks, each of which has different versions (for version control.)
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Version extends Object {
	
		var $vcID;
		var $cvIsApproved;
		var $cObj;
		
		function Version(&$c, $cvID = "ACTIVE", $extended = false) {

			$db = Loader::db();
			
			$q = "select cvID, cvIsApproved, cvIsNew, cvHandle, cvName, cvDescription, cvDateCreated, cvDatePublic, cvAuthorUID, cvApproverUID, cvComments from CollectionVersions where ";			
			$cID = $c->getCollectionID();			
			if (is_numeric($cvID)) {
				//$this->cvCanWrite = true;
				// instead of getting the active version, we're getting a specific version that's been passed
				$q .= "cID = '{$cID}' and cvID = '{$cvID}'";
			} else if ($cvID == "RECENT") {
				//$this->cvCanWrite = true;
				// we're getting the most recent
				$q .= "cID = '{$cID}' order by cvID desc limit 1";
			} else {
				// we get whatever's active
				$q .= "cID = '{$cID}' and cvIsApproved = 1";
			}

			$r = $db->query($q);
			if ($r) {
				$row = $r->fetchRow();					
				if ($row) {
					foreach ($row as $key => $value) {
						$this->{$key} = $value;
					}
				}
			}
			
			if ($extended) {
				if ($this->cvAuthorUID > 0) {
					$uAuthor = UserInfo::getByID($this->cvAuthorUID);
					$this->cvAuthorUname = $uAuthor->getUserName();
				}
				if ($this->cvApproverUID > 0) {
					$uApprover = UserInfo::getByID($this->cvApproverUID);
					$this->cvApproverUname = $uApprover->getUserName();
				}
			}
			
			$this->cObj = &$c;			
			$this->cvIsMostRecent = $this->_checkRecent();
			
			return $this;
		}
		
		function isApproved() {return $this->cvIsApproved;}
		function isMostRecent() {return $this->cvIsMostRecent;}
		function isNew() {return $this->cvIsNew;}
		function getVersionID() {return $this->cvID;}
		function getVersionName() {return $this->cvName;}	
		function getVersionComments() {return $this->cvComments;}
		function getVersionAuthorUserID() {return $this->cvAuthorUID;}
		function getVersionApproverUserID() {return $this->cvApproverUID;}
		function getVersionAuthorUserName() {return $this->cvAuthorUname;}
		function getVersionApproverUserName() {return $this->cvApproverUname;}
		function getVersionDateCreated() {return $this->cvDateCreated;}
		
		function canWrite() {return $this->cvCanWrite;}
		
		function setComment($comment) {
			$c = $this->cObj;
			$thisCVID = $this->getVersionID();
			$comment = ($comment != null) ? $comment : "Version {$thisCVID}";
			$v = array($comment, $thisCVID, $c->getCollectionID());
			$db = Loader::db();
			$q = "update CollectionVersions set cvComments = ? where cvID = ? and cID = ?";
			$r = $db->query($q, $v);
			
			$this->versionComments = $comment;
		}
		
		function createNew($versionComments) {
			$db = Loader::db();
			$newVID = $this->getVersionID() + 1;
			$c = $this->cObj;

			$u = new User();
			$versionComments = (!$versionComments) ? "New Version {$newVID}" : $versionComments;
			
			$dh = Loader::helper('date');
			$v = array($c->getCollectionID(), $newVID, $c->getCollectionName(), $c->getCollectionHandle(), $c->getCollectionDescription(), $c->getCollectionDatePublic(), $dh->getLocalDateTime(), $versionComments, $u->getUserID(), 1);
			$q = "insert into CollectionVersions (cID, cvID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsNew)
				values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				
			$q2 = "select akID, value from CollectionAttributeValues where cID = ? and cvID = ?";
			$v2 = array($c->getCollectionID(), $this->getVersionID());
			$r2 = $db->query($q2, $v2);
			while ($row2 = $r2->fetchRow()) {
				$v3 = array($c->getCollectionID(), $newVID, $row2['akID'], $row2['value']);
				$db->query("insert into CollectionAttributeValues (cID, cvID, akID, value) values (?, ?, ?, ?)", $v3);
				
			}
			
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
			
			$nv = new Version($c, $newVID);
			// now we return it
			return $nv;
		}
		
		function _checkRecent() {
			// basically checks to see if this version is the most recent version. You're not allowed to edit
			// versions that are not the most recent.
			
			$c = $this->cObj;
			$cID = $c->getCollectionID();
			
			$db = Loader::db();
			$q = "select cvID from CollectionVersions where cID = '{$cID}' order by cvID desc";
			$cvID = $db->getOne($q);
			return ($cvID == $this->cvID);
		}
		
		function approve() {
			$db = Loader::db();
			$u = new User();
			$uID = $u->getUserID();
			$cvID = $this->cvID;
			$c = $this->cObj;
			$cID = $c->getCollectionID();
			
			// first we remove approval for all versions of this collection
			$v = array($cID);
			$q = "update CollectionVersions set cvIsApproved = 0 where cID = ?";
			$r = $db->query($q, $v);
			
			// now we approve our version
			$v2 = array($uID, $cID, $cvID);
			$q2 = "update CollectionVersions set cvIsNew = 0, cvIsApproved = 1, cvApproverUID = ? where cID = ? and cvID = ?";
			$r = $db->query($q2, $v2);
			
			// next, we rescan our collection paths for the particular collection, but only if this isn't a generated collection
			if (!$c->isGeneratedCollection()) {
				$c->rescanCollectionPath();
			}
		}
		
		public function discard() {
			// discard's my most recent edit that is pending
			$u = new User();
			if ($this->isNew()) {
				$this->delete();
			}
		}
		
		public function removeNewStatus() {
			$db = Loader::db();
			$db->query("update CollectionVersions set cvIsNew = 0 where cID = ? and cvID = ?", array($this->cObj->getCollectionID(), $this->cvID));
		}
		
		function deny() {
			$db = Loader::db();
			$cvID = $this->cvID;
			$c = $this->cObj;
			$cID = $c->getCollectionID();
			
			// first we remove approval for all versions of this collection
			$v = array($cID);
			$q = "update CollectionVersions set cvIsApproved = 0 where cID = ?";
			$r = $db->query($q, $v);
			
			// now we deny our version
			$v2 = array($cID, $cvID);
			$q2 = "update CollectionVersions set cvIsApproved = 0, cvApproverUID = 0 where cID = ? and cvID = ?";
			$r2 = $db->query($q2, $v2);
		}
		
		function delete() {
			$db = Loader::db();
			
			$cvID = $this->cvID;
			$c = $this->cObj;
			$c->vObj = $this; // slightly recursive;
			$cID = $c->getCollectionID();
			
			$q = "select bID, arHandle from CollectionVersionBlocks where cID = '{$cID}' and cvID='{$cvID}'";
			$r = $db->query($q);
			if ($r) {
				while ($row = $r->fetchRow()) {
					if ($row['bID']) {
						$b = Block::getByID($row['bID'], $c, $row['arHandle']);
						$b->deleteBlock();
					}
					unset($b);
				}
			}
			
			$q = "delete from CollectionAttributeValues where cID = '{$cID}' and cvID = '{$cvID}'";
			$r = $db->query($q);
			
			$q = "delete from CollectionVersions where cID = '{$cID}' and cvID='{$cvID}'";
			$r = $db->query($q);
		}
	}

/**
 * An object that holds a list of versions for a particular collection.
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class VersionList extends Object {
	
		var $vArray = array();
		
		function VersionList(&$c) {
			$db = Loader::db();
			
			$cID = $c->getCollectionID();		
			$q = "select cvID from CollectionVersions where cID = '$cID' order by cvID desc";
			$r = $db->query($q);
	
			if ($r) {
				while ($row = $r->fetchRow()) {
					$this->vArray[] = new Version($c, $row['cvID'], true);
				}
				$r->free();
			}
					
			return $this;
		}
		
		function getVersionListArray() {
			return $this->vArray;
		}
		
		function getVersionListCount() {
			return count($this->vArray);
		}
	
	}
	
?>