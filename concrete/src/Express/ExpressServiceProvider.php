<?php
namespace Concrete\Core\Express;

use Concrete\Core\Express\Entry\Formatter\EntryFormatterInterface;
use Concrete\Core\Express\Entry\Formatter\LabelFormatter as EntryLabelFormatter;
use Concrete\Core\Express\Formatter\FormatterInterface;
use Concrete\Core\Express\Formatter\LabelFormatter;
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
        $this->app->singleton('Concrete\Core\Express\Association\Applier');
        $this->app->singleton('express', function() use ($app) {
           return $app->make('Concrete\Core\Express\ObjectManager');
        });
        $this->app->singleton('Concrete\Core\Express\Controller\Manager');

        $this->app->bind(FormatterInterface::class, LabelFormatter::class);
        $this->app->bind(EntryFormatterInterface::class, EntryLabelFormatter::class);
    }
}
