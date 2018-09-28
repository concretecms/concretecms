<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class LoggingServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the logger singleton
        $this->app->singleton(Logger::class, function ($app) {
            $logger = new Logger(Logger::CHANNEL_APPLICATION);
            return $logger;
        });

        // Bind the PSR-3 logger interface against the singleton
        $this->app->bind('Psr\Log\LoggerInterface', 'Concrete\Core\Logging\Logger');
        $this->app->singleton('log', function($app) {
            return $app->make(Logger::class);
        });
        $this->app->singleton('log/exceptions', function() {
            $logger = new Logger(LOG_TYPE_EXCEPTIONS);
            return $logger;
        });
    }
}
