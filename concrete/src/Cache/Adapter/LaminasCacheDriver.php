<?php
namespace Concrete\Core\Cache\Adapter;

use Concrete\Core\Cache\Cache;
use Core;
use Laminas\Cache\Exception;
use Laminas\Cache\Storage\Adapter\AbstractAdapter;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\Storage\FlushableInterface;

/**
 * Class LaminasCacheDriver
 * Adapter class to hook Laminas's cache into Concrete's cache.
 *
 * By passing this class into various Laminas classes, it tells Laminas use it for storing and retrieving
 * cache values. Values are passed through here and onto Concrete's caching layer which uses the
 * Stash library. Allows us to use many of the helpful Laminas classes without having to maintain
 * a separate cache configuration.
 *
 * Currently used by:
 *
 *     - Concrete\Core\Feed\FeedService
 *     - Concrete\Core\Localization\Localization
 *
 * \@package Concrete\Core\Cache\Adapter
 */
class LaminasCacheDriver extends AbstractAdapter implements StorageInterface, FlushableInterface
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
     *
     * @return mixed Data on success, null on failure
     *
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItem(&$normalizedKey, &$success = null, &$casToken = null)
    {
        /** @var Cache $cache  */
        $cache = Core::make($this->cacheName);
        $item = $cache->getItem('laminas/'.$normalizedKey);
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
     *
     * @return bool
     *
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItem(&$normalizedKey, &$value)
    {
        /** @var Cache $cache  */
        $cache = Core::make($this->cacheName);
        $item = $cache->getItem('laminas/'.$normalizedKey);
        if ($this->cacheLifetime !== null) {
            $item->setTTL($this->cacheLifetime);
        }

        if ($result = $item->set($value)) {
            $item->save();
        }

        return $result;
    }

    /**
     * Internal method to remove an item.
     *
     * @param  string $normalizedKey
     *
     * @return bool
     *
     * @throws Exception\ExceptionInterface
     */
    protected function internalRemoveItem(&$normalizedKey)
    {
        /** @var Cache $cache  */
        $cache = Core::make($this->cacheName);
        $item = $cache->getItem('laminas/'.$normalizedKey);

        return $item->clear();
    }

    /**
     * Flush the whole storage.
     *
     * @return bool
     */
    public function flush()
    {
        return Core::make($this->cacheName)->getItem('laminas')->clear();
    }
}
