<?php
namespace Concrete\Core\Block;

use Area;
use BlockType;
use Collection;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Feature\Assignment\Assignment as FeatureAssignment;
use Concrete\Core\Feature\Assignment\CollectionVersionAssignment as CollectionVersionFeatureAssignment;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Package\PackageList;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Config;
use Loader;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Page;
use Concrete\Core\Support\Facade\Facade;

class Block extends Object implements \Concrete\Core\Permission\ObjectInterface
{
    public $bID;
    public $btID;
    public $pkgID;
    public $btHandle;
    public $btName;
    public $uID;
    public $bDateAdded;
    public $bDateModified;
    protected $cID;
    protected $arHandle;
    protected $c;
    protected $issID;
    protected $proxyBlock = null;
    protected $bActionCID;
    protected $cacheSettings;
    protected $cbOverrideBlockTypeCacheSettings;
    protected $cbOverrideBlockTypeContainerSettings;
    protected $cbEnableBlockContainer;
    protected $cbOverrideAreaPermissions;
    protected $cbDisplayOrder;
    protected $bName;
    public $btCachedBlockRecord;
    public $a;
    public $originalBID;

    protected $bFilename;

    /**
     * Set the instance properties.
     *
     * @param array $blockInfo
     * @param \Concrete\Core\Page\Collection\Collection $c
     * @param \Concrete\Core\Area\Area|string|null $a
     *
     * @return self
     */
    public static function populateManually($blockInfo, $c, $a)
    {
        $b = new self();
        $b->setPropertiesFromArray($blockInfo);

        if (is_object($a)) {
            $b->a = $a;
            $b->arHandle = $a->getAreaHandle();
        } elseif (is_string($a) && $a !== '') {
            $b->arHandle = $a;
        }

        $b->cID = $c->getCollectionID();
        $b->c = $c;

        return $b;
    }

    /**
     * Get a block given its name.
     *
     * @return self|null
     */
    public static function getByName($blockName)
    {
        $result = null;
        if (is_string($blockName) && $blockName !== '') {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $row = $db->fetchAssoc(
                '
                    select
                        b.bID, cvb.arHandle, cvb.cID
                    from
                        Blocks AS b
                        inner join CollectionVersionBlocks AS cvb ON b.bID = cvb.bID
                        inner join CollectionVersions AS cv ON cvb.cID = cv.cID AND cvb.cvID = cv.cvID
                    where
                        b.bName = ? AND cv.cvIsApproved = 1
                    order by
                        cvb.cvID desc
                    limit 1
                ',
                [$blockName]
            );
            if ($row !== false) {
                $result = self::getByID($row['bID'], Page::getByID($row['cID']), $row['arHandle']);
            }
        }

        return $result;
    }

    /**
     * Get a block given its ID.
     *
     * @param int $bID
     * @param \Concrete\Core\Page\Collection\Collection|null $c
     * @param \Concrete\Core\Area\Area|null $a
     *
     * @return self|null
     */
    public static function getByID($bID, $c = null, $a = null)
    {
        $app = Facade::getFacadeApplication();
        $cache = $app->make('cache/request');
        if ($c == null && $a == null) {
            $cID = 0;
            $arHandle = '';
            $cvID = 0;
            $b = null;
            $cacheKey = 'block/'.$bID;
        } else {
            if (is_object($a)) {
                $arHandle = $a->getAreaHandle();
            } elseif (is_string($a) && $a !== '') {
                $arHandle = $a;
                $a = Area::getOrCreate($c, $a);
            } else {
                $arHandle = '';
                $a = null;
            }
            $cID = $c->getCollectionID();
            $cvID = $c->getVersionID();
            $cacheKey = 'block/' . $bID . ':' . $cID . ':' . $cvID . ':' . $arHandle;
        }

        if ($cache->isEnabled()) {
            $cacheItem = $cache->getItem($cacheKey);
            if (!$cacheItem->isMiss()) {
                $b = $cacheItem->get();
                if ($b instanceof self) {
                    return $b;
                }
            }
        }

        $db = $app->make('database')->connection();

        $result = null;

        $b = new self();
        if ($c == null && $a == null) {
            // just grab really specific block stuff
            $q = '
                select
                    bID,
                    bIsActive,
                    BlockTypes.btID,
                    Blocks.btCachedBlockRecord,
                    BlockTypes.btHandle,
                    BlockTypes.pkgID,
                    BlockTypes.btName,
                    bName,
                    bDateAdded,
                    bDateModified,
                    bFilename,
                    Blocks.uID
                from
                    Blocks
                    inner join BlockTypes on (Blocks.btID = BlockTypes.btID)
                where
                    bID = ?
                limit 1
            ';
            $v = [$bID];
            $b->isOriginal = 1;
        } else {
            $b->arHandle = $arHandle;
            $b->a = $a;
            $b->cID = $cID;
            $b->c = $c;
            $vo = $c->getVersionObject();
            $cvID = $vo->getVersionID();
            $q = '
                select
                    CollectionVersionBlocks.isOriginal,
                    CollectionVersionBlocks.cbIncludeAll,
                    Blocks.btCachedBlockRecord,
                    BlockTypes.pkgID,
                    CollectionVersionBlocks.cbOverrideAreaPermissions,
                    CollectionVersionBlocks.cbOverrideBlockTypeCacheSettings,
                    CollectionVersionBlocks.cbOverrideBlockTypeContainerSettings,
                    CollectionVersionBlocks.cbEnableBlockContainer,
                    CollectionVersionBlocks.cbDisplayOrder,
                    Blocks.bIsActive,
                    Blocks.bID,
                    Blocks.btID,
                    bName,
                    bDateAdded,
                    bDateModified,
                    bFilename,
                    btHandle,
                    Blocks.uID
                from
                    CollectionVersionBlocks
                    inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID)
                    inner join BlockTypes on (Blocks.btID = BlockTypes.btID)
                where
                    CollectionVersionBlocks.arHandle = ?
                    and CollectionVersionBlocks.cID = ?
                    and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll = 1)
                    and CollectionVersionBlocks.bID = ?
                limit 1
            ';
            $v = [$b->arHandle, $cID, $cvID, $bID];
        }
        $row = $db->fetchAssoc($q, $v);
        if ($row !== false) {
            $b->setPropertiesFromArray($row);

            $bt = $b->getBlockTypeObject();
            $class = $bt->getBlockTypeClass();
            if ($class) {
                $b->instance = $app->build($class);
                if ($cache->isEnabled()) {
                    $cacheItem->set(clone $b);
                }
                $result = $b;
            }
        }

        return $result;
    }

    /**
     * Get the block type ID.
     *
     * @return int
     */
    public function getBlockTypeID()
    {
        return $this->btID;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectIdentifier()
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->cID . ':' . $this->getAreaHandle() . ':' . $this->bID;
    }

    /**
     * Get the area handle.
     *
     * @return string
     */
    public function getAreaHandle()
    {
        return $this->arHandle;
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
     * Get the proxy block (used if this particular block is aliased to a particular collection).
     *
     * @return mixed|null
     */
    public function getProxyBlock()
    {
        return $this->proxyBlock;
    }

    /**
     * Set the proxy block (used if this particular block is aliased to a particular collection).
     *
     * @param mixed $block
     */
    public function setProxyBlock($block)
    {
        $this->proxyBlock = $block;
    }

    /**
     * Render this block.
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
     * @return bool
     * 
     * @deprecated Legacy: no more scrapbooks in the dashboard.
     */
    public function isGlobal()
    {
        return false;
    }

    /**
     * Return the cached block record (if available).
     *
     * @return string|null
     */
    public function getBlockCachedRecord()
    {
        return $this->btCachedBlockRecord;
    }

    /**
     * Return the cached output of this block.
     *
     * @param \Concrete\Core\Area\Area|null $area
     *
     * @return string|null
     */
    public function getBlockCachedOutput($area)
    {
        if ($this->isBlockInStack() && is_object($area)) {
            $arHandle = $area->getAreaHandle();
            $cx = Page::getCurrentPage();
            $cID = $cx->getCollectioniD();
            $cvID = $cx->getVersionID();
        } else {
            $arHandle = $this->getAreaHandle();
            $c = $this->getBlockCollectionObject();
            $cID = $c->getCollectionID();
            $cvID = $c->getVersionID();
        }
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $r = $db->fetchAssoc(
            '
                select
                    btCachedBlockOutput, btCachedBlockOutputExpires
                from
                    CollectionVersionBlocksOutputCache
                where
                    cID = ?
                    and cvID = ?
                    and bID = ?
                    and arHandle = ?
            ',
            [
                $cID,
                $cvID,
                $this->getBlockID(),
                $arHandle,
            ]
        );

        if ($r === false || $r['btCachedBlockOutputExpires'] < time()) {
            return null;
        } else {
            return $r['btCachedBlockOutput'];
        }
    }

    /**
     * Is this block in a stack?
     *
     * @return bool
     */
    public function isBlockInStack()
    {
        $result = false;
        $co = $this->getBlockCollectionObject();
        if ($co !== null) {
            if ($co->getPageTypeHandle() == STACKS_PAGE_TYPE) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get the collection containing the block.
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public function getBlockCollectionObject()
    {
        return is_object($this->c) ? $this->c : $this->getOriginalCollection();
    }

    /**
     * Find the original Page (where this bID is marked as isOriginal).
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public function getOriginalCollection()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = '
            select
                Pages.cID
            from
                Pages
                inner join CollectionVersionBlocks on (CollectionVersionBlocks.cID = Pages.cID)
            where
                CollectionVersionBlocks.bID = ?
                and CollectionVersionBlocks.isOriginal = 1
            limit 1
        ';
        $cID = $db->fetchColumn($q, [$this->bID]);

        return $cID ? Page::getByID($cID, 'ACTIVE') : null;
    }

    /**
     * Get the block ID.
     *
     * @return int
     */
    public function getBlockID()
    {
        return $this->bID;
    }

    /**
     * Store the block output to the cache.
     *
     * @param string $content
     * @param int $lifetime The cache duration (in seconds). If empty or negative: we assume 5 years.
     * @param \Concrete\Core\Area\Area|null $area
     */
    public function setBlockCachedOutput($content, $lifetime, $area)
    {
        $c = $this->getBlockCollectionObject();

        if ($lifetime > 0) {
            $btCachedBlockOutputExpires = time() + $lifetime;
        } else {
            $btCachedBlockOutputExpires = strtotime('+5 years');
        }
        if ($this->isBlockInStack() && is_object($area)) {
            $arHandle = $area->getAreaHandle();
            $cx = Page::getCurrentPage();
            $cID = $cx->getCollectioniD();
            $cvID = $cx->getVersionID();
        } else {
            $arHandle = $this->getAreaHandle();
            $cID = $c->getCollectionID();
            $cvID = $c->getVersionID();
        }

        if ($arHandle && $cID && $cvID) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $db->Replace(
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
                ]
            );
        }
    }

    /**
     * Include a file.
     *
     * @param string $file The path to the file (relative to this block path).
     */
    public function inc($file)
    {
        $fullpath = $this->getBlockPath() . '/' . $file;
        if (file_exists($fullpath)) {
            $b = $this;
            include $fullpath;
        }
    }

    /**
     * Get the block path.
     *
     * @return string
     */
    public function getBlockPath()
    {
        $pkgHandle = $this->getPackageHandle();
        $blockHandle = $this->getBlockTypeHandle();
        if ($pkgHandle) {
            $dirp = (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) ? DIR_PACKAGES : DIR_PACKAGES_CORE;
            $dir = $dirp . '/' . $pkgHandle . '/' . DIRNAME_BLOCKS . '/' . $blockHandle;
        } else {
            $dir = DIR_FILES_BLOCK_TYPES . '/' . $blockHandle;
            if (!is_dir($dir)) {
                $dir = DIR_FILES_BLOCK_TYPES_CORE . '/' . $blockHandle;
            }
        }

        return $dir;
    }

    /**
     * Get the ID of the package to which this block belongs to (0 if no package).
     *
     * @return int
     */
    public function getPackageID()
    {
        return (int) $this->pkgID;
    }

    /**
     * Get the handle of the package to which this block belongs to (empty string if no package).
     *
     * @return string|bool
     */
    public function getPackageHandle()
    {
        return $this->pkgID ? PackageList::getHandle($this->pkgID) : '';
    }

    /**
     * Get the handle of the block type.
     *
     * @return string
     */
    public function getBlockTypeHandle()
    {
        return $this->btHandle;
    }

    /**
     * Reset the permissions, imposing to inherit those of the containing area.
     */
    public function revertToAreaPermissions()
    {
        $c = $this->getBlockCollectionObject();

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $v = [$c->getCollectionID(), $c->getVersionID(), $this->bID];
        $db->executeQuery(
            '
                delete from
                    BlockPermissionAssignments
                where
                    cID = ?
                    and cvID = ?
                    and bID = ?
            ',
            $v
        );

        $v[] = $this->arHandle;
        $db->executeQuery(
            '
                update
                    CollectionVersionBlocks
                set
                    cbOverrideAreaPermissions = 0
                where
                    cID = ?
                    and (cvID = ? or cbIncludeAll = 1)
                    and bID = ?
                    and arHandle = ?
            ',
            $v
        );
    }

    /**
     * Set the current Collection/CollectionVersion.
     *
     * @param mixed $c
     */
    public function loadNewCollection($c)
    {
        $this->c = $c;
    }

    /**
     * Set the current Area.
     *
     * @param \Concrete\Core\Area\Area $a
     */
    public function setBlockAreaObject(\Concrete\Core\Area\Area $a)
    {
        $this->a = $a;
        $this->arHandle = $a->getAreaHandle();
    }

    /**
     * Shall we disable block versioning?
     *
     * @return bool
     */
    public function disableBlockVersioning()
    {
        return $this->cbIncludeAll;
    }

    /**
     * Get the number of versions of this block.
     *
     * @return int
     */
    public function getNumChildren()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $total = (int) $db->fetchColumn(
            '
                select
                    count(*) as total
                from
                    CollectionVersionBlocks
                where
                    bID = ?
                    and isOriginal = 0
            ',
            [$this->bID]
        );

        return $total;
    }

    /**
     * Get the Controller instance.
     *
     * @deprecated Use getInstance
     */
    public function getController()
    {
        return $this->getInstance();
    }

    /**
     * Get the block type instance.
     *
     * @return \Concrete\Core\Block\BlockController
     */
    public function getInstance()
    {
        $app = Facade::getFacadeApplication();
        if (
            $app->make('config')->get('concrete.cache.blocks')
            && $this->instance->cacheBlockRecord()
            && is_object($this->instance->getBlockControllerData())
        ) {
            $this->instance->__construct();
        } else {
            $bt = $this->getBlockTypeObject();
            $class = $bt->getBlockTypeClass();
            $this->instance = $app->build($class, [$this]);
        }
        $this->instance->setBlockObject($this);

        return $this->instance;
    }

    /**
     * Get the BlockType instance.
     *
     * @return \Concrete\Core\Block\BlockType\BlockType|null
     */
    public function getBlockTypeObject()
    {
        return $this->btID ? BlockType::getByID($this->btID) : null;
    }

    /**
     * Get a list of collections that include this block, along with area name, etc...
     *
     * @return \Concrete\Core\Page\Page[]
     */
    public function getCollectionList()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $r = $db->executeQuery(
            '
                select distinct
                    Pages.cID
                from
                    CollectionVersionBlocks
                    inner join Pages on (CollectionVersionBlocks.cID = Pages.cID)
                    inner join CollectionVersions on (CollectionVersions.cID = Pages.cID)
                where
                    CollectionVersionBlocks.bID = ?
            ',
            [
                $this->bID,
            ]
        );
        $result = [];
        while ($row = $r->fetchRow()) {
            $result[] = Page::getByID($row['cID'], 'RECENT');
        }
        $r->closeCursor();

        return $result;
    }

    /**
     * Update the fields common to every block.
     *
     * @param array $data
     */
    public function update($data)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $bDateModified = $app->make('helper/date')->getOverridableNow();
        $bID = $this->bID;

        $db->executeQuery(
            '
                update
                    Blocks
                set
                    bDateModified = ?
                where
                    bID = ?
                limit 1
            ',
            [$bDateModified, $bID]
        );

        $this->refreshBlockOutputCache();

        $bt = $this->getBlockTypeObject();
        $class = $bt->getBlockTypeClass();
        $bc = $app->build($class, [$this]);
        $bc->save($data);
    }

    /**
     * Clear the output cache stored in the database for this block. 
     */
    public function refreshBlockOutputCache()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $c = $this->getBlockCollectionObject();
        $db->executeQuery(
            '
                update
                    CollectionVersionBlocksOutputCache
                set
                    btCachedBlockOutputExpires = 0
                where
                    cID = ?
                    and cvID = ?
                    and arHandle = ?
                    and bID = ?
            ',
            [
                $c->getCollectionID(),
                $c->getVersionID(),
                $this->getAreaHandle(),
                $this->getBlockID(),
            ]
        );
    }

    /**
     * Get the block collection ID.
     *
     * @return int
     */
    public function getBlockCollectionID()
    {
        return (int) $this->cID;
    }

    /**
     * Is this block active?
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->bIsActive;
    }

    /**
     * Deactivate this block.
     */
    public function deactivate()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery(
            '
                update
                    Blocks
                set
                    bIsActive = 0
                where
                    bID = ?
                limit 1
            ',
            [$this->bID]
        );
        $this->bIsActive = 0;
    }

    /**
     * Activate this block.
     */
    public function activate()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery(
            '
                update
                    Blocks
                set
                    bIsActive = 1
                where
                    bID = ?
                limit 1
            ',
            [$this->bID]
        );
        $this->bIsActive = 1;
    }

    /**
     * Creates an alias of the block, attached to this collection, within the CollectionVersionBlocks table.
     * Additionally, this command grabs the permissions from the original record in the CollectionVersionBlocks table, and attaches them to the new one.
     *
     * @param \Concrete\Core\Page\Collection\Collection $c
     */
    public function alias($c)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $bID = $this->bID;
        $newBlockDisplayOrder = $c->getCollectionAreaDisplayOrder($this->getAreaHandle());
        $cvID = $c->getVersionID();
        $cID = $c->getCollectionID();
        $v = [$cID, $cvID, $this->bID, $this->getAreaHandle()];

        $total = (int) $db->fetchColumn(
            '
                select
                    bID
                from
                    CollectionVersionBlocks
                where
                    cID = ?
                    and cvID = ?
                    and bID = ?
                    and arHandle = ?
            ',
            $v
        );
        if ($total === 0) {
            array_push($v, $newBlockDisplayOrder, 0, $this->overrideAreaPermissions(), $this->overrideBlockTypeCacheSettings(), $this->overrideBlockTypeContainerSettings(), $this->enableBlockContainer() ? 1 : 0);
            $db->executeQuery(
                '
                    insert into CollectionVersionBlocks
                        (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions, cbOverrideBlockTypeCacheSettings, cbOverrideBlockTypeContainerSettings, cbEnableBlockContainer)
                        values
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ',
                $v
            );

            // styles
            $issID = $this->getCustomStyleSetID();
            if ($issID > 0) {
                $db->executeQuery(
                    '
                        insert into CollectionVersionBlockStyles
                            (cID, cvID, bID, arHandle, issID)
                            values
                            (?, ?, ?, ?, ?)
                    ',
                    [
                        $cID,
                        $cvID,
                        $this->bID,
                        $this->getAreaHandle(),
                        $issID,
                    ]
                );
            }

            // custom cache
            if ($this->overrideBlockTypeCacheSettings()) {
                $db->executeQuery(
                    '
                        insert into CollectionVersionBlocksCacheSettings
                            (cID, cvID, bID, arHandle, btCacheBlockOutput, btCacheBlockOutputOnPost, btCacheBlockOutputForRegisteredUsers, btCacheBlockOutputLifetime)
                            values
                            (?, ?, ?, ?, ?, ?, ?, ?)
                    ',
                    [
                        $cID,
                        $cvID,
                        $this->bID,
                        $this->getAreaHandle(),
                        (int) $this->cacheBlockOutput(),
                        (int) $this->cacheBlockOutputOnPost(),
                        (int) $this->cacheBlockOutputForRegisteredUsers(),
                        (int) $this->getBlockOutputCacheLifetime(),
                    ]
                );
            }
            // now we grab the permissions from the block we're aliasing from
            $oc = $this->getBlockCollectionObject();
            $ocID = $oc->getCollectionID();
            $ocvID = $oc->getVersionID();

            $rf = $db->executeQuery(
                '
                    select
                        faID
                    from
                        BlockFeatureAssignments
                    where
                        bID = ?
                        and cID = ?
                        and cvID = ?
                ',
                [$this->bID, $ocID, $ocvID]
            );
            while ($rowf = $rf->fetch()) {
                $db->Replace(
                    'BlockFeatureAssignments',
                    [
                        'cID' => $cID,
                        'cvID' => $cvID,
                        'bID' => $this->bID,
                        'faID' => $rowf['faID'],
                    ],
                    [
                        'cID',
                        'cvID',
                        'bID',
                        'faID',
                    ]
                );
            }
            $rf->closeCursor();

            $ra = $db->executeQuery(
                '
                    select
                        paID, pkID
                    from
                        BlockPermissionAssignments
                    where
                        bID = ?
                        and cID = ?
                        and cvID = ?
                ',
                [
                    $this->bID,
                    $ocID,
                    $ocvID,
                ]
            );
            while ($row_a = $ra->fetch()) {
                $db->Replace(
                    'BlockPermissionAssignments',
                    [
                        'cID' => $cID,
                        'cvID' => $cvID,
                        'bID' => $this->bID,
                        'paID' => $row_a['paID'],
                        'pkID' => $row_a['pkID'],
                    ],
                    [
                        'cID',
                        'cvID',
                        'bID',
                        'paID',
                        'pkID',
                    ]
                );
            }
            $ra->closeCursor();
        }
    }

    /**
     * Area permission are overridden?
     *
     * @return bool
     */
    public function overrideAreaPermissions()
    {
        if (!$this->cbOverrideAreaPermissions) {
            $this->cbOverrideAreaPermissions = 0;
        }

        return $this->cbOverrideAreaPermissions;
    }

    /**
     * Block type settings are overridden?
     *
     * @return bool
     */
    public function overrideBlockTypeCacheSettings()
    {
        if (!$this->cbOverrideBlockTypeCacheSettings) {
            $this->cbOverrideBlockTypeCacheSettings = 0;
        }

        return $this->cbOverrideBlockTypeCacheSettings;
    }

    /**
     * Block contained is enabled?
     *
     * @return bool
     */
    public function enableBlockContainer()
    {
        return (bool) $this->cbEnableBlockContainer;
    }

    /**
     * If true, container classes will not be wrapped around this block type in edit mode (if the theme in question supports a grid framework).
     *
     * @return bool
     */
    public function ignorePageThemeGridFrameworkContainer()
    {
        if ($this->overrideBlockTypeContainerSettings()) {
            return !$this->enableBlockContainer();
        }
        $controller = $this->getInstance();

        return $controller->ignorePageThemeGridFrameworkContainer();
    }

    /**
     * Override block type container settings?
     *
     * @return bool
     */
    public function overrideBlockTypeContainerSettings()
    {
        if (!$this->cbOverrideBlockTypeContainerSettings) {
            $this->cbOverrideBlockTypeContainerSettings = 0;
        }

        return $this->cbOverrideBlockTypeContainerSettings;
    }

    /**
     * Get the CacheSettings instance.
     *
     * @return CacheSettings
     */
    public function getBlockCacheSettingsObject()
    {
        if (!isset($this->cacheSettings)) {
            $this->cacheSettings = CacheSettings::get($this);
        }

        return $this->cacheSettings;
    }

    /**
     * Shall we cache the block output?
     *
     * @return bool
     */
    public function cacheBlockOutput()
    {
        $settings = $this->getBlockCacheSettingsObject();

        return $settings->cacheBlockOutput();
    }

    /**
     * Called by the scrapbook proxy block, this disables the original block container for the current request,
     * because the scrapbook block takes care of rendering the container.
     */
    public function disableBlockContainer()
    {
        $this->cbOverrideBlockTypeContainerSettings = true;
        $this->cbEnableBlockContainer = false;
    }

    /**
     * Shall we cache the block output on POST requests?
     *
     * @return bool
     */
    public function cacheBlockOutputOnPost()
    {
        $settings = $this->getBlockCacheSettingsObject();

        return $settings->cacheBlockOutputOnPost();
    }

    /**
     * Shall we cache the block output for registered users?
     *
     * @return bool
     */
    public function cacheBlockOutputForRegisteredUsers()
    {
        $settings = $this->getBlockCacheSettingsObject();

        return $settings->cacheBlockOutputForRegisteredUsers();
    }

    /**
     * The cache duration (in seconds).
     *
     * @return int
     */
    public function getBlockOutputCacheLifetime()
    {
        $settings = $this->getBlockCacheSettingsObject();

        return $settings->getBlockOutputCacheLifetime();
    }

    /**
     * The ID of the custom style set.
     *
     * @return int
     */
    public function getCustomStyleSetID()
    {
        if (!isset($this->issID)) {
            $arHandle = $this->getAreaHandle();
            if ($arHandle) {
                $app = Facade::getFacadeApplication();
                $db = $app->make('database')->connection();

                $a = $this->getBlockAreaObject();
                if ($a->isGlobalArea()) {
                    // We need to check against the global area name (we currently have the wrong area handle passed in).
                    $arHandle = STACKS_AREA_NAME;
                }

                $co = $this->getBlockCollectionObject();
                $this->issID = (int) $db->fetchColumn(
                    '
                        select
                            issID
                        from
                            CollectionVersionBlockStyles
                        where
                            cID = ?
                            and cvID = ?
                            and arHandle = ?
                            and bID = ?
                        limit 1
                    ',
                    [
                        $co->getCollectionID(),
                        $co->getVersionID(),
                        $arHandle,
                        $this->bID,
                    ]
                );
            } else {
                $this->issID = 0;
            }
        }

        return $this->issID;
    }

    /**
     * Get the Area object associated to this block.
     *
     * @return \Concrete\Core\Area\Area|null
     */
    public function getBlockAreaObject()
    {
        if (isset($this->a) && is_object($this->a)) {
            return $this->a;
        } else {
            return null;
        }
    }

    /**
     * Move the block to a new collection.
     *
     * @param \Concrete\Core\Page\Collection\Collection $collection The new collection.
     * @param \Concrete\Core\Area\Area $area The new area.
     *
     * @return bool
     */
    public function move(\Concrete\Core\Page\Collection\Collection $collection, \Concrete\Core\Area\Area $area)
    {
        $old_collection = $this->getBlockCollectionID();
        $new_collection = $collection->getCollectionID();

        $old_version = $this->getBlockCollectionObject()->getVersionObject()->getVersionID();
        $new_version = $collection->getVersionObject()->getVersionID();

        $old_area_handle = $this->getAreaHandle();
        $new_area_handle = $area->getAreaHandle();

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        return (bool) $db->update(
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
     * Duplicates the existing block into a new collection.
     *
     * @param \Concrete\Core\Page\Collection\Collection $nc The new collection.
     * @param bool $isCopyFromMasterCollectionPropagation
     *
     * @return \Concrete\Core\Block\Block|null
     */
    public function duplicate($nc, $isCopyFromMasterCollectionPropagation = false)
    {
        $app = Facade::getFacadeApplication();

        $bt = $this->getBlockTypeObject();
        $blockTypeClass = $bt->getBlockTypeClass();
        $bc = $app->build($blockTypeClass, [$this]);
        if (!$blockTypeClass) {
            return null;
        }
        $db = $app->make('database')->connection();

        $bDate = $app->make('helper/date')->getOverridableNow();
        $db->executeQuery(
            '
                insert into Blocks
                    (bName, bDateAdded, bDateModified, bFilename, btID, uID)
                    values
                    (?, ?, ?, ?, ?, ?)
            ',
            [
                $this->bName,
                $bDate,
                $bDate,
                $this->bFilename,
                $this->btID,
                $this->uID,
            ]
        );
        $newBID = $db->lastInsertId();

        // now, we duplicate the block-specific permissions
        $oc = $this->getBlockCollectionObject();
        $ocID = $oc->getCollectionID();
        $ovID = $oc->getVersionID();

        $ncID = $nc->getCollectionID();
        $nvID = $nc->getVersionID();

        // Composer specific
        $row = $db->fetchAssoc(
            '
                select
                    cID,
                    arHandle,
                    cbDisplayOrder,
                    ptComposerFormLayoutSetControlID
                from
                    PageTypeComposerOutputBlocks
                where
                    cID = ?
                    and bID = ?
                    and arHandle = ?
            ',
            [
                $ocID,
                $this->bID,
                $this->arHandle,
            ]
        );
        if ($row !== false) {
            $db->insert(
                'PageTypeComposerOutputBlocks',
                [
                    'cID' => $ncID,
                    'arHandle' => $this->arHandle,
                    'cbDisplayOrder' => $row['cbDisplayOrder'],
                    'ptComposerFormLayoutSetControlID' => $row['ptComposerFormLayoutSetControlID'],
                    'bID' => $newBID,
                ]
            );
        }

        $r = $db->executeQuery(
            '
                select
                    paID,
                    pkID
                from
                    BlockPermissionAssignments
                where
                    cID = ?
                    and bID = ?
                    and cvID = ?
            ',
            [
                $ocID,
                $this->bID,
                $ovID,
            ]
        );
        while ($row = $r->fetch()) {
            $db->Replace(
                'BlockPermissionAssignments',
                [
                    'cID' => $ncID,
                    'cvID' => $nvID,
                    'bID' => $newBID,
                    'paID' => $row['paID'],
                    'pkID' => $row['pkID'],
                ],
                [
                    'cID',
                    'cvID',
                    'bID',
                    'paID',
                    'pkID',
                ]
            );
        }
        $r->free();

        // we duplicate block-specific sub-content
        if ($isCopyFromMasterCollectionPropagation && method_exists($bc, 'duplicate_master')) {
            $bc->duplicate_master($newBID, $nc);
        } else {
            $bc->duplicate($newBID);
        }

        $features = $bc->getBlockTypeFeatureObjects();
        if (!empty($features)) {
            foreach ($features as $fe) {
                $fd = $fe->getFeatureDetailObject($bc);
                $fa = CollectionVersionFeatureAssignment::add($fe, $fd, $nc);
                $db->executeQuery(
                    '
                        insert into
                            BlockFeatureAssignments
                            (cID, cvID, bID, faID)
                            values
                            (?, ?, ?, ?)
                    ',
                    [
                        $ncID,
                        $nvID,
                        $newBID,
                        $fa->getFeatureAssignmentID(),
                    ]
                );
            }
        }

        // finally, we insert into the CollectionVersionBlocks table
        if (!is_numeric($this->cbDisplayOrder)) {
            $newBlockDisplayOrder = $nc->getCollectionAreaDisplayOrder($this->arHandle);
        } else {
            $newBlockDisplayOrder = $this->cbDisplayOrder;
        }
        $db->executeQuery(
            '
                insert into CollectionVersionBlocks
                    (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions, cbOverrideBlockTypeCacheSettings,cbOverrideBlockTypeContainerSettings, cbEnableBlockContainer)
                    values
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ',
            [
                $ncID,
                $nvID,
                $newBID,
                $this->arHandle,
                $newBlockDisplayOrder,
                1,
                $this->overrideAreaPermissions(),
                $this->overrideBlockTypeCacheSettings(),
                $this->overrideBlockTypeContainerSettings(),
                $this->enableBlockContainer() ? 1 : 0,
            ]
        );
        // now we make a DUPLICATE entry in the BlockRelations table, so that we know that the blocks are chained together
        $db->executeQuery(
            '
                insert into
                    BlockRelations
                    (originalBID, bID, relationType)
                    values (?, ?, ?)
            ',
            [
                $this->bID,
                $newBID,
                'DUPLICATE',
            ]
        );

        $nb = self::getByID($newBID, $nc, $this->arHandle);

        $issID = $this->getCustomStyleSetID();
        if ($issID > 0) {
            $db->executeQuery(
                '
                    insert into
                        CollectionVersionBlockStyles
                        (cID, cvID, bID, arHandle, issID)
                        values
                        (?, ?, ?, ?, ?)
                ',
                [
                    $ncID,
                    $nvID,
                    $newBID,
                    $this->arHandle,
                    $issID,
                ]
            );
        }

        return $nb;
    }

    /**
     * Get the custom styles for this block (return false if no custom style is set and $force is false).
     *
     * @param bool $force Set to true to always get custom styles.
     *
     * @return CustomStyle|CoreAreaLayoutCustomStyle|null
     */
    public function getCustomStyle($force = false)
    {
        $result = null;
        if ($force || $this->getCustomStyleSetID() > 0) {
            $csr = StyleSet::getByID($this->getCustomStyleSetID());
            $theme = $this->c->getCollectionThemeObject();
            switch ($this->getBlockTypeHandle()) {
                case BLOCK_HANDLE_LAYOUT_PROXY:
                    $result = new CoreAreaLayoutCustomStyle($csr, $this, $theme);
                    break;
                default:
                    $result = new CustomStyle($csr, $this, $theme);
                    break;
            }
        }

        return $result;
    }

    /**
     * Reset the block container settings.
     */
    public function resetBlockContainerSettings()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $bt = $this->getBlockTypeObject();
        $db->update(
            'CollectionVersionBlocks',
            [
                'cbOverrideBlockTypeContainerSettings' => 0,
                'cbEnableBlockContainer' => $bt->ignorePageThemeGridFrameworkContainer() ? 1 : 0,
            ],
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->bID,
            ]
        );
    }

    /**
     * Enable/disable the block container custom settings.
     *
     * @param bool $enableBlockContainer
     */
    public function setCustomContainerSettings($enableBlockContainer)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->update(
            'CollectionVersionBlocks',
            [
                'cbOverrideBlockTypeContainerSettings' => 1,
                'cbEnableBlockContainer' => $enableBlockContainer ? 1 : 0,
            ],
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->bID,
            ]
        );
    }

    /**
     * Reset the cache settings to the default ones.
     */
    public function resetCustomCacheSettings()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->update(
            'CollectionVersionBlocks',
            [
                'cbOverrideBlockTypeCacheSettings' => 0,
            ],
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->bID,
            ]
        );
        $db->delete(
            'CollectionVersionBlocksCacheSettings',
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->bID,
            ]
        );
    }

    /**
     * Set custom cache settings.
     *
     * @param bool $enabled Cache is enabled?
     * @param bool $enabledOnPost Cache is enabled for POST requests?
     * @param bool $enabledForRegistered Cache is enabled for registered users?
     * @param int $lifetime The cache duration (in seconds).
     */
    public function setCustomCacheSettings($enabled, $enabledOnPost, $enabledForRegistered, $lifetime)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->Replace(
            'CollectionVersionBlocksCacheSettings',
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->bID,
                'btCacheBlockOutput' => $enabled ? 1 : 0,
                'btCacheBlockOutputOnPost' => $enabledOnPost ? 1 : 0,
                'btCacheBlockOutputForRegisteredUsers' => $enabledForRegistered ? 1 : 0,
                'btCacheBlockOutputLifetime' => (int) $lifetime,
            ],
            [
                'cID',
                'cvID',
                'bID',
                'arHandle',
            ]
        );
        $db->update(
            'CollectionVersionBlocks',
            [
                'cbOverrideBlockTypeCacheSettings' => 1,
            ],
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->bID,
            ]
        );
    }

    /**
     * Set the custom style set.
     *
     * @param StyleSet $set
     */
    public function setCustomStyleSet(StyleSet $set)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->Replace(
            'CollectionVersionBlockStyles',
            [
                'cID' => $this->getBlockCollectionID(),
                'cvID' => $cvID,
                'arHandle' => $this->getAreaHandle(),
                'bID' => $this->bID,
                'issID' => $set->getID(),
            ],
            [
                'cID',
                'cvID',
                'bID',
                'arHandle',
            ]
        );
        $this->issID = $set->getID();
    }

    /**
     * Reset the block custom style.
     */
    public function resetCustomStyle()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $db->executeQuery(
            '
                delete from
                    CollectionVersionBlockStyles
                where
                    cID = ?
                    and cvID = ?
                    and arHandle = ?
                    and bID = ?
            ',
            [
                $this->getBlockCollectionID(),
                $cvID,
                $this->getAreaHandle(),
                $this->bID,
            ]
        );
        $this->issID = 0;
    }

    public function __destruct()
    {
        unset($this->c);
        unset($this->a);
        unset($this->instance);
    }

    /**
     * Removes a cached version of the block.
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


            $rows=$db->getAll('select cID, cvID, arHandle FROM CollectionVersionBlocks cvb inner join btCoreScrapbookDisplay bts on bts.bID = cvb.bID where bts.bOriginalID = ?', [$this->getBlockID()]);
            foreach($rows as $row){
                Cache::delete('block', $this->getBlockID() . ':' . intval($row['cID']) . ':' . intval($row['cvID']) . ':' . $row['arHandle'] );
                Cache::delete('block_view_output', $row['cID'] . ':' . $this->getBlockID() . ':' . $row['arHandle']);
                Cache::delete('block', $this->getBlockID());
            }

            if ($this->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY && is_object($a)) {
                $rows=$db->getAll('select cID, cvID, arHandle FROM CollectionVersionBlocks cvb inner join btCoreScrapbookDisplay bts on bts.bOriginalID = cvb.bID where bts.bID = ?', [$this->getBlockID()]);
                foreach($rows as $row){
                    Cache::delete('block', $row['bID'] . ':' . $c->getCollectionID() . ':' . $c->getVersionID() . ':' . $a->getAreaHandle());
                    Cache::delete('block_view_output', $c->getCollectionID() . ':' . $row['bID'] . ':' . $a->getAreaHandle());
                }
            }
        }
        */
    }

    /**
     * Set the collection containing the block.
     *
     * @param \Concrete\Core\Page\Collection\Collection $c
     */
    public function setBlockCollectionObject($c)
    {
        $this->c = $c;
        $this->cID = $c->getCollectionID();
    }

    /**
     * Get the block type name.
     *
     * @return string
     */
    public function getBlockTypeName()
    {
        return $this->btName;
    }

    /**
     * Get the ID of the user that added the block.
     *
     * @return int
     */
    public function getBlockUserID()
    {
        return $this->uID;
    }

    /**
     * Gets the date the block was added.
     *
     * @return string Date formated like: 2009-01-01 00:00:00
     */
    public function getBlockDateAdded()
    {
        return $this->bDateAdded;
    }

    /**
     * Gets the date the block was last modified.
     *
     * @return string Date formated like: 2009-01-01 00:00:00
     */
    public function getBlockDateLastModified()
    {
        return $this->bDateModified;
    }

    /**
     * Set the collection ID for the block action.
     *
     * @param int $bActionCID
     */
    public function setBlockActionCollectionID($bActionCID)
    {
        $this->bActionCID = $bActionCID;
    }

    /**
     * Return the URL of the edit block action.
     *
     * @return string
     */
    public function getBlockEditAction()
    {
        return $this->_getBlockAction();
    }

    /**
     * Return the base URL of a block action.
     *
     * @return string
     */
    public function _getBlockAction()
    {
        $app = Facade::getFacadeApplication();
        $request = $app->make('request');
        /* @var \Concrete\Core\Http\Request $request */
        $arHandle = rawurlencode((string) $this->getAreaHandle());
        $cID = ((int) $this->getBlockActionCollectionID()) ?: '';
        $bID = (int) $this->getBlockID();
        $result = DIR_REL . '/' . DISPATCHER_FILENAME . "?cID={$cID}&amp;bID={$bID}&amp;arHandle={$arHandle}";
        $step = $request->get('step');
        if (is_string($step) && $step !== '') {
            $result .= '&amp;step=' . rawurlencode($step);
        };
        $valt = $app->make('helper/validation/token');
        $result .= '&amp;ccm_token=' . $valt->generate();

        return $str;
    }

    /**
     * Get the block action collection id (or false if not found).
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
     * Return the URL of the update block information action.
     *
     * @return string
     */
    public function getBlockUpdateInformationAction()
    {
        $str = $this->_getBlockAction();

        return $str . '&amp;btask=update_information';
    }

    /**
     * Return the URL of the update block  action.
     *
     * @return string
     */
    public function getBlockUpdateCssAction()
    {
        $str = $this->_getBlockAction();

        return $str . '&amp;btask=update_block_css';
    }

    /**
     * Check if this block as an 'edit.php' file, meaning that it has an edit interface.
     *
     * @return bool
     */
    public function isEditable()
    {
        $bv = new BlockView($this);
        $path = $bv->getBlockPath(FILENAME_BLOCK_EDIT);
        if (file_exists($path . '/' . FILENAME_BLOCK_EDIT)) {
            return true;
        }

        return false;
    }

    /**
     * @param bool $forceDelete
     *
     * @deprecated Use the deleteBlock() method
     * @see deleteBlock
     */
    public function delete($forceDelete = false)
    {
        $this->deleteBlock($forceDelete);
    }

    /**
     * Delete this block.
     *
     * @param bool $forceDelete Force the block removed from everywhere, even if this block is an alias or in page type defaults (used by the administration console).
     *
     * @return bool Return false if this block is invalid, true otherwise.
     */
    public function deleteBlock($forceDelete = false)
    {
        if ($this->bID < 1) {
            return false;
        }

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $cID = $this->cID;
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();
        $bID = $this->bID;
        $arHandle = $this->arHandle;

        $db = Loader::db();

        // If this block is located in a master collection, we're going to delete all the instances of the block
        if (($c instanceof \Concrete\Core\Page\Page && $c->isMasterCollection() && !$this->isAlias()) || $forceDelete) {
            // This is an original: we're deleting it, and everything else having to do with it
            $db->executeQuery(
                'delete from CollectionVersionBlocks where bID = ?',
                [$bID]
            );
            $db->executeQuery(
                'delete from BlockPermissionAssignments where bID = ?',
                [$bID]
            );
            $db->executeQuery(
                'delete from CollectionVersionBlockStyles where bID = ?',
                [$bID]
            );
            $db->executeQuery(
                'delete from CollectionVersionBlocksCacheSettings where bID = ?',
                [$bID]
            );
        } else {
            $db->executeQuery(
                'delete from CollectionVersionBlocks where cID = ? and (cvID = ? or cbIncludeAll = 1) and bID = ? and arHandle = ?',
                [$cID, $cvID, $bID, $arHandle]
            );
            // Delete the groups instance of this block
            $db->executeQuery(
                'delete from BlockPermissionAssignments where bID = ? and cvID = ? and cID = ?',
                [$bID, $cvID, $cID]
            );
            $db->executeQuery(
                'delete from CollectionVersionBlockStyles where cID = ? and cvID = ? and bID = ? and arHandle = ?',
                [$cID, $cvID, $bID, $arHandle]
            );
            $db->executeQuery(
                'delete from CollectionVersionBlocksCacheSettings where cID = ? and cvID = ? and bID = ? and arHandle = ?',
                [$cID, $cvID, $bID, $arHandle]
            );
        }

        // Delete any feature assignments that have been attached to this block to the collection version
        $rows = $db->fetchAll(
            'select faID from BlockFeatureAssignments where cID = ? and cvID = ? and bID = ?',
            [$cID, $cvID, $bID]
        );
        foreach ($rows as $row) {
            $fa = FeatureAssignment::getByID($row['faID'], $c);
            if ($fa !== null) {
                $fa->delete();
            }
        }

        // See whether or not this block is aliased to anything else
        if (
            ((int) $db->fetchColumn('select count(*) from CollectionVersionBlocks where bID = ?', [$bID])) === 0
            &&
            ((int) $db->fetchColumn('select count(*) from btCoreScrapbookDisplay where bOriginalID = ?', [$bID])) === 0
        ) {
            $db->executeQuery('delete from BlockRelations where originalBID = ? or bID = ?', [$bID, $bID]);
            // This block is not referenced in the system any longer, so we delete the entry in the blocks table, as well as the entries in the corresponding sub-blocks table

            // so, first we delete the block's sub content
            $bt = $this->getBlockTypeObject();
            if ($bt && method_exists($bt, 'getBlockTypeClass')) {
                $class = $bt->getBlockTypeClass();
                if ($class) {
                    $bc = $app->build($class);
                    $bc->delete();
                }
            }

            // Now that the block's subcontent delete() method has been run, we delete the block from the Blocks table
            $db->executeQuery('delete from Blocks where bID = ?', [$bID]);

            // Aaaand then we delete all scrapbooked blocks to this entry
            $r = $db->executeQuery(
                '
                    select
                        cID,
                        cvID,
                        CollectionVersionBlocks.bID,
                        arHandle
                    from
                        CollectionVersionBlocks
                        inner join btCoreScrapbookDisplay on CollectionVersionBlocks.bID = btCoreScrapbookDisplay.bID
                    where
                    bOriginalID = ?
                ',
                [$bID]
            );
            while ($row = $r->fetch()) {
                $c = Page::getByID($row['cID'], $row['cvID']);
                if ($c) {
                    $b = self::getByID($row['bID'], $c, $row['arHandle']);
                    if ($b !== null) {
                        $b->deleteBlock();
                    }
                }
            }
        }

        return true;
    }

    /**
     * Is this block an alias?
     *
     * @param \Concrete\Core\Page\Collection\Collection $c
     *
     * @return bool
     */
    public function isAlias($c = null)
    {
        if ($c) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $db = Loader::db();
            $cID = $c->getCollectionID();
            $vo = $c->getVersionObject();
            $cvID = $vo->getVersionID();
            $r = $db->fetchColumn(
                'select bID from CollectionVersionBlocks where bID = ? and cID = ? and isOriginal = 0 and cvID = ? limit 1',
                [$this->bID, $cID, $cvID]
            );

            return $r !== false;
        } else {
            return !$this->isOriginal;
        }
    }

    /**
     * Set the original block ID.
     *
     * @param int $originalBID
     */
    public function setOriginalBlockID($originalBID)
    {
        $this->originalBID = $originalBID;
    }

    /**
     * Move this block.
     *
     * @param self|false $afterBlock Set to a block instance to move this block after $afterBlock, of set to something else to move this block at the top.
     */
    public function moveBlockToDisplayOrderPosition($afterBlock = null)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $c = $this->getBlockCollectionObject();
        if ($afterBlock instanceof self) {
            $block = self::getByID(
                $afterBlock->getBlockID(),
                $this->getBlockCollectionObject(),
                $this->getBlockAreaObject()
            );
            // Increase the display order of all blocks found after this one
            $db->executeQuery(
                '
                    update
                        CollectionVersionBlocks
                    set
                        cbDisplayOrder = cbDisplayOrder + 1
                    where
                        cID = ?
                        and (cvID = ? or cbIncludeAll = 1)
                        and arHandle = ?
                        and cbDisplayOrder > ?
                ',
                [
                    $c->getCollectionID(),
                    $c->getVersionID(),
                    $this->arHandle,
                    $block->getBlockDisplayOrder(),
                ]
            );
            // Set this block's display order to 1 + the current block
            $db->executeQuery(
                '
                    update
                        CollectionVersionBlocks
                    set
                        cbDisplayOrder = ?
                    where
                        bID = ?
                        and cID = ?
                        and (cvID = ? or cbIncludeAll = 1)
                        and arHandle = ?
                ',
                [
                    $block->getBlockDisplayOrder() + 1,
                    $this->getBlockID(),
                    $c->getCollectionID(),
                    $c->getVersionID(),
                    $this->arHandle,
                ]
            );
        } else {
            // Increase the display order of all blocks
            $db->executeQuery(
                '
                    update
                        CollectionVersionBlocks
                    set
                        cbDisplayOrder = cbDisplayOrder + 1
                    where
                        cID = ?
                        and (cvID = ? or cbIncludeAll = 1)
                        and arHandle = ?
                ',
                [
                    $c->getCollectionID(),
                    $c->getVersionID(),
                    $this->arHandle,
                ]
            );
            // Set this block's display order to 0
            $db->executeQuery(
                '
                    update
                        CollectionVersionBlocks
                    set
                        cbDisplayOrder = ?
                    where
                        bID = ?
                        and cID = ?
                        and (cvID = ? or cbIncludeAll = 1)
                        and arHandle = ?
                ',
                [
                    0,
                    $this->getBlockID(),
                    $c->getCollectionID(),
                    $c->getVersionID(),
                    $this->arHandle,
                ]
            );
        }
    }

    /**
     * Get the block display order.
     *
     * @return int
     */
    public function getBlockDisplayOrder()
    {
        return $this->cbDisplayOrder;
    }

    /**
     * Set the absolute block display order.
     *
     * @param int $displayOrder
     */
    public function setAbsoluteBlockDisplayOrder($displayOrder)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $cID = $this->cID;
        $bID = $this->bID;
        $arHandle = $this->arHandle;
        $c = $this->getBlockCollectionObject();
        $cvID = $c->getVersionID();

        $db->executeQuery(
            '
                update
                    CollectionVersionBlocks
                set
                    cbDisplayOrder = ?
                where
                    bID = ?
                    and cID = ?
                    and (cvID = ? or cbIncludeAll = 1)
                    and arHandle = ?
            ',
            [
                $displayOrder,
                $bID,
                $cID,
                $cvID,
                $arHandle,
            ]
        );
    }

    /**
     * Set the permissions of this block to override the ones of the area.
     */
    public function doOverrideAreaPermissions()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $c = $this->getBlockCollectionObject();
        $db->executeQuery(
            '
                update
                    CollectionVersionBlocks
                set
                    cbOverrideAreaPermissions = 1
                where
                    cID = ?
                    and (cvID = ? or cbIncludeAll = 1)
                    and bID = ?
                    and arHandle = ?
            ',
            [
                $c->getCollectionID(),
                $c->getVersionID(),
                $this->bID,
                $this->arHandle,
            ]
        );
        $db->executeQuery(
            '
                delete from
                    BlockPermissionAssignments
                where
                    cID = ?
                    and cvID = ?
                    and bID = ?
            ',
            [
                $c->getCollectionID(),
                $c->getVersionID(),
                $this->bID,
            ]
        );
        // Copy permissions from the page/area to this block
        $permissions = PermissionKey::getList('block');
        foreach ($permissions as $pk) {
            /* @var \Concrete\Core\Permission\Key\BlockKey $pk */
            $pk->setPermissionObject($this);
            $pk->copyFromPageOrAreaToBlock();
        }
    }

    /**
     * Set the block custom template.
     *
     * @param string $template
     */
    public function setCustomTemplate($template)
    {
        $data['bFilename'] = $template;
        $this->updateBlockInformation([
            'bFilename' => $template,
        ]);
    }

    /**
     * Update one or more block's information ('bName' for block name, 'bFilename' for block filename).
     *
     * @param array $data
     */
    public function updateBlockInformation(array $data)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $data += array(
            'bName' => $this->bName,
            'bFilename' => $this->bFilename,
        );

        $db->executeQuery(
            '
                update
                    Blocks
                set
                    bName = ?,
                    bFilename = ?,
                    bDateModified = ?
                where
                    bID = ?
            ',
            [
                $data['bName'],
                $data['bFilename'],
                $app->make('helper/date')->getOverridableNow(),
                $this->bID,
            ]
        );

        $this->refreshBlockOutputCache();
    }

    /**
     * Set the block name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->updateBlockInformation([
            'bName' => $name,
        ]);
    }

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

    /**
     * Export the data of this block.
     *
     * @param \SimpleXMLElement $node
     * @param string $exportType
     */
    public function export($node, $exportType = 'full')
    {
        if (!$this->isAliasOfMasterCollection()) {
            $blockNode = $node->addChild('block');
            $blockNode->addAttribute('type', $this->getBlockTypeHandle());
            $blockNode->addAttribute('name', $this->getBlockName());
            if ($this->getBlockFilename() != '') {
                $blockNode->addAttribute('custom-template', $this->getBlockFilename());
            }
            if (is_object($this->c) && $this->c->isMasterCollection()) {
                $app = Facade::getFacadeApplication();
                $mcBlockID = $app->make('helper/validation/identifier')->getString(8);
                ContentExporter::addMasterCollectionBlockID($this, $mcBlockID);
                $blockNode->addAttribute('mc-block-id', $mcBlockID);
            }
            if ($exportType === 'full') {
                $style = $this->getCustomStyle();
                if ($style !== null) {
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
                $bc = $this->getInstance();
                $bc->export($blockNode);
            }
        } else {
            $blockNode = $node->addChild('block');
            $blockNode->addAttribute('mc-block-id', ContentExporter::getMasterCollectionTemporaryBlockID($this));
        }
    }

    /**
     * Is this block an alias from master collection?
     *
     * @return bool
     */
    public function isAliasOfMasterCollection()
    {
        return $this->getBlockCollectionObject()->isBlockAliasedFromMasterCollection($this);
    }

    /**
     * Get the block name.
     *
     * @return string
     */
    public function getBlockName()
    {
        return $this->bName;
    }

    /**
     * Get the block file name.
     *
     * @return string
     */
    public function getBlockFilename()
    {
        return $this->bFilename;
    }
}
