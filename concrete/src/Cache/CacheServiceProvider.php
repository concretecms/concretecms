<?php
/**
 * Created by IntelliJ IDEA.
 * User: christopher
 * Date: 9/4/14
 * Time: 12:42 AM
 */

namespace Concrete\Core\Cache;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    public function register() {
        $this->app->singleton('cache', '\Concrete\Core\Cache\Level\ObjectCache');
        $this->app->singleton('cache/request', '\Concrete\Core\Cache\Level\RequestCache');
        $this->app->singleton('cache/expensive', '\Concrete\Core\Cache\Level\ExpensiveCache');
    }
} 