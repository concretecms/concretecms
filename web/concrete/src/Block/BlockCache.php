<?php
namespace Concrete\Core\Block;

use Concrete\Core\Cache\CacheLocal;

class BlockCache
{

    /** @type CacheLocal */
    protected $cache;

    /**
     * @return CacheLocal
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param CacheLocal $cache
     */
    public function setCacheLocalCache(CacheLocal $cache)
    {
        $this->cache = $cache;
    }

    public function has($block_id, \Collection $collection = null, \Area $area = null)
    {
        return $this->getCache()->has('block', $this->getCacheIdentifier($block_id, $collection, $area));
    }

    /**
     * Returns the block if it exists in the cache. You should call has before calling this.
     *
     * @param $block_id
     * @param \Collection|null $collection
     * @param \Area|null $area
     * @return \Concrete\Core\Block\Block|null
     */
    public function fetchBlock($block_id, \Collection $collection = null, \Area $area = null)
    {
        return $this->getCache()->getEntry('block', $this->getCacheIdentifier($block_id, $collection, $area));
    }

    /**
     * Saves a block to the cache
     *
     * @param \Concrete\Core\Block\Block $block
     * @return bool
     */
    public function save(Block $block)
    {
        return $this->getCache()->set('block', $this->getCacheIdentifier($block->getBlockID()), $block);
    }

    protected function getCacheIdentifier($block_id, \Collection $collection = null, \Area $area = null)
    {
        if ($collection && $area) {
            return implode(':', array(
                $block_id,
                $collection->getCollectionID(),
                $collection->getVersionID(),
                $area->getAreaHandle()));
        }

        return $block_id;
    }

}
