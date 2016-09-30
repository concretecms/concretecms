<?php
namespace Concrete\Core\Cache\Adapter;

use Concrete\Core\Cache\Cache;
use Zend\Cache\Exception;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\Storage\FlushableInterface;
use Concrete\Core\Support\Facade\Application;

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
 * \@package Concrete\Core\Cache\Adapter
 */
class ZendCacheDriver extends AbstractAdapter implements StorageInterface, FlushableInterface
{
    /**
     * @var string Name of the cache being used when the localization is fully initialized
     */
    private $finalCacheName;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * Is the localization system ready?
     *
     * @var bool
     */
    private $localizationReady;

    /**
     * @var int Number of seconds to consider the cache fresh before it expires
     */
    protected $cacheLifetime;

    /**
     * @param string $finalCacheName Name of the cache being used when the localization is fully initialized. Defaults to cache
     * @param int $cacheLifetime Number of seconds to consider the cache fresh before it expires
     */
    public function __construct($finalCacheName = 'cache', $cacheLifetime = null)
    {
        parent::__construct();
        $this->setApplication(Application::getFacadeApplication());
        $this->localizationReady = false;
        $this->finalCacheName = $finalCacheName;
        $this->cacheLifetime = $cacheLifetime;
    }

    public function setApplication(\Concrete\Core\Application\Application $app)
    {
        $this->app = $app;
        $this->config = $app->make('config');
    }

    /**
     * Is the localization system ready?
     *
     * @return bool
     */
    protected function isLocalizationReady()
    {
        if (!$this->localizationReady) {
            if ($this->config->get('app.bootstrap.packages_loaded') === true) {
                $this->localizationReady = true;
            }
        }

        return $this->localizationReady;
    }

    /**
     * @return \Concrete\Core\Cache\Cache|null
     */
    protected function getCache()
    {
        if ($this->isLocalizationReady()) {
            return $this->app->make($this->finalCacheName);
        } else {
            return null;
        }
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
        $cache = $this->getCache();
        $key = 'zend/'.$normalizedKey;
        $item = ($cache === null) ? null : $cache->getItem('zend/'.$normalizedKey);
        if ($item === null || $item->isMiss()) {
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
        $cache = $this->getCache();
        if ($cache === null) {
            $result = false;
        } else {
            $item = $cache->getItem('zend/'.$normalizedKey);
            if ($result = $item->set($value, $this->cacheLifetime)) {
                $item->save();
            }
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
        $cache = $this->getCache();
        if ($cache === null) {
            $result = true;
        } else {
            $item = $cache->getItem('zend/'.$normalizedKey);
            $result = $item->clear();
        }

        return $result;
    }

    /**
     * Flush the whole storage (even when the localization system is not ready).
     *
     * @return bool
     */
    public function flush()
    {
        return $this->app->make($this->finalCacheName)->getItem('zend')->clear();
    }
}
