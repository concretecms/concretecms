<?php

namespace Concrete\Core\Cache;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Command\ClearCacheCommandHandler;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Psr\Log\LoggerInterface;

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
        $this->app
            ->when(ClearCacheCommandHandler::class)
            ->needs(LoggerInterface::class)
            ->give(function (Application $app) {
                $factory = $app->make(LoggerFactory::class);
                return $factory->createLogger(Channels::CHANNEL_OPERATIONS);
            });
    }
}
