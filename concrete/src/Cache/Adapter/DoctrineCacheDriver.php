<?php
namespace Concrete\Core\Cache\Adapter;

use Concrete\Core\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use Core;

/**
 * Simple cache driver that enables doctrine to use c5's caching library.
 *
 * \@package Concrete\Core\Cache
 */
class DoctrineCacheDriver extends CacheProvider
{
    /** @var Cache  */
    private $c5Cache = null;
    /**
     * @var string Name of the cache being used
     */
    private $cacheName;

    /**
     * @param string $cacheName Name of the cache being used. Defaults to cache.
     */
    public function __construct($cacheName = 'cache')
    {
        $this->cacheName = $cacheName;
    }

    /**
     * @return Cache
     */
    private function getC5Cache()
    {
        if ($this->c5Cache === null) {
            $this->c5Cache = Core::make($this->cacheName);
        }

        return $this->c5Cache;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        if (!$this->getC5Cache()->isEnabled()) {
            return false;
        }
        $item = $this->getC5Cache()->getItem('doctrine/' . $id);

        return $item->isMiss() ? false : $item->get();
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        if (!$this->getC5Cache()->isEnabled()) {
            return false;
        }

        return $this->getC5Cache()->exists('doctrine/' . $id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $cache = $this->getC5Cache();
        if (!$cache->isEnabled()) {
            return false;
        }
        if ($lifeTime === 0) {
            $lifeTime = null;
        }

        return $cache->save($cache->getItem('doctrine/' . $id)->set($data, $lifeTime));
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        if (!$this->getC5Cache()->isEnabled()) {
            return false;
        }

        return $this->getC5Cache()->delete('doctrine/' . $id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        if (!$this->getC5Cache()->isEnabled()) {
            return false;
        }

        return $this->getC5Cache()->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return null;
    }
}
