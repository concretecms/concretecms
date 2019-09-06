<?php

namespace Concrete\Core\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as BaseServiceProvider;
use Concrete\Core\Site\Resolver\DriverInterface;
use Concrete\Core\Site\Resolver\StandardDriver;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\Type\Service as SiteTypeService;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->singleton(SiteService::class);
        $this->app->alias(SiteService::class, 'site');

        $this->app->singleton(SiteTypeService::class);
        $this->app->alias(SiteTypeService::class, 'site/type');

        $this->app->singleton(DriverInterface::class, function (Application $app) {
            return $this->app->make(StandardDriver::class);
        });
    }
}
