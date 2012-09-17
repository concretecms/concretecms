<?

defined('C5_EXECUTE') or die("Access Denied.");

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
 
	class Concrete5_Model_Collection extends Object {
		
		public $cID;
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

				$nc = $this->cloneVersion($versionComments);
				return $nc;
			}
		}

      public function getNextVersionComments() {
         $cvID = CollectionVersion::getNumericalVersionID($this->getCollectionID(), 'ACTIVE');
         return t("Version %d", $cvID+1);
      }
      

		public function cloneVersion($versionComments) {
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
			
			// duplicate any area styles
			$q = "select csrID, arHandle from CollectionVersionAreaStyles where cID = '$cID' and cvID = '$cvID'";
			$r = $db->query($q);
			while ($row = $r->FetchRow()) {
				$db->Execute('insert into CollectionVersionAreaStyles (cID, cvID, arHandle, csrID) values (?, ?, ?, ?)', array(
					$this->getCollectionID(),
					$nvObj->getVersionID(),
					$row['arHandle'],
					$row['csrID']
				));
			}
			
			// duplicate any area layout joins
			$q = "select * from CollectionVersionAreaLayouts where cID = '$cID' and cvID = '$cvID'";
			$r = $db->query($q);
			while ($row = $r->FetchRow()) {
				$db->Execute('insert into CollectionVersionAreaLayouts (cID, cvID, arHandle, layoutID, areaNameNumber, position) values (?, ?, ?, ?, ?, ?)', array(
					$this->getCollectionID(),
					$nvObj->getVersionID(),
					$row['arHandle'],
					$row['layoutID'],
					$row['areaNameNumber'],
					$row['position']
				));
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
					return $this->vObj->getAttribute($ak->getAttributeKeyHandle());
				} else {
					return $this->vObj->getAttribute($ak);
				}
			}
		}

		// remove the collection attributes for this version of a page
		public function clearCollectionAttributes($retainAKIDs = array()) {
			$db = Loader::db();
			if (count($retainAKIDs) > 0) {
				$cleanAKIDs=array();
				foreach($retainAKIDs as $akID) $cleanAKIDs[]=intval($akID);
				$akIDStr = implode(',', $cleanAKIDs);
				$v2 = array($this->getCollectionID(), $this->getVersionID());
				$db->query("delete from CollectionAttributeValues where cID = ? and cvID = ? and akID not in ({$akIDStr})", $v2);
			} else {
				$v2 = array($this->getCollectionID(), $this->getVersionID());
				$db->query("delete from CollectionAttributeValues where cID = ? and cvID = ?", $v2);
			}
			$this->reindex();
		}
		
		public static function reindexPendingPages() {
			$num = 0;
			$db = Loader::db();
			$r = $db->Execute("select cID from PageSearchIndex where cRequiresReindex = 1");
			while ($row = $r->FetchRow()) { 
				$pc = Page::getByID($row['cID']);
				$pc->reindex($this, true);
				$num++;
			}
			Config::save('DO_PAGE_REINDEX_CHECK', false);
			return $num;		
		}
				
		public function hasLayouts() {
			return $this->cHasLayouts;
		}
		
		public function reindex($index = false, $actuallyDoReindex = true) {
			if ($this->isAlias()) {
				return false;
			}
			if ($actuallyDoReindex || ENABLE_PROGRESSIVE_PAGE_REINDEX == false) { 
				$db = Loader::db();
				
				Loader::model('attribute/categories/collection');
				$attribs = CollectionAttributeKey::getAttributes($this->getCollectionID(), $this->getVersionID(), 'getSearchIndexValue');
		
				$db->Execute('delete from CollectionSearchIndexAttributes where cID = ?', array($this->getCollectionID()));
				$searchableAttributes = array('cID' => $this->getCollectionID());
				$rs = $db->Execute('select * from CollectionSearchIndexAttributes where cID = -1');
				AttributeKey::reindex('CollectionSearchIndexAttributes', $searchableAttributes, $attribs, $rs);
				
				if ($index == false) {
					Loader::library('database_indexed_search');
					$index = new IndexedSearch();
				}
				
				$index->reindexPage($this);
				$db->Replace('PageSearchIndex', array('cID' => $this->getCollectionID(), 'cRequiresReindex' => 0), array('cID'), false);
			} else { 			
				$db = Loader::db();
				Config::save('DO_PAGE_REINDEX_CHECK', true);
				$db->Replace('PageSearchIndex', array('cID' => $this->getCollectionID(), 'cRequiresReindex' => 1), array('cID'), false);
			}
		}
		
		public function getAttributeValueObject($ak, $createIfNotFound = false) {
			$db = Loader::db();
			$av = false;
			$v = array($this->getCollectionID(), $this->getVersionID(), $ak->getAttributeKeyID());
			$avID = $db->GetOne("select avID from CollectionAttributeValues where cID = ? and cvID = ? and akID = ?", $v);
			if ($avID > 0) {
				$av = CollectionAttributeValue::getByID($avID);
				if (is_object($av)) {
					$av->setCollection($this);
					$av->setAttributeKey($ak);
				}
			}
			
			if ($createIfNotFound) {
				$cnt = 0;
			
				// Is this avID in use ?
				if (is_object($av)) {
					$cnt = $db->GetOne("select count(avID) from CollectionAttributeValues where avID = ?", $av->getAttributeValueID());
				}
				
				if ((!is_object($av)) || ($cnt > 1)) {
					$av = $ak->addAttributeValue();
				}
			}
			
			return $av;
		}
		
		public function setAttribute($ak, $value) {
			Loader::model('attribute/categories/collection');
			if (!is_object($ak)) {
				$ak = CollectionAttributeKey::getByHandle($ak);
			}
			$ak->setAttribute($this, $value);
			unset($ak);
			$this->refreshCache();
			$this->reindex();
		}

		public function clearAttribute($ak) {
			$db = Loader::db();
			$cav = $this->getAttributeValueObject($ak);
			if (is_object($cav)) {
				$cav->delete();
			}
			$this->refreshCache();
			$this->reindex();
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
			$this->setAttribute($ak, $value);
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
		
		function getCollectionDateLastModified($mask = null, $type="system") {
			if(ENABLE_USER_TIMEZONES && $type == 'user') {
				$dh = Loader::helper('date');
				$cDateModified = $dh->getLocalDateTime($this->cDateModified);
			} else {
				$cDateModified = $this->cDateModified;
			}
			if ($mask == null) {
				return $cDateModified;
			} else {
				return date($mask, strtotime($cDateModified));
			}
		}

		function getVersionObject() {
			return $this->vObj;
		}

		function getCollectionHandle() {
			return $this->cHandle;
		}

		function getCollectionDateAdded($mask = null,$type = 'system') {
			if(ENABLE_USER_TIMEZONES && $type == 'user') {
				$dh = Loader::helper('date');
				$cDateAdded = $dh->getLocalDateTime($this->cDateAdded);
			} else {
				$cDateAdded = $this->cDateAdded;
			}
			
			if ($mask == null) {
				return $cDateAdded;
			} else {
				return date($mask, strtotime($cDateAdded));
			}
		}

		function getVersionID() {
			// shortcut
			return $this->vObj->cvID;
		}
		
	public function __destruct() {
		unset($this->vObj);
	}

	function getCollectionAreaDisplayOrder($arHandle, $ignoreVersions = false) {
		// this function queries CollectionBlocks to grab the highest displayOrder value, then increments it, and returns
		// this is used to add new blocks to existing Pages/areas

		$db = Loader::db();
		$cID = $this->cID;
		$cvID = $this->vObj->cvID;
		if ($ignoreVersions) {
			$q = "select max(cbDisplayOrder) as cbdis from CollectionVersionBlocks where cID = ? and arHandle = ?";
			$v = array($cID, $arHandle);
		} else {
			$q = "select max(cbDisplayOrder) as cbdis from CollectionVersionBlocks where cID = ? and cvID = ? and arHandle = ?";
			$v = array($cID, $cvID, $arHandle);
		}
		$r = $db->query($q, $v);
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
	
	/** 
	 * Retrieves all custom style rules that should be inserted into the header on a page, whether they are defined in areas
	 * or blocks
	 */
	public function outputCustomStyleHeaderItems($return = false) {	
		
		$db = Loader::db();
		$csrs = array();
		$txt = Loader::helper('text');
		$r1 = $db->GetAll('select bID, arHandle, csrID from CollectionVersionBlockStyles where cID = ? and cvID = ? and csrID > 0', array($this->getCollectionID(), $this->getVersionID()));
		$r2 = $db->GetAll('select arHandle, csrID from CollectionVersionAreaStyles where cID = ? and cvID = ? and csrID > 0', array($this->getCollectionID(), $this->getVersionID()));
		foreach($r1 as $r) {
			$csrID = $r['csrID'];
			$arHandle = $txt->filterNonAlphaNum($r['arHandle']);
			$bID = $r['bID'];
			$obj = CustomStyleRule::getByID($csrID);
			if (is_object($obj)) {
				$obj->setCustomStyleNameSpace('blockStyle' . $bID . $arHandle);
				$csrs[] = $obj;
			}
		}
		foreach($r2 as $r) {
			$csrID = $r['csrID'];
			$arHandle = $txt->filterNonAlphaNum($r['arHandle']);
			$obj = CustomStyleRule::getByID($csrID);
			if (is_object($obj)) {
				$obj->setCustomStyleNameSpace('areaStyle' . $arHandle);
				$csrs[] = $obj;
			}
		}
		
		// grab all the header block style rules for items in global areas on this page
		$rs = $db->GetCol('select arHandle from Areas where arIsGlobal = 1 and cID = ?', array($this->getCollectionID()));
		if (count($rs) > 0) {
			$pcp = new Permissions($this);
			foreach($rs as $garHandle) {
				if ($pcp->canViewPageVersions()) {
					$s = Stack::getByName($garHandle, 'RECENT');
				} else {
					$s = Stack::getByName($garHandle, 'ACTIVE');
				}
				if (is_object($s)) {
					$rs1 = $db->GetAll('select bID, csrID, arHandle from CollectionVersionBlockStyles where cID = ? and cvID = ? and csrID > 0', array($s->getCollectionID(), $s->getVersionID()));
					foreach($rs1 as $r) {
						$csrID = $r['csrID'];
						$arHandle = $txt->filterNonAlphaNum($r['arHandle']);
						$bID = $r['bID'];
						$obj = CustomStyleRule::getByID($csrID);
						if (is_object($obj)) {
							$obj->setCustomStyleNameSpace('blockStyle' . $bID . $arHandle);
							$csrs[] = $obj;
						}
					}
				}
			}
		}
		//get the header style rules
		$styleHeader = ''; 
		foreach($csrs as $st) { 
			if ($st->getCustomStyleRuleCSSID(true)) { 
				$styleHeader .= '#'.$st->getCustomStyleRuleCSSID(1).' {'. $st->getCustomStyleRuleText(). "} \r\n";  
			}
		} 		
		  
		$r3 = $db->GetAll('select l.layoutID, l.spacing, arHandle, areaNameNumber from CollectionVersionAreaLayouts cval LEFT JOIN Layouts AS l ON  cval.layoutID=l.layoutID WHERE cval.cID = ? and cval.cvID = ?', array($this->getCollectionID(), $this->getVersionID()));
		foreach($r3 as $data){  
			if(!intval($data['spacing'])) continue; 
			$layoutIDVal = strtolower('ccm-layout-'.TextHelper::camelcase($data['arHandle']).'-'.$data['layoutID'] . '-'. $data['areaNameNumber']);
			$layoutStyleRules='#' . $layoutIDVal . ' .ccm-layout-col-spacing { margin:0px '.ceil(floatval($data['spacing'])/2).'px }';
			$styleHeader .= $layoutStyleRules . " \r\n";  
		}  
		
		if(strlen(trim($styleHeader))) {
			if ($return == true) {
				return $styleHeader;
			} else {
				$v = View::getInstance();
				$v->addHeaderItem("<style type=\"text/css\"> \r\n".$styleHeader.'</style>', 'VIEW');
			}
		}
	} 

	public function getAreaCustomStyleRule($area) {
		$db = Loader::db();
		
		$csrID = $this->vObj->customAreaStyles[$area->getAreaHandle()];
		
		if ($csrID > 0) {
			$txt = Loader::helper('text');
			Loader::model('custom_style');
			$arHandle = $txt->filterNonAlphaNum($area->getAreaHandle());			
			$csr = CustomStyleRule::getByID($csrID);
			if (is_object($csr)) {
				$csr->setCustomStyleNameSpace('areaStyle' . $arHandle);
				return $csr;
			}
		}
	}

	public function resetAreaCustomStyle($area) {
		$db = Loader::db();
		$db->Execute('delete from CollectionVersionAreaStyles where cID = ? and cvID = ? and arHandle = ?', array(
			$this->getCollectionID(),
			$this->getVersionID(),
			$area->getAreaHandle()
		));
		$this->refreshCache();
	}
	
	public function setAreaCustomStyle($area, $csr) {
		$db = Loader::db();
		$db->Replace('CollectionVersionAreaStyles', 
			array('cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID(), 'arHandle' => $area->getAreaHandle(), 'csrID' => $csr->getCustomStyleRuleID()),
			array('cID', 'cvID', 'arHandle'), true
		);
		$this->refreshCache();
	}
	
	
	public function addAreaLayout($area, $layout, $addToPosition='bottom' ) {  
		$db = Loader::db();
		
		//get max layout name number, for fixed autonaming of layouts 
		$vals = array( intval($this->cID), $this->getVersionID(), $area->getAreaHandle() );
		$sql = 'SELECT MAX(areaNameNumber) FROM CollectionVersionAreaLayouts WHERE cID=? AND cvID=? AND arHandle=?';
		$nextNumber = intval($db->getOne($sql,$vals))+1;  
		
		if($addToPosition=='top'){  
			$position=-1; 
		}else{ 
		
			//does the main area already have blocks in it? 
			//$areaBlocks = $area->getAreaBlocksArray($this); 
			$areaBlocks = $this->getBlocks( $area->getAreaHandle() );
			
			//then copy those blocks from that area into a newly created 1x1 layout, so it can be above out new layout 
			if( count($areaBlocks) ){  
			
				//creat new 1x1 layout to hold existing parent area blocks
				//Loader::model('layout'); 
				$placeHolderLayout = new Layout( array('rows'=>1,'columns'=>1) );  
				$placeHolderLayout->save( $this );  
				$vals = array( $this->getCollectionID(), $this->getVersionID(), $area->getAreaHandle(), $placeHolderLayout->getLayoutID(), $nextNumber, 10000 );
				$sql = 'INSERT INTO CollectionVersionAreaLayouts ( cID, cvID, arHandle, layoutID, areaNameNumber, position ) values (?, ?, ?, ?, ?, ?)';
				$db->query($sql,$vals);	 
				
				//add parent area blocks to this new layout
				$placeHolderLayout->setAreaObj($area);
				$placeHolderLayout->setAreaNameNumber($nextNumber);   
				$placeHolderLayoutAreaHandle = $placeHolderLayout->getCellAreaHandle(1);
				//foreach($areaBlocks as $b){ 
					//$newBlock=$b->duplicate($this); 
					//$newBlock->move($this, $placeHolderLayoutArea); 
					//$newBlock->refreshCacheAll(); 
					//$b->delete();
					//$b->move($this, $placeHolderLayoutArea); 
					//$b->refreshCacheAll(); 
					
				//} 
				$v = array( $placeHolderLayoutAreaHandle, $this->getCollectionID(), $this->getVersionID(), $area->getAreaHandle() );
				$db->Execute('update CollectionVersionBlocks set arHandle=? WHERE cID=? AND cvID=? AND arHandle=?', $v);				
				
				$nextNumber++; 
			}
			
			$position=10001; 
		}
		
		
		$vals = array( $this->getCollectionID(), $this->getVersionID(), $area->getAreaHandle(), $layout->getLayoutID(), $nextNumber, $position );
		$sql = 'INSERT INTO CollectionVersionAreaLayouts ( cID, cvID, arHandle, layoutID, areaNameNumber, position ) values (?, ?, ?, ?, ?, ?)';
		$db->query($sql,$vals);	
		
		$layout->setAreaNameNumber($nextNumber);
		
		$this->refreshCache();
	}
	
	public function relateVersionEdits($oc) {
		$db = Loader::db();
		$v = array(
			$this->getCollectionID(),
			$this->getVersionID(),
			$oc->getCollectionID(),
			$oc->getVersionID()
		);
		$r = $db->GetOne('select count(*) from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?', $v);
		if ($r > 0) {
			return false;
		} else {
			$db->Execute('insert into CollectionVersionRelatedEdits (cID, cvID, cRelationID, cvRelationID) values (?, ?, ?, ?)', $v);
		}
	}
	
	public function updateAreaLayoutId( $cvalID=0, $newLayoutId=0){ 
		$db = Loader::db();
		//$vals = array( $newLayoutId, $oldLayoutId, $this->getCollectionID(), $this->getVersionID(), $area->getAreaHandle() );
		//$sql = 'UPDATE CollectionVersionAreaLayouts SET layoutID=? WHERE layoutID=? AND cID=? AND  cvID=? AND arHandle=?'; 
		$vals = array( $newLayoutId, $cvalID ); 
		$sql = 'UPDATE CollectionVersionAreaLayouts SET layoutID=? WHERE cvalID=?'; 
		$db->query($sql,$vals);	 
		
		$this->refreshCache();		
	}	
	
	
	public function deleteAreaLayout($area, $layout, $deleteBlocks=0){
		$db = Loader::db();
		$vals = array( $this->getCollectionID(), $this->getVersionID(), $area->getAreaHandle(), $layout->getLayoutID() );
		$db->Execute('delete from CollectionVersionAreaLayouts WHERE cID = ? AND cvID = ? AND arHandle = ? AND layoutID = ? LIMIT 1', $vals ); 
		
		//also delete this layouts blocks
		$layout->setAreaObj($area);
		//we'll try to grab more areas than necessary, just incase the layout size had been reduced at some point. 
		$maxCell = $layout->getMaxCellNumber()+20; 
		for( $i=1; $i<=$maxCell; $i++ ){ 
			if($deleteBlocks) $layout->deleteCellsBlocks($this,$i);  
			else $layout->moveCellsBlocksToParent($this,$i);  
		}
		
		Layout::cleanupOrphans();	 
			 
		$this->refreshCache();
	} 

	function getCollectionTypeID() {
		return false;
	}
	
	
	public function rescanDisplayOrder($areaName) {
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

		/**
		 * @param int $cID
		 * @param mixed $version 'RECENT'|'ACTIVE'|version id  
		 * @return Collection
		 */
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
		
		/* This function is slightly misnamed: it should be getOrCreateByHandle($handle) but I wanted to keep it brief 
		 * @param string $handle
		 * @return Collection
		 */
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
			Cache::delete('page_active', $this->getCollectionID());
			Cache::delete('page_recent', $this->getCollectionID());
			if ($this->getCollectionTypeHandle() == STACKS_PAGE_TYPE) {
				Cache::delete('stack_active', $this->getCollectionID());
				Cache::delete('stack_recent', $this->getCollectionID());
			}
			if ($this instanceof ComposerPage) {
				Cache::delete('composerpage_recent', $this->getCollectionID()  );
				Cache::delete('composerpage_active', $this->getCollectionID()  );
			}
			Cache::delete('page_path', $this->getCollectionID());
			Cache::delete('request_path_page', $this->getCollectionPath()  );
			Cache::delete('page_id_from_path', $this->getCollectionPath());
			Cache::delete('page_content', $this->getCollectionID());
			$vo = $this->getVersionObject();
			if (is_object($vo)) {
				Cache::delete('collection_blocks', $this->getCollectionID() . ':' . $vo->getVersionID());
			}
			$db = Loader::db();
			$areas = $db->GetCol('select arHandle from Areas where cID = ?', array($this->getCollectionID()));
			foreach($areas as $arHandle) {
				Cache::delete('area', $this->getCollectionID() . ':' . $arHandle);
			}
		}
		
		public function getGlobalBlocks() {
			$db = Loader::db();
			$v = array( Stack::ST_TYPE_GLOBAL_AREA );
			$rs = $db->GetCol('select stName from Stacks where Stacks.stType = ?', $v );
			$blocks = array();
			if (count($rs) > 0) {
				$pcp = new Permissions($this);
				foreach($rs as $garHandle) {
					if ($pcp->canViewPageVersions()) {
						$s = Stack::getByName($garHandle, 'RECENT');
					} else {
						$s = Stack::getByName($garHandle, 'ACTIVE');
					}
					if (is_object($s)) {
						$blocksTmp = $s->getBlocks(STACKS_AREA_NAME);
						$blocks = array_merge($blocks, $blocksTmp);
					}
				}
			}
			
			return $blocks;
		}
		
		public function getBlocks($arHandle = false) {
			
			$v = array($this->getCollectionID(), $this->getVersionID());
			$blocks = array();

			$blockIDs = Cache::get('collection_blocks', $this->getCollectionID() . ':' . $this->getVersionID());		
			
			if (!is_array($blockIDs)) {
				$db = Loader::db();
				$q = "select Blocks.bID, CollectionVersionBlocks.arHandle from CollectionVersionBlocks inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID) inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where CollectionVersionBlocks.cID = ? and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) order by CollectionVersionBlocks.cbDisplayOrder asc";
				$r = $db->GetAll($q, $v);
				$blockIDs = array();
				if (is_array($r)) {
					foreach($r as $bl) {
						$blockIDs[strtolower($bl['arHandle'])][] = $bl;
					}
				}
				Cache::set('collection_blocks', $this->getCollectionID() . ':' . $this->getVersionID(), $blockIDs);
			}
			
			if ($arHandle != false) {
				$blockIDsTmp = $blockIDs[strtolower($arHandle)];
				$blockIDs = $blockIDsTmp;
			} else {
			
				$blockIDsTmp = $blockIDs;
				$blockIDs = array();
				foreach($blockIDsTmp as $arHandle => $row) {
					foreach($row as $brow) {
						if (!in_array($brow, $blockIDs)) {
							$blockIDs[] = $brow;
						}
					}
				}
			}		
			
			$blocks = array();
			if (is_array($blockIDs)) {
				foreach($blockIDs as $row) {
					$ab = Block::getByID($row['bID'], $this, $row['arHandle']);
					if (is_object($ab)) {
						$blocks[] = $ab;
					}
				}
			}
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

			Cache::delete('collection_blocks', $cID . ':' . $vObj->getVersionID());
			
			return Block::getByID($nb->getBlockID(), $this, $a);
		}
		
		public function add($data) {
			$db = Loader::db();
			$dh = Loader::helper('date');
			$cDate = $dh->getSystemDateTime(); 
			$cDatePublic = ($data['cDatePublic']) ? $data['cDatePublic'] : $cDate;
			
			if (isset($data['cID'])) {
				$res = $db->query("insert into Collections (cID, cHandle, cDateAdded, cDateModified) values (?, ?, ?, ?)", array($data['cID'], $data['handle'], $cDate, $cDate));
				$newCID = $data['cID'];
			} else {
				$res = $db->query("insert into Collections (cHandle, cDateAdded, cDateModified) values (?, ?, ?)", array($data['handle'], $cDate, $cDate));
				$newCID = $db->Insert_ID();
			}
			
			$cvIsApproved = (isset($data['cvIsApproved']) && $data['cvIsApproved'] == 0) ? 0 : 1;
			$cvIsNew = 1;
			if ($cvIsApproved) {
				$cvIsNew = 0;
			}
			$data['name'] = Loader::helper('text')->sanitize($data['name']);
			if (is_object($this)) {
				$ptID = $this->getCollectionThemeID();
			} else {
				$ptID = 0;
			}
			$ctID = $data['ctID'];
			if (!$ctID) {
				$ctID = 0;
			}


			if ($res) {
				// now we add a pending version to the collectionversions table
				$v2 = array($newCID, 1, $ctID, $data['name'], $data['handle'], $data['cDescription'], $cDatePublic, $cDate, VERSION_INITIAL_COMMENT, $data['uID'], $cvIsApproved, $cvIsNew, $ptID);
				$q2 = "insert into CollectionVersions (cID, cvID, ctID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsApproved, cvIsNew, ptID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
			$cDateModified = $dh->getSystemDateTime();

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

				$q = "delete from CollectionSearchIndexAttributes where cID = {$cID}";
				$db->query($q);

			}
		}
		
		public function duplicate() {
			$db = Loader::db();
			$dh = Loader::helper('date');
			$cDate = $dh->getSystemDateTime();
			
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
					$vv = array($newCID, $row['cvID'], $row['ctID'], $row['cvName'], $row['cvHandle'], $row['cvDescription'], $row['cvDatePublic'], $cDate, $row['cvComments'], $row['cvAuthorUID'], $row['cvIsApproved'], $row['ptID']);
					$qv = "insert into CollectionVersions (cID, cvID, ctID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsApproved, ptID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$db->query($qv, $vv);
				}
				
				// duplicate layout records 
				$ql = "select * from CollectionVersionAreaLayouts where cID = '{$this->cID}' order by cvalID asc";
				$rl = $db->query($ql);
				while ($row = $rl->fetchRow()) { 
					$vl = array( $newCID, $row['cvID'], $row['arHandle'], $row['layoutID'], $row['position'], $row['areaNameNumber'] );
					$ql = "insert into CollectionVersionAreaLayouts (cID, cvID, arHandle, layoutID, position, areaNameNumber) values ( ?, ?, ?, ?, ?, ?)";
					$db->query($ql, $vl);
				}				

				$ql = "select * from CollectionVersionBlockStyles where cID = '{$this->cID}'";
				$rl = $db->query($ql);
				while ($row = $rl->fetchRow()) { 
					$vl = array( $newCID, $row['cvID'], $row['bID'], $row['arHandle'], $row['csrID'] );
					$ql = "insert into CollectionVersionBlockStyles (cID, cvID, bID, arHandle, csrID) values (?, ?, ?, ?, ?)";
					$db->query($ql, $vl);
				}
				$ql = "select * from CollectionVersionAreaStyles where cID = '{$this->cID}'";
				$rl = $db->query($ql);
				while ($row = $rl->fetchRow()) { 
					$vl = array( $newCID, $row['cvID'], $row['arHandle'], $row['csrID'] );
					$ql = "insert into CollectionVersionAreaStyles (cID, cvID, arHandle, csrID) values (?, ?, ?, ?)";
					$db->query($ql, $vl);
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
						$q2 = "select paID, pkID from BlockPermissionAssignments where cID = '{$this->cID}' and bID = '{$row['bID']}' and cvID = '{$row['cvID']}'";
						$r2 = $db->query($q2);
						while ($row2 = $r2->fetchRow()) {
							$db->Replace('BlockPermissionAssignments', 
								array('cID' => $newCID, 'cvID' => $row['cvID'], 'bID' => $row['bID'], 'paID' => $row2['paID'], 'pkID' => $row2['pkID']),
								array('cID', 'cvID', 'bID', 'paID', 'pkID'), true);
						}
					}
				}
	
				// duplicate any attributes belonging to the collection
				
				$v = array($this->getCollectionID());
				$q = "select akID, cvID, avID from CollectionAttributeValues where cID = ?";
				$r = $db->query($q, $v);
				while ($row = $r->fetchRow()) {
					$v2 = array($row['akID'], $row['cvID'], $row['avID'], $newCID);
					$db->query("insert into CollectionAttributeValues (akID, cvID, avID, cID) values (?, ?, ?, ?)", $v2);
				}			
				return Collection::getByID($newCID);
			}
			
		}
		

	}