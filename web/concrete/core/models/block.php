<?

defined('C5_EXECUTE') or die("Access Denied.");

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
class Concrete5_Model_Block extends Object {

	var $cID;
	var $arHandle;
	var $c;
	protected $csrID;
	protected $proxyBlock = false;
	protected $bIncludeInComposerIsSet = false;

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

	public function getPermissionObjectIdentifier() {
		return $this->cID . ':' . $this->getAreaHandle() . ':' . $this->bID;
	}
	
	public static function getByID($bID, $c = null, $a = null) {
		if ($c == null && $a == null) {
			$cID = 0;
			$arHandle = "";
			$cvID = 0;
			$b = CacheLocal::getEntry('block', $bID);
		} else {
			if (is_object($a)) {
				$arHandle = $a->getAreaHandle();
			} else if ($a != null) {
				$arHandle = $a;
				$a = Area::getOrCreate($c, $a);
			}
			$cID = $c->getCollectionID();
			$cvID = $c->getVersionID();
			$b = CacheLocal::getEntry('block', $bID . ':' . $cID . ':' . $cvID . ':' . $arHandle);
		}

		if ($b instanceof Block) {
			return $b;
		}

		$db = Loader::db();

		$b = new Block;
		if ($c == null && $a == null) {
			// just grab really specific block stuff
			$q = "select bID, bIsActive, BlockTypes.btID, Blocks.btCachedBlockRecord, BlockTypes.btHandle, BlockTypes.pkgID, BlockTypes.btName, bName, bDateAdded, bDateModified, bFilename, Blocks.uID from Blocks inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where bID = ?";
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
			$q = "select CollectionVersionBlocks.isOriginal, Blocks.btCachedBlockRecord, BlockTypes.pkgID, CollectionVersionBlocks.cbOverrideAreaPermissions, CollectionVersionBlocks.cbDisplayOrder, Blocks.bIsActive, Blocks.bID, Blocks.btID, bName, bDateAdded, bDateModified, bFilename, btHandle, Blocks.uID from CollectionVersionBlocks inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID) inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where CollectionVersionBlocks.arHandle = ? and CollectionVersionBlocks.cID = ? and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) and CollectionVersionBlocks.bID = ?";
		
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
				CacheLocal::set('block', $bID . ':' . $cID . ':' . $cvID . ':' . $arHandle, $b);
			} else {
				$ca = new Cache();
				CacheLocal::set('block', $bID, $b);
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
		if ($globalScrapbookPage->getCollectionID()) {
			$row = $db->getRow( 'SELECT b.bID, cvb.arHandle FROM Blocks AS b, CollectionVersionBlocks AS cvb '.
							  'WHERE b.bName=? AND b.bID=cvb.bID AND cvb.cID=? ORDER BY cvb.cvID DESC', 
							   array($globalBlockName, intval($globalScrapbookPage->getCollectionId()) ) ); 
			if ($row != false && isset($row['bID']) && $row['bID'] > 0) {
				return Block::getByID( $row['bID'], $globalScrapbookPage, $row['arHandle'] );
			}
		}
		
		//If we made it this far, either there's no scrapbook (clean installation of Concrete5.5+),
		// or the block wasn't in the legacy scrapbook -- so look in stacks...
		$sql = 'SELECT b.bID, cvb.arHandle, cvb.cID'
		 	 . ' FROM Blocks AS b'
			 . ' INNER JOIN CollectionVersionBlocks AS cvb ON b.bID = cvb.bID'
			 . ' INNER JOIN CollectionVersions AS cv ON cvb.cID = cv.cID AND cvb.cvID = cv.cvID'
		 	 . ' WHERE b.bName = ? AND cv.cvIsApproved = 1'
		 	 . ' AND cvb.cID IN (SELECT cID FROM Stacks)'
		 	 . ' ORDER BY cvb.cvID DESC'
			 . ' LIMIT 1';
		$vals = array($globalBlockName);
		$row = $db->getRow($sql, $vals);
		if ($row != false && isset($row['bID']) && $row['bID'] > 0) {
			return Block::getByID($row['bID'], Page::getByID($row['cID']), $row['arHandle']);
		} else {
			return new Block();
		}
	
	}
	
	public function setProxyBlock($block) {
		$this->proxyBlock = $block;
	}
	
	public function getProxyBlock() {
		return $this->proxyBlock;
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
			$vo = $c->getVersionObject();
			$cvID = $vo->getVersionID();
			$q = "select bID from CollectionVersionBlocks where bID = ? and cID=? and isOriginal = 0 and cvID = ?";
			$r = $db->query($q, array($this->bID, $cID, $cvID));
			if ($r) {
				return ($r->numRows() > 0);
			}
		} else {
			return (!$this->isOriginal);
		}
	}
	
	public function isAliasOfMasterCollection() {
		return $this->getBlockCollectionObject()->isBlockAliasedFromMasterCollection($this);
	}
	
	public function isGlobal() {
		return false; // legacy. no more scrapbooks in the dashboard.
	}
	
	public function isBlockInStack() {
		$co = $this->getBlockCollectionObject();
		if (is_object($co)) {
			if ($co->getCollectionTypeHandle() == STACKS_PAGE_TYPE) {
				return true;
			}
		}
		return false;
	}
	
	public function getBlockCachedRecord() {
		return $this->btCachedBlockRecord;
	}


	public function getBlockCachedOutput($area) {
		$db = Loader::db();
		
		$arHandle = $this->getAreaHandle();
		if ($this->isBlockInStack() && is_object($area)) {
			$arHandle = $area->getAreaHandle();
			$cx = Page::getCurrentPage();
			$cID = $cx->getCollectioniD();
			$cvID = $cx->getVersionID();
		} else {
			$c = $this->getBlockCollectionObject();
			$cID = $c->getCollectionID();
			$cvID = $c->getVersionID();
		}
		
		$r = $db->GetRow('select btCachedBlockOutput, btCachedBlockOutputExpires from CollectionVersionBlocksOutputCache where cID = ? and cvID = ? and bID = ? and arHandle = ? ', array(
			$cID, $cvID, $this->getBlockID(), $arHandle));
		if ($r['btCachedBlockOutputExpires'] < time()) {
			return false;
		}

		return $r['btCachedBlockOutput'];
	}

	public function setBlockCachedOutput($content, $lifetime, $area) {
		$db = Loader::db();
		$c = $this->getBlockCollectionObject();

		$btCachedBlockOutputExpires = strtotime('+5 years');
		if ($lifetime > 0) {
			$btCachedBlockOutputExpires = time() + $lifetime;
		}

		$arHandle = $this->getAreaHandle();
		$cID = $c->getCollectionID();
		$cvID = $c->getVersionID();
		if ($this->isBlockInStack() && is_object($area)) {
			$arHandle = $area->getAreaHandle();
			$cx = Page::getCurrentPage();
			$cID = $cx->getCollectioniD();
			$cvID = $cx->getVersionID();
		}

		if ($arHandle && $cID && $cvID) {
			$db->Replace('CollectionVersionBlocksOutputCache', array('cID' => $cID, 'cvID' => $cvID, 'bID' => $this->getBlockID(), 'arHandle' => $arHandle, 'btCachedBlockOutput' => $content, 'btCachedBlockOutputExpires' => $btCachedBlockOutputExpires), 
				array('cID', 'cvID', 'arHandle', 'bID'), true);
		}
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

	function revertToAreaPermissions() {
		$c = $this->getBlockCollectionObject();

		$db = Loader::db();
		$v = array($c->getCollectionID(), $c->getVersionID(), $this->bID);

		$db->query("delete from BlockPermissionAssignments where cID = ? and cvID = ? and bID = ?", $v);
		$v[] = $this->arHandle;
		$db->query("update CollectionVersionBlocks set cbOverrideAreaPermissions = 0 where cID = ? and (cvID = ? or cbIncludeAll=1) and bID = ? and arHandle = ?", $v);
	 }
	 
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
	
	public function isBlockIncludedInComposer() {
		if (!$this->bIncludeInComposerIsSet) {
			$this->setBlockComposerProperties();
		}

		return $this->bIncludeInComposer;
	}


	protected function setBlockComposerProperties() {
		$this->bIncludeInComposerIsSet = true;
		$db = Loader::db();
		if ($this->c != null) {
			$ct = CollectionType::getByID($this->c->getCollectionTypeID());
			if (is_object($ct)) {
				if ($ct->isCollectionTypeIncludedInComposer()) { 				
					if ($this->c->isMasterCollection()) {
						$ctID = $this->c->getCollectionTypeID();
						$ccbID = $this->bID;
					} else {
						$tempBID = $this->getBlockID();
						while ($tempBID != false && $tempBID != 0) {
							$originalBID = $tempBID;
							$tempBID = $db->GetOne('select distinct br.originalBID from BlockRelations br inner join CollectionVersionBlocks cvb on cvb.bID = br.bID where br.bID = ? and cvb.cID = ?', array($tempBID, $this->c->getCollectionID()));
						}
						if ($originalBID && is_object($this->c)) {
							$ctID = $this->c->getCollectionTypeID();
							$ccbID = $originalBID;
						}
					}
					
					if ($ctID && $ccbID) {
						$cb = $db->GetRow('select bID, ccFilename from ComposerContentLayout where ctID = ? and bID = ?', array($ctID, $ccbID));
						if (is_array($cb) && $cb['bID'] == $ccbID) {
							$this->bIncludeInComposer = 1;
							$this->cbFilename = $cb['ccFilename'];
						}
					}
				}
			}
		}
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
		$q = "select Pages.cID, cIsTemplate from Pages inner join CollectionVersionBlocks on (CollectionVersionBlocks.cID = Pages.cID) where CollectionVersionBlocks.bID = ? and CollectionVersionBlocks.isOriginal = 1";
		$r = $db->query($q, array($this->bID));
		if ($r) {
			$row = $r->fetchRow();
			$cID = $row['cID'];
			$nc = Page::getByID($cID, "ACTIVE");
			return $nc;
		}
	}

	function getNumChildren() {
		$db = Loader::db();
		$q = "select count(*) as total from CollectionVersionBlocks where bID = ? and isOriginal = 0";
		$total = $db->getOne($q, array($this->bID));
		return $total;
	}

	function passThruBlock($method) {
		// pass this onto the blocktype's class
		
		$method = "action_" . $method;
		if ($this->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
			$bOriginalID = $this->getController()->getOriginalBlockID();
			$bo = Block::getByID($bOriginalID);
			$btID = $bo->getBlockTypeID();
			$b = $bo;
		} else {
			$btID = $this->getBlockTypeID();	
			$b = $this;
		}
		$bt = BlockType::getByID($btID);
		$class = $bt->getBlockTypeClass();
		$bc = new $class($b);
		
		// ONLY ALLOWS ITEMS THAT START WITH "action_";
		
		if (is_callable(array($bc, $method))) {
			return $bc->{$method}();
		}
	}
	
	public function getInstance() {		
		if (ENABLE_BLOCK_CACHE && $this->instance->cacheBlockRecord() && is_object($this->instance->getBlockControllerData())) {
			$this->instance->__construct();
		} else {
			$this->instance = Loader::controller($this);
		}		
		return $this->instance;
	}
	
	
	public function getController() {
		return $this->getInstance();
	}
	
	function getCollectionList() {
		// gets a list of collections that include this block, along with area name, etc...
		// used in the block_details.php page in the admin control panel
		$db = Loader::db();
		$q = "select DISTINCT Pages.cID from CollectionVersionBlocks inner join Pages on (CollectionVersionBlocks.cID = Pages.cID) inner join CollectionVersions on (CollectionVersions.cID = Pages.cID) where CollectionVersionBlocks.bID = ?";
		$r = $db->query($q, array($this->bID));
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
		$bDateModified = $dh->getSystemDateTime();
		$bID = $this->bID;

		$v = array($bDateModified, $bID);
		$q = "update Blocks set bDateModified = ? where bID = ?";

		$r = $db->prepare($q);
		$res = $db->execute($r, $v);

		$cID = $this->getBlockCollectionID();
		$c = $this->getBlockCollectionObject();
		$v = array($c->getCollectionID(), $c->getVersionID(), $this->getAreaHandle(), $bID);
		$db->Execute('update CollectionVersionBlocksOutputCache set btCachedBlockOutputExpires = 0 where cID = ? and cvID = ? and arHandle = ? and bID = ?', $v);

		$btID = $this->getBlockTypeID();
		$bt = BlockType::getByID($btID);
		$class = $bt->getBlockTypeClass();
		$bc = new $class($this);
		$bc->save($data);
	}

	function isActive() {
		return $this->bIsActive;
	}

	function deactivate() {
		$db = Loader::db();
		$q = "update Blocks set bIsActive = 0 where bID = ?";
		$db->query($q, array($this->bID));
	}

	function activate() {
		$db = Loader::db();
		$q = "update Blocks set bIsActive = 1 where bID = ?";
		$db->query($q, array($this->bID));
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
			
			// styles
			$csrID = $this->getBlockCustomStyleRuleID();
            if ($csrID > 0) {
                $db->Execute('insert into CollectionVersionBlockStyles (cID, cvID, bID, arHandle, csrID) values (?, ?, ?, ?, ?)', array(
                        $cID, 
                        $cvID,
                        $this->bID,
                        $this->getAreaHandle(),
                        $csrID
                ));
            }
			if ($res) {
				// now we grab the permissions from the block we're aliasing from
				$oc = $this->getBlockCollectionObject();
				$ocID = $oc->getCollectionID();
				$ocvID = $oc->getVersionID();

				$qa = "select paID, pkID from BlockPermissionAssignments where bID = ? and cID = ? and cvID = ?";
				$ra = $db->query($qa, array($this->bID, $ocID, $ocvID));

				if ($ra) {
					while ($row_a = $ra->fetchRow()) {
						$db->Replace('BlockPermissionAssignments', 
							array('cID' => $cID, 'cvID' => $cvID, 'bID' => $this->bID, 'paID' => $row_a['paID'], 'pkID' => $row_a['pkID']),
							array('cID', 'cvID', 'bID', 'paID', 'pkID'), true);
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
          $bID = $this->getBlockID();
		$cID = $this->getBlockCollectionID();

		$newBlockDisplayOrder = $nc->getCollectionAreaDisplayOrder($area->getAreaHandle());

		
		$v = array($nc->getCollectionID(), $nc->getVersionID(), $area->getAreaHandle(), $newBlockDisplayOrder, $cID, $bID, $this->arHandle);
		$db->Execute('update CollectionVersionBlocks set cID = ?, cvID = ?, arHandle = ?, cbDisplayOrder = ? where cID = ? and bID = ? and arHandle = ? and isOriginal = 1', $v);
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
					
		$bDate = $dh->getSystemDateTime();
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

		$q = "select paID, pkID from BlockPermissionAssignments where cID = '$ocID' and bID = ? and cvID = ?";
		$r = $db->query($q, array($this->bID, $ovID));
		if ($r) {
			while ($row = $r->fetchRow()) {
				$db->Replace('BlockPermissionAssignments', 
					array('cID' => $ncID, 'cvID' => $nvID, 'bID' => $newBID, 'paID' => $row['paID'], 'pkID' => $row['pkID']),
					array('cID', 'cvID', 'bID', 'paID', 'pkID'), true);

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
		
		$csrID = $this->getBlockCustomStyleRuleID();
        if ($csrID > 0) {
            $v = array($ncID, $nvID, $newBID, $this->arHandle, $csrID);
            $db->Execute('insert into CollectionVersionBlockStyles (cID, cvID, bID, arHandle, csrID) values (?, ?, ?, ?, ?)', $v);
        }
		return $nb;
	}
	
	public function getBlockCustomStyleRule() {
		if ($this->getBlockCustomStyleRuleID() > 0) {
			Loader::model('custom_style');
			$txt = Loader::helper('text');
			$csr = CustomStyleRule::getByID($this->getBlockCustomStyleRuleID());
			$arHandle = $txt->filterNonAlphaNum($this->getAreaHandle());

			if (is_object($csr)) {
				$csr->setCustomStyleNameSpace('blockStyle' . $this->getBlockID() . $arHandle );
				return $csr;
			}
		}
	}
	
	public function getBlockCustomStyleRuleID() {
		$db = Loader::db();
		if (!isset($this->csrID)) {
			$co = $this->getBlockCollectionObject();
			$csrCheck = CacheLocal::getEntry('csrCheck', $co->getCollectionID() . ':' . $co->getVersionID());
			$csrObject = CacheLocal::getEntry('csrObject', $co->getCollectionID() . ':' . $co->getVersionID() . ':' . $this->getAreaHandle() . ':' . $this->getBlockID());
			if (is_object($csrObject)) {
				$this->csrID = $csrObject->getCustomStyleRuleID();
				return $csrObject->getCustomStyleRuleID();
			} else if ($csrCheck) {
				return false;
			}

			$arHandle = $this->getAreaHandle();
			if ($arHandle) {
				$a = $this->getBlockAreaObject();
				if ($a->isGlobalArea()) {
					// then we need to check against the global area name. We currently have the wrong area handle passed in
					$arHandle = STACKS_AREA_NAME;
				}

				$v = array(
					$co->getCollectionID(), 
					$co->getVersionID(),
					$arHandle,
					$this->bID
				);

				$this->csrID = $db->GetOne('select csrID from CollectionVersionBlockStyles where cID = ? and cvID = ? and arHandle = ? and bID = ?', $v);
			} else {
				$this->csrID = 0;
			}
		}
		return $this->csrID;
	}
	
	
	public function resetBlockCustomStyle($updateAll = false) {
		$db = Loader::db();
		$c = $this->getBlockCollectionObject();
		$cvID = $c->getVersionID();
		if ($updateAll) {
			$r = $db->Execute('select cID, cvID, bID, arHandle from CollectionVersionBlockStyles where bID = ? and csrID = ?', array($this->bID, $this->getBlockCustomStyleRuleID()));
			while ($row = $r->FetchRow()) {
				$c1 = Page::getByID($row['cID'], $row['cvID']);
				$b1 = Block::getByID($row['bID'], $c1, $row['arHandle']);
			}			
			$db->Execute('delete from CollectionVersionBlockStyles where bID = ? and csrID = ?', array(
				$this->bID,
				$this->getBlockCustomStyleRuleID()
			));
		} else {
			$db->Execute('delete from CollectionVersionBlockStyles where cID = ? and cvID = ? and arHandle = ? and bID = ?', array(
				$this->getBlockCollectionID(),
				$cvID,
				$this->getAreaHandle(),
				$this->bID
			));
		}
	}
	
	public function __destruct() {
		unset($this->c);
		unset($this->a);
		unset($this->instance);
	}
	
	public function setBlockCustomStyle($csr, $updateAll = false) {
		$db = Loader::db();
		$c = $this->getBlockCollectionObject();
		$cvID = $c->getVersionID();
		if ($updateAll) {
			$r = $db->Execute('select cID, cvID, bID, arHandle from CollectionVersionBlocks where bID = ?', array($this->bID));
			while ($row = $r->FetchRow()) {
				$c1 = Page::getByID($row['cID'], $row['cvID']);
				$b1 = Block::getByID($row['bID'], $c1, $row['arHandle']);
				$b1->setBlockCustomStyle($csr, false);
			}			
		} else {
            if ($csr->getCustomStyleRuleID() > 0) {
                $db->Replace('CollectionVersionBlockStyles', 
                        array('cID' => $this->getBlockCollectionID(), 'cvID' => $cvID, 'arHandle' => $this->getAreaHandle(), 'bID' => $this->bID, 'csrID' => $csr->getCustomStyleRuleID()),
                        array('cID', 'cvID', 'bID', 'arHandle'), true
                );
                $this->refreshCache();
            }
		}
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

	function getBlockComposerFilename() {
		if (!$this->bIncludeInComposerIsSet) {
			$this->setBlockComposerProperties();
		}
		return $this->cbFilename;
	}

	public function hasComposerBlockTemplate() {
		$bv = new BlockView();
		$bv->setBlockObject($this);
		$cpFilename = $this->getBlockComposerFilename();
		if ($cpFilename) {
			$cmpbase = $bv->getBlockPath(DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $cpFilename);
			if (file_exists($cmpbase . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $cpFilename)) {
				return true;
			}
		}
		
		$cmpbase = $bv->getBlockPath(FILENAME_BLOCK_COMPOSER);
		if (file_exists($cmpbase . '/' . FILENAME_BLOCK_COMPOSER)) {
			return true;
		}
		
		return false;
	}
	
	public function getBlockDisplayOrder() {
		return $this->cbDisplayOrder;
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

	/**
	 * Gets the date the block was added
	 * if user is specified, returns in the current user's timezone
	 * @param string $type (system || user)
	 * @return string date formated like: 2009-01-01 00:00:00 
	*/
	function getBlockDateAdded($type = 'system') {
		if(ENABLE_USER_TIMEZONES && $type == 'user') {
			$dh = Loader::helper('date');
			return $dh->getLocalDateTime($this->bDateAdded);
		} else {
			return $this->bDateAdded;
		}
	}

	function getBlockDateLastModified() {
		return $this->bDateModified;
	}

	function _getBlockAction() {
		$cID = $this->getBlockActionCollectionID();
		$bID = $this->getBlockID();
		$arHandle = urlencode($this->getAreaHandle());
		$step = ($_REQUEST['step']) ? '&amp;step=' . $_REQUEST['step'] : '';
		$valt = Loader::helper('validation/token');
		$token = $valt->generate();
		$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&amp;bID={$bID}&amp;arHandle={$arHandle}" . $step . "&amp;ccm_token=" . $token;
		return $str;
	}
	
	public function setBlockActionCollectionID($bActionCID) {
		$this->bActionCID = $bActionCID;
	}
	
	/**
	 * @return integer|false The block action collection id or false if not found
	 */
	public function getBlockActionCollectionID() {
		if ($this->bActionCID > 0) {
			return $this->bActionCID;
		}

		$c = Page::getCurrentPage();
		if (is_object($c)) {
			return $c->getCollectionID();
		}

		$c = $this->getBlockCollectionObject();
		if (is_object($c)) {
			return $c->getCollectionID();
		}

		return false;
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
	
	function getBlockUpdateCssAction() {
		$str = $this->_getBlockAction();
		return $str . '&amp;btask=update_block_css';
	}	

	function getBlockUpdateComposerSettingsAction() {
		$str = $this->_getBlockAction();
		return $str . '&amp;btask=update_composer_settings';
	}	

	function getBlockPassThruAction() {
		// is the block located in a stack?
		$pc = $this->getBlockCollectionObject();
		if ($pc->getCollectionTypeHandle() == STACKS_PAGE_TYPE) {
			$c = Page::getCurrentPage();
			$cID = $c->getCollectionID();
			$bID = $this->getBlockID();
			$valt = Loader::helper('validation/token');
			$token = $valt->generate();
			$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&amp;stackID=" . $this->getBlockActionCollectionID() . "&amp;bID={$bID}&amp;btask=passthru_stack&amp;ccm_token=" . $token;
			return $str;
		} else { 
			$str = $this->_getBlockAction();
			return $str . '&amp;btask=passthru';
		}
	}
	
	function isEditable() {
		$bv = new BlockView();
		$bv->setBlockObject($this);
		$path = $bv->getBlockPath(FILENAME_BLOCK_EDIT);
		if (file_exists($path . '/' . FILENAME_BLOCK_EDIT)) {
			return true;
		}
		return false;
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

			$q = "delete from ComposerContentLayout where bID = '$bID'";
			$r = $db->query($q);

			$q = "delete from BlockPermissionAssignments where bID = '$bID'";
			$r = $db->query($q);
			
			$q = "delete from CollectionVersionBlockStyles where bID = ".intval($bID);
			$r = $db->query($q);
			
		} else {
			$q = "delete from CollectionVersionBlocks where cID = '$cID' and (cvID = '$cvID' or cbIncludeAll=1) and bID = '$bID' and arHandle = '$arHandle'";
			$r = $db->query($q);

			// next, we delete the groups instance of this block
			$q = "delete from BlockPermissionAssignments where bID = '$bID' and cvID = '$cvID' and cID = '$cID'";
			$r = $db->query($q);
			
			$q = "delete from CollectionVersionBlockStyles where cID = '$cID' and cvID = '$cvID' and bID = '$bID' and arHandle = '$arHandle'";
			$r = $db->query($q);				
		}

		//then, we see whether or not this block is aliased to anything else
		$totalBlocks =  $db->GetOne('select count(*) from CollectionVersionBlocks where bID = ?', array($bID));
		$totalBlocks += $db->GetOne('select count(*) from btCoreScrapbookDisplay where bOriginalID = ?', array($bID));
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
			
			// Aaaand then we delete all scrapbooked blocks to this entry
			$r = $db->Execute('select cID, cvID, CollectionVersionBlocks.bID, arHandle from CollectionVersionBlocks inner join btCoreScrapbookDisplay on CollectionVersionBlocks.bID = btCoreScrapbookDisplay.bID where bOriginalID = ?', array($bID));
			while ($row = $r->FetchRow()) {
				$c = Page::getByID($row['cID'], $row['cvID']);
				$b = Block::getByID($row['bID'], $c, $row['arHandle']);
				$b->delete();
			}
			

		}
	}

	function setOriginalBlockID($originalBID) {
		$this->originalBID = $originalBID;
	}
	
	public function setAbsoluteBlockDisplayOrder($do) {
		$db = Loader::db();

		$cID = $this->cID;
		$bID = $this->bID;
		$arHandle = $this->arHandle;

		$c = $this->getBlockCollectionObject();
		$cvID = $c->getVersionID();
	
		$q = "update CollectionVersionBlocks set cbDisplayOrder = ? where bID = ? and cID = ? and (cvID = ? or cbIncludeAll=1) and arHandle = ?";
		$r = $db->query($q, array($do, $bID, $cID, $cvID, $arHandle));
		
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

	public function doOverrideAreaPermissions() {
		$db = Loader::db();
		$c = $this->getBlockCollectionObject();
		$v = array($c->getCollectionID(), $c->getVersionID(), $this->bID, $this->arHandle);
		$db->query("update CollectionVersionBlocks set cbOverrideAreaPermissions = 1 where cID = ? and (cvID = ? or cbIncludeAll = 1) and bID = ? and arHandle = ?", $v);
		$v = array($c->getCollectionID(), $c->getVersionID(), $this->bID);
		$db->query("delete from BlockPermissionAssignments where cID = ? and cvID = ? and bID = ?", $v);
		
		// copy permissions from the page to the area
		$permissions = PermissionKey::getList('block');
		foreach($permissions as $pk) { 
			$pk->setPermissionObject($this);
			$pk->copyFromPageOrAreaToBlock();
		}		
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
		/*
		// if the block is a global block, we need to delete all cached versions that reference it.
		if ($this->bIsGlobal) {
			$this->refreshCacheAll();
		} else { 
			$c = $this->getBlockCollectionObject();
			$a = $this->getBlockAreaObject();
			if (is_object($c) && is_object($a)) {
				Cache::delete('block', $this->getBlockID() . ':' . $c->getCollectionID() . ':' . $c->getVersionID() . ':' . $a->getAreaHandle());
				Cache::delete('block_view_output', $c->getCollectionID() . ':' . $this->getBlockID() . ':' . $a->getAreaHandle());
				Cache::delete('collection_blocks', $c->getCollectionID() . ':' . $c->getVersionID());
			}
			Cache::delete('block', $this->getBlockID());		
			
			// now we check the scrapbook display
			$db = Loader::db();
			
			
			$rows=$db->getAll('select cID, cvID, arHandle FROM CollectionVersionBlocks cvb inner join btCoreScrapbookDisplay bts on bts.bID = cvb.bID where bts.bOriginalID = ?', array($this->getBlockID()));
			foreach($rows as $row){
				Cache::delete('block', $this->getBlockID() . ':' . intval($row['cID']) . ':' . intval($row['cvID']) . ':' . $row['arHandle'] );
				Cache::delete('block_view_output', $row['cID'] . ':' . $this->getBlockID() . ':' . $row['arHandle']);
				Cache::delete('block', $this->getBlockID());
			}
			
			if ($this->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY && is_object($a)) {
				$rows=$db->getAll('select cID, cvID, arHandle FROM CollectionVersionBlocks cvb inner join btCoreScrapbookDisplay bts on bts.bOriginalID = cvb.bID where bts.bID = ?', array($this->getBlockID()));
				foreach($rows as $row){
					Cache::delete('block', $row['bID'] . ':' . $c->getCollectionID() . ':' . $c->getVersionID() . ':' . $a->getAreaHandle());
					Cache::delete('block_view_output', $c->getCollectionID() . ':' . $row['bID'] . ':' . $a->getAreaHandle());
				}
			}
		}
		*/
	}
	
	public function refreshCacheAll() {
		/*
		$db = Loader::db();
		$rows=$db->getAll( 'SELECT cID, cvID, arHandle FROM CollectionVersionBlocks WHERE bID='.intval($this->getBlockID()) ); 
		foreach($rows as $row){
			Cache::delete('block', $this->getBlockID() . ':' . intval($row['cID']) . ':' . intval($row['cvID']) . ':' . $row['arHandle'] );
			Cache::delete('block_view_output', $row['cID'] . ':' . $this->getBlockID(). ':' . $row['arHandle']);
			Cache::delete('collection_blocks', $row['cID'] . ':' . $row['cvID']);
			Cache::delete('block', $this->getBlockID());
		}
		*/
	}
	
	public function export($node, $exportType = 'full') {
		if (!$this->isAliasOfMasterCollection()) {
			$blockNode = $node->addChild('block');
			$blockNode->addAttribute('type', $this->getBlockTypeHandle());
			$blockNode->addAttribute('name', $this->getBlockName());
			if ($this->getBlockFilename() != '') {
				$blockNode->addAttribute('custom-template', $this->getBlockFilename());
			}
			if (is_object($this->c) && $this->c->isMasterCollection()) {
				$mcBlockID = Loader::helper('validation/identifier')->getString(8);
				ContentExporter::addMasterCollectionBlockID($this, $mcBlockID);
				$blockNode->addAttribute('mc-block-id', $mcBlockID);
			}			

			if ($exportType == 'composer') {
				$db = Loader::db();
				$cbFilename = $db->GetOne('select ccFilename from ComposerContentLayout where bID = ?', array($this->getBlockID()));
				if ($cbFilename) {
					$blockNode->addAttribute("composer-template", $cbFilename);
				}
			}
			
			if ($exportType == 'full') {
				$bc = $this->getInstance();
				$bc->export($blockNode);
			}
		} else {
			$blockNode = $node->addChild('block');
			$blockNode->addAttribute('mc-block-id', ContentExporter::getMasterCollectionTemporaryBlockID($this));			
		}
	}
	
	function updateBlockInformation($data) {
		// this is the function that updates a block's information, like its block filename, and block name
		$db = Loader::db();
		$dh = Loader::helper('date');
		$dt = $dh->getSystemDateTime();
		
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
		
	}

	function updateBlockComposerSettings($data) {
		$db = Loader::db();
		$this->updateBlockInformation($data);
		if ($this->c->isMasterCollection()) {
			$ctID = $this->c->getCollectionTypeID();
			if ($data['bIncludeInComposer']) {
				$displayOrder = $db->GetOne('select max(displayOrder) from ComposerContentLayout where ctID = ?', array($this->c->getCollectionTypeID()));
				if ($displayOrder !== false) {
					if ($displayOrder > 0) { 
						$displayOrder++;
					} else {
						$displayOrder = 1;
					}
				} else {
					$displayOrder = 0;
				}
			
				$db->Replace('ComposerContentLayout', array('bID' => $this->getBlockID(), 'ctID' => $ctID, 'ccFilename' => $data['cbFilename'], 'displayOrder' => $displayOrder), array('bID', 'ctID'), true);
			} else {
				$db->Execute('delete from ComposerContentLayout where ctID = ? and bID = ?', array($ctID, $this->getBlockID()));
			}
		}		
	}


}

/**
 * An Active Record object attached to a particular block. Data is automatically loaded into this object unless the block is too complex.
 *
 * @package Blocks
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Model_BlockRecord extends ADOdb_Active_Record {
		
		public function __construct($tbl = null) {
			if ($tbl) {
				$db = Loader::db();
				$this->_table = $tbl;
				parent::__construct($tbl);
			}
		}
		
	}
