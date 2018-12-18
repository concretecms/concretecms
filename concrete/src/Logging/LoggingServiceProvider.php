<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Logging\Configuration\ConfigurationFactory;
use Psr\Log\NullLogger;

class LoggingServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->singleton(LoggerFactory::class, function ($app) {
            if ($app->isInstalled()) {
                $logger = new LoggerFactory($app->make(ConfigurationFactory::class), $app->make('director'));
            } else {
                $logger = new NullLogger();
            }
            return $logger;
        });

        $this->app->singleton('log/factory', function($app) {
            return $app->make(LoggerFactory::class);
        });

        $this->app->singleton('log/application', function($app) {
            if ($app->isInstalled()) {
                $factory = $app->make(LoggerFactory::class);
                return $factory->createLogger(Channels::CHANNEL_APPLICATION);
            } else {
                return new NullLogger();
            }
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
