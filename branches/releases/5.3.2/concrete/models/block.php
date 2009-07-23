<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Contains the block object, which is an atomic unit of content on a Concrete page.
 * @package Blocks
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
/**
*
* A block object is a generic bit of content added to a collection. All blocks of any type share certain bits of metadata
* and the block object takes care of setting these.
* @author Andrew Embler <andrew@concrete5.org>
* @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
* @license    http://www.concrete5.org/license/     MIT License
* @package Blocks
* @category Concrete
*/	
	class Block extends Object {

		var $cID;
		var $arHandle;
		var $c;

		// isEditable doesn't necessarily mean by everyone, just means that a form exists
		// in the filesystem to handle an edit state
		var $isEditable = true;

		public static function populateManually($blockInfo, $c, $a) {
			$b = new Block;
			$b->setPropertiesFromArray($blockInfo);

			if (is_object($a)) {
				$b->a = $a;
				$b->arHandle = $a->getAreaHandle();
			} else if ($a != null) {
				$b->arHandle = $a; // passing the area name. We only pass the object when we're adding from the front-end
			}

			$b->cID = $c->getCollectionID();
			$b->c = $c;
			
			return $b;
		}
		
		public static function getByID($bID, $c = null, $a = null) {
			if ($c == null && $a == null) {
				$cID = 0;
				$arHandle = "";
				$cvID = 0;
			} else {
				if (is_object($a)) {
					$arHandle = $a->getAreaHandle();
				} else if ($a != null) {
					$arHandle = $a;
					$a = Area::getOrCreate($c, $a);
				}
				$cID = $c->getCollectionID();
				$cvID = $c->getVersionID();
			}

			$ca = new Cache(); 
			
			$b = $ca->get('block', $bID . ':' . $cID . ':' . $cvID . ':' . $arHandle);
			if ($b instanceof Block) {
				return $b;
			}
			$db = Loader::db();

			$b = new Block;
			if ($c == null && $a == null) {
				// just grab really specific block stuff
				$q = "select bID, bIsActive, BlockTypes.btID, BlockTypes.btHandle, BlockTypes.pkgID, BlockTypes.btName, bName, bDateAdded, bDateModified, bFilename, Blocks.uID from Blocks inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where bID = ?";
				$b->isOriginal = 1;
				$v = array($bID);				
			} else {

				$b->arHandle = $arHandle;
				$b->a = $a;
				$b->cID = $cID;
				$b->c = ($c) ? $c : '';

				$vo = $c->getVersionObject();
				$cvID = $vo->getVersionID();

				$v = array($b->arHandle, $cID, $cvID, $bID);
				$q = "select CollectionVersionBlocks.isOriginal, BlockTypes.pkgID, CollectionVersionBlocks.cbOverrideAreaPermissions, CollectionVersionBlocks.cbDisplayOrder,
				Blocks.bIsActive, Blocks.bID, Blocks.btID, bName, bDateAdded, bDateModified, bFilename, btHandle, Blocks.uID from CollectionVersionBlocks inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID)
				inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where CollectionVersionBlocks.arHandle = ? and CollectionVersionBlocks.cID = ? and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) and CollectionVersionBlocks.bID = ?";
			
			}

			$r = $db->query($q, $v);
			$row = $r->fetchRow();
			
			if (is_array($row)) {
				$b->setPropertiesFromArray($row);
				$r->free();
				
				$bt = BlockType::getByID($b->getBlockTypeID());
				$class = $bt->getBlockTypeClass();
				if ($class == false) {
					// we can't find the class file, so we return
					return false;
				}
				
				$b->instance = new $class($b);
				
				
				if ($c != null || $a != null) {
					$ca = new Cache();
					$ca->set('block', $bID . ':' . $cID . ':' . $cvID . ':' . $arHandle, $b);
				}
				return $b;				

			}
		}
		

		/** 
		 * Returns a global block 
		 */
		public static function getByName($globalBlockName) {
			if(!$globalBlockName) return;
			$db = Loader::db();
			$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
			$globalScrapbookPage=$scrapbookHelper->getGlobalScrapbookPage();
			$bID = $db->getOne( 'SELECT b.bID FROM Blocks AS b, CollectionVersionBlocks AS cvb '.
							  'WHERE b.bName=? AND b.bID=cvb.bID AND cvb.cID=?', 
							   array($globalBlockName, intval($globalScrapbookPage->getCollectionId()) ) ); 
			if ($bID > 0) {
				return Block::getByID( intval($bID) );
			} else {
				return new Block();
			}
		}
		
		public function display( $view = 'view', $args = array()){
			if ($this->getBlockTypeID() < 1) {
				return ;
			}
			
			$bv = new BlockView();
			$bt = BlockType::getByID( $this->getBlockTypeID() );  
			$bv->render($this, $view, $args);
		}

		// if $c is provided, then we check to see if this particular block is aliased
		// to this particular collection
		public function isAlias($c = null) {
			if ($c) {
				$db = Loader::db();
				$cID = $c->getCollectionID();
				$q = "select bID from CollectionVersionBlocks where bID = '{$this->bID}' and cID='{$cID}' and isOriginal = 0";
				$r = $db->query($q);
				if ($r) {
					return ($r->numRows() > 0);
				}
			} else {
				return (!$this->isOriginal);
			}
		}
		
		public function isGlobal(){
			$db = Loader::db();
			
			$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
			$globalScrapbookC = $scrapbookHelper->getGlobalScrapbookPage(); 
			$q = "SELECT b.bID FROM Blocks AS b, CollectionVersionBlocks AS cvb ".
				 "WHERE b.bID = '{$this->bID}' AND cvb.bID=b.bID AND cvb.cID=".intval($globalScrapbookC->getCollectionId())." LIMIT 1";
				 
			$r = $db->query($q);
			if ($r) {
				return ($r->numRows() > 0)?1:0;
			}			
			return 0;
		}

		public function inc($file) {
			$b = $this;
			if (file_exists($this->getBlockPath() . '/' . $file)) {
				include($this->getBlockPath() . '/' . $file);
			}
		}
		/*
		 * Returns a path to where the block type's files are located.
		 * @access public
		 * @return string $path
		 */
		 
		public function getBlockPath() {
			if ($this->getPackageID() > 0) {
				$pkgHandle = $this->getPackageHandle();
				$dirp = (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) ? DIR_PACKAGES : DIR_PACKAGES_CORE;
				$dir = $dirp . '/' . $pkgHandle . '/' . DIRNAME_BLOCKS . '/' . $this->getBlockTypeHandle();
			} else {
				if (is_dir(DIR_FILES_BLOCK_TYPES . '/' . $this->getBlockTypeHandle())) {
					$dir = DIR_FILES_BLOCK_TYPES . '/' . $this->getBlockTypeHandle();
				} else {
					$dir = DIR_FILES_BLOCK_TYPES_CORE . '/' . $this->getBlockTypeHandle();
				}
			}
			return $dir;	
		}

		function loadNewCollection(&$c) {
			$this->c = $c;
		}

		function setBlockAreaObject(&$a) {
			$this->a = $a;
			$this->arHandle = $a->getAreaHandle();
		}

		function getBlockAreaObject() {
			if (is_object($this->a)) {
				return $this->a;
			}
		}

		function getOriginalCollection() {
			// given a block ID, we find the original collection ID (where this bID is marked as isOriginal)
			$db = Loader::db();
			$q = "select Pages.cID, cIsTemplate from Pages inner join CollectionVersionBlocks on (CollectionVersionBlocks.cID = Pages.cID) where CollectionVersionBlocks.bID = '{$this->bID}' and CollectionVersionBlocks.isOriginal = 1";
			$r = $db->query($q);
			if ($r) {
				$row = $r->fetchRow();
				$cID = $row['cID'];
				$nc = Page::getByID($cID, "ACTIVE");
				return $nc;
			}
		}

		function getNumChildren() {
			$db = Loader::db();
			$q = "select count(*) as total from CollectionVersionBlocks where bID = '{$this->bID}' and isOriginal = 0";
			$total = $db->getOne($q);
			return $total;
		}

		function passThruBlock($method) {
			// pass this onto the blocktype's class
			
			$method = "action_" . $method;
			
			$btID = $this->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$class = $bt->getBlockTypeClass();
			$bc = new $class($this);
			
			// ONLY ALLOWS ITEMS THAT START WITH "action_";
			
			return @call_user_func_array(array($bc, $method), array());
		}
		
		public function getInstance() {
			return $this->instance;
		}

		function getCollectionList() {
			// gets a list of collections that include this block, along with area name, etc...
			// used in the block_details.php page in the admin control panel
			$db = Loader::db();
			$q = "select DISTINCT Pages.cID from CollectionVersionBlocks inner join Pages on (CollectionVersionBlocks.cID = Pages.cID) inner join PageTypes on (Pages.ctID = PageTypes.ctID) inner join CollectionVersions on (CollectionVersions.cID = Pages.cID) where CollectionVersionBlocks.bID = '{$this->bID}'";
			$r = $db->query($q);
			$cArray = array();
			if ($r) {
				while ($row = $r->fetchRow()) {
					$cArray[] = Page::getByID($row['cID'], 'RECENT');
				}
				$r->free();
				return $cArray;
			}
		}

		function update($data) {
			// this function updates fields common to every block

			$db = Loader::db();
			$dh = Loader::helper('date');
			$bDateModified = $dh->getLocalDateTime();
			$bID = $this->bID;

			$v = array($bDateModified, $bID);
			$q = "update Blocks set bDateModified = ? where bID = ?";

			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
			
			$btID = $this->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$class = $bt->getBlockTypeClass();
			$bc = new $class($this);
			$bc->save($data);
			
			$this->refreshCache();
		}

		function isActive() {
			return $this->bIsActive;
		}

		function deactivate() {
			$db = Loader::db();
			$q = "update Blocks set bIsActive = 0 where bID = '{$this->bID}'";
			$db->query($q);
			$this->refreshCache();
		}

		function activate() {
			$db = Loader::db();
			$q = "update Blocks set bIsActive = 1 where bID = '{$this->bID}'";
			$db->query($q);
			$this->refreshCache();
		}

		public function getPackageID() {return $this->pkgID;}
		public function getPackageHandle() {
			return PackageList::getHandle($this->pkgID);
		}
		
		function updateBlockName( $name, $force=0) {
			// this function allows children blocks to change the name of the block. This is useful
			// for the block search functionality - a content local block can make the block name
			// the fix 30 characters of the content field, for example. This only works if no name has
			// been assigned to the block already. If one has, then we disregard.
			$db = Loader::db();
			if (!$this->bName || $force==1) {  
				if( strlen($name)>60 ) $name = substr($name, 0, 60) . '...';
				$v = array(htmlspecialchars($name), $this->bID); 
				$q = "UPDATE Blocks SET bName = ? WHERE bID = ?";
				$r = $db->query($q,$v);
				//$res = $db->execute($r, $v);
				$this->bName=$name;
			}
			$this->refreshCache();
		}

		function alias($c) {	
		
			// creates an alias of the block, attached to this collection, within the CollectionVersionBlocks table
			// additionally, this command grabs the permissions from the original record in the
			// CollectionVersionBlocks table, and attaches them to the new one
			
			$db = Loader::db();
			$bID = $this->bID;
			$newBlockDisplayOrder = $c->getCollectionAreaDisplayOrder($this->getAreaHandle());
			$cvID = $c->getVersionID();
			$cID = $c->getCollectionID();
			$v = array($cID, $cvID, $this->bID, $this->getAreaHandle());
			$q = "select count(bID) from CollectionVersionBlocks where cID = ? and cvID = ? and bID = ? and arHandle = ?";
			$total = $db->getOne($q, $v);
			if ($total == 0) {
				array_push($v, $newBlockDisplayOrder, 0, $this->overrideAreaPermissions());
				$q = "insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions) values (?, ?, ?, ?, ?, ?, ?)";
				$r = $db->prepare($q);
				$res = $db->execute($r, $v);

				if ($res) {
					// now we grab the permissions from the block we're aliasing from
					$oc = $this->getBlockCollectionObject();
					$ocID = $oc->getCollectionID();
					$ocvID = $oc->getVersionID();

					$qa = "select gID, uID, cbgPermissions from CollectionVersionBlockPermissions where bID = '{$this->bID}' and cID = '$ocID' and cvID='{$ocvID}'";
					$ra = $db->query($qa);

					if ($ra) {
						while ($row_a = $ra->fetchRow()) {
							$qa2 = "insert into CollectionVersionBlockPermissions (cID, cvID, bID, gID, uID, cbgPermissions) values ('{$cID}', '{$cvID}', '{$this->bID}', '{$row_a['gID']}', '{$row_a['uID']}', '{$row_a['cbgPermissions']}')";
							$ra2 = $db->query($qa2);
						}
						$ra->free();
					}
				}
			}
		}
		
		/** 
		 * Moves a block onto a new page and into a new area. Does not change any data about the block otherwise
		 */
		function move($nc, $area) {
			$db = Loader::db();
			$cID = $this->getBlockCollectionID();

			$newBlockDisplayOrder = $nc->getCollectionAreaDisplayOrder($area->getAreaHandle());
			
			$v = array($nc->getCollectionID(), $nc->getVersionID(), $area->getAreaHandle(), $newBlockDisplayOrder, $cID, $this->arHandle);
			$db->Execute('update CollectionVersionBlocks set cID = ?, cvID = ?, arHandle = ?, cbDisplayOrder = ? where cID = ? and arHandle = ? and isOriginal = 1', $v);
		}
		
		function duplicate($nc) {
			// duplicate takes a new collection as its argument, and duplicates the existing block
			// to that collection

			$db = Loader::db();
			$dh = Loader::helper('date');
			
			$bt = BlockType::getByID($this->getBlockTypeID());
			$blockTypeClass = $bt->getBlockTypeClass();
			$bc = new $blockTypeClass($this);
			if(!$bc) return false;
						
			$bDate = $dh->getLocalDateTime();
			$v = array($this->bName, $bDate, $bDate, $this->bFilename, $this->btID, $this->uID);
			$q = "insert into Blocks (bName, bDateAdded, bDateModified, bFilename, btID, uID) values (?, ?, ?, ?, ?, ?)";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
			$newBID = $db->Insert_ID(); // this is the latest inserted block ID

			// now, we duplicate the block-specific permissions
			$oc = $this->getBlockCollectionObject();
			$ocID = $oc->getCollectionID();
			$ovID = $oc->getVersionID();

			$ncID = $nc->getCollectionID();
			$nvID = $nc->getVersionID();

			$q = "select gID, uID, cbgPermissions from CollectionVersionBlockPermissions where cID = '$ocID' and bID = '{$this->bID}' and cvID = '{$ovID}'";
			$r = $db->query($q);
			if ($r) {
				while ($row = $r->fetchRow()) {
					$q = "insert into CollectionVersionBlockPermissions (cID, cvID, bID, gID, uID, cbgPermissions) values ('$ncID', '$nvID', '{$newBID}', '{$row['gID']}', '{$row['uID']}', '{$row['cbgPermissions']}')";
					$r2 = $db->query($q);
				}
				$r->free();
			}

			// we duplicate block-specific sub-content 
			$bc->duplicate($newBID);

			// finally, we insert into the CollectionVersionBlocks table
			if (!is_numeric($this->cbDisplayOrder)) {
				$newBlockDisplayOrder = $nc->getCollectionAreaDisplayOrder($this->arHandle);
			} else {
				$newBlockDisplayOrder = $this->cbDisplayOrder;
			}
			//$v = array($ncID, $nvID, $newBID, $this->areaName, $newBlockDisplayOrder, 1);
			$v = array($ncID, $nvID, $newBID, $this->arHandle, $newBlockDisplayOrder, 1, $this->overrideAreaPermissions());
			$q = "insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions) values (?, ?, ?, ?, ?, ?, ?)";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);

			// now we make a DUPLICATE entry in the BlockRelations table, so that we know that the blocks are chained together
			$v2 = array($this->bID, $newBID, "DUPLICATE");
			$q2 = "insert into BlockRelations (originalBID, bID, relationType) values (?, ?, ?)";
			$r2 = $db->prepare($q2);
			$res2 = $db->execute($r2, $v2);
			$nb = Block::getByID($newBID, $nc, $this->arHandle);

			return $nb;
		}

		function getBlockCollectionObject() {
			if (is_object($this->c)) {
				return $this->c;
			} else {
				return $this->getOriginalCollection();
			}
		}

		function getBlockCollectionID() {
			return $this->cID;
		}

		function getBlockTypeName() {
			return $this->btName;
		}

		function getBlockTypeHandle() {
			return $this->btHandle;
		}

		function getBlockFilename() {
			return $this->bFilename;
		}


		function getBlockID() {
			return $this->bID;
		}
		
		function getBlockTypeID() {
			return $this->btID;
		}
		
		public function getBlockTypeObject() {
			return BlockType::getByID($this->btID);
		}
		
		function getAreaHandle() {
			return $this->arHandle;
		}
		
		function getBlockUserID() {
			return $this->uID;
		}

		function getBlockName() {
			return $this->bName;
		}

		function getBlockDateAdded() {
			return $this->bDateAdded;
		}

		function getBlockDateLastModified() {
			return $this->bDateModified;
		}

		function _getBlockAction() {
			$cID = $this->getBlockCollectionID();
			$bID = $this->getBlockID();
			$arHandle = $this->getAreaHandle();
			$step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';
			$valt = Loader::helper('validation/token');
			$token = $valt->generate();
			$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&amp;bID={$bID}&amp;arHandle={$arHandle}" . $step . "&ccm_token=" . $token;
			return $str;
		}

		function getBlockEditAction() {
			return $this->_getBlockAction();
		}

		function getBlockUpdateInformationAction() {
			$str = $this->_getBlockAction();
			return $str . '&amp;btask=update_information';
		}

		function getBlockMasterCollectionAliasAction() {
			$str = $this->_getBlockAction();
			return $str . '&amp;btask=mc_alias';
		}

		function getBlockPassThruAction() {
			$str = $this->_getBlockAction();
			return $str . '&amp;btask=passthru';
		}

		
		function isEditable() {
			return $this->isEditable;
		}

		function overrideAreaPermissions() {
			if (!$this->cbOverrideAreaPermissions) {
				$this->cbOverrideAreaPermissions = 0;
			}
			return $this->cbOverrideAreaPermissions;
		}

		public function delete($forceDelete = false) {
			$this->deleteBlock($forceDelete);
		}
		
		function deleteBlock($forceDelete = false) {
			$db = Loader::db();
						
			if ($this->bID < 1) {
				return false;
			}

			$this->refreshCache();

			$cID = $this->cID;
			$c = $this->getBlockCollectionObject();
			$cvID = $c->getVersionID();
			$bID = $this->bID;
			$arHandle = $this->arHandle;

			// if this block is located in a master collection, we're going to delete all the instances of the block,
			// regardless

			if (($c instanceof Page && $c->isMasterCollection() && !$this->isAlias()) || $forceDelete) {
				// forceDelete is used by the administration console

				// this is an original. We're deleting it, and everything else having to do with it
				$q = "delete from CollectionVersionBlocks where bID = '$bID'";
				$r = $db->query($q);

				$q = "delete from CollectionVersionBlockPermissions where bID = '$bID'";
				$r = $db->query($q);
			} else {
				$q = "delete from CollectionVersionBlocks where cID = '$cID' and (cvID = '$cvID' or cbIncludeAll=1) and bID = '$bID' and arHandle = '$arHandle'";
				$r = $db->query($q);

				// next, we delete the groups instance of this block
				$q = "delete from CollectionVersionBlockPermissions where bID = '$bID' and cvID = '$cvID' and cID = '$cID'";
				$r = $db->query($q);
			}

			//then, we see whether or not this block is aliased to anything else
			$q = "select count(*) as total from CollectionVersionBlocks where bID = '$bID'";
			$totalBlocks = $db->getOne($q);
			if ($totalBlocks < 1) {
				$q = "delete from BlockRelations where originalBID = ? or bID = ?";
				$r = $db->query($q, array($this->bID, $this->bID));
				// this block is not referenced in the system any longer, so we delete the entry in the blocks table, as well as the entries in the corresponding
				// sub-blocks table
	
				$v = array($this->bID);

				// so, first we delete the block's sub content				
				$bt = BlockType::getByID($this->getBlockTypeID());
				if( $bt && method_exists($bt,'getBlockTypeClass') ){
					$class = $bt->getBlockTypeClass();
					
					$bc = new $class($this);
					$bc->delete();
				}

				// now that the block's subcontent delete() method has been run, we delete the block from the Blocks table
				$q = "delete from Blocks where bID = ?";
				$r = $db->query($q, $v);

			}
		}

		function setOriginalBlockID($originalBID) {
			$this->originalBID = $originalBID;
		}
		function setBlockDisplayOrder($i) {
			// This function moves a block up or down
			// Since this is a function that has to be called from an instantiated block, then we already know the cID and areaName

			if (BLOCK_DISPLAY_ORDER == 'desc') {
				$i = ($i == 1) ? '-1' : '1';
			}

			$db = Loader::db();

			$cID = $this->cID;
			$bID = $this->bID;
			$arHandle = $this->arHandle;

			$c = $this->getBlockCollectionObject();
			$cvID = $c->getVersionID();
			$this->refreshCache();

			switch($i) {
				case '1':
					// we're moving the block up
					$q = "select cbDisplayOrder from CollectionVersionBlocks where cID = '$cID' and (cvID = '{$cvID}' or cbIncludeAll=1) and bID = '$bID' and arHandle = '$arHandle'";
					$origDisplayOrder = $db->getOne($q);

					// So now we have the display order for the original element. If it's 0, we do nothing.

					if ($origDisplayOrder != '0') {
						$newDisplayOrder = $origDisplayOrder - 1;
						$q = "update CollectionVersionBlocks set cbDisplayOrder = '$origDisplayOrder' where cbDisplayOrder = '$newDisplayOrder' and cID = '$cID' and (cvID = '{$cvID}' or cbIncludeAll=1) and arHandle = '$arHandle'";
						$r = $db->query($q);

						// now that we've set the other block to our original display order, we set our block to the new display order

						$q = "update CollectionVersionBlocks set cbDisplayOrder = '$newDisplayOrder' where bID = '$bID' and cID = '$cID' and (cvID = '{$cvID}' or cbIncludeAll=1) and arHandle = '$arHandle'";
						$r = $db->query($q);
					}
					break;
				case '-1':
					// we're moving the block down
					$q = "select cbDisplayOrder from CollectionVersionBlocks where cID = '$cID' and (cvID = '{$cvID}' or cbIncludeAll=1) and bID = '$bID' and arHandle = '$arHandle'";
					$origDisplayOrder = $db->getOne($q);

					// Now, to ensure that we don't screw up the display order stuff in the database, we can't set a display order greater than
					// n - 1 blocks (meaning if there are 5 blocks in this particular area+collection, we can't have a display order greater than 4

					$q = "select count(*) as total from CollectionVersionBlocks where cID = '$cID' and (cvID = '{$cvID}' or cbIncludeAll=1) and arHandle = '$arHandle'";
					$maxDisplayOrder = ($db->getOne($q) - 1);

					if ($origDisplayOrder <= $maxDisplayOrder) {
						$newDisplayOrder = $origDisplayOrder + 1;
						$q = "update CollectionVersionBlocks set cbDisplayOrder = '$origDisplayOrder' where cbDisplayOrder = '$newDisplayOrder' and cID = '$cID' and (cvID = '{$cvID}' or cbIncludeAll=1) and arHandle = '$arHandle'";
						$r = $db->query($q);

						// now that we've set the other block to our original display order, we set our block to the new display order

						$q = "update CollectionVersionBlocks set cbDisplayOrder = '$newDisplayOrder' where bID = '$bID' and cID = '$cID' and arHandle = '$arHandle'";
						$r = $db->query($q);
					}
					break;
			}
		}

		function updateBlockGroups($updateAll = false) {
			$db = Loader::db();
			$overrideAreaPermissions = ($_POST['cbOverrideAreaPermissions']) ? 1 : 0;
			// All right, so here's how we do this. We iterate through the posted form arrays, storing and concatenating
			// permission sets for each particular group. Then we delete all of the groups associated with this collectionblock
			// and insert new ones

			$gIDArray = array();
			$uIDArray = array();
			if (is_array($_POST['blockRead'])) {
				foreach ($_POST['blockRead'] as $ugID) {
					 if (strpos($ugID, 'uID') > -1) {
					 	$uID = substr($ugID, 4);
					 	$uIDArray[$uID] .= "r:";
					 } else {
					 	$gID = substr($ugID, 4);
					 	$gIDArray[$gID] .= "r:";

					 }
				}
			}

			if (is_array($_POST['blockWrite'])) {
				foreach($_POST['blockWrite'] as $ugID) {
					if (strpos($ugID, 'uID') > -1) {

						$uID = substr($ugID, 4);
						$uIDArray[$uID] .= "wa:";
					} else {

						$gID = substr($ugID, 4);
						$gIDArray[$gID] .= "wa:";
					}
				}
			}

			if (is_array($_POST['blockDelete'])) {
				foreach($_POST['blockDelete'] as $ugID) {
					 if (strpos($ugID, 'uID') > -1) {

					 	$uID = substr($ugID, 4);

					 	$uIDArray[$uID] .= "db:";

					 } else {
					 	$gID = substr($ugID, 4);

					 	$gIDArray[$gID] .= "db:";
					 }
				}
			}


			// now that we've gone through this and created an array of IDs, we're going to delete all permissions for this particular block
			// in the database, before we add them back in

			if ($updateAll) {
				// If we've called updateAll, that means we're updating all the groups that correspond to a particular
				// master collection. We're going to loop through, grab all the collection IDs for this particular
				// template, delete their records from the CollectionVersionBlockPermissions table, then loop through again and
				// add the new records in.
				// This would be a whole lot more efficient if mysql (3.23) supported multi-table deletes.

				$q = "select distinct cID from CollectionVersionBlocks where bID = '{$this->bID}'";
				$r = $db->query($q);
				if ($r) {
					while ($row = $r->fetchRow()) {
						$cList[] = $row['cID'];
					}

					$q = "select cID, cvID from CollectionVersionBlocks where bID = '{$this->bID}'";
					$r = $db->query($q);
					if ($r) {
						while ($row = $r->fetchRow()) {
							$cvList[] = $row;
						}

						if ($cList) {
							$cListIDs = implode(',', $cList);
							$q = "delete from CollectionVersionBlockPermissions where cID in ({$cListIDs}) and bID = '{$this->bID}'";
							$r = $db->query($q);
							if ($r) {
								foreach ($cvList as $cvRow) {
									$v3 = array($overrideAreaPermissions, $cvRow['cID'], $cvRow['cvID'], $this->bID, $this->arHandle);
									$q3 = "update CollectionVersionBlocks set cbOverrideAreaPermissions = ? where cID = ? and (cvID = ? or cbIncludeAll=1) and bID = ? and arHandle = ?";
									$r3 = $db->prepare($q3);
									$res3 = $db->execute($r3, $v3);

									if ($_POST['cbOverrideAreaPermissions']) {
										foreach ($gIDArray as $gID => $perms) {
											// we have to trim the trailing colon, if there is one
											$permissions = (strrpos($perms, ':') == (strlen($perms) - 1)) ? substr($perms, 0, strlen($perms) - 1) : $perms;
											$v = array($cvRow['cID'], $cvRow['cvID'], $this->bID, $gID, $permissions);
											$q = "insert into CollectionVersionBlockPermissions (cID, cvID, bID, gID, cbgPermissions) values (?, ?, ?, ?, ?)";
											$r = $db->prepare($q);
											$res = $db->execute($r, $v);
										}
										foreach ($uIDArray as $uID => $perms) {
											// we have to trim the trailing colon, if there is one
											$permissions = (strrpos($perms, ':') == (strlen($perms) - 1)) ? substr($perms, 0, strlen($perms) - 1) : $perms;
											$v = array($cvRow['cID'], $cvRow['cvID'], $this->bID, $uID, $permissions);
											$q = "insert into CollectionVersionBlockPermissions (cID, cvID, bID, uID, cbgPermissions) values (?, ?, ?, ?, ?)";
											$r = $db->prepare($q);
											$res = $db->execute($r, $v);
										}
									}
								}
							}
						}
					}
				}

			} else {
				// first, if we're overriding page-level permissions, we make a note of that

				$c = $this->getBlockCollectionObject();

				$v = array($overrideAreaPermissions, $c->getCollectionID(), $c->getVersionID(), $this->bID, $this->arHandle);
				$q = "update CollectionVersionBlocks set cbOverrideAreaPermissions = ? where cID = ? and (cvID = ? or cbIncludeAll=1) and bID = ? and arHandle = ?";
				$r = $db->prepare($q);
				$res = $db->execute($r, $v);

				$q = "delete from CollectionVersionBlockPermissions where cID = '{$this->cID}' and bID = '{$this->bID}'";
				$r = $db->query($q);


				// now, we only iterate through and add the blocks in if there's been no db problem, AND we're set to

				// currently override the page's settings. This is usually the case (if we've gotten this far), unless we're

				// removing previously set block-level permissions
				if ($r && $_POST['cbOverrideAreaPermissions']) {
					// now we iterate through, and add the permissions
					$c = $this->getBlockCollectionObject();
					$cID = $c->getCollectionID();
					$cvID = $c->getVersionID();

					foreach ($gIDArray as $gID => $perms) {
						// we have to trim the trailing colon, if there is one
						$permissions = (strrpos($perms, ':') == (strlen($perms) - 1)) ? substr($perms, 0, strlen($perms) - 1) : $perms;
						$v = array($cID, $cvID, $this->bID, $gID, $permissions);
						$q = "insert into CollectionVersionBlockPermissions (cID, cvID, bID, gID, cbgPermissions) values (?, ?, ?, ?, ?)";
						$r = $db->prepare($q);
						$res = $db->execute($r, $v);
					}

					foreach ($uIDArray as $uID => $perms) {
						// we have to trim the trailing colon, if there is one
						$permissions = (strrpos($perms, ':') == (strlen($perms) - 1)) ? substr($perms, 0, strlen($perms) - 1) : $perms;
						$v = array($cID, $cvID, $this->bID, $uID, $permissions);
						$q = "insert into CollectionVersionBlockPermissions (cID, cvID, bID, uID, cbgPermissions) values (?, ?, ?, ?, ?)";
						$r = $db->prepare($q);
						$res = $db->execute($r, $v);
					}
				}
			}

			$this->refreshCache();
			
		}
		
		public function setCustomTemplate($template) {
			$data['bFilename'] = $template;
			$this->updateBlockInformation($data);
		}
		
		public function setName($name) {
			$data['bName'] = $name;
			$this->updateBlockInformation($data);
		}
		
		/** 
		 * Removes a cached version of the block 
		 */
		public function refreshCache() {
			$c = $this->getBlockCollectionObject();
			$a = $this->getBlockAreaObject();
			if (is_object($c) && is_object($a)) {
				Cache::delete('block', $this->getBlockID() . ':' . $c->getCollectionID() . ':' . $c->getVersionID() . ':' . $a->getAreaHandle());
			}
		}
		
		public function refreshCacheAll(){
			$db = Loader::db();
			$rows=$db->getAll( 'SELECT cID, cvID, arHandle FROM CollectionVersionBlocks WHERE bID='.intval($this->getBlockID()) ); 
			foreach($rows as $row){
				Cache::delete('block', $this->getBlockID() . ':' . intval($row['cID']) . ':' . intval($row['cvID']) . ':' . $row['arHandle'] );
			}
		}
		
		function updateBlockInformation($data) {
			// this is the function that updates a block's information, like its block filename, and block name
			$db = Loader::db();
			$dh = Loader::helper('date');
			$dt = $dh->getLocalDateTime();
			
			$bName = $this->bName;
			$bFilename = $this->bFilename;
			if (isset($data['bName'])) {
				$bName = $data['bName'];
			}
			if (isset($data['bFilename'])) {
				$bFilename = $data['bFilename'];
			}
			
			$v = array($bName, $bFilename, $dt, $this->bID);
			$q = "update Blocks set bName = ?, bFilename = ?, bDateModified = ? where bID = ?";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
			$this->refreshCache();
			
		}

	}
?>