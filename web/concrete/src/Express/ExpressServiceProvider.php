<?php
namespace Concrete\Core\Express;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ExpressServiceProvider extends ServiceProvider
{

    public function register()
    {
        $app = $this->app;
        $this->app->bindShared('express.builder.object', function() use ($app) {
            return $app->make('Concrete\Core\Express\ObjectBuilder');
        });
        $this->app->bindShared('express.builder.association', function() use ($app) {
            return $app->make('Concrete\Core\Express\ObjectAssociationBuilder');
        });
        $this->app->bindShared('express.writer', function() use ($app) {
            $writer = $app->make('Concrete\Core\Express\EntityWriter');
            $writer->setNamespace($app['config']->get('express.entity_classes.namespace'));
            $writer->setOutputPath($app['config']->get('express.entity_classes.output_path'));
            return $writer;
        });
        $this->app->bindShared('express', function() use ($app) {
            return $app->make('Concrete\Core\Express\ObjectManager');
        });
    }

}
