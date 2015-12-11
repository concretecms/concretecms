<?php

namespace Concrete\Core\Block;

use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\View\BlockViewTemplate;
use Concrete\Core\Controller;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Legacy\BlockRecord;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Config;
use Database;
use Events;
use Package;
use Page;

class BlockController extends \Concrete\Core\Controller\AbstractController
{
    public $headerItems = array(); // blockrecord
    public $blockViewRenderOverride;
    protected $record;
    protected $helpers = array('form');
    protected $block;
    protected $bID;
    protected $btDescription = "";
    protected $btName = "";
    protected $btHandle = "";
    protected $btIsInternal = 0;
    protected $btSupportsInlineAdd = false;
    protected $btIgnorePageThemeGridFrameworkContainer = false;
    protected $btSupportsInlineEdit = false;
    protected $btCopyWhenPropagate = 0;
    protected $btIncludeAll = 0;
    protected $btInterfaceWidth = "400";
    protected $btInterfaceHeight = "400";
    protected $btHasRendered = false;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputLifetime = 0;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $bActionCID;
    protected $btExportPageColumns = array();
    protected $btExportFileColumns = array();
    protected $btExportPageTypeColumns = array();
    protected $btExportPageFeedColumns = array();
    protected $btWrapperClass = '';
    protected $btDefaultSet;
    protected $btFeatures = array();
    protected $btFeatureObjects;
    protected $identifier;
    protected $btTable = null;

    public function getBlockTypeExportPageColumns()
    {
        return $this->btExportPageColumns;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getBlockTypeWrapperClass()
    {
        return $this->btWrapperClass;
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
     *
     * @return mixed boolean or object having ->result (boolean) and ->message (string) properties
     */
    public function install($path)
    {
        // passed path is the path to this block (try saying that ten times fast)
        // create the necessary table

        if (!$this->btTable) {
            $r = new \stdClass();
            $r->result = true;

            return $r;
        }
        $ret = Package::installDB($path . '/' . FILENAME_BLOCK_DB);

        return $ret;
    }

    /**
     * Renders a view in the block's folder.
     * <code>
     * public function view() { // The view() method is automatically run when a block is viewed
     *     $this->render("other_special_view"); // don't use .php
     * }
     * </code>
     *
     * @param string $view
     */
    public function render($view)
    {
        $this->blockViewRenderOverride = $view;
    }

    public function validate($args)
    {
        return true;
    }

    public function getBlockControllerData()
    {
        return $this->record;
    }

    /**
     * Run when a block is added or edited. Automatically saves block data against the block's database table. If a block needs to do more than this (save to multiple tables, upload files, etc... it should override this.
     *
     * @param array $args
     */
    public function save($args)
    {
        //$argsMerged = array_merge($_POST, $args);
        if ($this->btTable) {
            $db = Database::connection();
            $columns = $db->MetaColumnNames($this->btTable);
            $this->record = new BlockRecord($this->btTable);
            $this->record->bID = $this->bID;
            foreach ($columns as $key) {
                if (isset($args[$key])) {
                    $this->record->{$key} = $args[$key];
                }
            }
            $this->record->Replace();
            if ($this->cacheBlockRecord() && Config::get('concrete.cache.blocks')) {
                $record = base64_encode(serialize($this->record));
                $db = Database::connection();
                $db->Execute('update Blocks set btCachedBlockRecord = ? where bID = ?', array($record, $this->bID));
            }
        }
    }

    public function cacheBlockRecord()
    {
        return $this->btCacheBlockRecord;
    }

    /**
     * @deprecated
     */
    public function getPermissionsObject()
    {
        return $this->getPermissionObject();
    }

    public function getBlockTypeDefaultSet()
    {
        return $this->btDefaultSet;
    }

    /**
     * Gets the permissions object for this controller's block.
     */
    public function getPermissionObject()
    {
        $bp = new Permissions(Block::getByID($this->bID));

        return $bp;
    }

    /**
     * Automatically run when a block is duplicated. This most likely happens when a block is edited: a block is first duplicated, and then presented to the user to make changes.
     *
     * @param int $newBlockID
     *
     * @return BlockRecord $newInstance
     */
    public function duplicate($newBID)
    {
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

    public function __wakeup()
    {
        $this->__construct();
    }

    /**
     * Instantiates the block controller.
     *
     * @param BlockType $obj |Block $obj
     */
    public function __construct($obj = null)
    {
        parent::__construct();
        if ($obj instanceof BlockType) {
            $this->identifier = 'BLOCKTYPE_' . $obj->getBlockTypeID();
            $this->btHandle = $obj->getBlockTypeHandle();
        } else {
            if ($obj instanceof Block) {
                $b = $obj;
                $this->identifier = 'BLOCK_' . $obj->getBlockID();
                $this->bID = $b->getBlockID();
                $this->btHandle = $obj->getBlockTypeHandle();
                $this->btCachedBlockRecord = $obj->getBlockCachedRecord();
                $this->setBlockObject($b);
                $this->load();
            }
        }
        $this->set('controller', $this);
    }

    /**
     * Sets the block object for this controller.
     */
    public function setBlockObject($b)
    {
        $this->block = $b;
    }

    /**
     * Loads the BlockRecord class based on its attribute names.
     */
    protected function load()
    {
        if ($this->btTable) {
            if ($this->btCacheBlockRecord && $this->btCachedBlockRecord && Config::get('concrete.cache.blocks')) {
                $this->record = unserialize(base64_decode($this->btCachedBlockRecord));
            } else {
                $this->record = new BlockRecord($this->btTable);
                $this->record->bID = $this->bID;
                $this->record->Load('bID=' . $this->bID);
                if ($this->btCacheBlockRecord && Config::get('concrete.cache.blocks')) {
                    // this is the first time we're loading
                    $record = base64_encode(serialize($this->record));
                    $db = Database::connection();
                    $db->Execute('update Blocks set btCachedBlockRecord = ? where bID = ?', array($record, $this->bID));
                }
            }
        }

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('record', $this->record);
        $event->setArgument('btHandle', $this->btHandle);
        $event->setArgument('bID', $this->bID);
        $ret = Events::dispatch('on_block_load', $event);
        $this->record = $ret->getArgument('record');

        if (is_object($this->record)) {
            foreach ($this->record as $key => $value) {
                $this->{$key} = $value;
                $this->set($key, $value);
            }
        }
    }

    public function getBlockTypeFeatureObjects()
    {
        if (!isset($this->btFeatureObjects)) {
            $this->btFeatureObjects = array();
            foreach ($this->btFeatures as $feHandle) {
                $fe = Feature::getByHandle($feHandle);
                if (is_object($fe)) {
                    $this->btFeatureObjects[] = $fe;
                }
            }
        }

        return $this->btFeatureObjects;
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $tables[] = $this->getBlockTypeDatabaseTable();
        if (isset($this->btExportTables)) {
            $tables = $this->btExportTables;
        }
        $db = Database::connection();

        foreach ($tables as $tbl) {
            if (!$tbl) {
                continue;
            }
            $data = $blockNode->addChild('data');
            $data->addAttribute('table', $tbl);
            $columns = $db->MetaColumns($tbl);
            // remove columns we don't want
            unset($columns['bid']);
            $r = $db->Execute('select * from ' . $tbl . ' where bID = ?', array($this->bID));
            while ($record = $r->FetchRow()) {
                $tableRecord = $data->addChild('record');
                foreach ($record as $key => $value) {
                    if (isset($columns[strtolower($key)])) {
                        if (in_array($key, $this->btExportPageColumns)) {
                            $tableRecord->addChild($key, ContentExporter::replacePageWithPlaceHolder($value));
                        } elseif (in_array($key, $this->btExportFileColumns)) {
                            $tableRecord->addChild($key, ContentExporter::replaceFileWithPlaceHolder($value));
                        } elseif (in_array($key, $this->btExportPageTypeColumns)) {
                            $tableRecord->addChild($key, ContentExporter::replacePageTypeWithPlaceHolder($value));
                        } elseif (in_array($key, $this->btExportPageFeedColumns)) {
                            $tableRecord->addChild($key, ContentExporter::replacePageFeedWithPlaceHolder($value));
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

    public function getBlockTypeDatabaseTable()
    {
        return $this->btTable;
    }

    public function import($page, $arHandle, \SimpleXMLElement $blockNode)
    {
        $db = Database::connection();
        // handle the adodb stuff
        $args = $this->getImportData($blockNode, $page);
        $blockData = array();

        $bt = BlockType::getByHandle($this->btHandle);
        $b = $page->addBlock($bt, $arHandle, $args);
        $bName = (string) $blockNode['name'];
        $bFilename = (string) $blockNode['custom-template'];
        if ($bName) {
            $blockData['bName'] = $bName;
        }
        if ($bFilename) {
            $blockData['bFilename'] = $bFilename;
        }
        if (count($blockData)) {
            $b->updateBlockInformation($blockData);
        }

        if ($page->isMasterCollection() && $blockNode['mc-block-id'] != '') {
            ContentImporter::addMasterCollectionBlockID($b, (string) $blockNode['mc-block-id']);
        }

        // now we insert stuff that isn't part of the btTable
        // we have to do this this way because we need a bID
        $this->importAdditionalData($b, $blockNode);

        // now we handle the styles
        if (isset($blockNode->style)) {
            $set = StyleSet::import($blockNode->style);
            $b->setCustomStyleSet($set);
        }

        // now we handle block caching
        $cache = (int) $blockNode['cache-output'];
        if ($cache) {
            $b->setCustomCacheSettings(true, $blockNode['cache-output-on-post'], $blockNode['cache-output-for-registered-users'],
                $blockNode['cache-output-lifetime']);
        }
    }

    protected function getImportData($blockNode, $page)
    {
        $args = array();
        $inspector = \Core::make('import/value_inspector');
        if (isset($blockNode->data)) {
            foreach ($blockNode->data as $data) {
                if ($data['table'] == $this->getBlockTypeDatabaseTable()) {
                    if (isset($data->record)) {
                        foreach ($data->record->children() as $node) {
                            $result = $inspector->inspect((string) $node);
                            $args[$node->getName()] = $result->getReplacedValue();
                        }
                    }
                }
            }
        }

        return $args;
    }

    protected function importAdditionalData($b, $blockNode)
    {
        $inspector = \Core::make('import/value_inspector');
        if (isset($blockNode->data)) {
            foreach ($blockNode->data as $data) {
                if (strtoupper($data['table']) != strtoupper($this->getBlockTypeDatabaseTable())) {
                    $table = (string) $data['table'];
                    if (isset($data->record)) {
                        foreach ($data->record as $record) {
                            $aar = new \Concrete\Core\Legacy\BlockRecord($table);
                            $aar->bID = $b->getBlockID();
                            foreach ($record->children() as $node) {
                                $nodeName = $node->getName();
                                $result = $inspector->inspect((string) $node);
                                $aar->{$nodeName} = $result->getReplacedValue();
                            }
                            $aar->Save();
                        }
                    }
                }
            }
        }
    }

    public function setPassThruBlockController(PageController $controller)
    {
        $controller->setPassThruBlockController($this->block, $this);
    }

    public function validateAddBlockPassThruAction(Checker $ap, BlockType $bt)
    {
        return $ap->canAddBlock($bt);
    }

    public function validateEditBlockPassThruAction(Block $b)
    {
        $bp = new \Permissions($b);
        return $bp->canEditBlock();
    }

    public function validateComposerAddBlockPassThruAction(Type $type)
    {
        $pp = new \Permissions($type);
        return $pp->canAddPageType();
    }

    public function validateComposerEditBlockPassThruAction(Block $b)
    {
        return $this->validateEditBlockPassThruAction($b);
    }

    public function getPassThruActionAndParameters($parameters)
    {
        $method = 'action_' . $parameters[0];
        $parameters = array_slice($parameters, 1);

        return array($method, $parameters);
    }

    public function isValidControllerTask($method, $parameters = array())
    {
        if (strpos($method, 'action_') !== 0) { // gotta start with action_
            return false;
        }
        if (is_callable(array($this, $method))) {
            $r = new \ReflectionMethod(get_class($this), $method);
            if (count($parameters) - $r->getNumberOfParameters() <= 1) {
                // how do we get <= 1? If it's 1, that means that the method has one fewer param. That's ok because
                // certain older blocks don't know that the last param ought to be a $bID. If they're equal it's zero
                // which is best. and if they're greater that's ok too.
                return true;
            }
        }

        return false;
    }

    public function cacheBlockOutput()
    {
        return $this->btCacheBlockOutput;
    }

    public function cacheBlockOutputForRegisteredUsers()
    {
        return $this->btCacheBlockOutputForRegisteredUsers;
    }

    public function cacheBlockOutputOnPost()
    {
        return $this->btCacheBlockOutputOnPost;
    }

    public function getBlockTypeCacheOutputLifetime()
    {
        return $this->btCacheBlockOutputLifetime;
    }

    public function getCollectionObject()
    {
        if (!$this->block) {
            return false;
        }

        if (!isset($this->bActionCID)) {
            $this->bActionCID = $this->block->getBlockActionCollectionID();
        }

        if ($this->bActionCID > 0) {
            return Page::getByID($this->bActionCID);
        }

        return Page::getCurrentPage();
    }

    public function field($fieldName)
    {
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

    /**
     * Gets the generic Block object attached to this controller's instance.
     *
     * @return Block $b
     */
    public function getBlockObject()
    {
        if (is_object($this->block)) {
            return $this->block;
        }

        return Block::getByID($this->bID);
    }

    public function post($field = false, $defaultValue = null)
    {
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

        return parent::post($field, $defaultValue);
    }

    /**
     * Automatically run when a block is deleted. This removes the special data from the block's specific database table. If a block needs to do more than this this method should be overridden.
     *
     * @return $void
     */
    public function delete()
    {
        if ($this->bID > 0) {
            if ($this->btTable) {
                $ni = new BlockRecord($this->btTable);
                $ni->bID = $this->bID;
                $ni->Load('bID=' . $this->bID);
                $ni->delete();
            }
        }
    }

    public function outputAutoHeaderItems()
    {
        $b = $this->getBlockObject();
        if (is_object($b)) {
            $bvt = new BlockViewTemplate($b);
            $bvt->registerTemplateAssets();
        }
    }

    public function registerViewAssets($outputContent = '')
    {
    }

    public function setupAndRun($method)
    {
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
     * Gets the Area object attached to this controller's instance.
     *
     * @return Area $a
     */
    public function getAreaObject()
    {
        return $this->area;
    }

    public function setAreaObject($a)
    {
        $this->area = $a;
    }

    /**
     * @access private
     *
     * @todo   Make block's uninstallable
     */
    public function uninstall()
    {
        // currently blocks cannot be uninstalled
    }

    /**
     * Returns the name of the block type.
     *
     * @return string $btName
     */
    public function getBlockTypeName()
    {
        return t($this->btName);
    }

    /**
     * Returns the width of the block type's interface when presented in page.
     *
     * @return int
     */
    public function getInterfaceWidth()
    {
        return $this->btInterfaceWidth;
    }

    /**
     * Returns the height of the block type's interface when presented in page.
     *
     * @return int
     */
    public function getInterfaceHeight()
    {
        return $this->btInterfaceHeight;
    }

    /**
     * Returns the description of the block type.
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t($this->btDescription);
    }

    /**
     * Returns HTML that will be shown when a user wants help for a given block type.
     */
    public function getBlockTypeHelp()
    {
        return $this->btHelpContent;
    }

    /**
     * @access private
     */
    public function isCopiedWhenPropagated()
    {
        return $this->btCopyWhenPropagate;
    }

    /**
     * Returns whether this block type is included in all versions. Default is false - block types are typically versioned but sometimes it makes sense not to do so.
     *
     * @return bool
     */
    public function includeAll()
    {
        return $this->btIncludeAll;
    }

    /**
     * Returns whether this block type is internal to Concrete. If it's internal it's not displayed in the front end interface. Examples include the LibraryFile block.
     *
     * @return bool
     */
    public function isBlockTypeInternal()
    {
        return $this->btIsInternal;
    }

    /**
     * if a the current BlockType supports inline edit or not.
     *
     * @return bool
     */
    public function supportsInlineEdit()
    {
        return $this->btSupportsInlineEdit;
    }

    /**
     * if a the current BlockType supports inline add or not.
     *
     * @return bool
     */
    public function supportsInlineAdd()
    {
        return $this->btSupportsInlineAdd;
    }

    /**
     * If true, container classes will not be wrapped around this block type in edit mode (if the
     * theme in question supports a grid framework.
     *
     * @return bool
     */
    public function ignorePageThemeGridFrameworkContainer()
    {
        return $this->btIgnorePageThemeGridFrameworkContainer;
    }

    /**
     * Returns a key/value array of strings that is used to translate items when used in javascript.
     */
    public function getJavaScriptStrings()
    {
        return array();
    }
}
