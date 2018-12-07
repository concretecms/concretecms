<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Logging\Configuration\ConfigurationFactory;

class LoggingServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->singleton(LoggerFactory::class, function ($app) {
            $logger = new LoggerFactory($app->make(ConfigurationFactory::class), $app->make('director'));
            return $logger;
        });

        $this->app->singleton('log/application', function($app) {
            $factory = $app->make(LoggerFactory::class);
            return $factory->createLogger(Channels::CHANNEL_APPLICATION);
        });

        // Bind the PSR-3 logger interface against the singleton
        $this->app->bind('Psr\Log\LoggerInterface', 'log/application');

        $this->app->singleton('log/exceptions', function($app) {
            $factory = $app->make(LoggerFactory::class);
            return $factory->createLogger(Channels::CHANNEL_EXCEPTIONS);
            return $logger;
        });

    }
}
