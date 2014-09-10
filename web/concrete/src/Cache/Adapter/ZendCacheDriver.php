<?php

namespace Concrete\Core\Cache\Adapter;


use Concrete\Core\Cache\Cache;
use Core;
use Zend\Cache\Exception;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\StorageInterface;

/**
 * Class ZendCacheDriver
 * Adapter class to hook Zend's cache into Concrete5's cache.
 *
 * @package Concrete\Core\Cache\Adapter
 */
class ZendCacheDriver extends AbstractAdapter implements StorageInterface
{

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
        $cache = Core::make('cache');
        $item = $cache->getItem('zend/' . $normalizedKey);
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
        $cache = Core::make('cache');
        $item = $cache->getItem('zend/' . $normalizedKey);
        return $item->set($value);
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
        $cache = Core::make('cache');
        $item = $cache->getItem('zend/' . $normalizedKey);
        return $item->clear();
    }
}
