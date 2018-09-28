<?php

namespace Concrete\Core\Cache;

use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    public function register()
    {
        foreach ([
            'cache' => Level\ObjectCache::class,
            'cache/request' => Level\RequestCache::class,
            'cache/expensive' => Level\ExpensiveCache::class,
            'cache/overrides' => Level\OverridesCache::class,
        ] as $alias => $class) {
            $this->app->singleton($class);
            $this->app->alias($class, $alias);
        }
        $this->app->singleton('cache/page', function () {
            return PageCache::getLibrary();
        });
    }
}
