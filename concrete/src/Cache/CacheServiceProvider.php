<?php

namespace Concrete\Core\Cache;

use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('cache', Level\ObjectCache::class);
        $this->app->singleton('cache/request', Level\RequestCache::class);
        $this->app->singleton('cache/expensive', Level\ExpensiveCache::class);
        $this->app->singleton('cache/overrides', Level\OverridesCache::class);
        $this->app->singleton('cache/page', function() {
            return PageCache::getLibrary();
        });
    }
}
