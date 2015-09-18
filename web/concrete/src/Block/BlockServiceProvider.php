<?php
namespace Concrete\Core\Block;

use Concrete\Core\Foundation\Service\Provider;

class BlockServiceProvider extends Provider
{

    /**
     * Registers the services provided by this provider.
     * @return void
     */
    public function register()
    {
        // Bind the BlockCache
        $this->app->bindShared(BlockCache::class, function() {
            /** @type CacheLocal $cache */
            $block_cache = new BlockCache();
            $block_cache->setCacheLocalCache(\CacheLocal::get());

            return $block_cache;
        });

        $this->app->bindShared(BlockFactory::class, function() {
            return new BlockFactory($this->app, $this->app['database']->connection());
        });
    }

    /**
     * Returns an array of things that this provider provides
     * This is used to determine what the service provider changed
     * @return array
     */
    public function provides()
    {
        return array(
            BlockCache::class,
            BlockFactory::class
        );
    }

}

