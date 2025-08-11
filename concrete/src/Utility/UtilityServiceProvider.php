<?php

namespace Concrete\Core\Utility;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class UtilityServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = [
            'helper/text' => Service\Text::class,
            'helper/arrays' => Service\Arrays::class,
            'helper/number' => Service\Number::class,
            'helper/xml' => Service\Xml::class,
            'helper/url' => Service\Url::class,
        ];
        foreach ($singletons as $alias => $className) {
            $this->app->alias($className, $alias);
            $this->app->singleton($className);
        }
    }
}
