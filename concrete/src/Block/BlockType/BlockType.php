<?php
namespace Concrete\Core\Block\BlockType;

use Block;
use BlockTypeSet;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Filesystem\TemplateFile;
use Concrete\Core\Package\PackageList;
use Core;
use Database as DB;
use Environment;
use Loader;
use Localization;
use Package;
use Page;
use User;

/**
 * @Entity
 * @Table(name="BlockTypes")
 */
class BlockType
{

    public $controller;

    /**
     * @Column(type="boolean")
     */
    protected $btIgnorePageThemeGridFrameworkContainer = false;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $btID;
    /**
     * @Column(type="string", length=128)
     */
    protected $btHandle;
    /**
     * @Column(type="string", length=128)
     */
    protected $btName;
    /**
     * @Column(type="text")
     */
    protected $btDescription;
    /**
     * @Column(type="boolean")
     */
    protected $btCopyWhenPropagate = false;
    /**
     * @Column(type="boolean")
     */
    protected $btIncludeAll = false;
    /**
     * @Column(type="boolean")
     */
    protected $btIsInternal = false;
    /**
     * @Column(type="boolean")
     */
    protected $btSupportsInlineEdit = false;
    /**
     * @Column(type="boolean")
     */
    protected $btSupportsInlineAdd = false;
    /**
     * @Column(type="integer")
     */
    protected $btInterfaceHeight;
    /**
     * @Column(type="integer")
     */
    protected $btInterfaceWidth;
    /**
     * @Column(columnDefinition="integer unsigned")
     */
    protected $pkgID = 0;

    /**
     * Retrieves a BlockType object based on its btHandle
     *
     * @return BlockType
     */
    public static function getByHandle($btHandle)
    {
        $em = \ORM::entityManager('core');
        $bt = $em->getRepository('\Concrete\Core\Block\BlockType\BlockType')->findOneBy(array('btHandle' => $btHandle));
        if (is_object($bt)) {
            $bt->loadController();
            return $bt;
        }
    }

    /**
     * Clears output and record caches.
     */
    public static function clearCache()
    {
        $db = DB::get();
        $r = $db->MetaTables();

        if (in_array('config', array_map('strtolower', $r))) {

            if (in_array('btcachedblockrecord', array_map('strtolower', $db->MetaColumnNames('Blocks')))) {
                $db->Execute('update Blocks set btCachedBlockRecord = null');
            }
            if (in_array('collectionversionblocksoutputcache', array_map('strtolower', $r))) {
                $db->Execute('truncate table CollectionVersionBlocksOutputCache');
            }
        }
    }

    /**
     * Retrieves a BlockType object based on its btID
     *
     * @return BlockType
     */
    public static function getByID($btID)
    {
        $em = \ORM::entityManager('core');
        $bt = $em->getRepository('\Concrete\Core\Block\BlockType\BlockType')->find($btID);
        $bt->loadController();
        return $bt;
    }

    /**
     * @deprecated
     */
    public static function installBlockTypeFromPackage($btHandle, $pkg)
    {
        static::installBlockType($btHandle, $pkg);
    }

    /**
     * Installs a BlockType that is passed via a btHandle string. The core or override directories are parsed.
     */

    public static function installBlockType($btHandle, $pkg = false)
    {
        $env = Environment::get();
        $pkgHandle = false;
        if (is_object($pkg)) {
            $pkgHandle = $pkg->getPackageHandle();
        }
        $class = static::getBlockTypeMappedClass($btHandle, $pkgHandle);
        $bta = new $class;
        $path = dirname($env->getPath(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_BLOCK_DB, $pkgHandle));

        //Attempt to run the subclass methods (install schema from db.xml, etc.)
        $r = $bta->install($path);

        $currentLocale = Localization::activeLocale();
        if ($currentLocale != 'en_US') {
            // Prevent the database records being stored in wrong language
            Localization::changeLocale('en_US');
        }

        //Install the block
        $bt = new static();
        $bt->loadFromController($bta);
        if ($pkg instanceof Package) {
            $bt->pkgID = $pkg->getPackageID();
        } else {
            $bt->pkgID = 0;
        }
        $bt->btHandle = $btHandle;
        if ($currentLocale != 'en_US') {
            Localization::changeLocale($currentLocale);
        }

        $em = \ORM::entityManager('core');
        $em->persist($bt);
        $em->flush();

        if ($bta->getBlockTypeDefaultSet()) {
            $set = Set::getByHandle($bta->getBlockTypeDefaultSet());
            if (is_object($set)) {
                $set->addBlockType($bt);
            }
        }

        return $bt;
    }

    /**
     * Return the class file that this BlockType uses
     *
     * @return string
     */
    public static function getBlockTypeMappedClass($btHandle, $pkgHandle = false)
    {
        $env = Environment::get();
        $txt = Loader::helper('text');
        $r = $env->getRecord(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_CONTROLLER);

        // Replace $pkgHandle if overridden via environment
        $r->pkgHandle and $pkgHandle = $r->pkgHandle;

        $prefix = $r->override ? true : $pkgHandle;
        $class = core_class('Block\\' . $txt->camelcase($btHandle) . '\\Controller', $prefix);
        return $class;
    }

    /**
     * Sets the Ignore Page Theme Gride Framework Container
     */
    public function setBlockTypeIgnorePageThemeGridFrameworkContainer($btIgnorePageThemeGridFrameworkContainer)
    {
        $this->btIgnorePageThemeGridFrameworkContainer = $btIgnorePageThemeGridFrameworkContainer;
    }

    /**
     * Sets the block type handle
     */
    public function setBlockTypeName($btName)
    {
        $this->btName = $btName;
    }

    /**
     * Sets the block type description
     */
    public function setBlockTypeDescription($btDescription)
    {
        $this->btDescription = $btDescription;
    }

    /**
     * Sets the block type handle
     */
    public function setBlockTypeHandle($btHandle)
    {
        $this->btHandle = $btHandle;
    }

    /**
     * Determines if the block type has templates available
     *
     * @return boolean
     */
    public function hasAddTemplate()
    {
        $bv = new BlockView($this);
        $path = $bv->getBlockPath(FILENAME_BLOCK_ADD);
        if (file_exists($path . '/' . FILENAME_BLOCK_ADD)) {
            return true;
        }
        return false;
    }

    /**
     * gets the available composer templates
     * used for editing instances of the BlockType while in the composer ui in the dashboard
     *
     * @return TemplateFile[]
     */
    function getBlockTypeComposerTemplates()
    {
        $btHandle = $this->getBlockTypeHandle();
        $files = array();
        $fh = Loader::helper('file');
        $dir = DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES_COMPOSER;
        if (is_dir($dir)) {
            $files = array_merge($files, $fh->getDirectoryContents($dir));
        }
        foreach (PackageList::get()->getPackages() as $pkg) {
            $dir =
                (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle()) ? DIR_PACKAGES : DIR_PACKAGES_CORE)
                . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $btHandle . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER;
            if (is_dir($dir)) {
                $files = array_merge($files, $fh->getDirectoryContents($dir));
            }
        }
        $dir = DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES_COMPOSER;
        if (file_exists($dir)) {
            $files = array_merge($files, $fh->getDirectoryContents($dir));
        }
        $templates = array();
        foreach (array_unique($files) as $file) {
            $templates[] = new TemplateFile($this, $file);
        }
        return TemplateFile::sortTemplateFileList($templates);
    }

    /**
     * @return string
     */
    public function getBlockTypeHandle()
    {
        return $this->btHandle;
    }

    /**
     * if a the current BlockType supports inline edit or not
     *
     * @return boolean
     */
    public function supportsInlineEdit()
    {
        return $this->btSupportsInlineEdit;
    }

    /**
     * if a the current BlockType supports inline add or not
     *
     * @return boolean
     */
    public function supportsInlineAdd()
    {
        return $this->btSupportsInlineAdd;
    }

    /**
     * Returns true if the block type is internal (and therefore cannot be removed) a core block
     *
     * @return boolean
     */
    public function isInternalBlockType()
    {
        return $this->btIsInternal;
    }

    /**
     * returns the width in pixels that the block type's editing dialog will open in
     *
     * @return int
     */
    public function getBlockTypeInterfaceWidth()
    {
        return $this->btInterfaceWidth;
    }

    /**
     * returns the height in pixels that the block type's editing dialog will open in
     *
     * @return int
     */
    public function getBlockTypeInterfaceHeight()
    {
        return $this->btInterfaceHeight;
    }

    /**
     * If true, container classes will not be wrapped around this block type in edit mode (if the
     * theme in question supports a grid framework.
     * @return bool
     */
    public function ignorePageThemeGridFrameworkContainer()
    {
        return $this->btIgnorePageThemeGridFrameworkContainer;
    }

    /**
     * returns the id of the BlockType's package if it's in a package
     *
     * @return int
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * gets the BlockTypes description text
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return $this->btDescription;
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return $this->btName;
    }

    /**
     * @return boolean
     */
    public function isCopiedWhenPropagated()
    {
        return $this->btCopyWhenPropagate;
    }

    /**
     * If true, this block is not versioned on a page – it is included as is on all versions of the page, even when updated.
     *
     * @return boolean
     */
    public function includeAll()
    {
        return $this->btIncludeAll;
    }

    /**
     * @deprecated
     */
    public function getBlockTypeClassFromHandle()
    {
        return $this->getBlockTypeClass();
    }

    /**
     * Returns the class for the current block type.
     */
    public function getBlockTypeClass()
    {
        return static::getBlockTypeMappedClass($this->btHandle, $this->getPackageHandle());
    }

    /**
     * returns the handle of the BlockType's package if it's in a package
     *
     * @return string
     */
    public function getPackageHandle()
    {
        return \Concrete\Core\Package\PackageList::getHandle($this->pkgID);
    }

    /**
     * Returns an array of all BlockTypeSet objects that this block is in
     */
    public function getBlockTypeSets()
    {
        $db = Loader::db();
        $list = array();
        $r = $db->Execute(
                'select btsID from BlockTypeSetBlockTypes where btID = ? order by displayOrder asc',
                array($this->getBlockTypeID()));
        while ($row = $r->FetchRow()) {
            $list[] = BlockTypeSet::getByID($row['btsID']);
        }
        $r->Close();
        return $list;
    }

    /**
     * @return int
     */
    public function getBlockTypeID()
    {
        return $this->btID;
    }

    /**
     * Returns the number of unique instances of this block throughout the entire site
     * note - this count could include blocks in areas that are no longer rendered by the theme
     *
     * @param boolean specify true if you only want to see the number of blocks in active pages
     * @return int
     */
    public function getCount($ignoreUnapprovedVersions = false)
    {
        $db = Loader::db();
        if ($ignoreUnapprovedVersions) {
            $count = $db->GetOne(
                        "SELECT count(btID) FROM Blocks b
                                            WHERE btID=?
                                            AND EXISTS (
                                                SELECT 1 FROM CollectionVersionBlocks cvb
                                                INNER JOIN CollectionVersions cv ON cv.cID=cvb.cID AND cv.cvID=cvb.cvID
                                                WHERE b.bID=cvb.bID AND cv.cvIsApproved=1
                                            )",
                        array($this->btID));
        } else {
            $count = $db->GetOne("SELECT count(btID) FROM Blocks WHERE btID = ?", array($this->btID));
        }
        return $count;
    }

    /**
     * Not a permissions call. Actually checks to see whether this block is not an internal one.
     *
     * @return boolean
     */
    public function canUnInstall()
    {
        return (!$this->isBlockTypeInternal());
    }

    /**
     * if a the current BlockType is Internal or not - meaning one of the core built-in concrete5 blocks
     *
     * @access private
     * @return boolean
     */
    function isBlockTypeInternal()
    {
        return $this->btIsInternal;
    }

    /**
     * Renders a particular view of a block type, using the public $controller variable as the block type's controller
     *
     * @param string template 'view' for the default
     * @return void
     */
    public function render($view = 'view')
    {
        $bv = new BlockView($this);
        $bv->render($view);
    }

    /**
     * get's the block type controller
     *
     * @return BlockTypeController
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Gets the custom templates available for the current BlockType
     *
     * @return TemplateFile[]
     */
    function getBlockTypeCustomTemplates()
    {
        $btHandle = $this->getBlockTypeHandle();
        $fh = Loader::helper('file');
        $files = array();
        $dir = DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES;
        if (is_dir($dir)) {
            $files = array_merge($files, $fh->getDirectoryContents($dir));
        }
        // NOW, we check to see if this btHandle has any custom templates that have been installed as separate packages
        foreach (PackageList::get()->getPackages() as $pkg) {
            $dir =
                (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle()) ? DIR_PACKAGES : DIR_PACKAGES_CORE)
                . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $btHandle . '/' . DIRNAME_BLOCK_TEMPLATES;
            if (is_dir($dir)) {
                $files = array_merge($files, $fh->getDirectoryContents($dir));
            }
        }
        $dir = DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES;
        if (is_dir($dir)) {
            $files = array_merge($files, $fh->getDirectoryContents($dir));
        }
        $templates = array();
        foreach (array_unique($files) as $file) {
            $templates[] = new TemplateFile($this, $file);
        }
        return TemplateFile::sortTemplateFileList($templates);
    }

    /**
     * @private
     */
    function setBlockTypeDisplayOrder($displayOrder)
    {
        $db = Loader::db();

        $displayOrder = intval($displayOrder); //in case displayOrder came from a string (so ADODB escapes it properly)

        $sql = "UPDATE BlockTypes SET btDisplayOrder = btDisplayOrder - 1 WHERE btDisplayOrder > ?";
        $vals = array($this->btDisplayOrder);
        $db->Execute($sql, $vals);

        $sql = "UPDATE BlockTypes SET btDisplayOrder = btDisplayOrder + 1 WHERE btDisplayOrder >= ?";
        $vals = array($displayOrder);
        $db->Execute($sql, $vals);

        $sql = "UPDATE BlockTypes SET btDisplayOrder = ? WHERE btID = ?";
        $vals = array($displayOrder, $this->btID);
        $db->Execute($sql, $vals);

        // now we remove the block type from cache
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache');
        $cache->delete('blockTypeByID/' . $this->btID);
        $cache->delete('blockTypeByHandle/' . $this->btHandle);
        $cache->delete('blockTypeList');
    }

    /**
     * refreshes the BlockType's database schema throws an Exception if error
     *
     * @return void
     */
    public function refresh()
    {
        $db = DB::get();
        $pkgHandle = false;
        if ($this->pkgID > 0) {
            $pkgHandle = $this->getPackageHandle();
        }

        $class = static::getBlockTypeMappedClass($this->btHandle, $pkgHandle);
        $bta = new $class;

        $this->loadFromController($bta);

        $em = \ORM::entityManager('core');
        $em->persist($this);
        $em->flush();

        $env = Environment::get();
        $r = $env->getRecord(DIRNAME_BLOCKS . '/' . $this->btHandle . '/' . FILENAME_BLOCK_DB, $this->getPackageHandle());
        if ($r->exists()) {
            $parser = Schema::getSchemaParser(simplexml_load_file($r->file));
            $parser->setIgnoreExistingTables(false);
            $toSchema = $parser->parse($db);

            $fromSchema = $db->getSchemaManager()->createSchema();
            $comparator = new \Doctrine\DBAL\Schema\Comparator();
            $schemaDiff = $comparator->compare($fromSchema, $toSchema);
            $saveQueries = $schemaDiff->toSaveSql($db->getDatabasePlatform());
            foreach($saveQueries as $query) {
                $db->query($query);
            }
        }
    }

    protected function loadFromController($bta)
    {
        $this->btName = $bta->getBlockTypeName();
        $this->btDescription = $bta->getBlockTypeDescription();
        $this->btCopyWhenPropagate = $bta->isCopiedWhenPropagated();
        $this->btIncludeAll = $bta->includeAll();
        $this->btIsInternal = $bta->isBlockTypeInternal();
        $this->btSupportsInlineEdit = $bta->supportsInlineEdit();
        $this->btSupportsInlineAdd = $bta->supportsInlineAdd();
        $this->btIgnorePageThemeGridFrameworkContainer = $bta->ignorePageThemeGridFrameworkContainer();
        $this->btInterfaceHeight = $bta->getInterfaceHeight();
        $this->btInterfaceWidth = $bta->getInterfaceWidth();
    }

    /**
     * Removes the block type. Also removes instances of content.
     */
    public function delete()
    {
        $db = Loader::db();
        $r = $db->Execute(
                'select cID, cvID, b.bID, arHandle
                from CollectionVersionBlocks cvb
                    inner join Blocks b on b.bID  = cvb.bID
                where btID = ?
                union
                select cID, cvID, cvb.bID, arHandle
                from CollectionVersionBlocks cvb
                    inner join btCoreScrapbookDisplay btCSD on cvb.bID = btCSD.bID
                    inner join Blocks b on b.bID = btCSD.bOriginalID
                where btID = ?',
                array($this->getBlockTypeID(), $this->getBlockTypeID()));
        while ($row = $r->FetchRow()) {
            $nc = Page::getByID($row['cID'], $row['cvID']);
            if (!is_object($nc) || $nc->isError()) {
                continue;
            }
            $b = Block::getByID($row['bID'], $nc, $row['arHandle']);
            if (is_object($b)) {
                $b->deleteBlock();
            }
        }

        $em = \ORM::entityManager('core');
        $em->remove($this);
        $em->flush();

        //Remove gaps in display order numbering (to avoid future sorting errors)
        BlockTypeList::resetBlockTypeDisplayOrder('btDisplayOrder');
    }

    /**
     * Adds a block to the system without adding it to a collection.
     * Passes page and area data along if it is available, however.
     *
     * @param mixed            $data
     * @param bool|\Collection $c
     * @param bool|\Area       $a
     * @return bool|\Concrete\Core\Block\Block
     */
    public function add($data, $c = false, $a = false)
    {
        $db = Loader::db();

        $u = new User();
        if (isset($data['uID'])) {
            $uID = $data['uID'];
        } else {
            $uID = $u->getUserID();
        }
        $bName = '';
        if (isset($data['bName'])) {
            $bName = $data['bName'];
        }

        $btID = $this->btID;
        $dh = Loader::helper('date');
        $bDate = $dh->getOverridableNow();
        $bIsActive = (isset($this->btActiveWhenAdded) && $this->btActiveWhenAdded == 1) ? 1 : 0;

        $v = array($bName, $bDate, $bDate, $bIsActive, $btID, $uID);
        $q = "insert into Blocks (bName, bDateAdded, bDateModified, bIsActive, btID, uID) values (?, ?, ?, ?, ?, ?)";

        $r = $db->prepare($q);
        $res = $db->execute($r, $v);

        $bIDnew = $db->Insert_ID();

        // we get the block object for the block we just added

        if ($res) {
            $nb = Block::getByID($bIDnew);

            $btHandle = $this->getBlockTypeHandle();

            $class = $this->getBlockTypeClass();
            if (is_object($c)) {
                $nb->setBlockCollectionObject($c);
            }
            if (is_object($a)) {
                $nb->setBlockAreaObject($a);
            }
            $bc = new $class($nb);
            $bc->save($data);
            return Block::getByID($bIDnew);

        }

    }

    /**
     * Loads controller
     */
    protected function loadController()
    {
        $class = static::getBlockTypeMappedClass($this->getBlockTypeHandle(), $this->getPackageHandle());
        $this->controller = new $class($this);
    }

}
