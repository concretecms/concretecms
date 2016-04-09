<?php
namespace Concrete\Core\Express;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ExpressServiceProvider extends ServiceProvider
{
    public function register()
    {
        $app = $this->app;
        $this->app->bindShared('express/builder/association', function () use ($app) {
            return $app->make('Concrete\Core\Express\ObjectAssociationBuilder');
        });
        $this->app->bindShared('express/control/type/manager', function () use ($app) {
            return $app->make('Concrete\Core\Express\Form\Control\Type\Manager');
        });
    }
}
