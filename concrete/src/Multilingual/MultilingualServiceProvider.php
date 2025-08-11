<?php
namespace Concrete\Core\Multilingual;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class MultilingualServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('multilingual/interface/flag', Service\UserInterface\Flag::class);
        $this->app->bind('multilingual/extractor', Service\Extractor::class);
        foreach ([
            'multilingual/detector' => Service\Detector::class,
        ] as $alias => $class) {
            $this->app->singleton($class);
            $this->app->alias($class, $alias);
        }
    }
}
