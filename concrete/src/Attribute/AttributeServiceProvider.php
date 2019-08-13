<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Category\Manager;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

/**
 * @since 8.0.0
 */
class AttributeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['manager/attribute/category'] = $this->app->share(function ($app) {
            return new Manager($app);
        });

    }
}
