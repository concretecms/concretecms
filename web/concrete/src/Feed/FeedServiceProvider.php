<?php
namespace Concrete\Core\Feed;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class FeedServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = array(
            'helper/feed' => '\Concrete\Core\Feed\FeedService',
        );

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }
    }
}
