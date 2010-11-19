<?php 
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Blocks
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * The parent object of all individual block type controllers. Responsible for installing the block, saving its data, rendering its various templates.
 *
 * @package Blocks
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

	class BlockController extends Controller {
		
		protected $record; // blockrecord
		protected $helpers = array('form');
		protected static $sets;
		
		protected $btDescription = "";
		protected $btName = "";
		protected $btHandle = "";
		protected $btIsInternal = 0;
		protected $btActiveWhenAdded = 1;
		protected $btCopyWhenPropagate = 0;
		protected $btIncludeAll = 0;
		protected $dbFile = 'db.xml';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "400";
		protected $btHasRendered = false;
		protected $btCacheBlockRecord = false;
		protected $btCacheBlockOutput = false;
		protected $btCacheBlockOutputLifetime = 1800; // seconds, half an hour 
		protected $btCacheBlockOutputOnPost = false;
		protected $btCacheBlockOutputForRegisteredUsers = false;
		public $headerItems = array();

		protected $identifier;
		
		/**
		 * Sets a value used by a particular block. These variables will automatically be present in the corresponding views used by the block.
		 * @param string $key
		 * @param string $value
		 * @return void
		 */
		public function set($key, $value) {
			BlockController::$sets[$this->identifier][$key] = $value;		
		}
		
		public function get($key) {
			if (isset(BlockController::$sets[$this->identifier][$key])) {
				return BlockController::$sets[$this->identifier][$key];
			}
			
			return parent::get($key);
		}

		/** 
		 * @access private
		 */
		public function getSets() {
			return BlockController::$sets[$this->identifier];		
		}

		/**
		 * Installs the current block's DB xml file. If a block needs to do more than this, this should be overridden.
		 * <code>
		 * public function install($path) {
		 *     $this->doMySpecialInstallMethod();
		 *     $this->doSecondSpecialInstallMethod();
		 *     parent::install($path);
		 * }
		 * </code>
		 * @param string $path
		 * @return bool $didInstallCorrectly
		 */
		function install($path) {
			// passed path is the path to this block (try saying that ten times fast)
			// create the necessary table
			$ret = Package::installDB($path . '/' . $this->dbFile);
			return $ret;
		}
		
		/**
		 * Renders a view in the block's folder.
		 * <code>
		 * public function view() { // The view() method is automatically run when a block is viewed
		 *     $this->render("other_special_view"); // don't use .php
		 * }
		 * </code>
		 * @param string $view
		 * @return void
		 */
		function render($view) {
			$bv = new BlockView();
			$bv->setController($this);
			// sometimes we need the block type available in here
			if (is_object($this->getBlockObject())) {
				$bt = BlockType::getByID($this->getBlockObject()->getBlockTypeID());
				$a = $this->getBlockObject()->getBlockAreaObject();
			}
			$this->renderOverride = $view;
		}
		
		public function validate($args) {
			return true;
		}
		
		/**
		 * Run when a block is added or edited. Automatically saves block data against the block's database table. If a block needs to do more than this (save to multiple tables, upload files, etc... it should override this.
		 * @param array $args
		 * @return void
		 */
		public function save($args) {
			//$argsMerged = array_merge($_POST, $args);
			$attribs = $this->record->getAttributeNames();
			foreach($attribs as $key) {
				if (isset($args[$key])) {
					$this->record->{$key} = $args[$key];
				}
			}
			$this->record->Replace();
		}
		
		/**
		 *
		 * Gets the permissions object for this controller's block
		 *
		 */
		public function getPermissionsObject() {
			$bp = new Permissions(Block::getByID($this->bID));
			return $bp;
		}
		
		/**
		 * Automatically run when a block is duplicated. This most likely happens when a block is edited: a block is first duplicated, and then presented to the user to make changes.
		 * @param int $newBlockID
		 * @return BlockRecord $newInstance
		 */
		public function duplicate($newBID) {
			$newInstance = clone $this->record;
			$newInstance->bID = $newBID;
			$newInstance->Insert();
			return $newInstance;
		}
		
		public function __wakeup() {
			$this->__construct();
		}
		

		public function cacheBlockOutput() {
			return $this->btCacheBlockOutput;
		}

		public function cacheBlockOutputForRegisteredUsers() {
			return $this->btCacheBlockOutputForRegisteredUsers;
		}

		public function cacheBlockOutputOnPost() {
			return $this->btCacheBlockOutputOnPost;
		}

		public function getBlockTypeCacheOutputLifetime() {
			return $this->btCacheBlockOutputLifetime;
		}
		
		public function getCollectionObject() {
			if ($this->bActionCID > 0) {
				return Page::getByID($this->bActionCID);
			} 
			return Page::getCurrentPage();
		}
		
		/**
		 * Automatically run when a block is deleted. This removes the special data from the block's specific database table. If a block needs to do more than this this method should be overridden.
		 * @return $void
		 */
		public function delete() {
			if ($this->bID > 0) {
				$this->record->delete();
			}
		}

		/** 
		 * Loads the BlockRecord class based on its attribute names
		 * @return void
		 */
		protected function load() {
			$attribs = $this->record->getAttributeNames();
			foreach($attribs as $key) {
				$this->{$key} = $this->record->$key;
				$this->set($key, $this->record->$key);
			}
		}
		
		/**
		 * Instantiates the block controller.
		 * @param BlockType $obj|Block $obj
		 */
		public function __construct($obj = null) {
			if ($obj instanceof BlockType) {
				$this->identifier = 'BLOCKTYPE:' . $obj->getBlockTypeID();
				$this->btHandle = $obj->getBlockTypeHandle();
			} else if ($obj instanceof Block) {
				$b = $obj;
				$this->identifier = 'BLOCK:' . $obj->getBlockID();
			
				// we either have a blockID passed, or nothing passed, if we're adding a block type				
				$this->bID = $b->getBlockID();
				if ($this->btTable) {
					$this->record = new BlockRecord($this->btTable);
					$this->record->bID = $this->bID;
					$this->record->Load('bID=' . $this->bID);
					$this->load();
				}
				$this->btHandle = $obj->getBlockTypeHandle();
				$this->bActionCID = $obj->getBlockActionCollectionID();
			}
			parent::__construct();
			$this->set('controller', $this);
		}
		
		public function outputAutoHeaderItems() {
			$b = $this->getBlockObject();
			$bvt = new BlockViewTemplate($b);
			
			$headers = $bvt->getTemplateHeaderItems();
			if (count($headers) > 0) {
				foreach($headers as $h) {
					$this->addHeaderItem($h);
				}
			}
		}
		
		public function addHeaderItem($file) {
			$namespace = 'BLOCK_CONTROLLER_' . strtoupper($this->btHandle);
			$this->headerItems[$namespace][] = $file;
			parent::addHeaderItem($file);
		}
		
		public function setupAndRun($method) {
			if ($method) {
				$this->task = $method;
			}
			if (method_exists($this, 'on_start')) {
				call_user_func_array(array($this, 'on_start'), array($method));
			}
			if ($method) {
				$this->runTask($method, array());
			}
			
			if (method_exists($this, 'on_before_render')) {
				call_user_func_array(array($this, 'on_before_render'), array($method));
			}
		}

		
		/**
		 * Gets the generic Block object attached to this controller's instance
		 * @return Block $b
		 */
		public function getBlockObject() {
			return Block::getByID($this->bID);
		}

		/**
		 * @access private
		 * @todo Make block's uninstallable
		 */
		public function uninstall() {
			// currently blocks cannot be uninstalled
		}
		
		/**
		 * Returns the name of the block type
		 * @return string $btName
		 */
		public function getBlockTypeName() {
			return t($this->btName);
		}
		
		/**
		 * Returns the width of the block type's interface when presented in page.
		 * @return int
		 */
		public function getInterfaceWidth() {
			return $this->btInterfaceWidth;
		}
		
		/**
		 * Returns the height of the block type's interface when presented in page.
		 * @return int
		 */
		public function getInterfaceHeight() {
			return $this->btInterfaceHeight;
		}
		
		/**
		 * Returns the description of the block type
		 * @return string
		 */
		public function getBlockTypeDescription() {
			return t($this->btDescription);
		}
		
		/**
		 * @access private
		 */
		public function isActiveWhenAdded() {
			return $this->btActiveWhenAdded;
		}
		
		/**
		 * @access private
		 */
		public function isCopiedWhenPropagated() {
			return $this->btCopyWhenPropagate;
		}
		
		/**
		 * Returns whether this block type is included in all versions. Default is false - block types are typically versioned but sometimes it makes sense not to do so.
		 * @return bool
		 */
		public function includeAll() {
			return $this->btIncludeAll;
		}
		
		/**
		 * Returns whether this block type is internal to Concrete. If it's internal it's not displayed in the front end interface. Examples include the LibraryFile block.
		 * @return bool
		 */
		public function isBlockTypeInternal() {
			return $this->btIsInternal;
		}
		
		/** 
		 * Returns a key/value array of strings that is used to translate items when used in javascript
		 */
		public function getJavaScriptStrings() {
			return array();
		}
		
	}
	