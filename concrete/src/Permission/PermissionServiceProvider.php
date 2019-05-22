<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $app = $this->app;
        $this->app->singleton('Concrete\Core\Permission\Inheritance\Registry\BlockRegistry');
        $this->app->singleton('permission/access/entity/factory', function() use ($app) {
            return new Access\Entity\Factory($app);
        });
    }
}
