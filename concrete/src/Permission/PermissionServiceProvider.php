<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Page\Type\Validator\Manager as ValidatorManager;
use Concrete\Core\Page\Type\Saver\Manager as SaverManager;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Concrete\Core\Permission\Inheritance\Registry\BlockRegistry');
    }
}
