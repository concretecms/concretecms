<?php

namespace Concrete\Core\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as BaseServiceProvider;
use Concrete\Core\Site\Resolver\DriverInterface;
use Concrete\Core\Site\Resolver\MultisiteDriver;
use Concrete\Core\Site\Resolver\StandardDriver;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\Type\Service as SiteTypeService;
use Concrete\Core\Url\DomainMapper\Map\Normalizer;
use Concrete\Core\Url\DomainMapper\Map\NormalizerInterface;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->singleton(SiteService::class);
        $this->app->alias(SiteService::class, 'site');

        $this->app->singleton(SiteTypeService::class);
        $this->app->alias(SiteTypeService::class, 'site/type');

        $this->app->singleton(InstallationService::class);
        $this->app->bind(NormalizerInterface::class, Normalizer::class);

        $this->app->singleton(DriverInterface::class, function (Application $app) {
            $service = $app->make(InstallationService::class);
            if ($service->isMultisiteEnabled()) {
                $resolver = $this->app->make(MultisiteDriver::class);
            } else {
                $resolver = $this->app->make(StandardDriver::class);
            }
            return $resolver;
        });

        $this->app->singleton('Concrete\Core\Site\User\Group\Service');
        $this->app->singleton('Concrete\Core\Site\Type\Controller\Manager');
    }
}
