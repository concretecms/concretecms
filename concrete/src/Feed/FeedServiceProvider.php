<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class FeedServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = [
            'helper/feed' => FeedService::class,
        ];
        foreach ($singletons as $alias => $className) {
            $this->app->singleton($className);
            $this->app->alias($className, $alias);
        }
    }
}
