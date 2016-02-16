<?php
namespace Concrete\Core\Cache;

/**
 * Manages opcaches.
 */
class OpCache
{
    /**
     * Clear the opcache.
     *
     * @param string|null $file If it's specified, we'll try to clear the cache only for this file
     */
    public static function clear($file = null)
    {
        if (static::hasEAccelerator()) {
            if (function_exists('eeaccelerator_clear')) {
                $paths = @ini_get('eaccelerator.allowed_admin_path');
                if (is_string($paths) && ($paths !== '')) {
                    $myPath = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__);
                    foreach (explode(PATH_SEPARATOR, $paths) as $path) {
                        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
                        if ($path !== '') {
                            $path = rtrim($path, '/');
                            if (($path === $myPath) || (strpos($myPath, $path.'/') === 0)) {
                                @eeaccelerator_clear();
                                break;
                            }
                        }
                    }
                }
            }
        }
        if (static::hasAPC()) {
            if (function_exists('apc_clear_cache')) {
                @apc_clear_cache('system');
            }
        }
        if (static::hasXCache()) {
            if (function_exists('xcache_clear_cache') && ini_get('xcache.admin.user') && ini_get('xcache.admin.pass')) {
                @xcache_clear_cache(XC_TYPE_PHP, 0);
            }
        }
        if (static::hasWinCache()) {
            if (function_exists('wincache_refresh_if_changed')) {
                if ($file) {
                    @wincache_refresh_if_changed((array) $file);
                } else {
                    @wincache_refresh_if_changed();
                }
            }
        }
        if (static::hasZendOpCache()) {
            if ($file && function_exists('opcache_invalidate')) {
                @opcache_invalidate($file, true);
            } elseif (function_exists('opcache_reset')) {
                @opcache_reset();
            }
        }
    }

    /**
     * Is eAccelerator installed and enabled?
     *
     * @return bool
     */
    public static function hasEAccelerator()
    {
        return extension_loaded('eaccelerator') && ini_get('eaccelerator.enable');
    }

    /**
     * Is Alternative PHP Cache (APC) installed and enabled?
     *
     * @return bool
     */
    public static function hasAPC()
    {
        return extension_loaded('apc') && ini_get('apc.enabled');
    }

    /**
     * Is XCache installed and enabled?
     *
     * @return bool
     */
    public static function hasXCache()
    {
        return extension_loaded('xcache') && ini_get('xcache.cacher') && (ini_get('xcache.size') !== 0) && (ini_get('xcache.size') !== '0');
    }

    /**
     * Is WinCacne installed and enabled?
     *
     * @return bool
     */
    public static function hasWinCache()
    {
        return extension_loaded('wincache') && ini_get('wincache.ocenabled');
    }

    /**
     * Is Zend OPCache installed and enabled?
     *
     * @return bool
     */
    public static function hasZendOpCache()
    {
        return extension_loaded('Zend OPcache') && ini_get('opcache.enable');
    }
}
