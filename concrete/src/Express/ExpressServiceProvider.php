<?php

namespace Concrete\Core\Express;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ExpressServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Service\Provider::register()
     */
    public function register()
    {
        foreach([
            Association\Applier::class => '',
            Controller\Manager::class => '',
            Form\Control\Type\Manager::class => 'express/control/type/manager',
            ObjectAssociationBuilder::class => 'express/builder/association',
            ObjectManager::class => 'express',
        ] as $class => $alias) {
            $this->app->singleton($class);
            if ($alias !== '') {
                $this->app->alias($class, $alias);
            }
        }
        $this->app->bind(Formatter\FormatterInterface::class, Formatter\LabelFormatter::class);
        $this->app->bind(Entry\Formatter\EntryFormatterInterface::class, Entry\Formatter\LabelFormatter::class);
    }
}
