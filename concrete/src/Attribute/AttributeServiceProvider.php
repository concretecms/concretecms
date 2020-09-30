<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Category\Manager;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class AttributeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('manager/attribute/category', function($app) {
            return new Manager($app);
        });
    }
}
