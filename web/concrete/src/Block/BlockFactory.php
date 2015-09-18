<?php
namespace Concrete\Core\Block;

use Concrete\Core\Application\Application;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Collection\Collection;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Block factory class
 *
 * // Get a block from a specific area and page
 * \Core::make(BlockFactory::class)->withCollectionAndArea($collection, $area)->withId($block_id);
 *
 * // Get a block with only the ID
 * \Core::make(BlockFactory::class)->withId($block_id);
 *
 * // Get a block without cache
 * with(new BlockFactory())->withId($block_id);
 *
 * @package Concrete\Core\Block
 */
class BlockFactory implements LoggerAwareInterface
{

    /** @type BlockCache */
    protected $cache;

    /** @type Area */
    protected $area;

    /** @type Collection */
    protected $collection;

    /** @type Application */
    protected $app;

    /** @type LoggerInterface */
    protected $logger;

    /** @type Connection */
    protected $connection;

    /** @type string */
    protected $block_class = Block::class;

    /** @type string */
    protected $block_type_class = BlockType::class;

    /** @type string */
    protected $block_cache_class = BlockCache::class;

    /**
     * BlockFactory constructor.
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Application $app, Connection $connection)
    {
        $this->app = $app;
        $this->connection = $connection;
    }

    /**
     * @return BlockCache
     */
    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = $this->app->make($this->getBlockCacheClass());
        }

        return $this->cache;
    }

    /**
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->block_class;
    }

    /**
     * @return string
     */
    public function getBlockTypeClass()
    {
        return $this->block_type_class;
    }

    /**
     * @return string
     */
    public function getBlockCacheClass()
    {
        return $this->block_cache_class;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the class to use
     *
     * @param string $class The classname: Foo::class
     * @return $this
     */
    public function withClass($class)
    {
        $this->block_class = $class;

        return $this;
    }

    /**
     * Set the blocktype class to use
     *
     * @param string $class The classname: Foo::class
     * @return $this
     */
    public function withBlockTypeClass($class)
    {
        $this->block_type_class = $class;

        return $this;
    }

    /**
     * Set the BlockCache class to use
     *
     * @param string $class The classname: Foo::class
     * @return $this
     */
    public function withBlockCacheClass($class)
    {
        $this->block_cache_class = $class;

        return $this;
    }

    /**
     * Set the Logger to use, wrapper method for setLogger
     */
    public function withLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $logger;
    }

    /**
     * Set the collection and area to pull the block from
     *
     * @param \Concrete\Core\Page\Collection\Collection $collection
     * @param \Concrete\Core\Area\Area $area
     * @return $this
     */
    public function withCollectionAndArea(Collection $collection, Area $area)
    {
        $this->area = $area;
        $this->collection = $collection;

        return $this;
    }

    /**
     * Create the block with this id
     *
     * @todo Use new BlockTypeFactory when made
     * @param int $id
     * @return \Concrete\Core\Block\Block|null
     * @throws RuntimeException If blocktype controller doesn't exist.
     */
    public function withId($id)
    {
        $block = null;
        $cache = $this->getCache();
        $collection = $this->getCollection();
        $area = $this->getArea();

        // Lets see if there's a block in the cache.
        if ($cache && $block = $cache->fetchBlock($id, $collection, $area)) {
            return $block;
        }

        $query = $this->fetchQuery((int) $id);
        $block = $this->getBootstrappedBlock();

        if ($row = $query->fetch()) {
            $block->setPropertiesFromArray($row);

            $this->populateController($block);

            if ($cache) {
                $cache->save($block);
            }
        }

        $query->closeCursor();
        return $block;
    }

    /**
     * Bootstrap a block and set the area and collection, or just make it an "original block"
     * @return \Concrete\Core\Block\Block
     */
    protected function getBootstrappedBlock()
    {
        $class = $this->getClass();

        /** @type Block $instance */
        $instance = $this->app->make($class);

        if (($collection = $this->getCollection()) && $area = $this->getArea()) {
            $instance->setArea($area);
            $instance->setCollection($collection);
        } else {
            $instance->setIsOriginal(true);
        }

        return $instance;
    }

    /**
     * Get the query statement instance
     *
     * @todo Use query builder.
     * @param int|string $id Int for by ID, string for by name
     * @return \Doctrine\DBAL\Driver\Statement
     */
    protected function fetchQuery($id)
    {
        if (($collection = $this->getCollection()) && $area = $this->getArea()) {
            $collection_version = $collection->getVersionObject();
            $collection_version_id = $collection_version->getVersionID();

            $value = array($area->getAreaHandle(), $collection->getCollectionID(), $collection_version_id, $id);

            $query = "select CollectionVersionBlocks.isOriginal, CollectionVersionBlocks.cbIncludeAll, " .
                "Blocks.btCachedBlockRecord, BlockTypes.pkgID, CollectionVersionBlocks.cbOverrideAreaPermissions, " .
                "CollectionVersionBlocks.cbOverrideBlockTypeCacheSettings, " .
                "CollectionVersionBlocks.cbOverrideBlockTypeContainerSettings, " .
                "CollectionVersionBlocks.cbEnableBlockContainer, CollectionVersionBlocks.cbDisplayOrder, " .
                "Blocks.bIsActive, Blocks.bID, Blocks.btID, bName, bDateAdded, bDateModified, bFilename, btHandle, " .
                "Blocks.uID from CollectionVersionBlocks inner join Blocks on " .
                "(CollectionVersionBlocks.bID = Blocks.bID) inner join BlockTypes on (Blocks.btID = BlockTypes.btID) " .
                "where CollectionVersionBlocks.arHandle = ? and CollectionVersionBlocks.cID = ? " .
                "and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) " .
                "and CollectionVersionBlocks.bID = ?";
        } else {
            // just grab really specific block stuff
            $query = "select bID, bIsActive, BlockTypes.btID, Blocks.btCachedBlockRecord, BlockTypes.btHandle, " .
                "BlockTypes.pkgID, BlockTypes.btName, bName, bDateAdded, bDateModified, bFilename, Blocks.uID  " .
                "from Blocks inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where bID = ?";

            $value = array($id);
        }

        return $this->getConnection()->executeQuery($query, $value);
    }

    /**
     * Populate the BlockController instance
     *
     * @todo Defer to BlockControllerFactory when built
     * @param Block $block
     * @throws RuntimeException
     */
    protected function populateController(Block $block)
    {
        $block_type_class = $this->getBlockTypeClass();
        $block_type = $block_type_class::getByID($block->getBlockTypeID());

        if (!($class = $block_type->getBlockTypeClass())) {
            if ($logger = $this->getLogger()) {
                $logger->critical(
                    'BlockType has no resolvable BlockController class.',
                    array($block, $this));
            }
            throw new RuntimeException("BlockType has no resolvable BlockController class.");
        }

        $block->setControllerInstance(new $class($block));
    }

}
