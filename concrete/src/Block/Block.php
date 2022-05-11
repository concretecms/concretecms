<?php

namespace Concrete\Core\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\Events\BlockDuplicate;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Page\Cloner;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;

class Block extends ConcreteObject implements ObjectInterface
{
    /**
     * @deprecated use the getBlockAreaObject() method (what's deprecated is the "public" part, it should be protected)
     *
     * @var \Concrete\Core\Area\Area|null
     */
    public $a;

    /**
     * @var string|null
     */
    public $bName;

    /**
     * @var int|null
     */
    public $btID;

    /**
     * @var \Concrete\Core\Block\BlockController|null
     */
    public $instance;

    /**
     * The ID of the collection containing the block.
     *
     * @var int|null
     */
    protected $cID;

    /**
     * The handle of the area containing the block.
     *
     * @var string|null
     */
    protected $arHandle;

    /**
     * The collection instance containing the block.
     *
     * @var \Concrete\Core\Page\Collection\Collection|null
     */
    protected $c;

    /**
     * The custom style set ID.
     *
     * @var int|null
     */
    protected $issID;

    /**
     * The proxy block instance.
     *
     * @var \Concrete\Core\Block\Block|false
     */
    protected $proxyBlock = false;

    /**
     * The ID of the associated block.
     *
     * @var int|null
     */
    protected $cbRelationID;

    /**
     * The ID of the collection that's associated to the block actions.
     *
     * @var int|null
     */
    protected $bActionCID;

    /**
     * The block cache settings.
     *
     * @var \Concrete\Core\Block\CacheSettings|null
     */
    protected $cacheSettings;

    /**
     * Override cache settings?
     *
     * @var int|null 1 for true; 0/null for false
     */
    protected $cbOverrideBlockTypeCacheSettings;

    /**
     * The custom template name.
     *
     * @var string|null
     */
    protected $bFilename;

    /**
     * @var bool|int|null
     */
    protected $isOriginal;

    /**
     * @var string|null
     */
    protected $btHandle;

    /**
     * @var string|null
     */
    protected $btName;

    /**
     * @var int|null
     */
    protected $uID;

    /**
     * @var string|null
     */
    protected $bDateAdded;

    /**
     * @var string|null
     */
    protected $bDateModified;

    /**
     * @var int|null
     */
    protected $bIsActive;

    /**
     * @var int|null
     */
    protected $cbIncludeAll;

    protected $cbOverrideBlockTypeContainerSettings;

    /**
     * @var bool|int|null
     */
    protected $cbEnableBlockContainer;

    /**
     * @var int|null
     */
    protected $cbOverrideAreaPermissions;

    /**
     * @var string|null
     */
    protected $btCachedBlockRecord;

    /**
     * Destruct the class instance.
     */
    public function __destruct()
    {
        unset($this->c, $this->a, $this->instance);
    }

    /**
     * Get a block instance given its ID.
     *
     * @param int $bID The block ID
     * @param \Concrete\Core\Page\Collection\Collection|null $c the collection instance containing the block
     * @param \Concrete\Core\Area\Area|string|null $a the area containing the block (or its handle)
     *
     * @throws \Doctrine\DBAL\Exception|\Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Concrete\Core\Block\Block|false|null Return NULL if the block wasn't found; false if the block type class wasn't found; a Block instance otherwise
     */
    public static function getByID($bID, $c = null, $a = null)
    {
        $app = Facade::getFacadeApplication();
        /** @var \Concrete\Core\Cache\Level\RequestCache $cache */
        $cache = $app->make('cache/request');
        $cID = 0;
        $arHandle = '';
        $cvID = 0;
        if ($c == null && $a == null) {
            $b = $cache->getItem('/block/' . $bID);
        } else {
            if (is_object($a)) {
                $arHandle = $a->getAreaHandle();
            } else {
                if ($a != null) {
                    $arHandle = $a;
                    $a = Area::getOrCreate($c, $a);
                }
            }
            $cID = $c->getCollectionID();
            $cvID = $c->getVersionID();
            $b = $cache->getItem('/block/' . $bID . ':' . $cID . ':' . $cvID . ':' . $arHandle);
        }

        if ($b->isHit()) {
            return $b->get();
        }

        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $b = new self();
        if ($c == null && $a == null) {
            // just grab really specific block stuff
            $q = 'select bID, bIsActive, BlockTypes.btID, Blocks.btCachedBlockRecord, BlockTypes.btHandle, BlockTypes.pkgID, BlockTypes.btName, bName, bDateAdded, bDateModified, bFilename, Blocks.uID from Blocks inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where bID = ?';
            $b->setOriginal(true);
            $v = [$bID];
        } else {
            $b->arHandle = $arHandle;
            $b->a = $a;
            $b->cID = $cID;
            $b->c = ($c) ?: '';

            $vo = $c->getVersionObject();
            $cvID = $vo->getVersionID();

            $v = [$b->arHandle, $cID, $cvID, $bID];
            $q = 'select CollectionVersionBlocks.isOriginal, CollectionVersionBlocks.cbIncludeAll, Blocks.btCachedBlockRecord, BlockTypes.pkgID, CollectionVersionBlocks.cbOverrideAreaPermissions, CollectionVersionBlocks.cbOverrideBlockTypeCacheSettings, CollectionVersionBlocks.cbRelationID,
 CollectionVersionBlocks.cbOverrideBlockTypeContainerSettings, CollectionVersionBlocks.cbEnableBlockContainer, CollectionVersionBlocks.cbDisplayOrder, Blocks.bIsActive, Blocks.bID, Blocks.btID, bName, bDateAdded, bDateModified, bFilename, btHandle, Blocks.uID from CollectionVersionBlocks inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID) inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where CollectionVersionBlocks.arHandle = ? and CollectionVersionBlocks.cID = ? and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) and CollectionVersionBlocks.bID = ?';
        }

        $row = $db->fetchAssociative($q, $v);

        if ($row !== false) {
            $b->setPropertiesFromArray($row);

            $bt = BlockType::getByID($b->getBlockTypeID());
            $class = $bt->getBlockTypeClass();
            if ($class == false) {
                // we can't find the class file, so we return
                return false;
            }

            $b->instance = $app->make($class, ['obj' => $b]);

            if ($c != null || $a != null) {
                $identifier = '/block/' . $bID . ':' . $cID . ':' . $cvID . ':' . $arHandle;
            } else {
                $identifier = '/block/' . $bID;
            }
            $cache->save($cache->getItem($identifier)->set($b));

            return $b;
        }

        return null;
    }

    /**
     * Get a block instance given its name.
     *
     * @param string $blockName
     *
     * @return \Concrete\Core\Block\Block|null returns NULL if $blockName is empty; a Block instance otherwise (the getBlockID() method will return NULL if the block was not found)
     */
    public static function getByName($blockName)
    {
        if (!$blockName) {
            return null;
        }
        $app = Application::getFacadeApplication();
        $now = $app->make('date')->getOverridableNow();
        $db = $app->make(Connection::class);
        $sql = <<<'EOT'
SELECT
    b.bID,
    cvb.arHandle,
    cvb.cID
FROM
    Blocks AS b
    INNER JOIN CollectionVersionBlocks AS cvb
        ON b.bID = cvb.bID
    INNER JOIN CollectionVersions AS cv
        ON cvb.cID = cv.cID AND cvb.cvID = cv.cvID
WHERE
    b.bName = ?
    AND cv.cvIsApproved = 1 AND (cv.cvPublishDate IS NULL or cv.cvPublishDate <= ?) AND (cv.cvPublishEndDate IS NULL or cv.cvPublishEndDate >= ?)
ORDER BY
    cvb.cvID DESC
LIMIT 1
EOT
        ;
        $vals = [$blockName, $now, $now];
        $row = $db->getRow($sql, $vals);
        if ($row != false && isset($row['bID']) && $row['bID'] > 0) {
            return self::getByID($row['bID'], Page::getByID($row['cID']), $row['arHandle']);
        }

        return new self();
    }

    /**
     * Initialize the instance by manually specifying the data.
     *
     * @param array|\Iterator $blockInfo A set key-value pairs used to initialize the instance
     * @param \Concrete\Core\Page\Collection\Collection $c The collection containing the block
     * @param \Concrete\Core\Area\Area|string|null $a the area containing the block (or its handle)
     *
     * @return \Concrete\Core\Block\Block
     */
    public static function populateManually($blockInfo, $c, $a)
    {
        $b = new self();
        $b->setPropertiesFromArray($blockInfo);

        if (is_object($a)) {
            $b->a = $a;
            $b->arHandle = $a->getAreaHandle();
        } else {
            if ($a != null) {
                $b->arHandle = $a; // passing the area name. We only pass the object when we're adding from the front-end
            }
        }

        $b->cID = $c->getCollectionID();
        $b->c = $c;

        return $b;
    }

    /**
     * Returns the block identifier (if available).
     *
     * @version <= 8.4.2 Method returns string|null
     * @version 8.4.3 Method returns int|null
     *
     * @return int|null
     */
    public function getBlockID()
    {
        return isset($this->bID) ? (int) $this->bID : null;
    }

    /**
     * Get the absolute path of the block directory (.../blocks/block_handle).
     *
     * @return string
     */
    public function getBlockPath()
    {
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

    /**
     * Get the block name.
     *
     * @return string|null
     */
    public function getBlockName()
    {
        return $this->bName ?? null;
    }

    /**
     * Set the name of the block.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $data['bName'] = $name;
        $this->updateBlockInformation($data);
    }

    /**
     * Get the name of the custom template.
     *
     * @return string|null
     */
    public function getBlockFilename()
    {
        return $this->bFilename;
    }

    public function setTempFilename(string $bFilename = null): void
    {
        $this->bFilename = $bFilename;

    }

    /**
     * Removes the block cache records (on this object) to null
     * disables all cache on the cache object
     */
    public function temporaryClearBlockCache():void
    {
        $this->btCachedBlockRecord = null;
        $this->btCacheBlockOutputForRegisteredUsers = false;
        $this->getBlockCacheSettingsObject()->temporarilyDisableCache();
    }

    /**
     * Set the name of the custom template.
     *
     * @param string $template
     */
    public function setCustomTemplate($template)
    {
        $data['bFilename'] = $template;
        $this->updateBlockInformation($data);
    }

    /**
     * Get the ID of the block type (if available).
     *
     * @return int|null
     */
    public function getBlockTypeID()
    {
        return $this->btID ?? null;
    }

    /**
     * Get the block type instance.
     *
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType|null
     */
    public function getBlockTypeObject()
    {
        $btID = $this->getBlockTypeID();

        return $btID ? BlockType::getByID($btID) : null;
    }

    /**
     * Get the block type handle.
     *
     * @return string|null
     */
    public function getBlockTypeHandle()
    {
        return $this->btHandle ?? null;
    }

    /**
     * Get the block type name.
     *
     * @return string|null
     */
    public function getBlockTypeName()
    {
        return $this->btName ?? null;
    }

    /**
     * Get the ID of the user that created the block instance.
     *
     * @return int|null
     */
    public function getBlockUserID()
    {
        return $this->uID ?? null;
    }

    /**
     * Gets the date/time when block was added (in the system time zone).
     *
     * @return string|null date/time formated like: 2009-01-01 00:00:00
     */
    public function getBlockDateAdded()
    {
        return $this->bDateAdded ?? null;
    }

    /**
     * Gets the date/time when block was last modified (in the system time zone).
     *
     * @return string|null date/time formated like: 2009-01-01 00:00:00
     */
    public function getBlockDateLastModified()
    {
        return $this->bDateModified ?? null;
    }

    /**
     * Get the ID of the package owning this block type.
     *
     * @return int|null
     */
    public function getPackageID()
    {
        return $this->pkgID ?? null;
    }

    /**
     * Get the handle of the package owning this block type.
     *
     * @return string|false
     */
    public function getPackageHandle()
    {
        $pkgID = $this->getPackageID();

        return $pkgID ? PackageList::getHandle($pkgID) : null;
    }

    /**
     * Get the ID of the collection containing the block.
     *
     * @return int|null
     */
    public function getBlockCollectionID()
    {
        return $this->cID;
    }

    /**
     * Get the collection instance containing the block.
     *
     * @return \Concrete\Core\Page\Page|\Concrete\Core\Page\Collection\Collection|null
     */
    public function getBlockCollectionObject()
    {
        if (is_object($this->c)) {
            return $this->c;
        }

        return $this->getOriginalCollection();
    }

    /**
     * Get the page instance where this block is defined (or the page where the original block is defined if this block is an alias).
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public function getOriginalCollection()
    {
        $bID = $this->getBlockID();
        if ($bID) {
            $db = app(Connection::class);
            $q = 'select Pages.cID, cIsTemplate from Pages inner join CollectionVersionBlocks on (CollectionVersionBlocks.cID = Pages.cID) where CollectionVersionBlocks.bID = ? and CollectionVersionBlocks.isOriginal = 1';
            $row = $db->fetchAssoc($q, [$bID]);
            if ($row !== false) {
                $cID = $row['cID'];
                $nc = Page::getByID($cID, 'ACTIVE');
                if (is_object($nc) && !$nc->isError()) {
                    return $nc;
                }
            }
        }

        return null;
    }

    /**
     * Gets a list of pages that include this block, along with area name, etc...
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return \Concrete\Core\Page\Page[]
     */
    public function getPageList()
    {
        $cArray = [];
        $bID = $this->getBlockID();
        if ($bID) {
            /** @var Connection $db */
            $db = app(Connection::class);
            $q = 'select DISTINCT Pages.cID from CollectionVersionBlocks inner join Pages on (CollectionVersionBlocks.cID = Pages.cID) inner join CollectionVersions on (CollectionVersions.cID = Pages.cID) where CollectionVersionBlocks.bID = ?';
            $r = $db->executeQuery($q, [$bID]);
            while ($row = $r->fetchAssociative()) {
                $cArray[] = Page::getByID($row['cID']);
            }
        }

        return $cArray;
    }

    /**
     * Set the collection instance containing the block.
     *
     * @param \Concrete\Core\Page\Collection\Collection $c
     *
     * @return void
     */
    public function setBlockCollectionObject($c)
    {
        $this->c = $c;
        $this->cID = $c ? $c->getCollectionID() : null;
    }

    /**
     * Get the handle of the area containing the block.
     *
     * @return string|null
     */
    public function getAreaHandle()
    {
        return $this->arHandle;
    }

    /**
     * Get the the instance of the Area containing the block.
     *
     * @return \Concrete\Core\Area\Area|null
     */
    public function getBlockAreaObject()
    {
        if (is_object($this->a)) {
            return $this->a;
        }

        return null;
    }

    /**
     * Set the area containing the block.
     *
     * @param \Concrete\Core\Area\Area $a
     *
     * @return void
     */
    public function setBlockAreaObject(&$a)
    {
        $this->a = $a;
        $this->arHandle = $a ? $a->getAreaHandle() : null;
    }

    /**
     * Get the block display order (if available).
     *
     * @return int|null
     */
    public function getBlockDisplayOrder()
    {
        return isset($this->cbDisplayOrder) && is_numeric($this->cbDisplayOrder) ? (int) $this->cbDisplayOrder : null;
    }

    /**
     * Move this block after another block (in the block page & area).
     *
     * @param \Concrete\Core\Block\Block|null $afterBlock set to NULL to move this block at the first position; Set to a Block instance to move this block after that instance
     */
    public function moveBlockToDisplayOrderPosition($afterBlock)
    {
        // first, we increase the display order of all blocks found after this one.
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        if ($afterBlock instanceof self) {
            $block = self::getByID(
                $afterBlock->getBlockID(),
                $this->getBlockCollectionObject(),
                $this->getBlockAreaObject()
            );
            $q = 'update CollectionVersionBlocks set cbDisplayOrder = cbDisplayOrder + 1 where cID = ? and (cvID = ? or cbIncludeAll=1) and arHandle = ? and cbDisplayOrder > ?';
            $v = [$c->getCollectionID(), $c->getVersionID(), $this->getAreaHandle(), $block->getBlockDisplayOrder()];
            $db->Execute($q, $v);

            // now we set this block's display order to 1 + the current block
            $q = 'update CollectionVersionBlocks set cbDisplayOrder = ? where bID = ? and cID = ? and (cvID = ? or cbIncludeAll=1) and arHandle = ?';
            $v = [
                $block->getBlockDisplayOrder() + 1,
                $this->getBlockID(),
                $c->getCollectionID(),
                $c->getVersionID(),
                $this->getAreaHandle(),
            ];
            $db->Execute($q, $v);
        } else {
            $q = 'update CollectionVersionBlocks set cbDisplayOrder = cbDisplayOrder + 1 where cID = ? and (cvID = ? or cbIncludeAll=1) and arHandle = ?';
            $v = [$c->getCollectionID(), $c->getVersionID(), $this->getAreaHandle()];
            $db->Execute($q, $v);

            $q = 'update CollectionVersionBlocks set cbDisplayOrder = ? where bID = ? and cID = ? and (cvID = ? or cbIncludeAll=1) and arHandle = ?';
            $v = [0, $this->getBlockID(), $c->getCollectionID(), $c->getVersionID(), $this->getAreaHandle()];
            $db->Execute($q, $v);
        }
    }

    /**
     * Set the absolute position of this block, regardless other blocks in the same page & area.
     *
     * @param int $do the new absolute position of the block (starting from 0)
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function setAbsoluteBlockDisplayOrder($do)
    {
        /** @var Connection $db */
        $db = app(Connection::class);

        $cID = $this->getBlockCollectionID();
        $bID = $this->getBlockID();
        $arHandle = $this->getAreaHandle();

        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();

        $db->executeStatement('update CollectionVersionBlocks set cbDisplayOrder = ? where bID = ? and cID = ? and (cvID = ? or cbIncludeAll=1) and arHandle = ?', [$do, $bID, $cID, $cvID, $arHandle]);
        $this->cbDisplayOrder = (int) $do;
    }

    /**
     * Get the proxy block instance.
     *
     * @return \Concrete\Core\Block\Block|false
     */
    public function getProxyBlock()
    {
        return $this->proxyBlock;
    }

    /**
     * Set the proxy block instance.
     *
     * @param \Concrete\Core\Block\Block|null $block
     */
    public function setProxyBlock($block)
    {
        $this->proxyBlock = $block;
    }

    /**
     * Get the ID of the associated block.
     *
     * @return int|null
     */
    public function getBlockRelationID()
    {
        return $this->cbRelationID;
    }

    /**
     * Get the ID of the collection that's associated to the block actions (or false if not found).
     *
     * @return int|false
     */
    public function getBlockActionCollectionID()
    {
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

    /**
     * Set the ID of the collection that's associated to the block actions.
     *
     * @param int|null $bActionCID
     */
    public function setBlockActionCollectionID($bActionCID)
    {
        $this->bActionCID = $bActionCID;
    }

    /**
     * Does this block have an edit user interface?
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return bool
     */
    public function isEditable()
    {
        if ($this->getBlockTypeHandle() === BLOCK_HANDLE_SCRAPBOOK_PROXY) {
            /** @var \Concrete\Block\CoreScrapbookDisplay\Controller $controller */
            $controller = $this->getController();
            $originalBlockID = $controller->getOriginalBlockID();
            $originalBlock = self::getByID($originalBlockID);

            return $originalBlock && $originalBlock->isEditable();
        }
        $bv = new BlockView($this);
        $path = $bv->getBlockPath(FILENAME_BLOCK_EDIT);
        if (file_exists($path . '/' . FILENAME_BLOCK_EDIT)) {
            return true;
        }

        return false;
    }

    /**
     * Is the block active?
     *
     * @return int 0 for false, 1 for true
     */
    public function isActive()
    {
        return $this->bIsActive ?? 0;
    }

    /**
     * Deactivate the block.
     */
    public function deactivate()
    {
        app(Connection::class)->update('Blocks', ['bIsActive' => 0], ['bID' => $this->getBlockID()]);
        $this->bIsActive = 0;
    }

    /**
     * Activate the block.
     */
    public function activate()
    {
        app(Connection::class)->update('Blocks', ['bIsActive' => 1], ['bID' => $this->getBlockID()]);
        $this->bIsActive = 1;
    }

    /**
     * Is the block versioning disabled?
     *
     * @return int 0 for false, 1 for true
     */
    public function disableBlockVersioning()
    {
        return $this->cbIncludeAll ?? 0;
    }

    /**
     * Check if this block instance is an alias.
     *
     * @param \Concrete\Core\Page\Collection\Collection|null $c if specified, check if the block with the ID of this instance is an alias in that page (otherwise the check is done agains this specific block instance)
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return bool|null return NULL if $c is specified but there's no block with the same ID in that page
     */
    public function isAlias($c = null)
    {
        if ($c) {
            /** @var Connection $db */
            $db = app(Connection::class);
            $cID = $c->getCollectionID();
            $vo = $c->getVersionObject();
            $cvID = $vo->getVersionID();
            $q = 'select bID from CollectionVersionBlocks where bID = ? and cID=? and isOriginal = 0 and cvID = ?';

            $r = $db->executeQuery($q, [$this->getBlockID(), $cID, $cvID]);

            return $r->rowCount() > 0;
        }

        return !$this->isOriginal();
    }

    public function setOriginal(bool $isOriginal): void
    {
        $this->isOriginal = $isOriginal;
    }

    public function isOriginal(): ?bool
    {
        if (!isset($this->isOriginal)) {
            return null;
        }
        
        return (bool)$this->isOriginal;
    }

    /**
     * Check if this block is an alias from a page default.
     *
     * @return bool
     */
    public function isAliasOfMasterCollection()
    {
        return $this->getBlockCollectionObject()->isBlockAliasedFromMasterCollection($this);
    }

    /**
     * Get the number of alias of this block.
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return int
     */
    public function getNumChildren()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $q = 'select count(*) as total from CollectionVersionBlocks where bID = ? and isOriginal = 0';

        return $db->fetchOne($q, [$this->getBlockID()]);
    }

    /**
     * Is this block inside a stack?
     *
     * @return bool
     */
    public function isBlockInStack()
    {
        $co = $this->getBlockCollectionObject();
        if (is_object($co)) {
            if ($co->getPageTypeHandle() == STACKS_PAGE_TYPE) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the ID of the custom style set.
     *
     * @return int returns 0 or false if the block does not have custom styles
     */
    public function getCustomStyleSetID()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        if (!isset($this->issID)) {
            $co = $this->getBlockCollectionObject();

            $arHandle = $this->getAreaHandle();
            if ($arHandle) {
                $a = $this->getBlockAreaObject();
                if ($a->isGlobalArea()) {
                    // then we need to check against the global area name. We currently have the wrong area handle passed in
                    $arHandle = STACKS_AREA_NAME;
                }

                $v = [
                    $co->getCollectionID(),
                    $co->getVersionID(),
                    $arHandle,
                    $this->getBlockID(),
                ];

                $this->issID = (int) $db->fetchOne(
                    'select issID from CollectionVersionBlockStyles where cID = ? and cvID = ? and arHandle = ? and bID = ?',
                    $v
                );
            } else {
                $this->issID = 0;
            }
        }

        return $this->issID;
    }

    /**
     * Get the custom style object associated to this block.
     *
     * @param bool $force Do you want a CustomStyle instance even if the block does not have custom styles?
     *
     * @return \Concrete\Core\Block\CustomStyle|null
     */
    public function getCustomStyle($force = false)
    {
        if ($this->getCustomStyleSetID() > 0 || $force) {
            $csr = StyleSet::getByID($this->getCustomStyleSetID());
            $theme = $this->c->getCollectionThemeObject();
            switch ($this->getBlockTypeHandle()) {
                case BLOCK_HANDLE_LAYOUT_PROXY:
                    $bs = new CoreAreaLayoutCustomStyle($csr, $this, $theme);
                    break;
                default:
                    $bs = new CustomStyle($csr, $this, $theme);
                    break;
            }

            return $bs;
        }

        return null;
    }

    /**
     * Set the block custom styles.
     *
     * @param \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet $set
     */
    public function setCustomStyleSet(\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet $set)
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->replace(
            'CollectionVersionBlockStyles',
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->getBlockID(),
                'issID' => $set->getID(),
            ],
            [
                'cID',
                'cvID',
                'bID',
                'arHandle',
            ],
            true
        );
        $this->issID = (int) $set->getID();
    }

    /**
     * Remove the block custom styles.
     */
    public function resetCustomStyle()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->executeStatement(
            'delete from CollectionVersionBlockStyles where cID = ? and cvID = ? and arHandle = ? and bID = ?',
            [
                $this->getBlockCollectionID(),
                $cvID,
                $this->getAreaHandle(),
                $this->getBlockID(),
            ]
        );
        $this->issID = 0;
    }

    /**
     * Get the cache settings instance.
     *
     * @return \Concrete\Core\Block\CacheSettings
     */
    public function getBlockCacheSettingsObject()
    {
        if ($this->cacheSettings === null) {
            $this->cacheSettings = CacheSettings::get($this);
        }

        return $this->cacheSettings;
    }

    /**
     * Override cache settings?
     *
     * @return int|null 1 for true; 0/null for false
     */
    public function overrideBlockTypeCacheSettings()
    {
        if (!$this->cbOverrideBlockTypeCacheSettings) {
            $this->cbOverrideBlockTypeCacheSettings = 0;
        }

        return $this->cbOverrideBlockTypeCacheSettings;
    }

    /**
     * Customize the cache settings, overriding the values of the block type controller.
     *
     * @param bool $enabled Should the block output be cached?
     * @param bool $enabledOnPost Should the block output be cached upon POST requests?
     * @param bool $enabledForRegistered Should the block output be cached when site visitors are registered users?
     * @param int $lifetime cache lifetime (in seconds); if empty we'll assume 5 years
     */
    public function setCustomCacheSettings($enabled, $enabledOnPost, $enabledForRegistered, $lifetime)
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();

        $enabled = (bool) $enabled;
        $enabledOnPost = (bool) $enabledOnPost;
        $enabledForRegistered = (bool) $enabledForRegistered;
        $lifetime = (int) $lifetime;

        $db->replace(
            'CollectionVersionBlocksCacheSettings',
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->getBlockID(),
                'btCacheBlockOutput' => (int) $enabled,
                'btCacheBlockOutputOnPost' => (int) $enabledOnPost,
                'btCacheBlockOutputForRegisteredUsers' => (int) $enabledForRegistered,
                'btCacheBlockOutputLifetime' => (int) $lifetime,
            ],
            [
                'cID',
                'cvID',
                'bID',
                'arHandle',
            ],
            true
        );
        $db->update(
            'CollectionVersionBlocks',
            ['cbOverrideBlockTypeCacheSettings' => 1],
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->getBlockID(),
            ]
        );
    }

    /**
     * Reset the cache settings, so that Concrete will use the values of the block type controller.
     */
    public function resetCustomCacheSettings()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->update(
            'CollectionVersionBlocks',
            ['cbOverrideBlockTypeCacheSettings' => 0],
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->getBlockID(),
            ]
        );
        $db->delete(
            'CollectionVersionBlocksCacheSettings',
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->getBlockID(),
            ]
        );
    }

    /**
     * Get the cached record of the block instance.
     *
     * @return string|null
     */
    public function getBlockCachedRecord()
    {
        return $this->btCachedBlockRecord ?? null;
    }

    /**
     * Should the block output be cached?
     *
     * @return bool
     */
    public function cacheBlockOutput()
    {
        $settings = $this->getBlockCacheSettingsObject();

        return $settings->cacheBlockOutput();
    }

    /**
     * Should the block output be cached upon POST requests?
     *
     * @return bool
     */
    public function cacheBlockOutputOnPost()
    {
        $settings = $this->getBlockCacheSettingsObject();

        return $settings->cacheBlockOutputOnPost();
    }

    /**
     * Should the block output be cached when site visitors are registered users?
     *
     * @return bool
     */
    public function cacheBlockOutputForRegisteredUsers()
    {
        $settings = $this->getBlockCacheSettingsObject();

        return $settings->cacheBlockOutputForRegisteredUsers();
    }

    /**
     * Get the lifetime (in seconds) of the block output cache.
     *
     * @return int
     */
    public function getBlockOutputCacheLifetime()
    {
        $settings = $this->getBlockCacheSettingsObject();

        return $settings->getBlockOutputCacheLifetime();
    }

    /**
     * Set the output cache.
     *
     * @param string $content the block output to be placed in the cache
     * @param int|null $lifetime The cache life time (in seconds). If empty we'll assume 5 years.
     * @param \Concrete\Core\Area\Area|null $area the stack area containing the block
     */
    public function setBlockCachedOutput($content, $lifetime, $area)
    {
        // We shouldn't create a cache for stack proxy
        if ($this->getBlockTypeHandle() === BLOCK_HANDLE_STACK_PROXY) {
            return false;
        }

        /** @var Connection $db */
        $db = app(Connection::class);
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
            $db->replace(
                'CollectionVersionBlocksOutputCache',
                [
                    'cID' => $cID,
                    'cvID' => $cvID,
                    'bID' => $this->getBlockID(),
                    'arHandle' => $arHandle,
                    'btCachedBlockOutput' => $content,
                    'btCachedBlockOutputExpires' => $btCachedBlockOutputExpires,
                ],
                [
                    'cID',
                    'cvID',
                    'arHandle',
                    'bID',
                ],
                true
            );
        }
    }

    /**
     * Get the cached output of the block instance (if available and not expired).
     *
     * @param \Concrete\Core\Area\Area|null $area
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return string|false
     */
    public function getBlockCachedOutput($area)
    {
        // We shouldn't get a cache for stack proxy
        if ($this->getBlockTypeHandle() === BLOCK_HANDLE_STACK_PROXY) {
            return false;
        }

        /** @var Connection $db */
        $db = app(Connection::class);

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

        $r = $db->fetchAssociative(
            'select btCachedBlockOutput, btCachedBlockOutputExpires from CollectionVersionBlocksOutputCache where cID = ? and cvID = ? and bID = ? and arHandle = ? ',
            [
                $cID,
                $cvID,
                $this->getBlockID(),
                $arHandle,
            ]
        );
        if (!is_array($r)) {
            return false;
        }
        if (array_get($r, 'btCachedBlockOutputExpires') < time()) {
            return false;
        }

        return $r['btCachedBlockOutput'];
    }

    /**
     * Mark the output cache as expired.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function refreshBlockOutputCache()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        $db->update('CollectionVersionBlocksOutputCache', ['btCachedBlockOutputExpires' => 0], ['cID' => $c->getCollectionID(), 'cvID' => $c->getVersionID(), 'arHandle' => $this->getAreaHandle(), 'bID' => $this->getBlockID()]);
    }

    /**
     * Refreshes the block record cache.
     *
     * The block record contains information from the block's $btTable.
     *
     * @since 8.4.1
     */
    public function refreshBlockRecordCache()
    {
        app(Connection::class)->update('Blocks', ['btCachedBlockRecord' => null], ['bID' => $this->getBlockID()]);
    }

    /**
     * Area permissions are overridden?
     *
     * @return int|null 1 for true; 0/null for false
     */
    public function overrideAreaPermissions()
    {
        if (!isset($this->cbOverrideAreaPermissions) || !$this->cbOverrideAreaPermissions) {
            $this->cbOverrideAreaPermissions = 0;
        }

        return $this->cbOverrideAreaPermissions;
    }

    /**
     * Mark the block as having permissions that override the ones of the area.
     * Initial permissions are copied from the page/area.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function doOverrideAreaPermissions()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        $v = [$c->getCollectionID(), $c->getVersionID(), $this->getBlockID(), $this->getAreaHandle()];
        $db->executeStatement(
            'update CollectionVersionBlocks set cbOverrideAreaPermissions = 1 where cID = ? and (cvID = ? or cbIncludeAll = 1) and bID = ? and arHandle = ?',
            $v
        );
        $v = [$c->getCollectionID(), $c->getVersionID(), $this->getBlockID()];
        $db->executeStatement('delete from BlockPermissionAssignments where cID = ? and cvID = ? and bID = ?', $v);

        // copy permissions from the page to the area
        $permissions = PermissionKey::getList('block');
        foreach ($permissions as $pk) {
            $pk->setPermissionObject($this);
            $pk->copyFromPageOrAreaToBlock();
        }
    }

    /**
     * Revert the permission of the object to the one of the area that contains the block.
     */
    public function revertToAreaPermissions()
    {
        $c = $this->getBlockCollectionObject();

        /** @var Connection $db */
        $db = app(Connection::class);
        $v = [$c->getCollectionID(), $c->getVersionID(), $this->getBlockID()];

        $db->executeStatement('delete from BlockPermissionAssignments where cID = ? and cvID = ? and bID = ?', $v);
        $v[] = $this->getAreaHandle();
        $db->executeStatement(
            'update CollectionVersionBlocks set cbOverrideAreaPermissions = 0 where cID = ? and (cvID = ? or cbIncludeAll=1) and bID = ? and arHandle = ?',
            $v
        );
    }

    /**
     * Is the block grid container enabled?
     *
     * @return bool
     */
    public function enableBlockContainer()
    {
        return isset($this->cbEnableBlockContainer) && $this->cbEnableBlockContainer;
    }

    /**
     * Should the view ignore the grid container?
     *
     * @return bool
     */
    public function ignorePageThemeGridFrameworkContainer()
    {
        if ($this->overrideBlockTypeContainerSettings()) {
            return !$this->enableBlockContainer();
        }
        $controller = $this->getController();

        return $controller->ignorePageThemeGridFrameworkContainer();
    }

    /**
     * Should this instance override the grid container settings of the block controller?
     *
     * @return int 0/false: false, 1/true: true
     */
    public function overrideBlockTypeContainerSettings()
    {
        if (!isset($this->cbOverrideBlockTypeContainerSettings) || !$this->cbOverrideBlockTypeContainerSettings) {
            $this->cbOverrideBlockTypeContainerSettings = 0;
        }

        return $this->cbOverrideBlockTypeContainerSettings;
    }

    /**
     * Set the custom settings related to the grid container (overriding the block type default values).
     *
     * @param bool $enableBlockContainer Is the block grid container enabled?
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function setCustomContainerSettings($enableBlockContainer)
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->update(
            'CollectionVersionBlocks',
            ['cbOverrideBlockTypeContainerSettings' => 1, 'cbEnableBlockContainer' => $enableBlockContainer ? 1 : 0],
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->getBlockID(),
            ]
        );
    }

    /**
     * Reset the settings related to the grid container to the block type default values.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function resetBlockContainerSettings()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $bt = $this->getBlockTypeObject();
        $enableBlockContainer = $bt->ignorePageThemeGridFrameworkContainer() ? 1 : 0;
        $db->update(
            'CollectionVersionBlocks',
            ['cbOverrideBlockTypeContainerSettings' => 0, 'cbEnableBlockContainer' => $enableBlockContainer],
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->getBlockID(),
            ]
        );
    }

    /**
     * Disable the original block container for the current request.
     * This is called by the scrapbook proxy block, because the scrapbook block takes care of rendering the container.
     */
    public function disableBlockContainer()
    {
        $this->cbOverrideBlockTypeContainerSettings = true;
        $this->cbEnableBlockContainer = false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectIdentifier()
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->getBlockCollectionID() . ':' . $this->getAreaHandle() . ':' . $this->getBlockID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionResponseClassName()
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\BlockResponse';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionAssignmentClassName()
     */
    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\BlockAssignment';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectKeyCategoryHandle()
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'block';
    }

    /**
     * Get the block type controller.
     *
     * @return \Concrete\Core\Block\BlockController
     */
    public function getController()
    {
        $app = Facade::getFacadeApplication();
        if (
            $app->make('config')->get('concrete.cache.blocks')
            && isset($this->instance)
            && $this->instance->cacheBlockRecord()
            && is_object($this->instance->getBlockControllerData())
            ) {
            $this->instance->__construct();
        } else {
            $bt = $this->getBlockTypeObject();
            $class = $bt->getBlockTypeClass();

            $this->instance = $app->make($class, ['obj' => $this]);
        }
        $this->instance->setBlockObject($this);
        $this->instance->setAreaObject($this->getBlockAreaObject());

        return $this->instance;
    }

    /**
     * Render the block display.
     *
     * @param string $view
     */
    public function display($view = 'view')
    {
        if ($this->getBlockTypeID() < 1) {
            return;
        }

        $bv = new BlockView($this);
        $bv->render($view);
    }

    /**
     * Include a file (if it exists).
     *
     * @param string $file The file name, relative to the block path (.../blocks/block_handle).
     */
    public function inc($file)
    {
        $b = $this;
        if (file_exists($this->getBlockPath() . '/' . $file)) {
            include $this->getBlockPath() . '/' . $file;
        }
    }

    /**
     * Updates fields common to every block.
     *
     * @param array $data the block type-specific data to be saved
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function update($data)
    {
        $app = Facade::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $dh = $app->make('helper/date');
        $bDateModified = $dh->getOverridableNow();
        $bID = $this->getBlockID();

        $v = [$bDateModified, $bID];
        $q = 'update Blocks set bDateModified = ? where bID = ?';

        $r = $db->prepare($q);
        $r->executeStatement($v);

        $this->refreshBlockOutputCache();

        $btID = $this->getBlockTypeID();
        $bt = BlockType::getByID($btID);
        $class = $bt->getBlockTypeClass();
        $app = Facade::getFacadeApplication();
        $bc = $app->make($class, ['obj' => $this]);
        $bc->save($data);
    }

    /**
     * Update the block information, like its block filename, and block name.
     *
     * @param array $data Valid keys:
     *                    - 'bName' to update the block name
     *                    - 'bFilename' to update the block custom template
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateBlockInformation($data)
    {
        $app = Facade::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $dh = $app->make('helper/date');
        $dt = $dh->getOverridableNow();

        $bName = $this->getBlockName();
        $bFilename = $this->getBlockFilename();
        if (isset($data['bName'])) {
            $bName = $data['bName'];
        }
        if (isset($data['bFilename'])) {
            // Check bFilename for valid Block filename. Must contain only alpha numeric and slashes/dashes, but it MAY
            // end in ".php"
            $bFilename = $data['bFilename'];
            if (!empty($bFilename)) {
                if (substr($bFilename, -4) === '.php') {
                    // Let's run our regular expression check on everything BEFORE ".php"
                    $bFilenameToCheck = substr($bFilename, 0, -4);
                } else {
                    $bFilenameToCheck = $bFilename; // We just check the entirety of what's passed in.
                }

                if (!preg_match('/^[A-Za-z0-9_-]+$/i', $bFilenameToCheck)) {
                    $bFilename = null;
                    throw new \RuntimeException(
                        t('Custom templates may only contain letters, numbers, dashes and underscores.')
                    );
                }
            }
        }

        $v = [$bName, $bFilename, $dt, $this->getBlockID()];
        $q = 'update Blocks set bName = ?, bFilename = ?, bDateModified = ? where bID = ?';
        $r = $db->prepare($q);
        $r->executeStatement($v);

        $this->refreshBlockOutputCache();
    }

    /**
     * Move the block to a new collection and/or area.
     *
     * @param \Concrete\Core\Page\Collection\Collection $collection
     * @param \Concrete\Core\Area\Area $area
     *
     * @return bool
     */
    public function move(Collection $collection, Area $area)
    {
        $old_collection = $this->getBlockCollectionID();
        $new_collection = $collection->getCollectionID();

        $old_version = $this->getBlockCollectionObject()->getVersionObject()->getVersionID();
        $new_version = $collection->getVersionObject()->getVersionID();

        $old_area_handle = $this->getAreaHandle();
        $new_area_handle = $area->getAreaHandle();

        return (bool) app(Connection::class)->update(
            'CollectionVersionBlocks',
            [
                'cID' => $new_collection,
                'cvID' => $new_version,
                'arHandle' => $new_area_handle,
            ],
            [
                'cID' => $old_collection,
                'cvID' => $old_version,
                'arHandle' => $old_area_handle,
                'bID' => $this->getBlockID(),
            ]
        );
    }

    /**
     * Duplicate this block to a new collection. Runs the $controllerMethodToTryAndRun on the block controller to
     * copy relevant data for a particular context. Valid options for $controllerMethodToTryAndRun are
     * 'duplicate', 'duplicate_master' and 'duplicate_clipboard'. If a method is specified but does not exist
     * we fall back to duplicate in all situations.
     *
     * @param \Concrete\Core\Page\Collection\Collection $nc The destination collection
     * @param string $controllerMethodToTryAndRun
     *
     * @throws \Doctrine\DBAL\Exception|\Doctrine\DBAL\Driver\Exception|\Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Concrete\Core\Block\Block|false returns false if the block type can't be found; the new block instance otherwise
     */
    public function duplicate($nc, string $controllerMethodToTryAndRun = 'duplicate')
    {
        $app = Facade::getFacadeApplication();
        /** @var Connection $connection */
        $connection = $app->make(Connection::class);
        /** @var Date $dh */
        $dh = $app->make('helper/date');

        $bt = BlockType::getByID($this->getBlockTypeID());
        $blockTypeClass = $bt->getBlockTypeClass();
        if (!$blockTypeClass) {
            return false;
        }
        $bc = $app->make($blockTypeClass, ['obj' => $this]);

        $bDate = $dh->getOverridableNow();
        $connection->insert('Blocks', [
            'bName' => $this->getBlockName(),
            'bDateAdded' => $bDate,
            'bDateModified' => $bDate,
            'bFilename' => $this->getBlockFilename(),
            'btID' => $this->getBlockTypeID(),
            'uID' => $this->getBlockUserID(),
        ]);
        $newBID = (int) $connection->lastInsertId(); // this is the latest inserted block ID

        // now, we duplicate the block-specific permissions
        $oc = $this->getBlockCollectionObject();
        $ocID = $oc->getCollectionID();
        $ovID = $oc->getVersionID();

        $ncID = $nc->getCollectionID();
        $nvID = $nc->getVersionID();

        // Composer specific
        $row = $connection->createQueryBuilder()
            ->select('cID', 'cvID', 'arHandle', 'cbDisplayOrder', 'ptComposerFormLayoutSetControlID')
            ->from('PageTypeComposerOutputBlocks')
            ->where('cID = :cID')
            ->andWhere('cvID = :cvID')
            ->andWhere('bID = :bID')
            ->andWhere('arHandle = :arHandle')
            ->setParameter('cID', $ocID)
            ->setParameter('cvID', $ovID)
            ->setParameter('bID', $this->getBlockID())
            ->setParameter('arHandle', $this->getAreaHandle())
            ->execute()->fetchAssociative();
        if ($row && is_array($row) && $row['cID']) {
            $connection->insert('PageTypeComposerOutputBlocks', [
                'cID' => $ncID,
                'cvID' => $nvID,
                'arHandle' => $this->getAreaHandle(),
                'cbDisplayOrder' => $row['cbDisplayOrder'],
                'ptComposerFormLayoutSetControlID' => $row['ptComposerFormLayoutSetControlID'],
                'bID' => $newBID,
            ]);
        }

        $r = $connection->createQueryBuilder()
            ->select('paID', 'pkID')
            ->from('BlockPermissionAssignments')
            ->where('cID = :cID')
            ->andWhere('bID = :bID')
            ->andWhere('cvID = :cvID')
            ->setParameter('cID', $ocID)
            ->setParameter('bID', $this->getBlockID())
            ->setParameter('cvID', $ovID)
            ->execute()
        ;

        while ($row = $r->fetchAssociative()) {
            $assignment = $connection->createQueryBuilder()
                ->select('cID', 'cvID', 'bID', 'pkID', 'paID')
                ->from('BlockPermissionAssignments')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->andWhere('bID = :bID')
                ->andWhere('pkID = :pkID')
                ->andWhere('paID = :paID')
                ->setParameter('cID', $ncID)
                ->setParameter('cvID', $nvID)
                ->setParameter('bID', $newBID)
                ->setParameter('pkID', $row['pkID'])
                ->setParameter('paID', $row['paID'])
                ->execute()->fetchOne();
            if ($assignment === false) {
                $connection->insert('BlockPermissionAssignments', [
                    'cID' => $ncID,
                    'cvID' => $nvID,
                    'bID' => $newBID,
                    'pkID' => $row['pkID'],
                    'paID' => $row['paID'],
                ]);
            }
        }

        // we duplicate block-specific sub-content
        if ($controllerMethodToTryAndRun !== 'duplicate' && method_exists($bc, $controllerMethodToTryAndRun)) {
            $bc->$controllerMethodToTryAndRun($newBID, $nc);
        } else {
            $bc->duplicate($newBID);
        }

        // we duplicate block-specific cache settings
        if ($this->overrideBlockTypeCacheSettings()) {
            $cacheSetting = $connection->createQueryBuilder()
                ->select('btCacheBlockOutput', 'btCacheBlockOutputOnPost', 'btCacheBlockOutputForRegisteredUsers', 'btCacheBlockOutputLifetime')
                ->from('CollectionVersionBlocksCacheSettings')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->andWhere('bID = :bID')
                ->andWhere('arHandle = :arHandle')
                ->setParameter('cID', $ocID)
                ->setParameter('cvID', $ovID)
                ->setParameter('bID', $this->getBlockID())
                ->setParameter('arHandle', $this->getAreaHandle())
                ->execute()->fetchAssociative();
            if ($cacheSetting) {
                $connection->insert('CollectionVersionBlocksCacheSettings', [
                    'cID' => $ncID,
                    'cvID' => $nvID,
                    'bID' => $newBID,
                    'arHandle' => $this->getAreaHandle(),
                    'btCacheBlockOutput' => $cacheSetting['btCacheBlockOutput'],
                    'btCacheBlockOutputOnPost' => $cacheSetting['btCacheBlockOutputOnPost'],
                    'btCacheBlockOutputForRegisteredUsers' => $cacheSetting['btCacheBlockOutputForRegisteredUsers'],
                    'btCacheBlockOutputLifetime' => $cacheSetting['btCacheBlockOutputLifetime'],
                ]);
            }
        }

        // finally, we insert into the CollectionVersionBlocks table
        $cbDisplayOrder = $this->getBlockDisplayOrder();
        if ($cbDisplayOrder === null) {
            $newBlockDisplayOrder = $nc->getCollectionAreaDisplayOrder($this->getAreaHandle());
        } else {
            $newBlockDisplayOrder = $cbDisplayOrder;
        }
        $connection->insert('CollectionVersionBlocks', [
            'cID' => $ncID,
            'cvID' => $nvID,
            'bID' => $newBID,
            'arHandle' => $this->getAreaHandle(),
            'cbRelationID' => $this->getBlockRelationID(),
            'cbDisplayOrder' => $newBlockDisplayOrder,
            'isOriginal' => 1,
            'cbOverrideAreaPermissions' => $this->overrideAreaPermissions(),
            'cbOverrideBlockTypeCacheSettings' => $this->overrideBlockTypeCacheSettings(),
            'cbOverrideBlockTypeContainerSettings' => $this->overrideBlockTypeContainerSettings(),
            'cbEnableBlockContainer' => $this->enableBlockContainer() ? 1 : 0,
        ]);

        $nb = self::getByID($newBID, $nc, $this->getAreaHandle());

        $issID = $this->getCustomStyleSetID();
        if ($issID > 0) {
            $connection->insert('CollectionVersionBlockStyles', [
                'cID' => $ncID,
                'cvID' => $nvID,
                'bID' => $newBID,
                'arHandle' => $this->getAreaHandle(),
                'issID' => $issID,
            ]);
        }

        $event = new BlockDuplicate($nb);
        $event->setOldBlock($this);

        $app->make('director')->dispatch('on_block_duplicate', $event);

        return $nb;
    }

    /**
     * Create an alias of the block, attached to this collection, within the CollectionVersionBlocks table.
     * Additionally, this command grabs the permissions from the original record in the CollectionVersionBlocks table, and attaches them to the new one.
     *
     * @param \Concrete\Core\Page\Collection\Collection $c The collection to add the block alias to
     * @param int|null $displayOrder The number to display this block at. (optional)
     */
    public function alias($c, int $displayOrder = null)
    {
        $app = Application::getFacadeApplication();
        /** @var Cloner $cloner */
        $cloner = $app->make(Cloner::class);
        $cloner->cloneBlock($this, $c, $displayOrder);
    }

    /**
     * Delete this block instance.
     *
     * @param bool $forceDelete If this is an alias block, should we delete all the block instances in addition to the alias?
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return bool|void Returns false if the block is not valid
     */
    public function deleteBlock($forceDelete = false)
    {
        /** @var Connection $db */
        $db = app(Connection::class);

        $bID = $this->getBlockID();
        if ($bID < 1) {
            return false;
        }

        $cID = $this->getBlockCollectionID();
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $arHandle = $this->getAreaHandle();

        // if this block is located in a master collection, we're going to delete all the instances of the block,
        // regardless
        if (($c instanceof Page && $c->isMasterCollection() && !$this->isAlias()) || $forceDelete) {
            // forceDelete is used by the administration console

            // this is an original. We're deleting it, and everything else having to do with it
            $db->executeStatement('delete from CollectionVersionBlocks where bID = ?', [$bID]);
            $db->executeStatement('delete from BlockPermissionAssignments where bID = ?', [$bID]);
            $db->executeStatement('delete from CollectionVersionBlockStyles where bID = ?', [$bID]);
            $db->executeStatement('delete from CollectionVersionBlocksCacheSettings where bID = ?', [$bID]);
        } else {
            $db->executeStatement('delete from CollectionVersionBlocks where cID = ? and (cvID = ? or cbIncludeAll=1) and bID = ? and arHandle = ?', [$cID, $cvID, $bID, $arHandle]);
            // next, we delete the groups instance of this block
            $db->executeStatement('delete from BlockPermissionAssignments where bID = ? and cvID = ? and cID = ?', [$bID, $cvID, $cID]);
            $db->executeStatement('delete from CollectionVersionBlockStyles where cID = ? and cvID = ? and bID = ? and arHandle = ?', [$cID, $cvID, $bID, $arHandle]);
            $db->executeStatement('delete from CollectionVersionBlocksCacheSettings where cID = ? and cvID = ? and bID = ? and arHandle = ?', [$cID, $cvID, $bID, $arHandle]);
        }

        //then, we see whether or not this block is aliased to anything else
        $totalBlocks = (int) $db->fetchOne('select count(*) from CollectionVersionBlocks where bID = ?', [$bID]);
        $totalBlocks += (int) $db->fetchOne('select count(*) from btCoreScrapbookDisplay where bOriginalID = ?', [$bID]);
        if ($totalBlocks < 1) {
            // this block is not referenced in the system any longer, so we delete the entry in the blocks table, as well as the entries in the corresponding
            // sub-blocks table

            $v = [$bID];

            // so, first we delete the block's sub content
            $bt = BlockType::getByID($this->getBlockTypeID());
            if ($bt && method_exists($bt, 'getBlockTypeClass')) {
                $class = $bt->getBlockTypeClass();
                $app = Facade::getFacadeApplication();
                $bc = $app->make($class, ['obj' => $this]);
                $bc->delete();
            }

            // now that the block's subcontent delete() method has been run, we delete the block from the Blocks table
            $db->executeStatement('delete from Blocks where bID = ?', $v);
            $db->executeStatement("delete from PileContents where itemType = 'BLOCK' and itemID = ?", [$bID]);

            // Aaaand then we delete all scrapbooked blocks to this entry
            $r = $db->executeQuery(
                'select cID, cvID, CollectionVersionBlocks.bID, arHandle from CollectionVersionBlocks inner join btCoreScrapbookDisplay on CollectionVersionBlocks.bID = btCoreScrapbookDisplay.bID where bOriginalID = ?',
                [$bID]
            );
            while ($row = $r->fetchAssociative()) {
                $c = Page::getByID($row['cID'], $row['cvID']);
                $b = self::getByID($row['bID'], $c, $row['arHandle']);
                $b->deleteBlock();
            }
        }
    }

    /**
     * Export the data associated to this block to an XML node.
     *
     * @param \SimpleXMLElement $node the parent node where we'll append the XML node to
     * @param string $exportType set to 'full' to export cache and custom style settings too
     */
    public function export($node, $exportType = 'full')
    {
        if (!$this->isAliasOfMasterCollection() || (($this->c instanceof Page) && $this->c->isMasterCollection())) {
            // We have the OR up here so that master collections that you have duplicated from other
            // master collections export properly.
            $blockNode = $node->addChild('block');
            $blockNode->addAttribute('type', $this->getBlockTypeHandle());
            $blockNode->addAttribute('name', $this->getBlockName());
            if ($this->getBlockFilename() != '') {
                $blockNode->addAttribute('custom-template', $this->getBlockFilename());
            }
            if ($this->overrideBlockTypeContainerSettings()) {
                $blockNode->addAttribute('custom-container-settings', $this->enableBlockContainer() ? '1' : '0');
            }
            if (($this->c instanceof Page) && $this->c->isMasterCollection()) {
                $mcBlockID = app('helper/validation/identifier')->getString(8);
                ContentExporter::addMasterCollectionBlockID($this, $mcBlockID);
                $blockNode->addAttribute('mc-block-id', $mcBlockID);
            }

            if ($exportType == 'full') {
                $style = $this->getCustomStyle();
                if (is_object($style)) {
                    $set = $style->getStyleSet();
                    $set->export($blockNode);
                }
                if ($this->overrideBlockTypeCacheSettings()) {
                    $settings = $this->getBlockCacheSettingsObject();
                    $blockNode['cache-output'] = $settings->cacheBlockOutput();
                    $blockNode['cache-output-lifetime'] = $settings->getBlockOutputCacheLifetime();
                    $blockNode['cache-output-on-post'] = $settings->cacheBlockOutputOnPost();
                    $blockNode['cache-output-for-registered-users'] = $settings->cacheBlockOutputForRegisteredUsers();
                }
                $bc = $this->getController();
                $bc->export($blockNode);
            }
        } else {
            $blockNode = $node->addChild('block');
            $blockNode->addAttribute('mc-block-id', ContentExporter::getMasterCollectionTemporaryBlockID($this));
        }
    }

    /**
     * Populate the queue to be used to add/update blocks of the pages of a specific type.
     *
     * @param bool $addBlock add this block to the pages where this block does not exist? If false, we'll only update blocks that already exist
     * @param bool $updateForkedBlocks
     * @param bool $forceDisplayOrder
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return array
     */
    public function queueForDefaultsAliasing($addBlock, $updateForkedBlocks, bool $forceDisplayOrder = false)
    {
        $app = Facade::getFacadeApplication();
        $records = [];
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $oc = $this->getBlockCollectionObject();
        $site = $app->make('site')->getSite();
        $cbRelationID = $this->getBlockRelationID();
        $treeIDs = [0];
        foreach ($site->getLocales() as $locale) {
            $tree = $locale->getSiteTree();
            if (is_object($tree)) {
                $treeIDs[] = $tree->getSiteTreeID();
            }
        }
        $treeIDs = implode(',', $treeIDs);

        $rows = $db->fetchAllAssociative('select p.cID, max(cvID) as cvID from Pages p inner join CollectionVersions cv on p.cID = cv.cID where pTemplateID = ? and ptID = ? and cIsTemplate = 0 and cIsActive = 1 and siteTreeID in (' . $treeIDs . ') group by cID order by cID', [$oc->getPageTemplateID(), $oc->getPageTypeID()]);

        // now we have a list of all pages of this type in the site.
        foreach ($rows as $row) {
            // Ok, first check. Does this block already exist with the SAME block ID on this page? If so, we don't need to do
            // anything, because the block has already been updated, because it's in an unforked state

            $r1 = $db->fetchAssociative('select arHandle, bID from CollectionVersionBlocks where cID = ? and cvID = ? and bID = ?', [
                $row['cID'], $row['cvID'], $this->getBlockID(),
            ]);

            if (!is_array($r1)) {
                // Ok, no block found. So let's see if a block with the same relationID exists on the page instead.

                $r2 = $db->fetchAssociative('select arHandle, bID from CollectionVersionBlocks where cID = ? and cvID = ? and cbRelationID = ?', [
                    $row['cID'], $row['cvID'], $cbRelationID,
                ]);

                if ((is_array($r2) && $r2['bID'] && $updateForkedBlocks) || (!is_array($r2) && $addBlock)) {
                    // Ok, so either this block doesn't appear on the page at all, but addBlock set to true,
                    // or, the block appears on the page and it is forked. Either way we're going to add it to the page.

                    $record = [
                        'cID' => $row['cID'],
                        'cvID' => $row['cvID'],
                        'bID' => $this->getBlockID(),
                    ];

                    if (is_array($r2)) {
                        $record['action'] = 'update_forked_alias';
                        $record['arHandle'] = $r2['arHandle'];
                        $record['bID'] = $r2['bID'];
                    } else {
                        $record['action'] = 'add_alias';
                        $record['arHandle'] = $this->getAreaHandle();
                    }

                    $records[] = $record;
                }
            } elseif ($forceDisplayOrder) {
                $record = [
                    'cID' => $row['cID'],
                    'cvID' => $row['cvID'],
                    'bID' => $this->getBlockID(),
                ];
                $record['action'] = 'force_display_order';
                $record['arHandle'] = $this->getAreaHandle();
                $records[] = $record;
            }




        }

        return $records;
    }

    /**
     * Populate the queue to be used to work on the block and it aliases.
     *
     * @param mixed $data Custom data to be added to the queue messages
     * @param bool $includeThisBlock Include this block instance in the queue?
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function queueForDefaultsUpdate($data, $includeThisBlock = true)
    {
        $blocks = [];
        /** @var Connection $db */
        $db = app(Connection::class);
        $rows = $db->fetchAllAssociative('select cID, max(cvID) as cvID, cbRelationID from CollectionVersionBlocks where cbRelationID = ? group by cID order by cID', [$this->getBlockRelationID()]);

        $oc = $this->getBlockCollectionObject();
        $ocID = $oc->getCollectionID();
        $ocvID = $oc->getVersionID();

        foreach ($rows as $row) {
            $row2 = $db->fetchAssociative('select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ? and cbRelationID = ?', [
                $row['cID'], $row['cvID'], $row['cbRelationID'],
            ]);

            if ($includeThisBlock || ($row['cID'] != $ocID || $row['cvID'] != $ocvID || $row2['arHandle'] != $this->getAreaHandle() || $row2['bID'] != $this->getBlockID())) {
                $blocks[] = [
                    'cID' => $row['cID'],
                    'cvID' => $row['cvID'],
                    'cbRelationID' => $row['cbRelationID'],
                    'bID' => $row2['bID'],
                    'arHandle' => $row2['arHandle'],
                    'data' => $data,
                ];
            }
        }

        return $blocks;
    }

    /**
     * @deprecated Use the setBlockCollectionObject() method
     *
     * @param \Concrete\Core\Page\Collection\Collection $c
     */
    public function loadNewCollection(&$c)
    {
        $this->setBlockCollectionObject($c);
    }

    /**
     * @deprecated We switched to route-based actions
     *
     * @return string
     */
    public function _getBlockAction()
    {
        $cID = $this->getBlockActionCollectionID();
        $bID = $this->getBlockID();
        $arHandle = urlencode($this->getAreaHandle());
        $valt = app('helper/validation/token');
        $token = $valt->generate();

        return DIR_REL . '/' . DISPATCHER_FILENAME . "?cID={$cID}&amp;bID={$bID}&amp;arHandle={$arHandle}&amp;ccm_token={$token}";
    }

    /**
     * @deprecated We switched to route-based actions
     *
     * @return string
     */
    public function getBlockEditAction()
    {
        return $this->_getBlockAction();
    }

    /**
     * @deprecated We switched to route-based actions
     *
     * @return string
     */
    public function getBlockUpdateInformationAction()
    {
        $str = $this->_getBlockAction();

        return $str . '&amp;btask=update_information';
    }

    /**
     * @deprecated We switched to route-based actions
     *
     * @return string
     */
    public function getBlockUpdateCssAction()
    {
        $str = $this->_getBlockAction();

        return $str . '&amp;btask=update_block_css';
    }

    /**
     * @deprecated no more scrapbooks in the dashboard
     *
     * @return bool
     */
    public function isGlobal()
    {
        return false;
    }

    /**
     * @deprecated use the getController() method
     *
     * @return \Concrete\Core\Block\BlockController
     */
    public function getInstance()
    {
        return $this->getController();
    }

    /**
     * @deprecated use the deleteBlock() method
     *
     * @param bool $forceDelete
     */
    public function delete($forceDelete = false)
    {
        $this->deleteBlock($forceDelete);
    }

    /**
     * @deprecated This method does nothing
     */
    public function refreshCache()
    {
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
                foreach($rows as $row) {
                    Cache::delete('block', $row['bID'] . ':' . $c->getCollectionID() . ':' . $c->getVersionID() . ':' . $a->getAreaHandle());
                    Cache::delete('block_view_output', $c->getCollectionID() . ':' . $row['bID'] . ':' . $a->getAreaHandle());
                }
            }
        }
         */
    }

    /**
     * @deprecated This method does nothing
     */
    public function refreshCacheAll()
    {
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
}
