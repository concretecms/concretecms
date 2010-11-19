<?php 

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Pages
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An area object is used within templates to mark certain portions of pages as editable and containers of dynamic content
 *
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Area extends Object {

	var $cID, $arID, $arHandle;
	var $c;

	/* area-specific attributes */

	var $maximumBlocks = -1; // limits the number of blocks in the area
	var $customTemplateArray = array(); // sets a custom template for all blocks in the area
	var $firstRunBlockTypeHandle; // block type handle for the block to automatically activate on first_run
	var $ratingThreshold = 0; // if set higher, any blocks that aren't rated high enough aren't seen (unless you have sufficient privs)
	var $showControls = true;
	var $attributes = array();

	var $enclosingStart = '';
	var $enclosingEnd = '';
	
	/* run-time variables */

	var $totalBlocks = 0; // the number of blocks currently rendered in the area
	var $areaBlocksArray; // not an array actually until it's set

	/*
		The constructor is used primarily on pages, to make an Area. We actually use Collection::getArea() when we want to interact with a fully
		qualified Area object
	*/

	function Area($arHandle) {
		$this->arHandle = $arHandle;
		$v = View::getInstance();
		if (!$v->editingEnabled()) {
			$this->showControls = false;
		}
	}

	function getCollectionID() {return $this->cID;}
	function getAreaCollectionObject() {return $this->c;}
	function getAreaID() {return $this->arID;}
	function getAreaHandle() {return $this->arHandle;}
	function getCustomTemplates() {return $this->customTemplateArray;}
	function setCustomTemplate($btHandle, $temp) {$this->customTemplateArray[$btHandle] = $temp;}
	
	/** 
	 * Returns the total number of blocks in an area. 
	 * @param Page $c must be passed if the display() method has not been run on the area object yet.
	 */
	function getTotalBlocksInArea($c = false) {
		if (!is_array($this->areaBlocksArray) && is_object($c)) {
			$this->getAreaBlocksArray($c);
		}
		return $this->totalBlocks; 
		
	}
	function overrideCollectionPermissions() {return $this->arOverrideCollectionPermissions; }
	function getAreaCollectionInheritID() {return $this->arInheritPermissionsFromAreaOnCID;}
	
	/** 
	 * Sets the total number of blocks an area allows. Does not limit by type.
	 */
	public function setBlockLimit($num) {
		$this->maximumBlocks = $num;
	}
	
	function setAttribute($attr, $val) {
		$this->attributes[$attr] = $val;
	}
	
	function getAttribute($attr) {
		return $this->attributes[$attr];
	}
	
	function disableControls() {
		$this->showControls = false;
	}

	function areaAcceptsBlocks() {
		return (($this->maximumBlocks > $this->totalBlocks) || ($this->maximumBlocks == -1));
	}

	function getMaximumBlocks() {return $this->maximumBlocks;}
	
	function getAreaUpdateAction($task = 'update', $alternateHandler = null) {
		$valt = Loader::helper('validation/token');
		$token = '&' . $valt->getParameter();
		$step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';
		$c = $this->getAreaCollectionObject();
		if ($alternateHandler) {
			$str = $alternateHandler . "?atask={$task}&cID=" . $c->getCollectionID() . "&arHandle=" . $this->getAreaHandle() . $step . $token;
		} else {
			$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?atask=" . $task . "&cID=" . $c->getCollectionID() . "&arHandle=" . $this->getAreaHandle() . $step . $token;
		}
		return $str;
	}

	function get(&$c, $arHandle) {
		
		$ca = new Cache();
		$a = Cache::get('area', $c->getCollectionID() . ':' . $arHandle);
		if ($a instanceof Area) {
			return $a;
		}
		
		$db = Loader::db();
		// First, we verify that this is a legitimate area
		$v = array($c->getCollectionID(), $arHandle);
		$q = "select arID, arOverrideCollectionPermissions, arInheritPermissionsFromAreaOnCID from Areas where cID = ? and arHandle = ?";
		$arRow = $db->getRow($q, $v);
		if ($arRow['arID'] > 0) {
			$area = new Area($arHandle);

			$area->arID = $arRow['arID'];
			$area->arOverrideCollectionPermissions = $arRow['arOverrideCollectionPermissions'];
			$area->arInheritPermissionsFromAreaOnCID = $arRow['arInheritPermissionsFromAreaOnCID'];
			$area->cID = $c->getCollectionID();
			$area->c = &$c;
			
			Cache::set('area', $c->getCollectionID() . ':' . $arHandle, $area);
			
			return $area;
		}
	}

	function getOrCreate(&$c, $arHandle) {

		/*
			different than get(), getOrCreate() is called by the templates. If no area record exists for the
			permissions cID / handle combination, we create one. This is to make our lives easier
		*/

		$area = Area::get($c, $arHandle);
		if (is_object($area)) {
			return $area;
		}

		// I'm pretty sure this next line is meaningless
		// because this will ALWAYS be true.
		// $cID = ($c->getCollectionInheritance()) ? $c->getCollectionID() : $c->getParentPermissionsCollectionID();
		$cID = $c->getCollectionID();
		$v = array($cID, $arHandle);
		$q = "insert into Areas (cID, arHandle) values (?, ?)";
		$db = Loader::db();
		$db->query($q, $v);

		$area = Area::get($c, $arHandle); // we're assuming the insert succeeded
		$area->rescanAreaPermissionsChain();
		return $area;

	}

	function getAreaBlocksArray(&$c) {
		if (is_array($this->areaBlocksArray)) {
			return $this->areaBlocksArray;
		}

		$this->cID = $c->getCollectionID();
		$this->c = $c;
		$this->areaBlocksArray = array();
		
		$cp = new Permissions($c);
		
		$blocks = $c->getBlocks($this->arHandle);
		foreach($blocks as $ab) {
			$ab->setBlockAreaObject($this);
			$this->areaBlocksArray[] = $ab;
			$this->totalBlocks++;
		}
		return $this->areaBlocksArray;
	}

	function getAddBlockTypes(&$c, &$ap) {
		if ($ap->canAddBlocks()) {
			$bt = new BlockTypeList($ap->addBlockTypes);
		} else {
			$bt = false;
		}
		return $bt;
	}
	
	public function getHandleList() {
		$db = Loader::db();
		$r = $db->Execute('select distinct arHandle from Areas order by arHandle asc');
		$handles = array();
		while ($row = $r->FetchRow()) {
			$handles[] = $row['arHandle'];
		}
		$r->Free();
		unset($r);
		unset($db);
		return $handles;
	}
	
	function revertToPagePermissions() {
		// this function removes all permissions records for a particular area on this page
		// and sets it to inherit from the page above
		// this function will also need to ensure that pages below it do the same
		
		$db = Loader::db();
		$v = array($this->getAreaHandle(), $this->getCollectionID());
		$db->query("delete from AreaGroups where arHandle = ? and cID = ?", $v);
		$db->query("delete from AreaGroupBlockTypes where arHandle = ? and cID = ?", $v);
		$db->query("update Areas set arOverrideCollectionPermissions = 0 where arID = ?", array($this->getAreaID()));
		
		// now we set rescan this area to determine where it -should- be inheriting from
		$this->arOverrideCollectionPermissions = false;
		$this->rescanAreaPermissionsChain();
		
		$areac = $this->getAreaCollectionObject();
		if ($areac->isMasterCollection()) {
			$this->rescanSubAreaPermissionsMasterCollection($areac);
		} else if ($areac->overrideTemplatePermissions()) {
			// now we scan sub areas
			$this->rescanSubAreaPermissions();
		}
		
		$ca = new Cache();
		$a = Cache::delete('area', $this->getCollectionID() . ':' . $this->getAreaHandle());
	}
	
	public function __destruct() {
		unset($this->c);
	}
	
	function rescanAreaPermissionsChain() {
		// works on the current area object to ensure that inheritance makes sense
		// and that areas actually inherit their permissions correctly up the chain
		// of collections. This needs to be run any time a page is moved, deleted, etc..
		$db = Loader::db();
		if ($this->overrideCollectionPermissions()) {
			return false;
		}
		// first, we obtain the inheritance of permissions for this particular collection
		$areac = $this->getAreaCollectionObject();
		if (is_a($areac, 'Page')) {
			if ($areac->getCollectionInheritance() == 'PARENT') {				
				
				$cIDToCheck = $areac->getCollectionParentID();
				// first, we temporarily set the arInheritPermissionsFromAreaOnCID to whatever the arInheritPermissionsFromAreaOnCID is set to
				// in the immediate parent collection
				$arInheritPermissionsFromAreaOnCID = $db->getOne("select a.arInheritPermissionsFromAreaOnCID from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?", array($cIDToCheck, $this->getAreaHandle()));
				$db->query("update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?", array($arInheritPermissionsFromAreaOnCID, $this->getAreaID()));
				
				// now we do the recursive rescan to see if any areas themselves override collection permissions

				while ($cIDToCheck > 0) {
					$row = $db->getRow("select c.cParentID, c.cID, a.arHandle, a.arOverrideCollectionPermissions, a.arID from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?", array($cIDToCheck, $this->getAreaHandle()));
					if ($row['arOverrideCollectionPermissions'] == 1) {
						break;
					} else {
						$cIDToCheck = $row['cParentID'];
					}
				}
				
				if (is_array($row)) {
					if ($row['arOverrideCollectionPermissions']) {
						// then that means we have successfully found a parent area record that we can inherit from. So we set
						// out current area to inherit from that COLLECTION ID (not area ID - from the collection ID)
						$db->query("update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?", array($row['cID'], $this->getAreaID()));
						$this->arInheritPermissionsFromAreaOnCID = $row['cID']; 
					}
				}
			} else if ($areac->getCollectionInheritance() == 'TEMPLATE') {
				 // we grab an area on the master collection (if it exists)
				$doOverride = $db->getOne("select arOverrideCollectionPermissions from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?", array($areac->getPermissionsCollectionID(), $this->getAreaHandle()));
				if ($doOverride) {
					$db->query("update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?", array($areac->getPermissionsCollectionID(), $this->getAreaID()));
					$this->arInheritPermissionsFromAreaOnCID = $areac->getPermissionsCollectionID();
				}			
			}
		}
		
		Cache::delete('area', $this->getCollectionID() . ':' . $this->getAreaHandle());
	}
	
	function rescanSubAreaPermissions($cIDToCheck = null) {
		// works a lot like rescanAreaPermissionsChain() but it works down. This is typically only 
		// called when we update an area to have specific permissions, and all areas that are on pagesbelow it with the same 
		// handle, etc... should now inherit from it.
		$db = Loader::db();
		if (!$cIDToCheck) {
			$cIDToCheck = $this->getCollectionID();
		}
		
		$v = array($this->getAreaHandle(), 'PARENT', $cIDToCheck);
		$r = $db->query("select Areas.arID, Areas.cID from Areas inner join Pages on (Areas.cID = Pages.cID) where Areas.arHandle = ? and cInheritPermissionsFrom = ? and arOverrideCollectionPermissions = 0 and cParentID = ?", $v);
		while ($row = $r->fetchRow()) {
			// these are all the areas we need to update.
			$db->query("update Areas set arInheritPermissionsFromAreaOnCID = " . $this->getAreaCollectionInheritID() . " where arID = " . $row['arID']);
			$this->rescanSubAreaPermissions($row['cID']);
		}
		
	}
	
	function rescanSubAreaPermissionsMasterCollection($masterCollection) {
		// like above, but for those who have setup their pages to inherit master collection permissions
		// this might make more sense in the collection class, but I'm putting it here
		if (!$masterCollection->isMasterCollection()) {
			return false;
		}
		
		// if we're not overriding permissions on the master collection then we set the ID to zero. If we are, then we set it to our own ID
		$toSetCID = ($this->overrideCollectionPermissions()) ? $masterCollection->getCollectionID() : 0;		
		
		$db = Loader::db();
		$v = array($this->getAreaHandle(), 'TEMPLATE', $masterCollection->getCollectionID());
		$db->query("update Areas, Pages set Areas.arInheritPermissionsFromAreaOnCID = " . $toSetCID . " where Areas.cID = Pages.cID and Areas.arHandle = ? and cInheritPermissionsFrom = ? and arOverrideCollectionPermissions = 0 and cInheritPermissionsFromCID = ?", $v);
	}
	
	function display(&$c, $alternateBlockArray = null) {

		if(!intval($c->cID)){
			//Invalid Collection
			return false;
		}
		
		$currentPage = Page::getCurrentPage();
		$ourArea = Area::getOrCreate($c, $this->arHandle);
		if (count($this->customTemplateArray) > 0) {
			$ourArea->customTemplateArray = $this->customTemplateArray;
		}
		if (count($this->attributes) > 0) {
			$ourArea->attributes = $this->attributes;
		}
		if ($this->maximumBlocks > -1) {
			$ourArea->maximumBlocks = $this->maximumBlocks;
		}
		$ap = new Permissions($ourArea);
		$blocksToDisplay = ($alternateBlockArray) ? $alternateBlockArray : $ourArea->getAreaBlocksArray($c, $ap);
		$this->totalBlocks = $ourArea->getTotalBlocksInArea();
		$u = new User();
		
		$bv = new BlockView();
		
		// now, we iterate through these block groups (which are actually arrays of block objects), and display them on the page
		
		if (($this->showControls) && ($c->isEditMode() && ($ap->canAddBlocks() || $u->isSuperUser()))) {
			$bv->renderElement('block_area_header', array('a' => $ourArea));	
		}

		$bv->renderElement('block_area_header_view', array('a' => $ourArea));	

		//display layouts tied to this area 
		//Might need to move this to a better position  
		$areaLayouts = $this->getAreaLayouts($c);
		if(is_array($areaLayouts) && count($areaLayouts)){ 
			foreach($areaLayouts as $layout){
				$layout->display($c,$this);  
			}
			if($this->showControls && ($c->isArrangeMode() || $c->isEditMode())) {
				echo '<div class="ccm-layouts-block-arrange-placeholder ccm-block-arrange"></div>';
			}
		}


		foreach ($blocksToDisplay as $b) {
			$bv = new BlockView();
			$bv->setAreaObject($ourArea); 
			
			// this is useful for rendering areas from one page
			// onto the next and including interactive elements
			if ($currentPage->getCollectionID() != $c->getCollectionID()) {
				$b->setBlockActionCollectionID($c->getCollectionID());
			}
			$p = new Permissions($b);
			if (($p->canWrite() || $p->canDeleteBlock()) && $c->isEditMode() && $this->showControls) {
				$includeEditStrip = true;
			}

			if ($p->canRead()) {
				if (!$c->isEditMode()) {
					echo $this->enclosingStart;
				}
				if ($includeEditStrip) {
					$bv->renderElement('block_controls', array(
						'a' => $ourArea,
						'b' => $b,
						'p' => $p
					));
					$bv->renderElement('block_header', array(
						'a' => $ourArea,
						'b' => $b,
						'p' => $p
					));
				}

				$bv->render($b);
				if ($includeEditStrip) {
					$bv->renderElement('block_footer');
				}
				if (!$c->isEditMode()) {
					echo $this->enclosingEnd;
				}
			}
		}

		$bv->renderElement('block_area_footer_view', array('a' => $ourArea));	

		if (($this->showControls) && ($c->isEditMode() && ($ap->canAddBlocks() || $u->isSuperUser()))) {
			$bv->renderElement('block_area_footer', array('a' => $ourArea));	
		}
	}
	
	/** 
	 * Load all layout grid objects for a collection 
	 */	
	function getAreaLayouts($c){ 
		
		if( !intval($c->cID) ){
			//Invalid Collection
			return false;
		}
		
		$db = Loader::db();
		$vals = array( intval($c->cID), $c->getVersionID(), $this->getAreaHandle() );
		$sql = 'SELECT * FROM CollectionVersionAreaLayouts WHERE cID=? AND cvID=? AND arHandle=? ORDER BY position ASC, cvalID ASC';
		$rows = $db->getArray($sql,$vals); 
		
		$layouts=array();
		$i=0;
		if(is_array($rows)) foreach($rows as $row){  
			$layout = Layout::getById( intval($row['layoutID']) );
			if( is_object($layout) ){  
				
				$i++; 
			
				//check position is correct, update if not 
				if( $i != $row['position'] || $renumbering ){  
					$renumbering=1;
					$db->query( 'UPDATE CollectionVersionAreaLayouts SET position=? WHERE cvalID=?' , array($i, $row['cvalID']) ); 
				}
				$layout->position=$i; 
				
				$layout->cvalID = intval($row['cvalID']); 
				
				$layout->setAreaObj( $this );
				
				$layout->setAreaNameNumber( intval($row['areaNameNumber']) );
				
				$layouts[]=$layout; 
			} 
		}
		
		return $layouts; 
	}
	
	

	/** 
	 * Specify HTML to automatically print before blocks contained within the area
	 */
	function setBlockWrapperStart($html) {
		$this->enclosingStart = $html;
	}
	
	/** 
	 * Set HTML that automatically prints after any blocks contained within the area
	 */
	function setBlockWrapperEnd($html) {
		$this->enclosingEnd = $html;
	}

	function update($aKeys, $aValues) {
		$db = Loader::db();

		// now it's permissions time

		$gIDArray = array();
		$uIDArray = array();
		if (is_array($_POST['areaRead'])) {
			foreach ($_POST['areaRead'] as $ugID) {
				if (strpos($ugID, 'uID') > -1) {
					$uID = substr($ugID, 4);
					$uIDArray[$uID] .= "r:";
				} else {
					$gID = substr($ugID, 4);
					$gIDArray[$gID] .= "r:";
				}
			}
		}

		if (is_array($_POST['areaReadAll'])) {
			foreach ($_POST['areaReadAll'] as $ugID) {
				if (strpos($ugID, 'uID') > -1) {
					$uID = substr($ugID, 4);
					$uIDArray[$uID] .= "rb:";
				} else {
					$gID = substr($ugID, 4);
					$gIDArray[$gID] .= "rb:";
				}
			}
		}

		if (is_array($_POST['areaEdit'])) {
			foreach ($_POST['areaEdit'] as $ugID) {
				if (strpos($ugID, 'uID') > -1) {
					$uID = substr($ugID, 4);
					$uIDArray[$uID] .= "wa:";
				} else {
					$gID = substr($ugID, 4);
					$gIDArray[$gID] .= "wa:";
				}
			}
		}

		if (is_array($_POST['areaDelete'])) {
			foreach ($_POST['areaDelete'] as $ugID) {
				if (strpos($ugID, 'uID') > -1) {
					$uID = substr($ugID, 4);
					$uIDArray[$uID] .= "db:";
				} else {
					$gID = substr($ugID, 4);
					$gIDArray[$gID] .= "db:";
				}
			}
		}

		$gBTArray = array();
		$uBTArray = array();
		if (is_array($_POST['areaAddBlockType'])) {
			foreach($_POST['areaAddBlockType'] as $btID => $ugArray) {
				// this gets us the block type that particular groups/users are given access to
				foreach($ugArray as $ugID) {
					if (strpos($ugID, 'uID') > -1) {
						$uID = substr($ugID, 4);
						$uBTArray[$uID][] = $btID;
					} else {
						$gID = substr($ugID, 4);
						$gBTArray[$gID][] = $btID;
					}
				}
			}
		}

		$db = Loader::db();
		$cID = $this->getCollectionID();
		$v = array($cID, $this->getAreaHandle());
		// update the Area record itself. Hopefully it's been created.
		$db->query("update Areas set arOverrideCollectionPermissions = 1, arInheritPermissionsFromAreaOnCID = 0 where arID = ?", array($this->getAreaID()));
		
		$db->query("delete from AreaGroups where cID = ? and arHandle = ?", $v);
		$db->query("delete from AreaGroupBlockTypes where cID = ? and arHandle = ?", $v);

		// now we iterate through, and add the permissions
		foreach ($gIDArray as $gID => $perms) {
		   // since this can now be either groups or users, we have prepended gID or uID to each gID value
			// we have to trim the trailing colon, if there is one
			$permissions = (strrpos($perms, ':') == (strlen($perms) - 1)) ? substr($perms, 0, strlen($perms) - 1) : $perms;
			$v = array($cID, $this->getAreaHandle(), $gID, $permissions);
			$q = "insert into AreaGroups (cID, arHandle, gID, agPermissions) values (?, ?, ?, ?)";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
		}

		// iterate through and add user-level permissions
		foreach ($uIDArray as $uID => $perms) {
		   // since this can now be either groups or users, we have prepended gID or uID to each gID value
			// we have to trim the trailing colon, if there is one
			$permissions = (strrpos($perms, ':') == (strlen($perms) - 1)) ? substr($perms, 0, strlen($perms) - 1) : $perms;
			$v = array($cID, $this->getAreaHandle(), $uID, $permissions);
			$q = "insert into AreaGroups (cID, arHandle, uID, agPermissions) values (?, ?, ?, ?)";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
		}

		foreach($uBTArray as $uID => $uBTs) {
			foreach($uBTs as $btID) {
				$v = array($cID, $this->getAreaHandle(), $uID, $btID);
				$q = "insert into AreaGroupBlockTypes (cID, arHandle, uID, btID) values (?, ?, ?, ?)";
				$r = $db->query($q, $v);
			}
		}

		foreach($gBTArray as $gID => $gBTs) {
			foreach($gBTs as $btID) {
				$v = array($cID, $this->getAreaHandle(), $gID, $btID);
				$q = "insert into AreaGroupBlockTypes (cID, arHandle, gID, btID) values (?, ?, ?, ?)";
				$r = $db->query($q, $v);
			}
		}
		
		// finally, we rescan subareas so that, if they are inheriting up the tree, they inherit from this place
		$this->arInheritPermissionsFromAreaOnCID = $this->getCollectionID(); // we don't need to actually save this on the area, but we need it for the rescan function
		$this->arOverrideCollectionPermissions = 1; // to match what we did above - useful for the rescan functions below
		
		$acobj = $this->getAreaCollectionObject();
		if ($acobj->isMasterCollection()) {
			// if we're updating the area on a master collection we need to go through to all areas set on subpages that aren't set to override to change them to inherit from this area
			$this->rescanSubAreaPermissionsMasterCollection($acobj);
		} else {
			$this->rescanSubAreaPermissions();
		}

		$a = Cache::delete('area', $this->getCollectionID() . ':' . $this->getAreaHandle());

	}
}