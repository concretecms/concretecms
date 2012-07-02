<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Contains the blocktype object, the block type list (which is just a wrapper for querying the system for block types, and the block type
 * DB wrapper for ADODB.
 * @package Blocks
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 

	class BlockTypeList extends Object {
			
		/**
		 * array of BlockType objects - should likely be considered a protected property
		 * use getBlockTypeList instead of accessing this property directly
		 * @see BlockTypeList::getBlockTypeList()
		 * @var BlockType[] $btArray
		 */
		public $btArray = array();
		
		/**
		 * Gets an array of BlockTypes for a given Package
		 * @param Package $pkg
		 * @return BlockType[]
		 */
		public static function getByPackage($pkg) {
			$db = Loader::db();
			$r = $db->Execute("select btID from BlockTypes where pkgID = ?", $pkg->getPackageID());
			$blockTypes = array();
			while ($row = $r->FetchRow()) {
				$blockTypes[] = BlockType::getByID($row['btID']);
			}
			return $blockTypes;
		}
		
		
		/**
		 * @todo comment this one
		 * @param string $xml
		 * @return void
		 */
		public static function exportList($xml) {
			$attribs = BlockTypeList::getInstalledList();
			$nxml = $xml->addChild('blocktypes');
			foreach($attribs as $bt) {
				$type = $nxml->addChild('blocktype');
				$type->addAttribute('handle', $bt->getBlockTypeHandle());
				$type->addAttribute('package', $bt->getPackageHandle());
			}
		}
		
		/**
		 * returns an array of Block Types used in the concrete5 Dashboard
		 * @param Area $ap
		 * @return BlockType[]
		 */
		public static function getDashboardBlockTypes($ap) {
			$blockTypeIDs = $ap->getAddBlockTypes();
			$db = Loader::db();
			$btIDs = $db->GetCol('select btID from BlockTypes where btHandle like "dashboard_%" order by btID asc');
			$blockTypes = array();
			foreach($btIDs as $btID) {
				if (in_array($btID, $blockTypeIDs)) {
					$blockTypes[] = BlockType::getByID($btID);
				}
			}
			return $blockTypes;
		}
		
		/**
		 * BlockTypeList class constructor
		 * @param array $allowedBlocks array of allowed BlockType id's if you'd like to limit the list to just those
		 * @return BlockTypeList
		 */
		function __construct($allowedBlocks = null) {
			$db = Loader::db();
			$this->btArray = array();
						
			$q = "select btID from BlockTypes where btIsInternal = 0 ";
			if ($allowedBlocks != null) {
				$q .= ' and btID in (' . implode(',', $allowedBlocks) . ') ';
			}
			$q .= ' order by btID asc';
			
			$r = $db->query($q);
	
			if ($r) {
				while ($row = $r->fetchRow()) {
					$bt = BlockType::getByID($row['btID']);
					if (is_object($bt)) {
						$this->btArray[] = $bt;
					}
				}
				$r->free();
			}
											
			return $this;
		}
		
		/**
		 * gets the array of BlockType objects
		 * @return BlockType[]
		 * @see BlockTypeList::getInstalledList()
		 */
		public function getBlockTypeList() {
			return $this->btArray;
		}

		/**
		 * Gets a list of block types that are not installed, used to get blocks that can be installed
		 * This function only surveys the web/blocks directory - it's not looking at the package level.
		 * @return BlockType[] 
		 */
		public static function getAvailableList() {
			$blocktypes = array();
			$dir = DIR_FILES_BLOCK_TYPES;
			$db = Loader::db();
			
			$btHandles = $db->GetCol("select btHandle from BlockTypes");
			
			$aDir = array();
			if (is_dir($dir)) {
				$handle = opendir($dir);
				while(($file = readdir($handle)) !== false) {
					if (strpos($file, '.') === false) {
						$fdir = $dir . '/' . $file;
						if (is_dir($fdir) && !in_array($file, $btHandles) && file_exists($fdir . '/' . FILENAME_BLOCK_CONTROLLER)) {
							$bt = new BlockType;
							$bt->btHandle = $file;
							$class = $bt->getBlockTypeClassFromHandle($file);
							
							require_once($fdir . '/' . FILENAME_BLOCK_CONTROLLER);
							if (!class_exists($class)) {
								continue;
							}
							$bta = new $class;
							$bt->btName = $bta->getBlockTypeName();
							$bt->btDescription = $bta->getBlockTypeDescription();
							$bt->hasCustomViewTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_VIEW);
							$bt->hasCustomEditTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_EDIT);
							$bt->hasCustomAddTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_ADD);
							
							
							$btID = $db->GetOne("select btID from BlockTypes where btHandle = ?", array($file));
							$bt->installed = ($btID > 0);
							$bt->btID = $btID;
							
							$blocktypes[] = $bt;
							
						}
					}				
				}
			}
			
			return $blocktypes;
		}

		/**
		 * gets a list of installed BlockTypes
		 * @return BlockType[]
		 */	
		public static function getInstalledList() {
			$db = Loader::db();
			$r = $db->query("select btID from BlockTypes order by btName asc");
			$btArray = array();
			while ($row = $r->fetchRow()) {
				$bt = BlockType::getByID($row['btID']);
				if (is_object($bt)) {
					$btArray[] = $bt;
				}
			}
			return $btArray;
		}
		
		/**
		 * Gets a list of installed BlockTypes 
		 * - could be defined as static
		 * @todo we have three duplicate functions getBlockTypeArray, getInstalledList, getBlockTypeList
		 * @return BlockType[]
		 */	
		public function getBlockTypeArray() {
			$db = Loader::db();
			$q = "select btID from BlockTypes order by btID asc";
			$r = $db->query($q);
			$btArray = array();
			if ($r) {
				while ($row = $r->fetchRow()) {
					$bt = BlockType::getByID($row['btID']);
					if (is_object($bt)) {
						$btArray[] = $bt;
					}
				}
				$r->free();
			}
			return $btArray;
		}
		
		/**
		 * gets the block types that are allowed to be added to the given area - given the current user's permissions
		 * @param Area
		 * @param CollectionPermissions
		 * @return BlockType[]
		 */
		public function getAreaBlockTypes(&$a, &$cp) {
			$btl = new BlockTypeList();
			$btlaTMP = $btl->getBlockTypeList();
			$btla = array();
			foreach($btlaTMP as $bt) {
				$bt->setAreaPermissions($a, $cp);
				$btla[] = $bt;
			}
			return $btla;
		}
		
		/**
		 * gets the form post action for the current block type given the area
		 * @param Area $a
		 * @return string
		 */
		public function getBlockTypeAddAction(&$a) {
			$step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';
			$arHandle = urlencode($a->getAreaHandle());
			$c = $a->getAreaCollectionObject();
			$cID = $c->getCollectionID();
			$valt = Loader::helper('validation/token');
			$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&amp;areaName={$arHandle}&amp;mode=edit&amp;btask=add" . $step . '&' . $valt->getParameter();
			return $str;			
		}
		
		/**
		 * gets the form post action for the current block type given the area
		 * @param Area $a
		 * @return string
		 */
		public function getBlockTypeAliasAction(&$a) {
			$step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';
			$arHandle = urlencode($a->getAreaHandle());
			$c = $a->getAreaCollectionObject();
			$cID = $c->getCollectionID();
			$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&amp;areaName={$arHandle}&amp;mode=edit&amp;btask=alias" . $step . '&' . $valt->getParameter();
			return $str;			
		}
			
	}

/**
*
* @access private
*/	
	class BlockTypeDB extends ADOdb_Active_Record {
		public $_table = 'BlockTypes';
	}

/**
*
* Any type of content that can be added to pages is represented as a type of block, and thereby a block type object.
* @package Blocks
* @author Andrew Embler <andrew@concrete5.org>
* @license    http://www.concrete5.org/license/     MIT License
* @package Blocks
* @category Concrete
*/		
	class BlockType extends Object {
			
		/**
		 * @var array $addBTUArray
		 */
		public $addBTUArray = array();
		
		/**
		 * @var array $addBTGArray
		 */
		public $addBTGArray = array();
		
		/**
		 * @var BlockTypeController
		 */
		public $controller;
		
		/**
		 * Gets the BlockType object for the given Block Type Handle
		 * ex: 
		 * <code><?php
		 * $bt = BlockType::getByHandle('content'); // returns the BlockType object for the core Content block
		 * ?></code
		 * @param string $handle
		 * @return BlockType
		 */
		public static function getByHandle($handle) {
			$ca = new Cache();
			$bt = $ca->get('blockTypeByHandle', $handle);
			if (!is_object($bt)) {
				$where = 'btHandle = ?';
				$bt = BlockType::get($where, array($handle));
				$ca->set('blockTypeByHandle', $handle, $bt);
			}
			if (is_object($bt)) {
				$bt->controller = Loader::controller($bt);
				return $bt;
			}
		}

		/**
		 * Gets the BlockType for a given Block Type ID
		 * @param int $btID
		 * @return BlockType
		 */
		public static function getByID($btID) {
			$ca = new Cache();
			$bt = $ca->get('blockTypeByID', $btID);
			if (!is_object($bt)) {
				$where = 'btID = ?';
				$bt = BlockType::get($where, array($btID));			
				$ca->set('blockTypeByID', $btID, $bt);
			}
			if (is_object($bt)) {
				$bt->controller = Loader::controller($bt);
				return $bt;
			}
			return $bt;
		}
		
		/**
		 * internal method to query the BlockTypes table and get a BlockType object
		 * @param string
		 * @param array
		 */
		protected static function get($where, $properties) {
			$db = Loader::db();
			
			$q = "SELECT btID, btName, btDescription, btHandle, pkgID, btActiveWhenAdded, btIsInternal, btCopyWhenPropagate, btIncludeAll, btInterfaceWidth, btInterfaceHeight from BlockTypes where {$where}";
			$r = $db->query($q, $properties);
			
			if ($r->numRows() > 0) {
				$row = $r->fetchRow();
				$bt = new BlockType;
				$bt->setPropertiesFromArray($row);
				return $bt;
			}
			
		}
		
		/** 
		 * if a the current BlockType is Internal or not - meaning one of the core built-in concrete5 blocks
		 * @access private
		 * @return boolean
		 */
		function isBlockTypeInternal() {return $this->btIsInternal;}
		
		/** 
		 * Returns true if the block type is internal (and therefore cannot be removed) a core block
		 * @return boolean
		 */
		public function isInternalBlockType() {
			return $this->btIsInternal;
		}
		
		/** 
		 * Returns true if the block type ships with concrete5 by checking to see if it's in the concrete/blocks/ directory
		 * @deprecated
		 */
		public function isCoreBlockType() {
			return is_dir(DIR_FILES_BLOCK_TYPES_CORE . '/' . $this->getBlockTypeHandle());
		}

		/**
		 * Determines if the block type has templates available
		 * @return boolean
		 */
		public function hasAddTemplate() {
			$bv = new BlockView();
			$bv->setBlockObject($this);
			$path = $bv->getBlockPath(FILENAME_BLOCK_ADD);
			if (file_exists($path . '/' . FILENAME_BLOCK_ADD)) {
				return true;
			}
			return false;
		}
		
		
		/**
		 * returns the width in pixels that the block type's editing dialog will open in
		 * @return int
		 */
		public function getBlockTypeInterfaceWidth() {return $this->btInterfaceWidth;}
		
		/**
		 * returns the height in pixels that the block type's editing dialog will open in
		 * @return int
		 */
		public function getBlockTypeInterfaceHeight() {return $this->btInterfaceHeight;}
		
		/**
		 * returns the id of the BlockType's package if it's in a package
		 * @return int
		 */
		public function getPackageID() {return $this->pkgID;}
		
		/**
		 * returns the handle of the BlockType's package if it's in a package
		 * @return string
		 */
		public function getPackageHandle() {
			return PackageList::getHandle($this->pkgID);
		}
		
		
		/**
		 * determines if a user or group can add a block of the current BlockType
		 * @param UserInfo|Group $obj
		 * @return boolean
		 */
		public function canAddBlock($obj) {
			switch(strtolower(get_class($obj))) {
				case 'group':
					return in_array($obj->getGroupID(), $this->addBTGArray);
					break;
				case 'userinfo':
					return in_array($obj->getUserID(), $this->addBTUArray);
					break;
			}
		}
		
		/** 
		 * Returns the number of unique instances of this block throughout the entire site
		 * note - this count could include blocks in areas that are no longer rendered by the theme
		 * @return int
		 */
		public function getCount() {
			$db = Loader::db();
			$count = $db->GetOne("select count(btID) from Blocks where btID = ?", array($this->btID));
			return $count;
		}
		
		/**
		 * Not a permissions call. Actually checks to see whether this block is not an internal one.
		 * @return boolean
		 */
		public function canUnInstall() {
			/*$cnt = $this->getCount();
			if ($cnt > 0 || $this->isBlockTypeInternal()) {
				return false;
			}*/
			
			return (!$this->isBlockTypeInternal());
		}
		
		/**
		 * gets the BlockTypes description text
		 * @return string
		 */
		function getBlockTypeDescription() {
			return $this->btDescription;
		}
		
		/**
		 * Gets the custom templates available for the current BlockType
		 * @return array an array of strings
		 */
		function getBlockTypeCustomTemplates() {
			$btHandle = $this->getBlockTypeHandle();
			$pkgHandle = $this->getPackageHandle();

			$templates = array();
			$fh = Loader::helper('file');
			
			if (file_exists(DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES)) {
				$templates = array_merge($templates, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES));
			}
			
			/*
			if ($pkgHandle != null) {
				if (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) {
					$templates = array_merge($templates, $fh->getDirectoryContents(DIR_PACKAGES . "/{$pkgHandle}/" . DIRNAME_BLOCKS . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES));
				} else {
					$templates = array_merge($templates, $fh->getDirectoryContents(DIR_PACKAGES_CORE . "/{$pkgHandle}/" . DIRNAME_BLOCKS . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES));
				}
			}
			*/ 
			
			// NOW, we check to see if this btHandle has any custom templates that have been installed as separate packages
			$pl = PackageList::get();
			$packages = $pl->getPackages();
			foreach($packages as $pkg) {
				$d = (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle())) ? DIR_PACKAGES . '/'. $pkg->getPackageHandle() : DIR_PACKAGES_CORE . '/'. $pkg->getPackageHandle();
				if (is_dir($d . '/' . DIRNAME_BLOCKS . '/' . $btHandle . '/' . DIRNAME_BLOCK_TEMPLATES)) {
					$templates = array_merge($templates, $fh->getDirectoryContents($d . '/' . DIRNAME_BLOCKS . '/' . $btHandle . '/' . DIRNAME_BLOCK_TEMPLATES));
				}
			}
			
			if (file_exists(DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES)) {
				$templates = array_merge($templates, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES));
			}

			$templates = array_unique($templates);
	
			return $templates;
		}

		
		/** 
		 * gets the available composer templates 
		 * used for editing instances of the BlockType while in the composer ui in the dashboard
		 * @return array array of strings
		 */
		function getBlockTypeComposerTemplates() {
			$btHandle = $this->getBlockTypeHandle();
			$pkgHandle = $this->getPackageHandle();

			$templates = array();
			$fh = Loader::helper('file');
			
			if (file_exists(DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES_COMPOSER)) {
				$templates = array_merge($templates, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES_COMPOSER));
			}

			if (file_exists(DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES_COMPOSER)) {
				$templates = array_merge($templates, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES_COMPOSER));
			}

			$templates = array_unique($templates);
	
			return $templates;
		}
		
		/**
		 * sets the permissions on the area??
		 * @todo Documnetation?? not sure what type $cp is or how this is used?
		 * @param Area $area
		 * @param unknown $cp
		 */
		function setAreaPermissions(&$area, &$cp) {
			$db = Loader::db();
			if ($area->overrideCollectionPermissions()) {
				$setBlocksVia = "AREA";
			} else {
				if ($area->getAreaCollectionInheritID() > 0) {
					// see if the area/page we're supposed to be getting these from actually has a record
					$arOverrideCollectionPermissions = $db->getOne("select arOverrideCollectionPermissions from Areas where cID = ? and arHandle = ?", array($area->getAreaCollectionInheritID(), $area->getAreaHandle()));
					if ($arOverrideCollectionPermissions) {
						$setBlocksVia = "AREA";
					}
				}
				
				if (!isset($setBlocksVia)) {
					$setBlocksVia = "PAGE";
				}				
			}
			
			if ($setBlocksVia == "AREA") { 
				$c = $area->getAreaCollectionObject();
				$cpID = ($area->getAreaCollectionInheritID() > 0) ? $area->getAreaCollectionInheritID() : $c->getCollectionID();
				$v = array($cpID, $area->getAreaHandle(), $this->getBlockTypeID());
				$q = "select uID, gID from AreaGroupBlockTypes where cID = ? and arHandle = ? and btID = ?";
				$r = $db->query($q, $v);
				while ($row = $r->fetchRow()) {
					if ($row['uID'] != 0) {
						$this->addBTUArray[] = $row['uID'];
					}
					if ($row['gID'] != 0) {
						$this->addBTGArray[] = $row['gID'];
					}
				}
			} else {
				$cID = $area->getCollectionID();
				// we grab all the uID/gID combos from PagePermissions that can edit the page
				// then we allow them to add all the blocks they want
				$cInheritCID = $db->getOne('select cInheritPermissionsFromCID from Pages where cID = ?', array($cID));
				
				$v = array($cInheritCID);
				$q = "select uID, gID, cgPermissions from PagePermissions where cID = ?";
				$r = $db->query($q, $v);
				while ($row = $r->fetchRow()) {
					if ($row['uID'] != 0 && strpos($row['cgPermissions'], 'wa') !== false) {
						$this->addBTUArray[] = $row['uID'];
					}
					if ($row['gID'] != 0 && strpos($row['cgPermissions'], 'wa') !== false) {
						$this->addBTGArray[] = $row['gID'];
					}
				}
			}
		}

		
		/**
		 * installs a new BlockType from a package, 
		 * typicaly called from a package controller's install() method durring package installation 
		 * @todo Documentation how is the btID used, if you want to reserve/specify a btID??
		 * @param string $btHandle the block Type's handle
		 * @param Package $pkg
		 * @param int $btID if it's an existing block type
		 * @return void|string error message
		 */
		public function installBlockTypeFromPackage($btHandle, $pkg, $btID = 0) {
			$dir1 = DIR_PACKAGES . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_BLOCKS;
			$dir2 = DIR_PACKAGES_CORE . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_BLOCKS;
			
			if (file_exists($dir1)) {
				$dir = $dir1;
			} else {
				$dir = $dir2;
			}

			// now we check to see if it's been overridden in the site root and if so we do it there
			if ($btID > 0) { 
				// this is only necessary when it's an existing refresh
				if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $btHandle . '/' . FILENAME_BLOCK_CONTROLLER)) {
					$dir = DIR_FILES_BLOCK_TYPES;
				}
			}
			
			$bt = new BlockType;
			$bt->btHandle = $btHandle;
			$bt->pkgHandle = $pkg->getPackageHandle();
			$bt->pkgID = $pkg->getPackageID();
			return BlockType::doInstallBlockType($btHandle, $bt, $dir, $btID);
		}
		
		/**
		 * refreshes the BlockType's database schema throws an Exception if error
		 * @return void
		 */
		public function refresh() {
			if ($this->getPackageID() > 0) {
				$pkg = Package::getByID($this->getPackageID());
				$resp = BlockType::installBlockTypeFromPackage($this->getBlockTypeHandle(), $pkg, $this->getBlockTypeID());			
				if ($resp != '') {
					throw new Exception($resp);
				}
			} else {
				$resp = BlockType::installBlockType($this->getBlockTypeHandle(), $this->getBlockTypeID());			
				if ($resp != '') {
					throw new Exception($resp);
				}
			}
		}
		
		/**
		 * installs a core or root level BlockType (from /blocks or /concrete/blocks, not a package)
		 * should likely be a static method
		 * @param string $btHandle
		 * @param int $btID btID if it's an existing block type
		 */
		public function installBlockType($btHandle, $btID = 0) {
		
			if ($btID == 0) {
				// then we don't allow one to already exist
				$db = Loader::db();
				$cnt = $db->GetOne("select btID from BlockTypes where btHandle = ?", array($btHandle));
				if ($cnt > 0) {
					return false;
				}
			}
			
			if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $btHandle . '/' . FILENAME_BLOCK_CONTROLLER)) {
				$dir = DIR_FILES_BLOCK_TYPES;
			} else {
				$dir = DIR_FILES_BLOCK_TYPES_CORE;
			}
			
			$bt = new BlockType;
			$bt->btHandle = $btHandle;
			$bt->pkgHandle = null;
			$bt->pkgID = 0;
			return BlockType::doInstallBlockType($btHandle, $bt, $dir, $btID);
		}
		
		/** 
		 * Renders a particular view of a block type, using the public $controller variable as the block type's controller
		 * @param string template 'view' for the default
		 * @return void
		 */
		public function render($view) {
			$bv = new BlockView();
			$bv->setController($this->controller);
			$bv->render($this, $view);
		}			
		
		/**
		 * get's the block type controller
		 * @return BlockTypeController
		 */
		public function getController() {
			return $this->controller;
		}
		
		/**
		 * installs a block type
		 * @param string $btHandle
		 * @param BlockType $bt
		 * @param string $dir
		 * @param int $btID
		 */
		protected function doInstallBlockType($btHandle, $bt, $dir, $btID = 0) {
			$db = Loader::db();
			
			if (file_exists($dir . '/' . $btHandle . '/' . FILENAME_BLOCK_CONTROLLER)) {
				$class = $bt->getBlockTypeClassFromHandle();
				
				$path = $dir . '/' . $btHandle;
				if (!class_exists($class)) {
					require_once($dir . '/' . $btHandle . '/' . FILENAME_BLOCK_CONTROLLER);
				}
				
				if (!class_exists($class)) {
					throw new Exception(t("%s not found. Please check that the block controller file contains the correct class name.", $class));
				}
				$bta = new $class;
				
				// first run the subclass methods. If they work then we install the block
				$r = $bta->install($path);
				if (!$r->result) {
					return $r->message;
				}
				
				$btd = new BlockTypeDB();
				$btd->btHandle = $btHandle;
				$btd->btName = $bta->getBlockTypeName();
				$btd->btDescription = $bta->getBlockTypeDescription();
				$btd->btActiveWhenAdded = $bta->isActiveWhenAdded();
				$btd->btCopyWhenPropagate = $bta->isCopiedWhenPropagated();
				$btd->btIncludeAll = $bta->includeAll();
				$btd->btIsInternal = $bta->isBlockTypeInternal();
				$btd->btInterfaceHeight = $bta->getInterfaceHeight();
				$btd->btInterfaceWidth = $bta->getInterfaceWidth();
				$btd->pkgID = $bt->getPackageID();
				
				if ($btID > 0) {
					$btd->btID = $btID;
					$r = $btd->Replace();
				} else {
					$r = $btd->save();
				}
				
				// now we remove the block type from cache
				$ca = new Cache();
				$ca->delete('blockTypeByID', $btID);
				$ca->delete('blockTypeByHandle', $btHandle);
				$ca->delete('blockTypeList', false);		 	
				
				if (!$r) {
					return $db->ErrorMsg();
				}
			} else {
				return t("No block found with the handle %s.", $btHandle);
			}
		}
		
		/**
		 * Returns a path to where the block type's files are located.
		 * @access public
		 * @return string $path
		 */
		public function getBlockTypePath() {
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
		
/** @todo Continue documenting from here down **/
		
		 
		/*
		 * @access private
		 *
		 */
		private function _getClass() {
			$btHandle = $this->btHandle;
			$pkgHandle = $this->getPackageHandle();
			if (file_exists(DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . FILENAME_BLOCK_CONTROLLER)) {
				$classfile = DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . FILENAME_BLOCK_CONTROLLER;
			} else if ($pkgHandle != null) {
				if (file_exists(DIR_PACKAGES . "/{$pkgHandle}/" . DIRNAME_BLOCKS . "/{$btHandle}/" . FILENAME_BLOCK_CONTROLLER)) {
					$classfile = DIR_PACKAGES . "/{$pkgHandle}/" . DIRNAME_BLOCKS . "/{$btHandle}/" . FILENAME_BLOCK_CONTROLLER;
				} else if (file_exists(DIR_PACKAGES_CORE . "/{$pkgHandle}/" . DIRNAME_BLOCKS . "/{$btHandle}/" . FILENAME_BLOCK_CONTROLLER)) {
					$classfile = DIR_PACKAGES_CORE . "/{$pkgHandle}/" . DIRNAME_BLOCKS . "/{$btHandle}/" . FILENAME_BLOCK_CONTROLLER;
				}
			} else {			
				if (file_exists(DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . FILENAME_BLOCK_CONTROLLER)) {
					$classfile = DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . FILENAME_BLOCK_CONTROLLER;
				}
			}
			
			if ($classfile) {
				require_once($classfile);
				
				// takes the handle and performs some magic to get the class;
				$btHandle = $this->getBlockTypeHandle();
				// split by underscores or dashes
				$words = preg_split('/\_|\-/', $btHandle);
				for ($i = 0; $i < count($words); $i++) {
					$words[$i] = ucfirst($words[$i]);
				}
				
				$class = implode('', $words);
				$class = $class . 'BlockController';
				return $class;
			}
		}
		
		public function inc($file, $args = array()) {
			extract($args);
			$bt = $this;
			global $c;
			global $a;
			if ($this->getPackageID() > 0) {
				if (is_dir(DIR_PACKAGES . '/' . $this->getPackageHandle())) {
					include(DIR_PACKAGES . '/' . $this->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $this->getBlockTypeHandle() . '/' . $file);
				} else {
					include(DIR_PACKAGES_CORE . '/' . $this->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $this->getBlockTypeHandle() . '/' . $file);
				}
			} else if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $this->getBlockTypeHandle() . '/' . $file)) {
				include(DIR_FILES_BLOCK_TYPES . '/' . $this->getBlockTypeHandle() . '/' . $file);
			} else {
				include(DIR_FILES_BLOCK_TYPES_CORE . '/' . $this->getBlockTypeHandle() . '/' . $file);
			}
		}
		
		public function getBlockTypeClass() {
			$btHandle = $this->getBlockTypeHandle();
			return $this->_getClass($btHandle);
		}
		
		public function getBlockTypeClassFromHandle() {
			return $this->_getClass();
		}
		
		/** 
		 * Removes the block type. Also removes instances of content.
		 */
		public function delete() {
			$db = Loader::db();
			$r = $db->Execute('select cID, cvID, b.bID, arHandle from CollectionVersionBlocks cvb inner join Blocks b on b.bID = cvb.bID where btID = ?', array($this->getBlockTypeID()));
			while ($row = $r->FetchRow()) {
				$nc = Page::getByID($row['cID'], $row['cvID']);
				$b = Block::getByID($row['bID'], $nc, $row['arHandle']);
				$b->deleteBlock();
			}
			
			$ca = new Cache();
			$ca->delete('blockTypeByID', $this->btID);
			$ca->delete('blockTypeByHandle', $btHandle);		 	
			$ca->delete('blockTypeList', false);		 	
			$db->Execute("delete from BlockTypes where btID = ?", array($this->btID));
		}
		
		/** 
		 * Allows block types to be updated
		 * @param array $data
		 */
		 
		 public function update($data) {
		 	$db = Loader::db();
		 	$btHandle = $this->btHandle;
		 	$btName = $this->btName;
		 	$btDescription = $this->btDescription;
		 	if (isset($data['btHandle'])) {
		 		$btHandle = $data['btHandle'];
		 	}
		 	if (isset($data['btName'])) {
		 		$btName = $data['btName'];
		 	}
		 	if (isset($data['btDescription'])) {
		 		$btDescription = $data['btDescription'];
		 	}
		 	$db->Execute('update BlockTypes set btHandle = ?, btName = ?, btDescription = ? where btID = ?', array($btHandle, $btName, $btDescription, $this->btID));

			// now we remove the block type from cache
			$ca = new Cache();
			$ca->delete('blockTypeByID', $this->btID);
			$ca->delete('blockTypeByHandle', $btHandle);
			$ca->delete('blockTypeList', false);		 	
		 }
		 
		 
		/* 
		 * Adds a block to the system without adding it to a collection. 
		 * Passes page and area data along if it is available, however.
		 */
		public function add($data, $c = false, $a = false) {
			$db = Loader::db();
			
			$u = new User();
			if (isset($data['uID'])) {
				$uID = $data['uID'];
			} else { 
				$uID = $u->getUserID();
			}
			$btID = $this->btID;
			$dh = Loader::helper('date');
			$bDate = $dh->getSystemDateTime();
			$bIsActive = ($this->btActiveWhenAdded == 1) ? 1 : 0;
			
			$v = array($_POST['bName'], $bDate, $bDate, $bIsActive, $btID, $uID);
			$q = "insert into Blocks (bName, bDateAdded, bDateModified, bIsActive, btID, uID) values (?, ?, ?, ?, ?, ?)";
			
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);

			$bIDnew = $db->Insert_ID();

			// we get the block object for the block we just added

			if ($res) {
				$nb = Block::getByID($bIDnew);
	
				$btHandle = $this->getBlockTypeHandle();
				
				$class = $this->getBlockTypeClass();
				$bc = new $class($nb);
				if (is_object($c)) {
					$bc->setCollectionObject($c);
				}
				$bc->save($data);
				
				// the previous version of the block above is cached without the values				
				$nb->refreshCache();
				
				return Block::getByID($bIDnew);
				
			}
			
		}
		
		function getBlockTypeID() {
			return $this->btID;
		}
		
		function getBlockTypeHandle() {
			return $this->btHandle;
		}
		
		// getBlockAddAction vs. getBlockTypeAddAction() - The difference is very simple. We call getBlockTypeAddAction() to grab the
		// action properties for the form that presents the drop-down select menu for selecting which type of block to add. We call the other
		// function when we've already chosen a type to add, and we're interested in actually adding the block - content completed - to the database
				
		function getBlockAddAction(&$a, $alternateHandler = null) {
			// Note: This is fugly, since we're just grabbing query string variables, but oh well. Not _everything_ can be object oriented
			$btID = $this->btID;
			$step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';			
			$c = $a->getAreaCollectionObject();
			$cID = $c->getCollectionID();
			$arHandle = urlencode($a->getAreaHandle());
			$valt = Loader::helper('validation/token');
			
			
			if ($alternateHandler) {
				$str = $alternateHandler . "?cID={$cID}&arHandle={$arHandle}&btID={$btID}&mode=edit" . $step . '&' . $valt->getParameter();
			} else {
				$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&arHandle={$arHandle}&btID={$btID}&mode=edit" . $step . '&' . $valt->getParameter();
			}
			return $str;			
		}
		
		
		function getBlockTypeName() {
			return $this->btName;
		}
		
		function isInstalled() {
			return $this->installed;
		}
		
		function getBlockTypeActiveWhenAdded() {
			return $this->btActiveWhenAdded;
		}
		
		function isCopiedWhenPropagated() {
			return $this->btCopyWhenPropagate;
		}

		function includeAll() {
			return $this->btIncludeAll;
		}
		
		function hasCustomEditTemplate() {
			return $this->hasCustomEditTemplate;
		}
		
		function hasCustomViewTemplate() {
			return $this->hasCustomViewTemplate;
		}
		
		function hasCustomAddTemplate() {
			return $this->hasCustomAddTemplate;
		}

	}
	
	
	

?>
