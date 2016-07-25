<?php
namespace Concrete\Core\Page\Type;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Page\Type\Validator\Manager as ValidatorManager;
use Concrete\Core\Page\Type\Saver\Manager as SaverManager;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['manager/page_type/validator'] = $this->app->share(function ($app) {
            return new ValidatorManager($app);
        });
        $this->app['manager/page_type/saver'] = $this->app->share(function ($app) {
            return new SaverManager($app);
        });
    }
}
