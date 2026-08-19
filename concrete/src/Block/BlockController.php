<?php
namespace Concrete\Core\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Block\View\BlockViewTemplate;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Legacy\BlockRecord;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Utility\Service\Xml;
use Config;
use Database;
use Events;
use Package;
use Page;

class BlockController extends \Concrete\Core\Controller\AbstractController
{
    public $headerItems = []; // blockrecord
    public $blockViewRenderOverride;
    protected $record;
    protected $helpers = ['form'];
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
    /** @var string | int  */
    protected $btInterfaceWidth = "400";
    /** @var string | int  */
    protected $btInterfaceHeight = "400";
    protected $btHasRendered = false;
    /**
     * @var bool Defaults to true. When block caching is enabled, this means that the block's database record data will
     *           also be cached. This can almost always be set to true.
     */
    protected $btCacheBlockRecord = true;
    /**
     * @var bool Defaults to false. When block caching is enabled, enabling this boolean means that the output of the
     *           block will be saved and delivered without rendering the view() function or hitting the database at all.
     */
    protected $btCacheBlockOutput = false;
    /**
     * @var int Defaults to no time limit (0). When block caching is enabled and output caching is enabled for a block,
     *          this is the value in seconds that the cache will last before being refreshed.
     */
    protected $btCacheBlockOutputLifetime = 0;
    /**
     * @var bool Defaults to false. This determines whether a block will cache its output on POST. Some blocks can
     *           cache their output but must serve uncached output on POST in order to show error messages, etc…
     */
    protected $btCacheBlockOutputOnPost = false;
    /**
     * @var bool Defaults to false. Determines whether a block that can cache its output will continue to cache its
     *           output even if the current user viewing it is logged in.
     */
    protected $btCacheBlockOutputForRegisteredUsers = false;
    /**
     * @var bool Defaults to false. This determines whether a block will cache its output on edit mode.
     *           If the block show messages like "This block is disabled in edit mode" for editing users but still
     *           enable to cache it's output for other users, consider keeping this value false and set true for
     *           $btCacheBlockOutputForRegisteredUsers.
     */
    protected $btCacheBlockOutputOnEditMode = false;
    protected $bActionCID;

    /**
     * The name of the main database table where the block stores its data.
     * If defined, the table must have at least an integer field named bID.
     *
     * @var string|null
     */
    protected $btTable = null;

    /**
     * The names of all the database tables managed by the block.
     * Eevry table must have at least an integer field named bID.
     *
     * @var string[]|null
     */
    protected $btExportTables = null;

    /**
     * The names of the fields in the database table defined by $btTable and $btExportTables that contain the ID of a Concrete page.
     *
     * @var string[]
     */
    protected $btExportPageColumns = [];

    /**
     * The names of the fields in the database table defined by $btTable and $btExportTables that contain the ID of a Concrete file.
     *
     * @var string[]
     */
    protected $btExportFileColumns = [];

    /**
     * The names of the fields in the database table defined by $btTable and $btExportTables that contain Rich Text or HTML.
     *
     * @var string[]
     */
    protected $btExportContentColumns = [];

    /**
     * The names of the fields in the database table defined by $btTable and $btExportTables that contain the ID of a page type.
     *
     * @var string[]
     */
    protected $btExportPageTypeColumns = [];

    /**
     * The names of the fields in the database table defined by $btTable and $btExportTables that contain the ID of an RSS page feed.
     *
     * @var string[]
     */
    protected $btExportPageFeedColumns = [];

    /**
     * The names of the fields in the database table defined by $btTable and $btExportTables that contain the ID of an Concrete folder of files.
     *
     * @var string[]
     */
    protected $btExportFileFolderColumns = [];

    /**
     * The value built by createExportDeclarations(), once getExportDeclarations() has been called.
     *
     * @var \Concrete\Core\Block\ExportDeclarations|null
     */
    private $exportDeclarations;

    protected $btWrapperClass = '';
    protected $btDefaultSet;
    protected $identifier;
    protected $btID;
    /** @var array */
    protected $requestArray;
    /**
     * @deprecated What's deprecated is the "public" part.
     * @var string|null
     */
    public $btCachedBlockRecord;

    /**
     * @internal
     * Note: do not rely on these being here. These are going to be moved.
     */
    public static $btTitleFormats = ['h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'p' => 'Normal'];

    public $saveMode = SaveMode::SAVE_MODE_REQUEST;

    /**
     * Set this to true if the data sent to the save/performSave methods can contain NULL values that should be persisted.
     *
     * @var bool
     */
    protected $supportSavingNullValues = false;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var \Concrete\Core\Area\Area|null
     */
    public $area;

    public function getBlockTypeInSetName()
    {
        return $this->getBlockTypeName();
    }

    /**
     * Get the names of the fields in the database table defined by $btTable and $btExportTables that contain the ID of a Concrete page.
     *
     * @deprecated use getExportDeclarations() to get all the declarations of the block type at once
     *
     * @return string[]
     *
     * @see \Concrete\Core\Block\BlockController::getExportDeclarations()
     */
    public function getBlockTypeExportPageColumns()
    {
        return $this->btExportPageColumns;
    }

    /**
     * Get what this block type declares about the data it owns, so that it can be exported to (and
     * imported from) the installation-independent CIF format.
     *
     * The declarations are built just once: override createExportDeclarations() to customize them.
     */
    final public function getExportDeclarations(): ExportDeclarations
    {
        if ($this->exportDeclarations === null) {
            $this->exportDeclarations = $this->createExportDeclarations();
        }

        return $this->exportDeclarations;
    }

    /**
     * Build what this block type declares about the data it owns.
     *
     * Block types that don't simply list their columns in the $btExport... properties should override
     * this method: its result is cached by getExportDeclarations(), so it's called just once.
     */
    protected function createExportDeclarations(): ExportDeclarations
    {
        return new ExportDeclarations(
            (string) $this->getBlockTypeDatabaseTable(),
            $this->btExportTables === null ? [] : $this->btExportTables,
            [
                // the deprecated getter may be overridden, so we can't simply read the property here
                ExportDeclarations::REFERENCE_PAGE => $this->getBlockTypeExportPageColumns(),
                ExportDeclarations::REFERENCE_FILE => $this->btExportFileColumns,
                ExportDeclarations::REFERENCE_PAGE_TYPE => $this->btExportPageTypeColumns,
                ExportDeclarations::REFERENCE_PAGE_FEED => $this->btExportPageFeedColumns,
                ExportDeclarations::REFERENCE_FILE_FOLDER => $this->btExportFileFolderColumns,
                ExportDeclarations::REFERENCE_CONTENT => $this->btExportContentColumns,
            ]
        );
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
     * </code>.
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
    public function install($path, string $importMode = ContentImporter::IMPORT_MODE_UPGRADE)
    {
        // passed path is the path to this block (try saying that ten times fast)
        // create the necessary table

        if (!$this->btTable) {
            $r = new \stdClass();
            $r->result = true;

            return $r;
        }
        $ret = Package::installDB($path . '/' . FILENAME_BLOCK_DB, $importMode);

        return $ret;
    }

    /**
     * Renders a view in the block's folder.
     * <code>
     * public function view() { // The view() method is automatically run when a block is viewed
     *     $this->render("other_special_view"); // don't use .php
     * }
     * </code>.
     *
     * @param string $view
     */
    public function render($view)
    {
        $this->blockViewRenderOverride = $view;
    }

    /**
     * Used to validate a blocks data before saving to the database
     * Generally should return an empty ErrorList if valid
     * Custom Packages may return a boolean value
     *
     * @param $args array|string|null
     * @version <= 8.4.3 Method returns ErrorList|boolean
     * @version 8.5.0a3 Method returns ErrorList
     * @return ErrorList|boolean
     */
    public function validate($args)
    {
        $e = $this->app->make(ErrorList::class);
        return $e;
    }

    public function getBlockControllerData()
    {
        return $this->record;
    }

    /**
     * Persist the block options.
     *
     * @param array $args An array that contains the block options
     * @param bool $loadExisting Shall we initialize the record to be saved with the current data?
     */
    protected function performSave($args, $loadExisting = false)
    {
        //$argsMerged = array_merge($_POST, $args);
        if ($this->btTable) {
            $db = Database::connection();
            $columns = $db->MetaColumnNames($this->btTable);
            $this->record = new BlockRecord($this->btTable);
            $this->record->bID = $this->bID;
            if ($loadExisting) {
                $this->record->Load('bID=' . $this->bID);
            }

            if ($this->supportSavingNullValues) {
                foreach ($columns as $key) {
                    if (array_key_exists($key, $args)) {
                        $this->record->{$key} = $args[$key];
                    }
                }
            } else {
                foreach ($columns as $key) {
                    if (isset($args[$key])) {
                        $this->record->{$key} = $args[$key];
                    }
                }
            }

            $this->record->Replace();
            if ($this->cacheBlockRecord() && Config::get('concrete.cache.blocks')) {
                $record = base64_encode(serialize($this->record));
                $db = Database::connection();
                $db->Execute('update Blocks set btCachedBlockRecord = ? where bID = ?', [$record, $this->bID]);
            }
        }

        if ($this instanceof FileTrackableInterface) {
            $declarations = $this->getExportDeclarations();
            $fields = array_merge(
                $declarations->getColumns(ExportDeclarations::REFERENCE_FILE),
                $declarations->getColumns(ExportDeclarations::REFERENCE_CONTENT)
            );
            foreach ($fields as $field) {
                if (property_exists($this, $field)) {
                    $this->{$field} = $args[$field] ?? null;
                }
            }
            $this->app->make(AggregateTracker::class)->track($this);
        }
    }

    /**
     * Run when a block is added or edited. Automatically saves block data against the block's database table. If a block needs to do more than this (save to multiple tables, upload files, etc... it should override this.
     *
     * @param array<string,mixed> $args
     * @return void
     */
    public function save($args)
    {
        $this->performSave($args);
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
        $bp = new Checker($this->block);
        return $bp;
    }

    /**
     * Automatically run when a block is duplicated. This most likely happens when a block is edited: a block is first duplicated, and then presented to the user to make changes.
     *
     * @param int $newBlockID
     *
     * @return BlockRecord | null $newInstance
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
            $this->btID = $obj->getBlockTypeID();
            $this->identifier = 'BLOCKTYPE_' . $obj->getBlockTypeID();
            $this->btHandle = $obj->getBlockTypeHandle();
        } else {
            if ($obj instanceof Block) {
                $b = $obj;
                $this->btID = $b->getBlockTypeID();
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
                    $db->Execute('update Blocks set btCachedBlockRecord = ? where bID = ?', [$record, $this->bID]);
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

    /**
     * Add to a <block /> XML node the data stored in the block database tables.
     * In case a block uses more than one database table, we should have more <data /> elements (one for every table).
     *
     * @example: the Content block will add these children to $blockNode:
     * ```
     * <data table="btContentLocal">
     *     <record>
     *         <content>The Rich Text of the Content block</content>
     *     </record>
     * </data>
     * ```
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $declarations = $this->getExportDeclarations();
        $db = $this->app->make(Connection::class);

        $xml = $this->app->make(Xml::class);
        foreach ($declarations->getTables() as $tbl) {
            if (!$tbl) {
                continue;
            }
            $data = $blockNode->addChild('data');
            $data->addAttribute('table', $tbl);
            $columns = $db->MetaColumns($tbl);
            // remove columns we don't want
            unset($columns['bid']);
            $r = $db->executeQuery('select * from ' . $tbl . ' where bID = ?', [$this->bID]);
            while (($record = $r->fetchAssociative()) !== false) {
                $tableRecord = $data->addChild('record');
                foreach ($record as $key => $value) {
                    if (isset($columns[strtolower($key)])) {
                        if ($value === null) {
                            $tableRecord->addChild($key)->addAttribute('null', 'true');
                        } elseif ($value === 0 || $value === '0') {
                            $tableRecord->addChild($key, '0');
                        } else {
                            $xml->createChildElement($tableRecord, $key, $this->exportRecordValue($value, $declarations->getColumnReference($key)));
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the exported representation of the value of a record column.
     *
     * @param mixed $value
     * @param string $reference the kind of reference contained in the column (one of the ExportDeclarations::REFERENCE_... constants) (empty string if none)
     *
     * @return mixed
     */
    protected function exportRecordValue($value, string $reference)
    {
        switch ($reference) {
            case ExportDeclarations::REFERENCE_PAGE:
                return ContentExporter::replacePageWithPlaceHolder($value);
            case ExportDeclarations::REFERENCE_FILE:
                return ContentExporter::replaceFileWithPlaceHolder($value);
            case ExportDeclarations::REFERENCE_PAGE_TYPE:
                return ContentExporter::replacePageTypeWithPlaceHolder($value);
            case ExportDeclarations::REFERENCE_PAGE_FEED:
                return ContentExporter::replacePageFeedWithPlaceHolder($value);
            case ExportDeclarations::REFERENCE_FILE_FOLDER:
                return ContentExporter::replaceFileFolderWithPlaceHolder($value);
            case ExportDeclarations::REFERENCE_CONTENT:
                return LinkAbstractor::export((string) $value);
            default:
                return $value;
        }
    }

    public function getBlockTypeDatabaseTable()
    {
        return $this->btTable;
    }

    /**
     * Import a block instance extracting from an XML <block> element the data to be stored in the database.
     * The extraction is performed by getImportData() (its returned value will be passed to the save() method).
     * In case of more than one database table, importAdditionalData() will store in the database the data for the secondary database tables.
     *
     * @param \Concrete\Core\Page\Page $page
     * @param \Concrete\Core\Area\Area|string $arHandle the area instance (or its handle) where the block should be imported into
     *
     * @return \Concrete\Core\Block\Block
     */
    public function import($page, $arHandle, \SimpleXMLElement $blockNode)
    {
        $xml = $this->app->make(Xml::class);
        // handle the adodb stuff
        $args = $this->getImportData($blockNode, $page);
        $blockData = [];

        $bt = \Concrete\Core\Block\BlockType\BlockType::getByHandle($this->btHandle);
        $b = $page->addBlock($bt, $arHandle, $args, SaveMode::SAVE_MODE_IMPORT);
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

        // now we handle container settings
        $bCustomContainerSettings = $xml->getBool($blockNode['custom-container-settings'], null);
        if ($bCustomContainerSettings !== null) {
            $b->setCustomContainerSettings($bCustomContainerSettings);
        }

        // now we handle the styles
        if (isset($blockNode->style)) {
            $set = StyleSet::import($blockNode->style);
            $b->setCustomStyleSet($set);
        }

        // now we handle block caching
        if ($xml->getBool($blockNode['cache-output'])) {
            $b->setCustomCacheSettings(
                true,
                $xml->getBool($blockNode['cache-output-on-post']),
                $xml->getBool($blockNode['cache-output-for-registered-users']),
                $xml->getBool($blockNode['cache-output-lifetime'])
            );
        }

        if ($this instanceof FileTrackableInterface) {
            $blockController = $b->getController(); // We have to do this because we need it loaded with the right block object, data.
            $this->app->make(AggregateTracker::class)->track($blockController);
        }

        return $b;
    }

    /**
     * Extracts data from an XML <block> element, generating an array that will be passed to the save() method.
     *
     * @param \SimpleXMLElement $blockNode
     * @param \Concrete\Core\Page\Page $page
     *
     * @return array<string,mixed>
     */
    protected function getImportData($blockNode, $page)
    {
        $args = [];
        if (isset($blockNode->data)) {
            $declarations = $this->getExportDeclarations();
            foreach ($blockNode->data as $data) {
                if ($data['table'] == $this->getBlockTypeDatabaseTable()) {
                    if (isset($data->record)) {
                        foreach ($data->record->children() as $key => $node) {
                            $args[$node->getName()] = $this->importRecordValue($node, $declarations->getColumnReference($key));
                        }
                    }
                }
            }
        }

        return $args;
    }

    /**
     * Extract the data to be passed to the save() method from a block value received via the API.
     *
     * The API exposes block values in the CIF format (see the API block transformers), so they may contain
     * placeholders like {ccm:export:page:...}: this method resolves them exactly as an import from a CIF
     * file would do, by rebuilding the XML representation of the value and handing it to getImportData().
     *
     * Values whose key can't be an XML element name, as well as values that aren't strings, numbers or NULL,
     * are left untouched (they can't be columns handled by getImportData()).
     *
     * @param \Concrete\Core\Page\Page $page
     * @param array<string,mixed> $value
     *
     * @return array<string,mixed>
     */
    public function getImportDataFromApiValue($page, array $value): array
    {
        $xml = $this->app->make(Xml::class);
        $blockNode = new \SimpleXMLElement('<block></block>');
        $dataNode = $blockNode->addChild('data');
        $dataNode->addAttribute('table', (string) $this->getBlockTypeDatabaseTable());
        $recordNode = $dataNode->addChild('record');
        $untouched = [];
        foreach ($value as $key => $keyValue) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_.\-]*$/', (string) $key)) {
                $untouched[$key] = $keyValue;
            } elseif ($keyValue === null) {
                // that's how the export() method marks NULL values
                $recordNode->addChild((string) $key)->addAttribute('null', 'true');
            } elseif (is_string($keyValue) || is_int($keyValue) || is_float($keyValue)) {
                $xml->createChildElement($recordNode, (string) $key, (string) $keyValue);
            } else {
                $untouched[$key] = $keyValue;
            }
        }

        return array_merge($this->getImportData($blockNode, $page), $untouched);
    }

    /**
     * Get the imported representation of the value of a record column.
     *
     * @param string $reference the kind of reference contained in the column (one of the ExportDeclarations::REFERENCE_... constants) (empty string if none)
     *
     * @return string|null
     */
    protected function importRecordValue(\SimpleXMLElement $node, string $reference)
    {
        $value = (string) $node;
        if ($value === '' && isset($node['null']) && filter_var((string) $node['null'], FILTER_VALIDATE_BOOLEAN)) {
            return null;
        }
        if ($reference === '') {
            return $value;
        }
        $result = \Core::make('import/value_inspector')->inspect($value);

        return $reference === ExportDeclarations::REFERENCE_CONTENT ? $result->getReplacedContent() : $result->getReplacedValue();
    }

    /**
     * Save into the secondary database tables the data extracted from the <data> child elements of a an XML <block> element into the database.
     *
     * @param \Concrete\Core\Block\Block $b
     * @param \SimpleXMLElement $blockNode
     */
    protected function importAdditionalData($b, $blockNode)
    {
        if (isset($blockNode->data)) {
            $declarations = $this->getExportDeclarations();
            foreach ($blockNode->data as $data) {
                if (strtoupper((string) $data['table']) != strtoupper((string) $this->getBlockTypeDatabaseTable())) {
                    $table = (string) $data['table'];
                    if (isset($data->record)) {
                        foreach ($data->record as $record) {
                            $aar = new \Concrete\Core\Legacy\BlockRecord($table);
                            $aar->bID = $b->getBlockID();
                            foreach ($record->children() as $key => $node) {
                                $aar->{$node->getName()} = $this->importRecordValue($node, $declarations->getColumnReference($key));
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

    /**
     * @return int|null
     */
    public function getBlockTypeID()
    {
        return $this->btID;
    }


    public function validateComposerEditBlockPassThruAction(Block $b)
    {
        return $this->validateEditBlockPassThruAction($b);
    }

    public function getPassThruActionAndParameters($parameters)
    {
        $method = 'action_' . $parameters[0];
        $parameters = array_slice($parameters, 1);
        return [$method, $parameters];
    }

    /**
     * Creates a URL that can be posted or navigated to that, when done so, will automatically run the corresponding method inside the block's controller.
     * It can also be used to perform system operations, accordingly to the current action.
     *
     * @param mixed $task,... The arguments to build the URL (variable number of arguments).
     *
     * @return \Concrete\Core\Url\UrlImmutable|null Return NULL in case of problems
     */
    public function getActionURL($task)
    {
        try {

            if (is_object($this->block)) {
                if (is_object($this->block->getProxyBlock())) {
                    $b = $this->block->getProxyBlock();
                } else {
                    $b = $this->block;
                }

                $action = $this->getAction();
                if ($action === 'view' || strpos($action, 'action_') === 0) {
                    $c = Page::getCurrentPage();
                    if (is_object($b) && is_object($c)) {
                        $arguments = func_get_args();
                        $arguments[] = $b->getBlockID();
                        array_unshift($arguments, $c);

                        return call_user_func_array(array('\URL', 'page'), $arguments);
                    }
                } else {
                    $c = $this->getCollectionObject();
                    $arguments = array_merge(array('/ccm/system/block/action/edit',
                        $c->getCollectionID(),
                        urlencode($this->getAreaObject()->getAreaHandle()),
                        $this->block->getBlockID(),
                    ), func_get_args());

                    return call_user_func_array(array('\URL', 'to'), $arguments);

                }
            } else {
                $c = \Page::getCurrentPage();
                $arguments = array_merge(array('/ccm/system/block/action/add',
                    $c->getCollectionID(),
                    urlencode($this->getAreaObject()->getAreaHandle()),
                    $this->getBlockTypeID(),
                ), func_get_args());

                return call_user_func_array(array('\URL', 'to'), $arguments);

            }
        } catch (\Exception $e) {
        }

    }

    public function isValidControllerTask($method, $parameters = [])
    {
        if (strpos($method, 'action_') !== 0) { // gotta start with action_
            return false;
        }
        if (is_callable([$this, $method])) {
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

    public function cacheBlockOutputOnEditMode()
    {
        return $this->btCacheBlockOutputOnEditMode;
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

        // This block is new for 9.0.2 – we need this because we're passing around blocks with page objects in them
        // for file trackability, but without this code we lose the reference to the proper collection + collection version.
        // We ignore blocks on system pages because they are stacks.
        $blockPage = $this->block->getBlockCollectionObject();
        if ($blockPage && !$blockPage->isSystemPage()) {
            return $blockPage;
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
        if (isset($req['_bf']) && is_array($req['_bf'])) {
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
     * @return void
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
        if ($this instanceof FileTrackableInterface) {
            $this->app->make(AggregateTracker::class)->forget($this);
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
            $this->runTask($method, []);
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
        return isset($this->btHelpContent) ? $this->btHelpContent : null;
    }

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
        return [];
    }
}
