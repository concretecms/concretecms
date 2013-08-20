<?
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

	class Concrete5_Library_BlockController extends Controller {
		
		protected $record; // blockrecord
		protected $helpers = array('form');
		protected static $sets;
		
		protected $block;
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
		protected $btCacheBlockOutputLifetime = 0; //until manually updated or cleared
		protected $btCacheBlockOutputOnPost = false;
		protected $btCacheBlockOutputForRegisteredUsers = false;
		
		protected $btExportPageColumns = array();
		protected $btExportFileColumns = array();
		protected $btExportPageTypeColumns = array();
		
		protected $btWrapperClass = '';
		
		public $headerItems = array();
		
		

		protected $identifier;
		
		public function getIdentifier() {
			return $this->identifier;
		}

		/**
		 * Sets a value used by a particular block. These variables will automatically be present in the corresponding views used by the block.
		 * @param string $key
		 * @param string $value
		 * @return void
		 */
		public function set($key, $value) {
			self::$sets[$this->identifier][$key] = $value;		
		}
		
		public function get($key, $defaultValue = null) {
			if (isset(BlockController::$sets[$this->identifier][$key])) {
				return BlockController::$sets[$this->identifier][$key];
			}
			
			return parent::get($key, $defaultValue);
		}

		public function getBlockTypeWrapperClass() {return $this->btWrapperClass;}
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
		 *
		 * There are several different possible return values:
		 *  Returns FALSE if $btTable is set but no db.xml file exists.
		 *  Otherwise returns object with two properties: ->result (a boolean), and ->message (a string).
		 *  If ->result is true, the installation was successful
		 *  (although the db.xml file might only have one field declared which will cause C5 to have problems later on, so you you will want to check for that separately).
		 *  If ->result is false, the installation failed and you can check ->message for the explanation
		 *  (usually -- sometimes ->message will be blank, in which case there's either a malformed db.xml file or an "unknown database error").
		 * See concrete/models/block_types.php::doInstallBlockType() for usage example.
		 *
		 * @param string $path
		 * @return mixed boolean or object having ->result (boolean) and ->message (string) properties
		 */
		function install($path) {
			// passed path is the path to this block (try saying that ten times fast)
			// create the necessary table
			if (!$this->btTable) {
				$r = new stdClass;
				$r->result = true;
				return $r;
			}
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
		
		public function getBlockControllerData() {
			return $this->record;
		}
		
		/**
		 * Run when a block is added or edited. Automatically saves block data against the block's database table. If a block needs to do more than this (save to multiple tables, upload files, etc... it should override this.
		 * @param array $args
		 * @return void
		 */
		public function save($args) {
			//$argsMerged = array_merge($_POST, $args);
			if ($this->btTable) {
				$db = Loader::db();
				$columns = $db->GetCol('show columns from `' . $this->btTable . '`'); // I have no idea why getAttributeNames isn't working anymore.
				$this->record = new BlockRecord($this->btTable);
				$this->record->bID = $this->bID;
				foreach($columns as $key) {
					if (isset($args[$key])) {
						$this->record->{$key} = $args[$key];
					}
				}
				$this->record->Replace();
				if ($this->cacheBlockRecord() && ENABLE_BLOCK_CACHE) {
					$record = serialize($this->record);
					$db = Loader::db();
					$db->Execute('update Blocks set btCachedBlockRecord = ? where bID = ?', array($record, $this->bID));
				}
			}
		}
		
		/**
		 *
		 * Gets the permissions object for this controller's block
		 *
		 */
		public function getPermissionObject() {
			$bp = new Permissions(Block::getByID($this->bID));
			return $bp;
		}

		/** 
		 * @deprecated
		 */
		public function getPermissionsObject() {
			return $this->getPermissionObject();
		}
				
		/**
		 * Automatically run when a block is duplicated. This most likely happens when a block is edited: a block is first duplicated, and then presented to the user to make changes.
		 * @param int $newBlockID
		 * @return BlockRecord $newInstance
		 */
		public function duplicate($newBID) {
			if ($this->btTable) {
				$ni = new BlockRecord($this->btTable);
				$ni->bID = $this->bID;
				$ni->Load('bID=' . $this->bID);
				$newInstance = clone $ni;
				$newInstance->bID = $newBID;
				$newInstance->Insert();
				return $newInstance;
			}
		}
		
		public function __wakeup() {
			$this->__construct();
		}
		
		public function getBlockTypeDatabaseTable() {
			return $this->btTable;
		}
		
		public function export(SimpleXMLElement $blockNode) {

			$tables[] = $this->getBlockTypeDatabaseTable();
			if (isset($this->btExportTables)) {
				$tables = $this->btExportTables;
			}
			$db = Loader::db();
		
			foreach($tables as $tbl) {
				if (!$tbl) {
					continue;
				}
				$data = $blockNode->addChild('data');
				$data->addAttribute('table', $tbl);
				$columns = $db->MetaColumns($tbl);
				// remove columns we don't want
				unset($columns['BID']);
				$r = $db->Execute('select * from ' . $tbl . ' where bID = ?', array($this->bID));
				while ($record = $r->FetchRow()) {
					$tableRecord = $data->addChild('record');
					foreach($record as $key => $value) {
						if (isset($columns[strtoupper($key)])) {
							if (in_array($key, $this->btExportPageColumns)) {
								$tableRecord->addChild($key, ContentExporter::replacePageWithPlaceHolder($value));
							} else if (in_array($key, $this->btExportFileColumns)) {
								$tableRecord->addChild($key, ContentExporter::replaceFileWithPlaceHolder($value));
							} else if (in_array($key, $this->btExportPageTypeColumns)) {
								$tableRecord->addChild($key, ContentExporter::replacePageTypeWithPlaceHolder($value));
							} else {
								$cnode = $tableRecord->addChild($key);
								$node = dom_import_simplexml($cnode);
								$no = $node->ownerDocument;
								$node->appendChild($no->createCDataSection($value));
							}
						}
					}
				}
			}
		}

		protected function getImportData($blockNode) {
			$args = array();
			if (isset($blockNode->data)) {
				foreach($blockNode->data as $data) {
					if ($data['table'] == $this->getBlockTypeDatabaseTable()) {
						if (isset($data->record)) {
							foreach($data->record->children() as $node) {
								$args[$node->getName()] = ContentImporter::getValue((string) $node);
							}
						}
					} 
				}
			}
			return $args;
		}

		protected function importAdditionalData($b, $blockNode) {
			if (isset($blockNode->data)) {
				foreach($blockNode->data as $data) {
					if (strtoupper($data['table']) != strtoupper($this->getBlockTypeDatabaseTable())) {
						$table = (string) $data['table'];
						if (isset($data->record)) {
							foreach($data->record as $record) {
								$aar = new ADODB_Active_Record($table);
								$aar->bID = $b->getBlockID();
								foreach($record->children() as $node) {
									$nodeName = $node->getName();
									$aar->{$nodeName} = ContentImporter::getValue((string) $node);
								}
								$aar->Save();
							}
						}								
					}
				}
			}
		}
		
		public function import($page, $arHandle, SimpleXMLElement $blockNode) {
			$args = array();
			$db = Loader::db();
			// handle the adodb stuff
			$args = $this->getImportData($blockNode);
			
			$bt = BlockType::getByHandle($this->btHandle);
			$b = $page->addBlock($bt, $arHandle, $args);
			$b->updateBlockInformation(array('bName' => $blockNode['name'], 'bFilename' => $blockNode['custom-template']));
			
			if ($page->isMasterCollection() && $blockNode['mc-block-id'] != '') {
				ContentImporter::addMasterCollectionBlockID($b, (string) $blockNode['mc-block-id']);		
			}					
			
			// now we insert stuff that isn't part of the btTable
			// we have to do this this way because we need a bID
			$this->importAdditionalData($b, $blockNode);
		}
		
		public function cacheBlockRecord() {
			return $this->btCacheBlockRecord;
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

		public function field($fieldName) {
			$field = '_bf[' . $this->identifier;
			$b = $this->getBlockObject();
			if (is_object($b)) {
				$xc = $b->getBlockCollectionObject();
				if (is_object($xc)) {
					$field .= '_' . $xc->getCollectionID();
				}
			}
			$field .= '][' . $fieldName . ']';
			return $field;
		}

		public function post($field = false, $defaultValue = null) {
			// the only post that matters is the one for this attribute's name space
			$req = ($this->requestArray == false) ? $_POST : $this->requestArray;
			if (is_array($req['_bf'])) {
				$identifier = $this->identifier;
				$b = $this->getBlockObject();
				if (is_object($b)) {
					$xc = $b->getBlockCollectionObject();
					if (is_object($xc)) {
						$identifier .= '_' . $xc->getCollectionID();
					}
				}

				$p = $req['_bf'][$identifier];
				if ($field) {
					return $p[$field];
				}
				return $p;
			}			
			return parent::post($field,$defaultValue);
		}

		/**
		 * Automatically run when a block is deleted. This removes the special data from the block's specific database table. If a block needs to do more than this this method should be overridden.
		 * @return $void
		 */
		public function delete() {
			if ($this->bID > 0) {
				if ($this->btTable) {
					$ni = new BlockRecord($this->btTable);
					$ni->bID = $this->bID;
					$ni->Load('bID=' . $this->bID);
					$ni->delete();
				}
			}
		}

		/** 
		 * Loads the BlockRecord class based on its attribute names
		 * @return void
		 */
		protected function load() {
			if ($this->btTable) {
				if ($this->btCacheBlockRecord && $this->btCachedBlockRecord && ENABLE_BLOCK_CACHE) {
					$this->record = unserialize($this->btCachedBlockRecord);
				} else { 
					$this->record = new BlockRecord($this->btTable);
					$this->record->bID = $this->bID;
					$this->record->Load('bID=' . $this->bID);
					if ($this->btCacheBlockRecord && ENABLE_BLOCK_CACHE) {
						// this is the first time we're loading
						$record = serialize($this->record);
						$db = Loader::db();
						$db->Execute('update Blocks set btCachedBlockRecord = ? where bID = ?', array($record, $this->bID));
					}
				}
			}

			$ret = Events::fire('on_block_load', $this->record, $this->btHandle, $this->bID);
			if ($ret && is_object($ret)){
				$this->record = $ret;
			}

			if (is_object($this->record)) {
				foreach($this->record as $key => $value) {
					$this->{$key} = $value;
					$this->set($key, $value);
				}
			}
		}
		
		/**
		 * Instantiates the block controller.
		 * @param BlockType $obj|Block $obj
		 */
		public function __construct($obj = null) {
			if ($obj instanceof BlockType) {
				$this->identifier = 'BLOCKTYPE_' . $obj->getBlockTypeID();
				$this->btHandle = $obj->getBlockTypeHandle();
			} else if ($obj instanceof Block) {
				$b = $obj;
				$this->identifier = 'BLOCK_' . $obj->getBlockID();
				$this->bID = $b->getBlockID();
				$this->btHandle = $obj->getBlockTypeHandle();
				$this->bActionCID = $obj->getBlockActionCollectionID();
				$this->btCachedBlockRecord = $obj->getBlockCachedRecord();
				$this->load();
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
				$this->on_start($method);
			}
			if ($method) {
				$this->runTask($method, array());
			}
			
			if (method_exists($this, 'on_before_render')) {
				$this->on_before_render($method);
			}
		}

		
		/**
		 * Gets the generic Block object attached to this controller's instance
		 * @return Block $b
		 */
		public function getBlockObject() {
			if (is_object($this->block)) {
				return $this->block;
			}
			return Block::getByID($this->bID);
		}

		/** 
		 * Sets the block object for this controller
		 */
		public function setBlockObject($b) {
			$this->block = $b;
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
		 * Returns HTML that will be shown when a user wants help for a given block type
		 */
		public function getBlockTypeHelp() {
			return $this->btHelpContent;
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
	
