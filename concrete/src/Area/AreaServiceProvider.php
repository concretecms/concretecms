<?php

namespace Concrete\Core\Area;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class AreaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CustomStyleRepository::class);
    }
}