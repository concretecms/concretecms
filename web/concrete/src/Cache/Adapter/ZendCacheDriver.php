<?php

namespace Concrete\Core\Cache\Adapter;

use Concrete\Core\Cache\Cache;
use Core;
use Zend\Cache\Exception;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\Storage\FlushableInterface;

/**
 * Class ZendCacheDriver
 * Adapter class to hook Zend's cache into Concrete5's cache.
 * 
 * By passing this class into various Zend classes, it tells Zend use it for storing and retrieving
 * cache values. Values are passed through here and onto Concrete5's caching layer which uses the
 * Stash library. Allows us to use many of the helpful Zend classes without having to maintain
 * a separate cache configuration.
 * 
 * Currently used by:
 * 
 *     - Concrete\Core\Feed\FeedService
 *     - Concrete\Core\Localization\Localization
 *
 * @package Concrete\Core\Cache\Adapter
 */
class ZendCacheDriver extends AbstractAdapter implements StorageInterface, FlushableInterface
{
    /**
     * @var string Name of the cache being used
     */
    private $cacheName;

    /**
     * @var int Number of seconds to consider the cache fresh before it expires
     */
    protected $cacheLifetime;

    /**
     * @param string $cacheName Name of the cache being used. Defaults to cache.
     * @param int $cacheLifetime Number of seconds to consider the cache fresh before it expires.
     */
    public function __construct($cacheName = 'cache', $cacheLifetime = null)
    {
        parent::__construct();

        $this->cacheName = $cacheName;
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Internal method to get an item.
     *
     * @param  string $normalizedKey
     * @param  bool $success
     * @param  mixed $casToken
     * @return mixed Data on success, null on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        /** @var Cache $cache  */
        $cache = Core::make($this->cacheName);
        $item = $cache->getItem('zend/'.$normalizedKey);
        if ($item->isMiss()) {
            $success = false;

            return null;
        } else {
            $success = true;

            return $item->get();
        }
    }

    /**
     * Internal method to store an item.
     *
     * @param  string $normalizedKey
     * @param  mixed $value
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItem(& $normalizedKey, & $value)
    {
        /** @var Cache $cache  */
        $cache = Core::make($this->cacheName);
        $item = $cache->getItem('zend/'.$normalizedKey);

        return $item->set($value, $this->cacheLifetime);
    }

    /**
     * Internal method to remove an item.
     *
     * @param  string $normalizedKey
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalRemoveItem(& $normalizedKey)
    {
        /** @var Cache $cache  */
        $cache = Core::make($this->cacheName);
        $item = $cache->getItem('zend/'.$normalizedKey);

        return $item->clear();
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return Core::make($this->cacheName)->getItem('zend')->clear();
    }
}
