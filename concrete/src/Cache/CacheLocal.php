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
        if (!empty($id)) {
            return trim($group, '/') . '/' . trim($id, '/');
        } else {
            return trim($group, '/');
        }
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
            $item = $cache->getItem(self::key($type, $id));
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
            $cache->delete(self::key($type, $id));
        }
	}

	public static function set($type, $id, $object) {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/request');

        if (!$cache->isEnabled()) {
            return false;
        }

        if (is_object($object)) {
            $object = clone $object;
        }

        return $cache->getItem(self::key($type, $id))->set($object);
	}
}
