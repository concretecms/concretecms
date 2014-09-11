<?php

namespace Concrete\Core\Cache;
use Core;

/**
 * @deprecated
 * @package Concrete\Core\Cache
 */
class CacheLocal
{
    /**
     * Creates a cache key based on the group and id by running it through md5
     * @param string $group Name of the cache group
     * @param string $id Name of the cache item ID
     * @return string The cache key
     */
    public static function key($group, $id)
    {
        return $group . '/' . $id;
    }

	public static function get() {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}
		return $instance;
	}

	public static function getEntry($type, $id) {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/request');
        if ($cache->isEnabled()) {
            $item = $cache->getItem($type . '/' . str_replace('/', '_', $id));
            if (!$item->isMiss()) {
                return $item->get();
            }
        }
	}

	public static function flush() {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/request');
        $cache->flush();
	}

	public static function delete($type, $id) {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/request');
        if ($cache->isEnabled()) {
            $cache->delete($type . '/' . str_replace('/', '_', $id));
        }
	}

	public static function set($type, $id, $object) {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/request');

        if (!$cache->isEnabled()) {
            return false;
        }

        return $cache->getItem($type . '/' . str_replace('/', '_', $id))->set($object);
	}
}
