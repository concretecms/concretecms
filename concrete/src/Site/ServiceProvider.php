<?php
namespace Concrete\Core\Site;

use Concrete\Core\Foundation\Service\Provider as BaseServiceProvider;
use Concrete\Core\Site\Resolver\MultisiteDriver;
use Concrete\Core\Site\Resolver\Resolver;
use Concrete\Core\Site\Resolver\StandardDriver;
use Concrete\Core\Url\DomainMapper\Map\Normalizer;
use Concrete\Core\Url\DomainMapper\Map\NormalizerInterface;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $app = $this->app;
        $this->app->singleton('site', function() use ($app) {
            return $app->make('Concrete\Core\Site\Service');
        });
        $this->app->singleton('site/type', function() use ($app) {
            return $app->make('Concrete\Core\Site\Type\Service');
        });

        $this->app->singleton(InstallationService::class);

        $this->app->bind(NormalizerInterface::class, Normalizer::class);

        $this->app->singleton('Concrete\Core\Site\Resolver\DriverInterface', function() use ($app) {
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
